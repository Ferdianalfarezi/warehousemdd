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
        'is_committed',
        'is_installed',
    ];

    protected $casts = [
        'quantity_used' => 'integer',
        'is_committed' => 'boolean',
        'is_installed' => 'boolean',
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

        // Kurangi stock HANYA saat part sudah di-commit (finish checkup)
        static::updated(function ($replacement) {
            // Jika status berubah dari not committed ke committed
            if ($replacement->is_committed && $replacement->getOriginal('is_committed') === false) {
                $part = $replacement->part;
                if ($part) {
                    $part->stock -= $replacement->quantity_used;
                    $part->save();
                }
            }
        });

        // Kembalikan stock saat part replacement dihapus
        // HANYA jika belum di-commit (masih temporary)
        static::deleting(function ($replacement) {
            // Jika part belum di-commit, tidak perlu kembalikan stock
            // Karena stock belum dikurangi
            if ($replacement->is_committed) {
                $part = $replacement->part;
                if ($part) {
                    $part->stock += $replacement->quantity_used;
                    $part->save();
                }
            }
        });
    }

    /**
     * Scope untuk part yang belum di-install
     */
    public function scopeNotInstalled($query)
    {
        return $query->where('is_installed', false);
    }

    /**
     * Scope untuk part yang sudah di-install
     */
    public function scopeInstalled($query)
    {
        return $query->where('is_installed', true);
    }
}