<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
    ];

    /**
     * Get the parts for the supplier.
     */
    public function parts()
    {
        return $this->hasMany(Part::class);
    }

    /**
     * Get the barangs for the supplier.
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}