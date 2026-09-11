<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckIndicator extends Model
{
    protected $fillable = [
        'barang_id',
        'nama_bagian',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function standards(): HasMany
    {
        return $this->hasMany(CheckIndicatorStandard::class);
    }
}