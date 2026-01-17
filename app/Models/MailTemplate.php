<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'system_key',
        'name',
        'subject',
        'content',
        'variables',
        'is_system',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_system' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
