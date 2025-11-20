<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryCheckupDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_checkup_id',
        'check_indicator_id',
        'check_indicator_standard_id',
        'nama_bagian',
        'poin',
        'status',
        'catatan',
    ];

    /**
     * Relationships
     */
    public function historyCheckup()
    {
        return $this->belongsTo(HistoryCheckup::class);
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        return strtoupper($this->status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $statusClass = [
            'ok' => 'bg-green-100 text-green-800',
            'ng' => 'bg-red-100 text-red-800'
        ];

        return $statusClass[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Scope for OK status
     */
    public function scopeOk($query)
    {
        return $query->where('status', 'ok');
    }

    /**
     * Scope for NG status
     */
    public function scopeNg($query)
    {
        return $query->where('status', 'ng');
    }
}