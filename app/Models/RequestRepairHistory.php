<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRepairHistory extends Model
{
    protected $table = 'request_repair_histories';

    protected $fillable = [
        'barang_id',
        'no',
        'tanggal_pengajuan',
        'group',
        'shift',
        'jumlah_stroke',
        'line_mesin',
        'part_no',
        'nama',
        'process_no',
        'customer',
        'jenis',
        'target_selesai',
        'kategori_problem',
        'detail_proyek',
        // On Trial — Section 1: Tindakan Perbaikan
        'analisa_penyebab',
        'tindakan_perbaikan',
        'catatan_penggantian_sparepart',
        // On Trial — Section 2: Penanganan Problem Burry
        'item',
        'proses_grinding',
        'shim_up',
        'status_burry',
        'standart_burry',
        'group_leader',
        'operator',
        // On Trial — Section 3: Target Trial After Repair
        'plan',
        'actual',
        'remark',
        'judge',
        // Closed — Section 1: Monitoring Dies Temporary
        'tanggal_cek',
        'lot_prod',
        'awal',
        'tengah',
        'akhir',
        'qty',
        'remark_monitoring',
        'judge_monitoring',
        // Closed — Section 2: Target Permanen Action
        'plan_permanen',
        'actual_permanen',
        'rootcause',
        'recovery',
        'assy_trial_check',
        'judge_permanen',
        // Timestamps & durasi
        'on_process_at',
        'on_trial_at',
        'closed_at',
        'durasi_on_process_seconds',
        'durasi_on_trial_seconds',
        'durasi_total_seconds',
        'repair_count',
    ];

    protected $casts = [
        'tanggal_pengajuan'         => 'date',
        'target_selesai'            => 'date',
        'tanggal_cek'               => 'date',
        'jumlah_stroke'             => 'integer',
        'on_process_at'             => 'datetime',
        'on_trial_at'               => 'datetime',
        'closed_at'                 => 'datetime',
        'durasi_on_process_seconds' => 'integer',
        'durasi_on_trial_seconds'   => 'integer',
        'durasi_total_seconds'      => 'integer',
        'repair_count'              => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // ── Format durasi ────────────────────────────────────────
    public static function formatDurasi(?int $seconds): string
    {
        if (!$seconds || $seconds <= 0) return '-';

        $days    = intdiv($seconds, 86400);
        $hours   = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $parts = [];
        if ($days)    $parts[] = $days    . ' hari';
        if ($hours)   $parts[] = $hours   . ' jam';
        if ($minutes) $parts[] = $minutes . ' menit';

        return $parts ? implode(' ', $parts) : '< 1 menit';
    }

    // ── Timeline data ────────────────────────────────────────
    public function getTimelineData(): array
    {
        return [
            'on_process_at'     => $this->on_process_at?->toISOString(),
            'on_trial_at'       => $this->on_trial_at?->toISOString(),
            'closed_at'         => $this->closed_at?->toISOString(),
            'durasi_on_process' => self::formatDurasi($this->durasi_on_process_seconds),
            'durasi_on_trial'   => self::formatDurasi($this->durasi_on_trial_seconds),
            'durasi_total'      => self::formatDurasi($this->durasi_total_seconds),
        ];
    }

    // ── Summary per part_no ──────────────────────────────────
    public static function getSummaryByPartNo(string $partNo): array
    {
        $all = self::where('part_no', $partNo)
            ->orderBy('closed_at', 'asc')
            ->get();

        $totalRepair = $all->count();

        // Hitung OK/NG dari judge_permanen, fallback ke judge_monitoring
        $totalOk = $all->filter(fn($h) =>
            ($h->judge_permanen ?? $h->judge_monitoring) === 'OK'
        )->count();

        $totalNg = $all->filter(fn($h) =>
            ($h->judge_permanen ?? $h->judge_monitoring) === 'NG'
        )->count();

        $avgSeconds = $totalRepair > 0
            ? (int) $all->avg('durasi_total_seconds')
            : 0;

        return [
            'total_repair'    => $totalRepair,
            'total_ok'        => $totalOk,
            'total_ng'        => $totalNg,
            'avg_durasi'      => self::formatDurasi($avgSeconds),
            'avg_durasi_secs' => $avgSeconds,
        ];
    }
}