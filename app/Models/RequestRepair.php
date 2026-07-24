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
        'line_id',
        'line_mesin',
        // Status timestamps
        'on_process_at',
        'on_trial_at',
        // NG attempt tracking
        'ng_attempt_count',
        // Pause/Resume tracking (⬅️ baru)
        'is_paused',
        'paused_at',
        'pause_reason',
        'total_paused_seconds',
        'cycle_number',
    ];

    protected $casts = [
        'tanggal_pengajuan'    => 'date',
        'jumlah_stroke'        => 'integer',
        'kekuatan_stock_fg'    => 'integer',
        'on_process_at'        => 'datetime',
        'on_trial_at'          => 'datetime',
        'ng_attempt_count'     => 'integer',
        'is_paused'            => 'boolean',   // ⬅️ baru
        'paused_at'            => 'datetime',  // ⬅️ baru
        'total_paused_seconds' => 'integer',   // ⬅️ baru
        'cycle_number'         => 'integer',   // ⬅️ baru
    ];

    // ── Constants ───────────────────────────────────────────
    const STATUS_OPEN       = 'open';
    const STATUS_ON_PROCESS = 'on_process';
    const STATUS_ON_TRIAL   = 'on_trial';
    const STATUS_CLOSED     = 'closed';

    const ROLES_TO_ON_PROCESS = [1, 7];
    const ROLES_TO_ON_TRIAL   = [1, 2, 3, 7]; // sudah tidak dipakai untuk gate on_trial (digantikan PIC + admin override), dibiarkan untuk referensi
    const ROLES_TO_CLOSED     = [1, 8, 4];

    const ROLE_ADMIN_OVERRIDE = 1; // role yang bisa override PIC di transisi on_process -> on_trial, dan pause/resume

    // ── Relations ───────────────────────────────────────────
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'request_repair_pics', 'request_repair_id', 'user_id')
            ->withTimestamps();
    }

    public function attempts() // ⬅️ baru — riwayat percobaan yang berakhir NG
    {
        return $this->hasMany(RequestRepairAttempt::class);
    }

    public function pauses() // ⬅️ baru — log pause/resume
    {
        return $this->hasMany(RequestRepairPause::class);
    }

    // ── Accessor gambar_url ─────────────────────────────────
    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    // ── Helpers ─────────────────────────────────────────────
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function canConfirmToProcess(): bool
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

    /**
     * Cek apakah user tertentu tercatat sebagai salah satu PIC request repair ini.
     * Pakai collection yang sudah di-eager-load kalau ada (hindari N+1 query di list).
     */
    public function isPic(int $userId): bool
    {
        if ($this->relationLoaded('pics')) {
            return $this->pics->contains('id', $userId);
        }
        return $this->pics()->where('users.id', $userId)->exists();
    }

    /**
     * Aturan konfirmasi On Process -> On Trial:
     * harus salah satu PIC yang dipilih waktu On Process, ATAU role admin override (role_id 1).
     */
    public function canUserConfirmToOnTrial(User $user): bool
    {
        return $this->canConfirmToOnTrial()
            && ($user->role_id === self::ROLE_ADMIN_OVERRIDE || $this->isPic($user->id));
    }

    /**
     * ⬅️ baru — Aturan Pause/Resume: sama seperti konfirmasi On Trial —
     * harus PIC yang tercatat ATAU admin override (role_id 1), dan status masih On Process.
     */
    public function canUserPause(User $user): bool
    {
        return $this->status === self::STATUS_ON_PROCESS
            && ($user->role_id === self::ROLE_ADMIN_OVERRIDE || $this->isPic($user->id));
    }

    /**
     * Nama-nama PIC, digabung koma. Dipakai untuk badge tabel & pesan error.
     */
    public function picNamesString(): string
    {
        $names = $this->relationLoaded('pics')
            ? $this->pics->pluck('nama')
            : $this->pics()->pluck('nama');

        return $names->filter()->implode(', ') ?: '-';
    }

    // ── Durasi helpers ──────────────────────────────────────
    public function getDurasiOnProcessSeconds(): ?int
    {
        $start = $this->on_process_at ?? $this->created_at;
        $end   = $this->on_trial_at ?? now();
        return $start ? max(0, (int) $start->diffInSeconds($end) - $this->total_paused_seconds) : null;
    }

    public function getDurasiOnTrialSeconds(): ?int
    {
        if (!$this->on_trial_at) return null;
        return (int) $this->on_trial_at->diffInSeconds(now());
    }

    public function getDurasiTotalSeconds(): ?int
    {
        $start = $this->on_process_at ?? $this->created_at;
        return $start ? max(0, (int) $start->diffInSeconds(now()) - $this->total_paused_seconds) : null;
    }

    public static function formatDurasi(int $seconds): string
    {
        $days    = intdiv($seconds, 86400);
        $hours   = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs    = $seconds % 60;

        $hoursStr   = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutesStr = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $secsStr    = str_pad($secs, 2, '0', STR_PAD_LEFT);

        if ($days > 0) {
            return "{$days}H.{$hoursStr}.{$minutesStr}.{$secsStr}";
        }

        return "{$hoursStr}.{$minutesStr}.{$secsStr}";
    }

    public function getTimelineData(): array
    {
        $start = $this->on_process_at ?? $this->created_at;

        $onProcessSec = $this->on_trial_at && $start
            ? max(0, (int) $start->diffInSeconds($this->on_trial_at) - $this->total_paused_seconds)
            : null;

        return [
            'on_process_at'             => $this->on_process_at?->toISOString(),
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