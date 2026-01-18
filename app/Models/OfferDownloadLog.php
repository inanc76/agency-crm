<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferDownloadLog extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false; // The migration has downloaded_at, not created_at/updated_at

    protected $fillable = [
        'id',
        'offer_id',
        'downloader_email',
        'downloaded_at',
        'file_name',
        'ip_address',
        'user_agent',
        'is_read_log',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
            'is_read_log' => 'boolean',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
