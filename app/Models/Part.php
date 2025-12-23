<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $status
 * @property-read string $status_label
 * @property-read string $status_badge_class
 */
class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_part',
        'gambar',
        'gambar_source',
        'nama',
        'stock',
        'min_stock',
        'max_stock',
        'satuan',
        'address',
        'line',
        'supplier_id',
        'id_pud',
    ];

    /**
     * Append accessor ke array/JSON
     */
    protected $appends = ['image_path', 'status', 'status_label', 'status_badge_class'];

    /**
     * Remove boot method - tidak perlu lagi
     */
    // protected static function boot() - HAPUS INI

    /**
     * Status Accessor - Modern Laravel Way (Laravel 9+)
     * Computed real-time berdasarkan stock level
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->calculateStatus(),
        );
    }

    /**
     * Calculate status berdasarkan stock level
     */
    public function calculateStatus(): string
    {
        if ($this->stock == 0) {
            return 'habis';
        }
        
        if ($this->stock > 0 && $this->stock < $this->min_stock) {
            return 'low';
        }
        
        return 'normal';
    }

    /**
     * Status Label Accessor
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'habis' => 'Habis',
                'low' => 'Low Stock',
                'normal' => 'Normal',
                default => ucfirst($this->status),
            }
        );
    }

    /**
     * Status Badge Class Accessor
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'habis' => 'bg-red-100 text-red-800',
                'low' => 'bg-yellow-100 text-yellow-800',
                'normal' => 'bg-green-100 text-green-800',
                default => 'bg-gray-100 text-gray-800',
            }
        );
    }

    /**
     * Get the supplier that owns the part
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the barangs for the part
     */
    public function barangs()
    {
        return $this->belongsToMany(Barang::class, 'detail_barangs')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the detail barangs for the part
     */
    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class);
    }

    /**
     * Check if stock is below minimum
     */
    public function isBelowMinStock(): bool
    {
        return $this->stock < $this->min_stock;
    }

    /**
     * Check if stock is above maximum
     */
    public function isAboveMaxStock(): bool
    {
        return $this->stock > $this->max_stock;
    }

    /**
     * Get full image path berdasarkan source
     */
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->gambar) {
                    return null;
                }

                // Jika dari import, ambil dari public/images/parts
                if ($this->gambar_source === 'import') {
                    return asset('images/parts/' . $this->gambar);
                }

                // Jika dari CRUD manual, ambil dari storage/parts
                return asset('storage/parts/' . $this->gambar);
            }
        );
    }

    /**
     * DEPRECATED - Use $part->status_badge_class instead
     */
    public function getStatusBadgeClass(): string
    {
        return $this->status_badge_class;
    }

    /**
     * DEPRECATED - Use $part->status_label instead
     */
    public function getStatusLabel(): string
    {
        return $this->status_label;
    }
}