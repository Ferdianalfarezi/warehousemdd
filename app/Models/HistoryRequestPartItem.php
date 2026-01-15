<?php
// app/Models/HistoryRequestPartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRequestPartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_request_part_id',
        'part_id',
        'part_code',
        'part_name',
        'quantity',
        'quantity_approved',
        'keterangan',
    ];

    public function historyRequestPart()
    {
        return $this->belongsTo(HistoryRequestPart::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}