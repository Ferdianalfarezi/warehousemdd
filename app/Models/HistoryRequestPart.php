<?php
// app/Models/HistoryRequestPart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRequestPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_part_id',
        'request_number',
        'user_id',
        'requester_name',
        'status',
        'warehouse_order_id',
        'catatan',
        'tanggal_request',
        'tanggal_completed',
        'tanggal_verified',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_request' => 'datetime',
        'tanggal_completed' => 'datetime',
        'tanggal_verified' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(HistoryRequestPartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}