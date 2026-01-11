<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🗃️ ReferenceCategory Model - Referans Kategorileri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $key             Kategori anahtarı (unique, örn: CUSTOMER_TYPE)
 * @property string $name            Kategori adı (görünen isim)
 * @property string|null $description Açıklama
 * @property bool $is_active         Aktiflik durumu
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<ReferenceItem> $items
 *                HasMany: Kategoriye ait referans item'ları
 * 
 * ReferenceCategory, ReferenceItem'ların GRUPLANDIRILDIĞı kategoridir:
 * - Örnek kategoriler: CUSTOMER_TYPE, OFFER_STATUS, SERVICE_CATEGORY
 * - Her kategori altında birden fazla ReferenceItem bulunur
 * - Ayarlar sayfasında yönetilir
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
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
