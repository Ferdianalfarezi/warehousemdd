<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportItem extends Model
{
    protected $fillable = [
        'daily_report_id', 'user_id',
        'mulai_at', 'selesai_at', 'durasi_seconds',
        'kategori',
        'source_type', 'source_id', 'cycle_number', 'segment_index',
        'ref_no', 'ref_part_no', 'ref_nama',
        'keterangan', 'is_auto', 'is_overlap', 'is_running',
    ];

    protected $casts = [
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
        'is_auto'    => 'boolean',
        'is_overlap' => 'boolean',
        'is_running' => 'boolean',
    ];

    // ── Source type ─────────────────────────────────────────
    public const SRC_REPAIR  = 'request_repair';
    public const SRC_PAUSE   = 'request_repair_pause';
    public const SRC_CHECKUP = 'general_checkup';
    public const SRC_MANUAL  = 'manual';

    // ── Master kategori ─────────────────────────────────────
    // key adjust_dimensi..meeting sengaja disamain sama
    // RequestRepairPause::ALASAN_LABELS biar bisa dipetakan langsung.
    public const KATEGORI_LABELS = [
        'repair'         => 'Repair',
        'checkup'        => 'General Checkup',
        'adjust_dimensi' => 'Adjust Dimensi',
        'repair_line'    => 'Repair di Line',
        'trial'          => 'Trial',
        'cek_dies'       => 'Cek Dies',
        'meeting'        => 'Meeting',
        'briefing'       => 'Briefing / 5R',
        'bantu_line'     => 'Bantu Line',
        'administrasi'   => 'Administrasi',
        'lain'           => 'Lain-lain',
    ];

    /** Kategori yang boleh dipilih user pas nambah baris manual. */
    public const KATEGORI_MANUAL = [
        'adjust_dimensi', 'repair_line', 'trial', 'cek_dies', 'meeting',
        'briefing', 'bantu_line', 'administrasi', 'lain',
    ];

    public const KATEGORI_WARNA = [
        'repair'         => ['bg' => '#dbeafe', 'text' => '#1e40af', 'bar' => '#3b82f6'],
        'checkup'        => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'bar' => '#6366f1'],
        'adjust_dimensi' => ['bg' => '#fef9c3', 'text' => '#854d0e', 'bar' => '#eab308'],
        'repair_line'    => ['bg' => '#ffedd5', 'text' => '#9a3412', 'bar' => '#f97316'],
        'trial'          => ['bg' => '#dcfce7', 'text' => '#166534', 'bar' => '#22c55e'],
        'cek_dies'       => ['bg' => '#ccfbf1', 'text' => '#115e59', 'bar' => '#14b8a6'],
        'meeting'        => ['bg' => '#fae8ff', 'text' => '#701a75', 'bar' => '#d946ef'],
        'briefing'       => ['bg' => '#f1f5f9', 'text' => '#334155', 'bar' => '#64748b'],
        'bantu_line'     => ['bg' => '#fee2e2', 'text' => '#991b1b', 'bar' => '#ef4444'],
        'administrasi'   => ['bg' => '#e2e8f0', 'text' => '#1e293b', 'bar' => '#475569'],
        'lain'           => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'bar' => '#9ca3af'],
    ];

    public function report()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function kategoriLabel(): string
    {
        return self::KATEGORI_LABELS[$this->kategori] ?? $this->kategori;
    }

    /** Baris auto dikunci: jam & kategori gak boleh diubah/dihapus. */
    public function isLocked(): bool
    {
        return (bool) $this->is_auto;
    }
}