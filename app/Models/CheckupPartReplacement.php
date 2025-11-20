<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckupPartReplacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_checkup_id',
        'checkup_detail_id',
        'part_id',
        'quantity_used',
        'catatan',
    ];

    protected $casts = [
        'quantity_used' => 'integer',
    ];

    /**
     * Relationships
     */
    public function generalCheckup()
    {
        return $this->belongsTo(GeneralCheckup::class);
    }

    public function checkupDetail()
    {
        return $this->belongsTo(CheckupDetail::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Kurangi stock saat part replacement dibuat
        static::created(function ($replacement) {
            $part = $replacement->part;
            if ($part) {
                $part->stock -= $replacement->quantity_used;
                $part->save();
            }
        });

        // Kembalikan stock saat part replacement dihapus
        static::deleted(function ($replacement) {
            $part = $replacement->part;
            if ($part) {
                $part->stock += $replacement->quantity_used;
                $part->save();
            }
        });
    }
}