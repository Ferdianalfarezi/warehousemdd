<?php
// app/Models/RequestPart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'requester_name',
        'department_id',
        'status',
        'warehouse_order_id',
        'catatan',
        'keterangan',
        'tanggal_request',
        'tanggal_approve_kadiv',
        'tanggal_approve_kagud',
        'tanggal_verified',
        'approved_by_kadiv',
        'approved_by_kagud',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_request' => 'datetime',
        'tanggal_approve_kadiv' => 'datetime',
        'tanggal_approve_kagud' => 'datetime',
        'tanggal_verified' => 'datetime',
    ];

    /**
     * Default attributes
     */
    protected $attributes = [
        'department_id' => 9,
        'status' => 'pending',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($request) {
            // Generate request number
            $request->request_number = static::generateRequestNumber();
            
            // Set default department if not provided
            if (empty($request->department_id)) {
                $request->department_id = 9;
            }
            
            // Set keterangan based on status
            $request->keterangan = static::getKeteranganByStatus($request->status);
        });
        
        // 🔥 FIX: Update keterangan when status changes
        static::updating(function ($request) {
            if ($request->isDirty('status')) {
                $request->keterangan = static::getKeteranganByStatus($request->status);
            }
        });
        
        // 🔥 TAMBAHAN: Also update after save (for manual updates)
        static::updated(function ($request) {
            // This ensures keterangan is always in sync with status
            $currentKeterangan = static::getKeteranganByStatus($request->status);
            if ($request->keterangan !== $currentKeterangan) {
                $request->keterangan = $currentKeterangan;
                $request->saveQuietly(); // Avoid infinite loop
            }
        });
    }

    public static function generateRequestNumber()
    {
        $prefix = 'REQ-MDD-' . date('Ymd') . '-';
        $lastRequest = static::where('request_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$lastRequest) {
            return $prefix . '0001';
        }
        
        $lastNumber = intval(substr($lastRequest->request_number, -4));
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function getKeteranganByStatus($status)
    {
        return match($status) {
            'pending' => 'Menunggu approval Kepala Dept.',
            'approved_kadiv' => 'Menunggu approval PUD',
            'approved_kagud' => 'Barang sedang disiapkan',
            'ready' => 'Barang siap diambil',
            'completed' => 'Menunggu verifikasi penerimaan',
            'verified' => 'Request selesai - Barang diterima',
            'rejected' => 'Request ditolak',
            default => ''
        };
    }

    // ==================== RELATIONSHIPS ====================

    public function items()
    {
        return $this->hasMany(RequestPartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByKadiv()
    {
        return $this->belongsTo(User::class, 'approved_by_kadiv');
    }

    public function approvedByKagud()
    {
        return $this->belongsTo(User::class, 'approved_by_kagud');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function history()
    {
        return $this->hasOne(HistoryRequestPart::class);
    }

    // ==================== ACCESSOR ====================

    public function getDepartmentNameAttribute()
    {
        return 'MDD Department';
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'pending',
            'approved_kadiv',
            'approved_kagud',
            'ready',
            'completed'
        ]);
    }

    public function scopeReadyToVerify($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNeedSync($query)
    {
        return $query->whereNotNull('warehouse_order_id')
            ->whereNotIn('status', ['verified', 'rejected']);
    }
}