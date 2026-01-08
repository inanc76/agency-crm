<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasUuids;
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

    public function relatedCustomers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_relations',
            'customer_id',
            'related_customer_id'
        )->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'entity_id')->where('entity_type', 'CUSTOMER');
    }
}
