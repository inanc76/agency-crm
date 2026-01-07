<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mail_settings';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'smtp_secure' => 'boolean',
    ];
}
