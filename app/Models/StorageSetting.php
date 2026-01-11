<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💾 StorageSetting Model - Object Storage Ayarları (Minio)
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $provider        Storage provider (minio, s3, etc.)
 * @property string $endpoint        Minio endpoint URL
 * @property int $port               Port numarası
 * @property bool $use_ssl           SSL kullanımı
 * @property string $access_key      Access key
 * @property string $secret_key      Secret key (encrypted)
 * @property string $bucket_name     Bucket adı
 * @property bool $is_active         Aktiflik durumu
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * StorageSetting, MINIO object storage yapılandırmasını saklar:
 * - MinioService bu ayarları kullanarak dosya yükleme/silme yapar
 * - OfferAttachment dosyaları Minio'da saklanır
 * - Ayarlar sayfasında yönetilir
 * 
 * ⚠️ secret_key encrypted olarak saklanmalıdır
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
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
