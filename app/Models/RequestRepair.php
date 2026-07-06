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
        'kekuatan_stock_fg',
        'kategori_problem',
        'detail_proyek',
        'gambar',
        'created_by',
        'status',
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
        'line_id',    // ⬅️ baru
        'line_mesin',
        // Status timestamps
        'on_process_at',
        'on_trial_at',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'jumlah_stroke'     => 'integer',
        'kekuatan_stock_fg'  => 'integer',
        'on_process_at'     => 'datetime',
        'on_trial_at'       => 'datetime',
    ];

    // ── Constants ───────────────────────────────────────────
    const STATUS_OPEN       = 'open';       // ⬅️ baru
    const STATUS_ON_PROCESS = 'on_process';
    const STATUS_ON_TRIAL   = 'on_trial';
    const STATUS_CLOSED     = 'closed';

    const ROLES_TO_ON_PROCESS = [1, 2, 3, 7]; // ⬅️ baru — open → on_process
    const ROLES_TO_ON_TRIAL   = [1, 2, 3, 7];
    const ROLES_TO_CLOSED     = [1, 4];

    // ── Relations ───────────────────────────────────────────
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function line() // ⬅️ baru
    {
        return $this->belongsTo(Line::class);
    }

    // ── Accessor gambar_url ─────────────────────────────────
    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    // ── Helpers ─────────────────────────────────────────────
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_OPEN; // ⬅️ diubah, dulu on_process
    }

    public function canConfirmToProcess(): bool // ⬅️ baru
    {
        return $this->status === self::STATUS_OPEN;
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
        return (int) $this->on_trial_at->diffInSeconds(now());
    }

    public function getDurasiTotalSeconds(): ?int
    {
        $start = $this->on_process_at ?? $this->created_at;
        return $start ? (int) $start->diffInSeconds(now()) : null;
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

        return [
            'on_process_at'             => $this->on_process_at?->toISOString(), // ⬅️ diubah, jangan fallback ke created_at biar keliatan kapan mulai diproses beneran
            'on_trial_at'               => $this->on_trial_at?->toISOString(),
            'closed_at'                 => null,
            'durasi_on_process'         => $onProcessSec !== null ? self::formatDurasi($onProcessSec) : null,
            'durasi_on_trial'           => null,
            'durasi_total'              => null,
            'durasi_on_process_seconds' => $onProcessSec,
            'durasi_on_trial_seconds'   => null,
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