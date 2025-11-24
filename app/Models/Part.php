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
        'gambar_source', // 🔥 TAMBAH INI
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
     * Append accessor to array/JSON
     */
    protected $appends = ['image_path'];

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

    /**
     * Get full image path berdasarkan source
     * 
     * @return string|null
     */
    public function getImagePathAttribute()
    {
        if (!$this->gambar) {
            return null;
        }

        // Jika dari import, ambil dari public/images/parts
        if ($this->gambar_source === 'import') {
            return asset('images/parts/' . $this->gambar); // 🔥 GANTI JADI 'parts'
        }

        // Jika dari CRUD manual, ambil dari storage/parts
        return asset('storage/parts/' . $this->gambar);
    }
}