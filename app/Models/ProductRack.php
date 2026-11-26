<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRack extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'rack_id',
        'is_primary',
        'note',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }
}
