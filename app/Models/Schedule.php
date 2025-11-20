<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'gambar',
        'kode_barang',
        'nama',
        'mulai_service',
        'periode',
        'interval_value',
        'service_berikutnya',
        'terakhir_service',
        'status'
    ];

    protected $casts = [
        'mulai_service' => 'date',
        'service_berikutnya' => 'date',
        'terakhir_service' => 'date',
        'interval_value' => 'integer', // CASTING
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Calculate next service date
     */
    /**
 * Calculate next service date
 */
    public function calculateNextService()
    {
        // Pastikan interval_value adalah integer
        $interval = (int) $this->interval_value;
        $baseDate = $this->terakhir_service ?: $this->mulai_service;
        
        // Gunakan method yang berbeda berdasarkan periode
        switch ($this->periode) {
            case 'harian':
                $this->service_berikutnya = Carbon::parse($baseDate)->addDays($interval);
                break;
            case 'mingguan':
                $this->service_berikutnya = Carbon::parse($baseDate)->addWeeks($interval);
                break;
            case 'bulanan':
                $this->service_berikutnya = Carbon::parse($baseDate)->addMonths($interval);
                break;
            case 'custom':
                $this->service_berikutnya = Carbon::parse($baseDate)->addDays($interval);
                break;
            default:
                $this->service_berikutnya = Carbon::parse($baseDate)->addDays($interval);
        }
        
        // Simple status calculation
        $today = Carbon::today();
        $nextService = Carbon::parse($this->service_berikutnya);
        
        if ($nextService->lessThan($today)) {
            $this->status = 'terlambat';
        } elseif ($nextService->isToday()) {
            $this->status = 'hari_ini';
        } elseif ($nextService->diffInDays($today) <= 7) {
            $this->status = 'segera';
        } else {
            $this->status = 'terjadwal';
        }
    }

    /**
     * Get display text for periode
     */
    public function getPeriodeDisplayAttribute()
    {
        $satuan = [
            'harian' => 'hari',
            'mingguan' => 'minggu', 
            'bulanan' => 'bulan',
            'custom' => 'hari'
        ];

        return ucfirst($this->periode) . " ({$this->interval_value} {$satuan[$this->periode]})";
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statusText = [
            'terjadwal' => 'Terjadwal',
            'segera' => 'Segera',
            'hari_ini' => 'Hari Ini',
            'terlambat' => 'Terlambat'
        ];

        return $statusText[$this->status] ?? $this->status;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($schedule) {
            $schedule->calculateNextService();
        });
    }
}