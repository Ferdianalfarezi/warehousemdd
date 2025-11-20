<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'part_id',
        'nama_bagian',
    ];

    /**
     * Get the barang that owns the check indicator.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Get the part that owns the check indicator (optional).
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Get the standards for this check indicator.
     */
    public function standards()
    {
        return $this->hasMany(CheckIndicatorStandard::class);
    }
}