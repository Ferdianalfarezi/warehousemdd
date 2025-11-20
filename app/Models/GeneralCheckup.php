<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GeneralCheckup extends Model
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
        'status',
        'catatan_umum',
    ];

    protected $casts = [
        'tanggal_terjadwal' => 'date',
        'tanggal_checkup' => 'date',
        'mulai_perbaikan' => 'datetime',
        'waktu_selesai' => 'datetime',
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
        return $this->hasMany(CheckupDetail::class);
    }

    public function partReplacements()
    {
        return $this->hasMany(CheckupPartReplacement::class);
    }

    /**
     * Calculate duration in minutes
     */
    public function calculateDuration()
    {
        if (!$this->mulai_perbaikan || !$this->waktu_selesai) {
            return null;
        }

        $mulai = Carbon::parse($this->mulai_perbaikan);
        $selesai = Carbon::parse($this->waktu_selesai);

        return $mulai->diffInMinutes($selesai);
    }

    /**
     * Get duration display attribute
     */
    public function getDurasiDisplayAttribute()
    {
        $minutes = $this->calculateDuration();

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
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statusText = [
            'pending' => 'Pending',
            'on_process' => 'On Proses',
            'finish' => 'Selesai'
        ];

        return $statusText[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $statusClass = [
            'pending' => 'bg-gray-100 text-gray-800',
            'on_process' => 'bg-blue-100 text-blue-800',
            'finish' => 'bg-green-100 text-green-800'
        ];

        return $statusClass[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if can start repair
     */
    public function canStartRepair()
    {
        return in_array($this->status, ['pending', 'on_process']) && !$this->mulai_perbaikan;
    }

    /**
     * Check if can continue process
     */
    public function canContinueProcess()
    {
        return $this->status === 'on_process' && $this->mulai_perbaikan;
    }

    /**
     * Check if has NG status
     */
    public function hasNGStatus()
    {
        return $this->details()->where('status', 'ng')->exists();
    }

    /**
     * Check if all standards filled
     */
    public function allStandardsFilled()
    {
        $barang = $this->barang;
        $totalStandards = $barang->checkIndicators()
            ->with('standards')
            ->get()
            ->sum(function ($indicator) {
                return $indicator->standards->count();
            });

        $filledStandards = $this->details()->count();

        return $totalStandards === $filledStandards;
    }

    /**
     * Auto calculate status based on details
     */
    public function autoCalculateStatus()
    {
        if ($this->details()->count() === 0) {
            return 'pending';
        }

        $hasNG = $this->hasNGStatus();

        if ($hasNG) {
            return 'on_process';
        }

        // Jika semua OK dan semua standard terisi
        if ($this->allStandardsFilled()) {
            return 'finish';
        }

        return 'on_process';
    }

    /**
     * Scope for pending status
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for on process status
     */
    public function scopeOnProcess($query)
    {
        return $query->where('status', 'on_process');
    }

    /**
     * Scope for finish status
     */
    public function scopeFinish($query)
    {
        return $query->where('status', 'finish');
    }

    /**
     * Scope for by line
     */
    public function scopeByLine($query, $line)
    {
        return $query->where('line', $line);
    }

    /**
     * Scope for today's schedule
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_terjadwal', Carbon::today());
    }
}