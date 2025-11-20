<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    use HasFactory;

    protected $table = 'detail_barangs';

    protected $fillable = [
        'barang_id',
        'part_id',
        'quantity',
    ];

    /**
     * Get the barang that owns the detail.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Get the part that owns the detail.
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}