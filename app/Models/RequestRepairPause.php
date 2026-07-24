<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRepairPause extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_repair_id',
        'no',
        'cycle_number',
        'alasan',
        'paused_at',
        'resumed_at',
        'paused_by',
        'durasi_paused_seconds',
    ];

    protected $casts = [
        'paused_at'  => 'datetime',
        'resumed_at' => 'datetime',
    ];

    // ── Daftar alasan pause yang valid ───────────────────────
    const ALASAN_ADJUST_DIMENSI = 'adjust_dimensi';
    const ALASAN_REPAIR_LINE    = 'repair_line';
    const ALASAN_TRIAL          = 'trial';
    const ALASAN_CEK_DIES       = 'cek_dies';
    const ALASAN_MEETING        = 'meeting';

    const ALASAN_LABELS = [
        self::ALASAN_ADJUST_DIMENSI => 'Adjust Dimensi',
        self::ALASAN_REPAIR_LINE    => 'Repair di Line',
        self::ALASAN_TRIAL          => 'Trial',
        self::ALASAN_CEK_DIES       => 'Cek Dies',
        self::ALASAN_MEETING        => 'Meeting',
    ];

    // ── Relations ───────────────────────────────────────────
    public function requestRepair()
    {
        return $this->belongsTo(RequestRepair::class);
    }

    public function pausedBy()
    {
        return $this->belongsTo(User::class, 'paused_by');
    }

    // ── Helper ──────────────────────────────────────────────
    public function getAlasanLabelAttribute(): string
    {
        return self::ALASAN_LABELS[$this->alasan] ?? $this->alasan;
    }
}