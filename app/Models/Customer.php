<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'title',
        'email',
        'emails',
        'phone',
        'phones',
        'address',
        'city_id',
        'country_id',
        'tax_number',
        'tax_office',
        'website',
        'websites',
        'current_code',
        'logo_url',
        'customer_type'
    ];

    protected function casts(): array
    {
        return [
            'emails' => AsArrayObject::class,
            'phones' => AsArrayObject::class,
            'websites' => AsArrayObject::class,
        ];
    }

    /**
     * Get the related customers (İlişkili Firmalar)
     */
    public function relatedCustomers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_relations',
            'customer_id',
            'related_customer_id'
        )->withTimestamps();
    }
}
