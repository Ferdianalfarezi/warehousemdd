<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_part',
        'gambar',
        'nama',
        'stock',
        'min_stock',
        'max_stock',
        'satuan',
        'address',
        'line',
        'supplier_id',
    ];

    /**
     * Get the supplier that owns the part.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the barangs for the part.
     */
    public function barangs()
    {
        return $this->belongsToMany(Barang::class, 'detail_barangs')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the detail barangs for the part.
     */
    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class);
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