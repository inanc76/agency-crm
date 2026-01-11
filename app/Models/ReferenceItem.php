<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🗂️ ReferenceItem Model - Dinamik Referans Verileri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id                  UUID primary key
 * @property string $category_key        Kategori anahtarı (FK: reference_categories.key)
 * @property string $key                 Item anahtarı (unique per category)
 * @property string $display_label       Görünen etiket (UI'da gösterilir)
 * @property string|null $description    Açıklama
 * @property int $sort_order             Sıralama (ASC)
 * @property bool $is_active             Aktiflik durumu
 * @property bool $is_default            Varsayılan seçim mi?
 * @property array|null $metadata        Ek meta veriler (JSON)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read ReferenceCategory $category BelongsTo: Kategorisi
 * 
 * ReferenceItem, sistemdeki TÜM DİNAMİK SEÇİMLERİN kaynağıdır:
 * - customer_type, offer_status, service_category, vb.
 * - Hardcoded array'ler yerine DB'den beslenir (Zero Hard-Coding)
 * - UI'da useReferenceData hook ile çekilir
 * - Ayarlar sayfasında yönetilir
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
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
