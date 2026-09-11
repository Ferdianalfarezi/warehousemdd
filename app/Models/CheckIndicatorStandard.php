<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIndicatorStandard extends Model
{
    protected $fillable = [
        'check_indicator_id',
        'poin',
        'standar',
    ];

    public function checkIndicator(): BelongsTo
    {
        return $this->belongsTo(CheckIndicator::class);
    }
}