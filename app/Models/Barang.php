<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'gambar',
        'nama',
        'stock',
        'min_stock',
        'max_stock',
        'satuan',
        'address',
        'line',
    ];

    /**
     * Get the parts for the barang.
     */
    public function parts()
    {
        return $this->belongsToMany(Part::class, 'detail_barangs')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the detail barangs for the barang.
     */
    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class);
    }

    /**
     * Get the schedule for the barang.
     */
    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the check indicators for the barang.
     */
    public function checkIndicators()
    {
        return $this->hasMany(CheckIndicator::class);
    }

    /**
     * Get general checkups for this barang
     */
    public function generalCheckups()
    {
        return $this->hasMany(GeneralCheckup::class);
    }

    /**
     * Get history checkups for this barang
     */
    public function historyCheckups()
    {
        return $this->hasMany(HistoryCheckup::class);
    }

    /**
     * Check if stock is below minimum
     */
    public function isBelowMinStock()
    {
        return $this->stock < $this->min_stock;
    }

    /**
     * Check if stock is above maximum
     */
    public function isAboveMaxStock()
    {
        return $this->stock > $this->max_stock;
    }
}