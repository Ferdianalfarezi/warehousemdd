<?php
// app/Models/PartRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'catatan',
        'created_by',
        'closed_at',
        'closed_by',
        'receive_notes'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(PartRequestItem::class);
    }

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'part_request_items')
                    ->withPivot('quantity', 'approved_quantity', 'status', 'keterangan')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Status helpers
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Approval Kadiv',
            'approved_kadiv' => 'Menunggu Approval PUD',
            'approved_kagud' => 'Sedang Disiapkan',
            'rejected' => 'Ditolak',
            'ready' => 'Siap Diambil',
            'completed' => 'Siap Diterima',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning text-dark',
            'approved_kadiv' => 'bg-info text-white',
            'approved_kagud' => 'bg-primary text-white',
            'rejected' => 'bg-danger text-white',
            'ready' => 'bg-success text-white',
            'completed' => 'bg-success text-white pulse',
            default => 'bg-secondary text-white'
        };
    }

    public function canBeClosed()
    {
        return $this->status === 'completed' && is_null($this->closed_at);
    }

    public function isClosed()
    {
        return !is_null($this->closed_at);
    }
}