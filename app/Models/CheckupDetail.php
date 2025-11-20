<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckupDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_checkup_id',
        'check_indicator_id',
        'check_indicator_standard_id',
        'status',
        'catatan',
    ];

    /**
     * Relationships
     */
    public function generalCheckup()
    {
        return $this->belongsTo(GeneralCheckup::class);
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
        return $this->hasMany(CheckupPartReplacement::class);
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
     * Check if status is NG
     */
    public function isNG()
    {
        return $this->status === 'ng';
    }

    /**
     * Check if status is OK
     */
    public function isOK()
    {
        return $this->status === 'ok';
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