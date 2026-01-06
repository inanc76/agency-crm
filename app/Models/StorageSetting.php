<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StorageSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'provider',
        'endpoint',
        'port',
        'use_ssl',
        'access_key',
        'secret_key',
        'bucket_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'use_ssl' => 'boolean',
            'is_active' => 'boolean',
            'port' => 'integer',
        ];
    }
}
