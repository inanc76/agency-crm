<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_key',
        'key',
        'display_label',
        'description',
        'sort_order',
        'is_active',
        'is_default',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        // Relationship links 'category_key' on this model to 'key' on ReferenceCategory
        return $this->belongsTo(ReferenceCategory::class, 'category_key', 'key');
    }
}
