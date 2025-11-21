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
        'ng_action_type',
        'ng_action_status',
        'ng_action_data',
    ];

    protected $casts = [
        'ng_action_data' => 'array',
    ];

    /**
     * Relationships
     */
    public function historyCheckup()
    {
        return $this->belongsTo(HistoryCheckup::class);
    }

    public function checkIndicator()
    {
        return $this->belongsTo(CheckIndicator::class);
    }

    public function checkIndicatorStandard()
    {
        return $this->belongsTo(CheckIndicatorStandard::class);
    }

    public function partReplacements()
    {
        return $this->hasMany(HistoryCheckupPartReplacement::class, 'history_checkup_detail_id');
    }

    /**
     * Get inhouse request data from JSON
     */
    public function getInhouseRequestAttribute()
    {
        if ($this->ng_action_type === 'inhouse' && $this->ng_action_data) {
            return (object) $this->ng_action_data;
        }
        return null;
    }

    /**
     * Get outhouse request data from JSON
     */
    public function getOuthouseRequestAttribute()
    {
        if ($this->ng_action_type === 'outhouse' && $this->ng_action_data) {
            return (object) $this->ng_action_data;
        }
        return null;
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