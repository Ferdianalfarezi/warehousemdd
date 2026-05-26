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
        'supplier_id',
        'cust',
        'model',
    ];

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'detail_barangs')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class);
    }

    public function diesDetails()
    {
        return $this->hasMany(DiesDetail::class)->orderBy('sort_order');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function checkIndicators()
    {
        return $this->hasMany(CheckIndicator::class);
    }

    public function generalCheckups()
    {
        return $this->hasMany(GeneralCheckup::class);
    }

    public function historyCheckups()
    {
        return $this->hasMany(HistoryCheckup::class);
    }

    public function isBelowMinStock()
    {
        return $this->stock < $this->min_stock;
    }

    public function isAboveMaxStock()
    {
        return $this->stock > $this->max_stock;
    }
}