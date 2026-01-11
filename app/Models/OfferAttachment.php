<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OfferAttachment extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'id',
        'offer_id',
        'title',
        'description',
        'price',
        'currency',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'file_size' => 'integer',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (OfferAttachment $attachment) {
            if ($attachment->file_path) {
                try {
                    $minioService = app(\App\Services\MinioService::class);
                    $minioService->deleteFile($attachment->file_path);
                } catch (\Exception $e) {
                    // Log error but allow deletion to proceed so we don't block DB cleanup
                    \Illuminate\Support\Facades\Log::error("Failed to delete attachment file: " . $e->getMessage());
                }
            }
        });
    }
}
