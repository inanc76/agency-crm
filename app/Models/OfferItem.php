<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OfferItem extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'id',
        'offer_id',
        'service_id',
        'service_name',
        'description',
        'price',
        'currency',
        'duration',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration' => 'integer',
            'quantity' => 'integer',
        ];
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getLineTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
