<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'part_number',
        'name',
        'category_id',
        'unit_id',
        'buy_price',
        'sell_price',
        'stock',
        'min_stock',
        'description',
        'is_active',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ProductPhoto::class)->where('is_primary', true);
    }

    public function productRacks()
    {
        return $this->hasMany(ProductRack::class);
    }

    public function racks()
    {
        return $this->belongsToMany(Rack::class, 'product_racks')
            ->withPivot('is_primary', 'note')
            ->withTimestamps();
    }

    public function primaryRack()
    {
        return $this->hasOne(ProductRack::class)->where('is_primary', true);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }
        if ($this->stock <= $this->min_stock) {
            return 'menipis';
        }
        return 'aman';
    }

    public function getPrimaryRackCodeAttribute(): string
    {
        $primary = $this->productRacks->firstWhere('is_primary', true);
        if ($primary && $primary->rack) {
            return $primary->rack->code;
        }
        return '-';
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $photo = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();
        if ($photo && $photo->path) {
            return asset('storage/' . $photo->path);
        }
        return asset('images/no-image.png');
    }
}
