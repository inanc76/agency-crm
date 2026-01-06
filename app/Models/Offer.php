<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'title',
        'offer_no',
        'status',
        'total_amount',
        'currency',
        'offer_date',
        'valid_until',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'offer_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
