<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = ['nama_line', 'mesin'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    /**
     * Operator (Leader / Asst Leader) yang pegang line ini.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'line_user');
    }
}