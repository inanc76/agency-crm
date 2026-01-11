<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📎 OfferAttachment Model - Teklif Ekleri (Minio Entegrasyonu)
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $offer_id        Teklif UUID (FK: offers)
 * @property string|null $title      Ek başlığı
 * @property string|null $description Ek açıklaması
 * @property float|null $price       Ek fiyatı (opsiyonel)
 * @property string|null $currency   Para birimi
 * @property string $file_path       Minio dosya yolu (bucket/path/filename)
 * @property string $file_name       Orijinal dosya adı
 * @property string|null $file_type  MIME type (application/pdf, image/png, etc.)
 * @property int|null $file_size     Dosya boyutu (bytes)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Offer $offer       BelongsTo: Ekin ait olduğu teklif
 * 
 * OfferAttachment, teklife eklenen DOSYALARI temsil eder:
 * - Dosyalar Minio object storage'da saklanır
 * - booted() event: Attachment silindiğinde Minio'dan da dosya temizlenir
 * - MinioService ile entegre çalışır (upload/delete)
 * - Teklif silindiğinde cascade delete (Offer::booted() içinde)
 * 
 * ⚠️ Dosya boyutu limiti: 10MB (frontend validation)
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
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
