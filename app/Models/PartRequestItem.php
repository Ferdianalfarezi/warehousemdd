<?php
// app/Models/PartRequestItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_request_id',
        'part_id',
        'quantity',
        'approved_quantity',
        'keterangan',
        'status'
    ];

    public function partRequest()
    {
        return $this->belongsTo(PartRequest::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(PartStockHistory::class);
    }

    // Auto-set approved_quantity = quantity jika belum diset
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            if (is_null($item->approved_quantity)) {
                $item->approved_quantity = $item->quantity;
            }
        });
    }
}