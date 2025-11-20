<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryCheckupPartReplacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_checkup_id',
        'history_checkup_detail_id',
        'part_id',
        'kode_part',
        'nama_part',
        'quantity_used',
        'catatan',
    ];

    protected $casts = [
        'quantity_used' => 'integer',
    ];

    /**
     * Relationships
     */
    public function historyCheckup()
    {
        return $this->belongsTo(HistoryCheckup::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}