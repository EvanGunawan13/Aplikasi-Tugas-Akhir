<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'zone', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productRacks()
    {
        return $this->hasMany(ProductRack::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_racks')
            ->withPivot('is_primary', 'note')
            ->withTimestamps();
    }
}
