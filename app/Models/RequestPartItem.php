<?php
// app/Models/RequestPartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_part_id',
        'part_id',
        'quantity',
        'quantity_approved',
        'keterangan',
        'item_status',
    ];

    public function requestPart()
    {
        return $this->belongsTo(RequestPart::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}