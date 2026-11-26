<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerOutputItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_output_id',
        'product_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function partnerOutput()
    {
        return $this->belongsTo(PartnerOutput::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
