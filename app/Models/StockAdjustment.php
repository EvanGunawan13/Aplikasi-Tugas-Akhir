<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_before',
        'stock_after',
        'difference',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'difference' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
