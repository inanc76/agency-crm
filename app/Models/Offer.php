<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Offer extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'number',
        'customer_id',
        'status',
        'title',
        'description',
        'total_amount',
        'original_amount',
        'discount_percentage',
        'discounted_amount',
        'currency',
        'valid_until',
        'pdf_url',
        'tracking_token',
        'vat_rate',
        'vat_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discounted_amount' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'valid_until' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OfferItem::class);
    }
}
