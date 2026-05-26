<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiesDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'child_part_code',
        'part_name',
        'cust',
        'model',
        'process_name',
        'process_no',
        'sort_order',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}