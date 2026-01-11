<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💵 PriceDefinition Model - Fiyat Tanımları (Şablon)
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $name            Fiyat tanımı adı
 * @property string|null $category   Kategori (ReferenceData)
 * @property int|null $duration      Süre (gün/ay/yıl)
 * @property float $price            Fiyat
 * @property string $currency        Para birimi (TRY, USD, EUR)
 * @property string|null $description Açıklama
 * @property bool $is_active         Aktiflik durumu
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * PriceDefinition, hizmet fiyatlarının ŞABLON tanımlarıdır.
 * Service ve OfferItem oluşturulurken bu şablonlardan kopyalanır.
 * Ayarlar sayfasında yönetilir.
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class PriceDefinition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'category',
        'duration',
        'price',
        'currency',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'float',
    ];
}
