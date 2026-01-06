<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferenceCategory extends Model
{
    use HasUuids;

    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        // Relationship links 'key' on this model to 'category_key' on ReferenceItem
        return $this->hasMany(ReferenceItem::class, 'category_key', 'key');
    }
}
