<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RequestRepair extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'tanggal_pengajuan',
        'group',
        'shift',
        'jumlah_stroke',
        'line_mesin',
        'barang_id',
        'part_no',
        'nama',
        'process_no',
        'customer',
        'jenis',
        'target_selesai',
        'kategori_problem',
        'detail_proyek',
        'status',
        // Additional info on trial
        'penyebab_vc',
        'tindakan_repair',
        // Closed info
        'status_after_trial',
        'point_verifikasi',
        'approval_section_chief',
        // Status timestamps
        'on_process_at',
        'on_trial_at',
        'closed_at',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'target_selesai'    => 'date',
        'jumlah_stroke'     => 'integer',
        'on_process_at'     => 'datetime',
        'on_trial_at'       => 'datetime',
        'closed_at'         => 'datetime',
    ];

    // ── Constants ───────────────────────────────────────────
    const STATUS_ON_PROCESS = 'on_process';
    const STATUS_ON_TRIAL   = 'on_trial';
    const STATUS_CLOSED     = 'closed';

    const ROLES_TO_ON_TRIAL = [1, 2, 3, 7];
    const ROLES_TO_CLOSED   = [1, 4];

    // ── Relations ───────────────────────────────────────────
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_ON_PROCESS;
    }

    public function canConfirmToOnTrial(): bool
    {
        return $this->status === self::STATUS_ON_PROCESS;
    }

    public function canConfirmToClosed(): bool
    {
        return $this->status === self::STATUS_ON_TRIAL;
    }

    // ── Durasi helpers ──────────────────────────────────────
    public function getDurasiOnProcessSeconds(): ?int
    {
        $start = $this->on_process_at ?? $this->created_at;
        $end   = $this->on_trial_at ?? now();
        return $start ? (int) $start->diffInSeconds($end) : null;
    }

    public function getDurasiOnTrialSeconds(): ?int
    {
        if (!$this->on_trial_at) return null;
        $end = $this->closed_at ?? now();
        return (int) $this->on_trial_at->diffInSeconds($end);
    }

    public function getDurasiTotalSeconds(): ?int
    {
        $start = $this->on_process_at ?? $this->created_at;
        $end   = $this->closed_at ?? now();
        return $start ? (int) $start->diffInSeconds($end) : null;
    }

    public static function formatDurasi(int $seconds): string
    {
        $days    = intdiv($seconds, 86400);
        $hours   = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $parts = [];
        if ($days)    $parts[] = "{$days} hari";
        if ($hours)   $parts[] = "{$hours} jam";
        if ($minutes) $parts[] = "{$minutes} menit";

        return $parts ? implode(' ', $parts) : '< 1 menit';
    }

    public function getTimelineData(): array
    {
        $start = $this->on_process_at ?? $this->created_at;

        $onProcessSec = $this->on_trial_at && $start
            ? (int) $start->diffInSeconds($this->on_trial_at)
            : null;

        $onTrialSec = ($this->on_trial_at && $this->closed_at)
            ? (int) $this->on_trial_at->diffInSeconds($this->closed_at)
            : null;

        $totalSec = ($start && $this->closed_at)
            ? (int) $start->diffInSeconds($this->closed_at)
            : null;

        return [
            'on_process_at'             => $start?->toISOString(),
            'on_trial_at'               => $this->on_trial_at?->toISOString(),
            'closed_at'                 => $this->closed_at?->toISOString(),
            'durasi_on_process'         => $onProcessSec !== null ? self::formatDurasi($onProcessSec) : null,
            'durasi_on_trial'           => $onTrialSec   !== null ? self::formatDurasi($onTrialSec)   : null,
            'durasi_total'              => $totalSec      !== null ? self::formatDurasi($totalSec)     : null,
            'durasi_on_process_seconds' => $onProcessSec,
            'durasi_on_trial_seconds'   => $onTrialSec,
        ];
    }

    // ── Auto-generate nomor RR ──────────────────────────────
    public static function generateNo(): string
    {
        $year   = now()->format('Y');
        $prefix = "RR-{$year}-";

        $last = static::where('no', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(no, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('no');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}