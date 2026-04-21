<?php
// app/Models/PartStockHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'part_request_item_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'notes',
        'created_by'
    ];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(PartRequestItem::class, 'part_request_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            default => 'Unknown'
        };
    }
}