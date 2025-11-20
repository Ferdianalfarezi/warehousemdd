<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIndicatorStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_indicator_id',
        'poin',
        'metode',
        'standar',
    ];

    /**
     * Get the check indicator that owns the standard.
     */
    public function checkIndicator()
    {
        return $this->belongsTo(CheckIndicator::class);
    }
}