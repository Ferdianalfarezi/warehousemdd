<?php
// app/Models/InhouseRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InhouseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_checkup_id',
        'checkup_detail_id',
        'problem',
        'proses_dilakukan',
        'mesin',
        'status',
        'confirmed_by',
        'confirmed_at',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function generalCheckup()
    {
        return $this->belongsTo(GeneralCheckup::class);
    }

    public function checkupDetail()
    {
        return $this->belongsTo(CheckupDetail::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'on_process' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'on_process' => 'On Proses',
            'completed' => 'Selesai',
            default => $this->status,
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOnProcess($query)
    {
        return $query->where('status', 'on_process');
    }
}