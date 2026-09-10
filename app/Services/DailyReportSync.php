<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\DailyReportItem;
use App\Models\GeneralCheckup;
use App\Models\RequestRepair;
use App\Models\RequestRepairPause;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nyusun daily report otomatis dari aktivitas yang udah tercatat di sistem.
 *
 * Sumber baris:
 *   - RequestRepair  : on_process_at → on_trial_at        (kategori: repair)
 *       ↳ dipotong tiap pause; tiap pause jadi baris sendiri pakai alasan-nya
 *   - GeneralCheckup : mulai_perbaikan → waktu_selesai    (kategori: checkup)
 *
 * TIDAK dicatat: on_trial → closed. Itu nunggu hasil lot produksi,
 * bisa berhari-hari, bukan kerja aktif.
 */
class DailyReportSync
{
    /** Aktivitas jam 06:00 hari ini s/d 05:59 besok = tanggal kerja HARI INI. */
    public const CUTOFF_HOUR = 6;

    /** Gap minimal (detik) yang dianggap "tidak tercatat" dan ditawarin diisi. */
    public const GAP_MIN_SECONDS = 300;

    // ════════════════════════════════════════════════════════
    // HELPER TANGGAL KERJA
    // ════════════════════════════════════════════════════════

    public static function tanggalKerja($datetime): Carbon
    {
        $d = Carbon::parse($datetime);
        if ($d->hour < self::CUTOFF_HOUR) {
            $d = $d->copy()->subDay();
        }
        return $d->copy()->startOfDay();
    }

    /** @return array{0: Carbon, 1: Carbon} window tanggal kerja [06:00, 06:00 besok) */
    public static function windowTanggalKerja(Carbon $tanggalKerja): array
    {
        $start = $tanggalKerja->copy()->startOfDay()->addHours(self::CUTOFF_HOUR);
        return [$start, $start->copy()->addDay()];
    }

    // ════════════════════════════════════════════════════════
    // SYNC UTAMA
    // ════════════════════════════════════════════════════════

    public function sync(User $user, Carbon $tanggalKerja): DailyReport
    {
        $tanggalKerja = $tanggalKerja->copy()->startOfDay();
        [$winStart, $winEnd] = self::windowTanggalKerja($tanggalKerja);

        $report = DailyReport::firstOrCreate(
            ['user_id' => $user->id, 'tanggal_kerja' => $tanggalKerja->toDateString()],
            ['shift' => 1]
        );

        $segments = $this->collectSegments($user, $winStart, $winEnd);

        DB::transaction(function () use ($report, $segments, $user) {
            $touchedRefs  = [];
            $producedKeys = [];

            foreach ($segments as $seg) {
                $ref = $seg['source_type'] . '|' . $seg['source_id'] . '|' . $seg['cycle_number'];
                $touchedRefs[]  = $ref;
                $producedKeys[] = $ref . '|' . $seg['segment_index'];

                DailyReportItem::updateOrCreate(
                    [
                        'daily_report_id' => $report->id,
                        'source_type'     => $seg['source_type'],
                        'source_id'       => $seg['source_id'],
                        'cycle_number'    => $seg['cycle_number'],
                        'segment_index'   => $seg['segment_index'],
                    ],
                    [
                        'user_id'        => $user->id,
                        'mulai_at'       => $seg['mulai_at'],
                        'selesai_at'     => $seg['selesai_at'],
                        'durasi_seconds' => $seg['durasi_seconds'],
                        'kategori'       => $seg['kategori'],
                        'ref_no'         => $seg['ref_no'],
                        'ref_part_no'    => $seg['ref_part_no'],
                        'ref_nama'       => $seg['ref_nama'],
                        'is_auto'        => true,
                        'is_running'     => $seg['is_running'],
                    ]
                );
            }

            $touchedRefs  = array_flip(array_unique($touchedRefs));
            $producedKeys = array_flip(array_unique($producedKeys));

            // Bersihin segmen basi — TAPI cuma buat source yang barusan kita proses.
            // Source yang udah gak ada (RR Closed OK → hard delete) dibiarin,
            // itu justru arsip yang harus dipertahankan.
            DailyReportItem::where('daily_report_id', $report->id)
                ->where('is_auto', true)
                ->get()
                ->each(function (DailyReportItem $it) use ($touchedRefs, $producedKeys) {
                    $ref = $it->source_type . '|' . $it->source_id . '|' . $it->cycle_number;
                    if (isset($touchedRefs[$ref]) && !isset($producedKeys[$ref . '|' . $it->segment_index])) {
                        $it->delete();
                    }
                });
        });

        return $this->recalculate($report->fresh());
    }

    // ════════════════════════════════════════════════════════
    // KUMPULIN SEGMEN
    // ════════════════════════════════════════════════════════

    protected function collectSegments(User $user, Carbon $winStart, Carbon $winEnd): array
    {
        $now  = now();
        $segs = [];

        // ── 1. Request Repair (user sebagai PIC) ────────────
        $rrs = RequestRepair::query()
            ->whereNotNull('on_process_at')
            ->where('on_process_at', '<', $winEnd)
            ->whereHas('pics', fn ($q) => $q->where('users.id', $user->id))
            ->get();

        foreach ($rrs as $rr) {
            $start = Carbon::parse($rr->on_process_at);
            $end   = $rr->on_trial_at ? Carbon::parse($rr->on_trial_at) : $now->copy();
            if ($end->lte($start)) continue;

            $stillRunning = !$rr->on_trial_at;

            // Pause siklus berjalan, di-clip ke rentang repair
            $blocks = [];
            $pauses = RequestRepairPause::where('request_repair_id', $rr->id)
                ->where('cycle_number', $rr->cycle_number)
                ->orderBy('paused_at')
                ->get();

            foreach ($pauses as $p) {
                $ps = Carbon::parse($p->paused_at);
                $pe = $p->resumed_at ? Carbon::parse($p->resumed_at) : $now->copy();
                if ($pe->lte($start) || $ps->gte($end)) continue;

                $blocks[] = [
                    'pause'   => $p,
                    'start'   => $ps->lt($start) ? $start->copy() : $ps,
                    'end'     => $pe->gt($end) ? $end->copy() : $pe,
                    'running' => !$p->resumed_at,
                ];
            }

            // Jalan dari awal: repair dipotong tiap pause
            $cursor = $start->copy();
            $idx    = 0;

            foreach ($blocks as $b) {
                if ($b['start']->gt($cursor)) {
                    $segs[] = $this->buildSegment([
                        'mulai'       => $cursor,
                        'selesai'     => $b['start'],
                        'kategori'    => 'repair',
                        'source_type' => DailyReportItem::SRC_REPAIR,
                        'source_id'   => $rr->id,
                        'cycle'       => (int) $rr->cycle_number,
                        'index'       => $idx++,
                        'ref_no'      => $rr->no,
                        'ref_part_no' => $rr->part_no,
                        'ref_nama'    => $rr->nama,
                        'running'     => false,
                    ], $winStart, $winEnd);
                }

                $segs[] = $this->buildSegment([
                    'mulai'       => $b['start'],
                    'selesai'     => $b['end'],
                    'kategori'    => $b['pause']->alasan,
                    'source_type' => DailyReportItem::SRC_PAUSE,
                    'source_id'   => $b['pause']->id,
                    'cycle'       => (int) $b['pause']->cycle_number,
                    'index'       => 0,
                    'ref_no'      => $rr->no,
                    'ref_part_no' => $rr->part_no,
                    'ref_nama'    => $rr->nama,
                    'running'     => $b['running'],
                ], $winStart, $winEnd);

                if ($b['end']->gt($cursor)) $cursor = $b['end']->copy();
            }

            if ($cursor->lt($end)) {
                $segs[] = $this->buildSegment([
                    'mulai'       => $cursor,
                    'selesai'     => $end,
                    'kategori'    => 'repair',
                    'source_type' => DailyReportItem::SRC_REPAIR,
                    'source_id'   => $rr->id,
                    'cycle'       => (int) $rr->cycle_number,
                    'index'       => $idx++,
                    'ref_no'      => $rr->no,
                    'ref_part_no' => $rr->part_no,
                    'ref_nama'    => $rr->nama,
                    'running'     => $stillRunning,
                ], $winStart, $winEnd);
            }
        }

        // ── 2. General Checkup ──────────────────────────────
        if (Schema::hasColumn('general_checkups', 'dikerjakan_oleh')) {
            $checkups = GeneralCheckup::query()
                ->whereNotNull('mulai_perbaikan')
                ->where('dikerjakan_oleh', $user->id)
                ->where('mulai_perbaikan', '<', $winEnd)
                ->get();

            foreach ($checkups as $ck) {
                $start = Carbon::parse($ck->mulai_perbaikan);
                $end   = $ck->waktu_selesai ? Carbon::parse($ck->waktu_selesai) : $now->copy();
                if ($end->lte($start)) continue;

                $segs[] = $this->buildSegment([
                    'mulai'       => $start,
                    'selesai'     => $end,
                    'kategori'    => 'checkup',
                    'source_type' => DailyReportItem::SRC_CHECKUP,
                    'source_id'   => $ck->id,
                    'cycle'       => 0,
                    'index'       => 0,
                    'ref_no'      => null,
                    'ref_part_no' => $ck->kode_barang,
                    'ref_nama'    => $ck->nama,
                    'running'     => !$ck->waktu_selesai,
                ], $winStart, $winEnd);
            }
        }

        return array_values(array_filter($segs));
    }

    /** Clip segmen ke window tanggal kerja. Return null kalau di luar window. */
    protected function buildSegment(array $s, Carbon $winStart, Carbon $winEnd): ?array
    {
        $mulai   = $s['mulai']->lt($winStart) ? $winStart->copy() : $s['mulai']->copy();
        $selesai = $s['selesai']->gt($winEnd) ? $winEnd->copy()   : $s['selesai']->copy();

        if ($selesai->lte($mulai)) return null;

        // Kalau ujungnya kepotong window, berarti buat hari ini dia gak "berjalan"
        $running = $s['running'] && $selesai->equalTo($s['selesai']);

        return [
            'mulai_at'       => $mulai,
            'selesai_at'     => $running ? null : $selesai,
            'durasi_seconds' => (int) $mulai->diffInSeconds($selesai),
            'kategori'       => $s['kategori'],
            'source_type'    => $s['source_type'],
            'source_id'      => $s['source_id'],
            'cycle_number'   => $s['cycle'],
            'segment_index'  => $s['index'],
            'ref_no'         => $s['ref_no'],
            'ref_part_no'    => $s['ref_part_no'],
            'ref_nama'       => $s['ref_nama'],
            'is_running'     => $running,
        ];
    }

    // ════════════════════════════════════════════════════════
    // HITUNG ULANG HEADER
    // ════════════════════════════════════════════════════════

    public function recalculate(DailyReport $report): DailyReport
    {
        $now   = now();
        $items = $report->items()->orderBy('mulai_at')->orderBy('id')->get();

        // 1. Durasi tiap baris (baris berjalan dihitung sampai sekarang)
        foreach ($items as $it) {
            $end = $it->selesai_at ?: $now;
            $d   = max(0, (int) $it->mulai_at->diffInSeconds($end));
            if ((int) $it->durasi_seconds !== $d) {
                $it->durasi_seconds = $d;
                $it->saveQuietly();
            }
        }

        // 2. Flag overlap
        $arr = $items->values()->all();
        foreach ($arr as $i => $a) {
            $aEnd    = $a->selesai_at ?: $now;
            $overlap = false;
            foreach ($arr as $j => $b) {
                if ($i === $j) continue;
                $bEnd = $b->selesai_at ?: $now;
                if ($a->mulai_at->lt($bEnd) && $b->mulai_at->lt($aEnd)) { $overlap = true; break; }
            }
            if ((bool) $a->is_overlap !== $overlap) {
                $a->is_overlap = $overlap;
                $a->saveQuietly();
            }
        }

        // 3. Shift — ditebak dari jam mulai aktivitas paling awal
        $shift = (int) ($report->shift ?: 1);
        if ($items->count()) {
            $h     = $items->first()->mulai_at->hour;
            $shift = ($h >= 6 && $h < 18) ? 1 : 2;
        }
        [$shiftStart, $shiftEnd] = DailyReport::windowShift(
            Carbon::parse($report->tanggal_kerja), $shift
        );

        // 4. Union interval → total bersih & overtime
        $union    = $this->unionIntervals($items, $now);
        $bersih   = 0;
        $overtime = 0;

        foreach ($union as [$s, $e]) {
            $dur     = (int) $s->diffInSeconds($e);
            $bersih += $dur;

            $ovStart = $s->lt($shiftStart) ? $shiftStart : $s;
            $ovEnd   = $e->gt($shiftEnd)   ? $shiftEnd   : $e;
            $inside  = $ovEnd->gt($ovStart) ? (int) $ovStart->diffInSeconds($ovEnd) : 0;

            $overtime += ($dur - $inside);
        }

        $report->update([
            'shift'                => $shift,
            'shift_start_at'       => $shiftStart,
            'shift_end_at'         => $shiftEnd,
            'total_durasi_seconds' => (int) $items->sum('durasi_seconds'),
            'total_bersih_seconds' => $bersih,
            'overtime_seconds'     => max(0, $overtime),
            'last_synced_at'       => $now,
        ]);

        return $report->fresh();
    }

    /** Gabungin interval yang tumpang tindih jadi satu. */
    protected function unionIntervals($items, Carbon $now): array
    {
        $pairs = [];
        foreach ($items as $it) {
            $pairs[] = [$it->mulai_at->copy(), ($it->selesai_at ?: $now)->copy()];
        }
        usort($pairs, fn ($a, $b) => $a[0]->timestamp <=> $b[0]->timestamp);

        $union = [];
        foreach ($pairs as [$s, $e]) {
            if ($e->lte($s)) continue;
            $n = count($union);
            if ($n && $s->lte($union[$n - 1][1])) {
                if ($e->gt($union[$n - 1][1])) $union[$n - 1][1] = $e;
            } else {
                $union[] = [$s, $e];
            }
        }
        return $union;
    }

    // ════════════════════════════════════════════════════════
    // GAP — jam dalam shift yang belum ada aktivitasnya
    // ════════════════════════════════════════════════════════

    public function buildGaps(DailyReport $report): array
    {
        $now   = now();
        $items = $report->items()->orderBy('mulai_at')->get();

        $from = $report->shift_start_at ? $report->shift_start_at->copy() : null;
        $to   = $report->shift_end_at   ? $report->shift_end_at->copy()   : null;
        if (!$from || !$to) return [];

        // Kalau shift-nya masih jalan, gap cuma sampai jam sekarang
        if ($now->lt($to)) $to = $now->copy();
        if ($to->lte($from)) return [];

        $gaps   = [];
        $cursor = $from->copy();

        foreach ($this->unionIntervals($items, $now) as [$s, $e]) {
            if ($e->lte($from) || $s->gte($to)) continue;
            $s = $s->lt($from) ? $from->copy() : $s;
            $e = $e->gt($to)   ? $to->copy()   : $e;

            if ($s->gt($cursor) && $cursor->diffInSeconds($s) >= self::GAP_MIN_SECONDS) {
                $gaps[] = ['mulai_at' => $cursor->copy(), 'selesai_at' => $s->copy()];
            }
            if ($e->gt($cursor)) $cursor = $e->copy();
        }

        if ($cursor->lt($to) && $cursor->diffInSeconds($to) >= self::GAP_MIN_SECONDS) {
            $gaps[] = ['mulai_at' => $cursor->copy(), 'selesai_at' => $to->copy()];
        }

        return $gaps;
    }
}