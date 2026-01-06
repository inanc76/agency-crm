<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'name',
        'email',
        'emails',
        'phone',
        'phones',
        'position',
        'status',
        'gender',
        'birth_date',
        'social_profiles',
        'extensions'
    ];

    protected function casts(): array
    {
        return [
            'emails' => AsArrayObject::class,
            'phones' => AsArrayObject::class,
            'social_profiles' => AsArrayObject::class,
            'extensions' => AsArrayObject::class,
            'birth_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
