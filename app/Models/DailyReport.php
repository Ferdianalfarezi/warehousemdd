<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id', 'tanggal_kerja', 'shift',
        'shift_start_at', 'shift_end_at',
        'total_durasi_seconds', 'total_bersih_seconds', 'overtime_seconds',
        'catatan', 'last_synced_at',
    ];

    protected $casts = [
        'tanggal_kerja'  => 'date',
        'shift_start_at' => 'datetime',
        'shift_end_at'   => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    // ── Window shift ────────────────────────────────────────
    // Shift 1 : 07:00 – 16:00 (hari yang sama)
    // Shift 2 : 20:00 – 05:00 (nyebrang ke besoknya)
    public const SHIFT_WINDOWS = [
        1 => ['start' => '07:00', 'end' => '16:00', 'cross_day' => false],
        2 => ['start' => '20:00', 'end' => '05:00', 'cross_day' => true],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(DailyReportItem::class);
    }

    /**
     * Hitung window shift (datetime aktual) dari tanggal kerja + nomor shift.
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function windowShift(Carbon $tanggalKerja, int $shift): array
    {
        $cfg = self::SHIFT_WINDOWS[$shift] ?? self::SHIFT_WINDOWS[1];

        [$sh, $sm] = explode(':', $cfg['start']);
        [$eh, $em] = explode(':', $cfg['end']);

        $start = $tanggalKerja->copy()->startOfDay()->setTime((int) $sh, (int) $sm);
        $end   = $tanggalKerja->copy()->startOfDay()->setTime((int) $eh, (int) $em);

        if ($cfg['cross_day']) {
            $end->addDay();
        }

        return [$start, $end];
    }

    public function shiftLabel(): string
    {
        $cfg = self::SHIFT_WINDOWS[$this->shift] ?? self::SHIFT_WINDOWS[1];
        return 'Shift ' . $this->shift . ' (' . $cfg['start'] . '–' . $cfg['end'] . ')';
    }

    /** Durasi window shift dalam detik (buat hitung coverage). */
    public function shiftDurationSeconds(): int
    {
        if (!$this->shift_start_at || !$this->shift_end_at) return 0;
        return (int) $this->shift_start_at->diffInSeconds($this->shift_end_at);
    }

    public static function formatDurasi(?int $seconds): string
    {
        $seconds = max(0, (int) $seconds);
        if ($seconds < 60) return $seconds . ' detik';

        $hari  = intdiv($seconds, 86400);
        $jam   = intdiv($seconds % 86400, 3600);
        $menit = intdiv($seconds % 3600, 60);

        $parts = [];
        if ($hari)  $parts[] = $hari . ' hari';
        if ($jam)   $parts[] = $jam . 'j';
        if ($menit) $parts[] = $menit . 'm';

        return $parts ? implode(' ', $parts) : '0m';
    }
}