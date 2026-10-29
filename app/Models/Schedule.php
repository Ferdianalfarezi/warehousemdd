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
        'interval_value' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * true = jangan hitung ulang service_berikutnya saat save (cuma status yang di-refresh).
     * Dipakai saat edit Mulai Service dengan pilihan "tetap" / tanggal di-set manual.
     * (property biasa, bukan kolom tabel)
     */
    public bool $keepServiceBerikutnya = false;

    /**
     * Tanggal + 1 periode (sesuai jenis periode & interval schedule ini)
     */
    public function addPeriod($date): Carbon
    {
        $interval = (int) $this->interval_value;
        $date = Carbon::parse($date);

        return match ($this->periode) {
            'mingguan' => $date->addWeeks($interval),
            'bulanan'  => $date->addMonths($interval),
            'tahunan'  => $date->addYears($interval),
            default    => $date->addDays($interval), // harian, custom
        };
    }

    /**
     * Calculate next service date: dari service terakhir, atau dari mulai service kalau belum pernah service
     */
    public function calculateNextService()
    {
        $baseDate = $this->terakhir_service ?: $this->mulai_service;
        $this->service_berikutnya = $this->addPeriod($baseDate);

        $this->refreshStatus();
    }

    /**
     * Status dari tanggal service berikutnya.
     * (Carbon 3: diffInDays() bisa negatif, jadi pakai perbandingan tanggal langsung)
     */
    public function refreshStatus(): void
    {
        $today = Carbon::today();
        $nextService = Carbon::parse($this->service_berikutnya);

        if ($nextService->lessThan($today)) {
            $this->status = 'terlambat';
        } elseif ($nextService->isToday()) {
            $this->status = 'hari_ini';
        } elseif ($nextService->lte($today->copy()->addDays(7))) {
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
            'tahunan' => 'tahun',
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
            if ($schedule->keepServiceBerikutnya) {
                $schedule->refreshStatus();
            } else {
                $schedule->calculateNextService();
            }
        });
    }
}