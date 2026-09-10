<?php

namespace App\Console\Commands;

use App\Http\Controllers\DailyReportController;
use App\Models\User;
use App\Services\DailyReportSync;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * PENTING — kenapa command ini perlu di-cron:
 *
 * RequestRepair yang Closed (OK) itu di-HARD DELETE, dan yang NG di-reset
 * (on_process_at di-null-in + pics()->detach()). Kalau sync cuma jalan pas
 * halaman dibuka, aktivitas yang keburu Closed sebelum halaman pernah dibuka
 * bakal hilang selamanya.
 *
 * Cron tiap 10–15 menit bikin datanya keburu "dipotret" ke daily_report_items
 * sebelum sumbernya hilang.
 *
 *   Kernel.php:
 *   $schedule->command('daily-report:sync')->everyTenMinutes();
 */
class SyncDailyReports extends Command
{
    protected $signature = 'daily-report:sync
                            {--days=2 : Berapa hari ke belakang yang ikut di-sync}
                            {--user= : Sync 1 user aja (id)}';

    protected $description = 'Sinkronisasi daily report otomatis untuk user role member (role_id 7)';

    public function handle(DailyReportSync $sync): int
    {
        $days = max(1, (int) $this->option('days'));

        $users = User::query()
            ->when($this->option('user'), fn ($q) => $q->where('id', $this->option('user')))
            ->when(!$this->option('user'), fn ($q) => $q->where('role_id', DailyReportController::ROLE_MEMBER))
            ->get();

        if ($users->isEmpty()) {
            $this->warn('Tidak ada user yang perlu di-sync.');
            return self::SUCCESS;
        }

        $base  = DailyReportSync::tanggalKerja(now());
        $count = 0;

        foreach ($users as $user) {
            for ($i = 0; $i < $days; $i++) {
                $tgl = $base->copy()->subDays($i);
                try {
                    $sync->sync($user, $tgl);
                    $count++;
                } catch (\Exception $e) {
                    Log::error('DailyReport Sync Command Error', [
                        'user_id' => $user->id,
                        'tanggal' => $tgl->toDateString(),
                        'error'   => $e->getMessage(),
                    ]);
                    $this->error("Gagal: user {$user->id} tanggal {$tgl->toDateString()} — {$e->getMessage()}");
                }
            }
        }

        $this->info("Selesai. {$count} laporan ter-sync ({$users->count()} user × {$days} hari).");

        return self::SUCCESS;
    }
}