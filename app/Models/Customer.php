<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'title',
        'email',
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
            'phones' => AsArrayObject::class,
            'websites' => AsArrayObject::class,
        ];
    }
}
