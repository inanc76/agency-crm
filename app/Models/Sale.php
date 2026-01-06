<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'offer_id',
        'amount',
        'currency',
        'sale_date'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sale_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
