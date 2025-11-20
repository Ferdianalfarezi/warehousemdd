<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HistoryCheckup extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'barang_id',
        'kode_barang',
        'gambar',
        'nama',
        'line',
        'tanggal_terjadwal',
        'tanggal_checkup',
        'mulai_perbaikan',
        'waktu_selesai',
        'durasi_perbaikan',
        'status',
        'catatan_umum',
        'total_ok',
        'total_ng',
        'total_part_used',
    ];

    protected $casts = [
        'tanggal_terjadwal' => 'date',
        'tanggal_checkup' => 'date',
        'mulai_perbaikan' => 'datetime',
        'waktu_selesai' => 'datetime',
        'durasi_perbaikan' => 'integer',
        'total_ok' => 'integer',
        'total_ng' => 'integer',
        'total_part_used' => 'integer',
    ];

    /**
     * Relationships
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function details()
    {
        return $this->hasMany(HistoryCheckupDetail::class);
    }

    public function partReplacements()
    {
        return $this->hasMany(HistoryCheckupPartReplacement::class);
    }

    /**
     * Get duration display attribute
     */
    public function getDurasiDisplayAttribute()
    {
        $minutes = $this->durasi_perbaikan;

        if (!$minutes) {
            return '-';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours} jam {$mins} menit";
        }

        return "{$mins} menit";
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        $total = $this->total_ok + $this->total_ng;
        if ($total === 0) {
            return 0;
        }

        return round(($this->total_ok / $total) * 100, 2);
    }

    /**
     * Scope for by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_checkup', [$startDate, $endDate]);
    }

    /**
     * Scope for by line
     */
    public function scopeByLine($query, $line)
    {
        return $query->where('line', $line);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_checkup', Carbon::today());
    }

    /**
     * Scope for this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('tanggal_checkup', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope for this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('tanggal_checkup', Carbon::now()->year)
                     ->whereMonth('tanggal_checkup', Carbon::now()->month);
    }
}