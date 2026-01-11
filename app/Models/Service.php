<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'asset_id',
        'price_definition_id',
        'service_name',
        'service_category',
        'service_duration',
        'service_price',
        'service_currency',
        'start_date',
        'end_date',
        'description',
        'is_active',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'service_price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
