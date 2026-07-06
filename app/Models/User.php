<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'nama',
        'nik',
        'role_id',
        'last_login',
        'status',
        'jabatan', // 'Leader' | 'Asst Leader', hanya relevan untuk operator (role_id 4)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login' => 'datetime',
            'role_id' => 'integer',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Lines yang dipegang user ini (khusus operator - Leader max 3, Asst Leader max 1).
     */
    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_user');
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'aktif';
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin()
    {
        return $this->role && $this->role->nama === 'superadmin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role && in_array($this->role->nama, ['superadmin', 'admin']);
    }

    /**
     * Check if user is operator (role_id 4) - hanya operator yang punya Jabatan & Line
     */
    public function isOperator()
    {
        return (int) $this->role_id === 4;
    }

    /**
     * Maksimal jumlah Line yang boleh dipegang berdasarkan Jabatan
     */
    public static function maxLinesForJabatan(?string $jabatan): ?int
    {
        return match ($jabatan) {
            'Leader'      => 3,
            'Asst Leader' => 1,
            default       => null,
        };
    }
}