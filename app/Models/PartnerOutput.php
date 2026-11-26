<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'output_no',
        'partner_company_id',
        'output_date',
        'delivery_fee',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'output_date' => 'date',
        'delivery_fee' => 'decimal:2',
    ];

    public function partnerCompany()
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function items()
    {
        return $this->hasMany(PartnerOutputItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }
}
