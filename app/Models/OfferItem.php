<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📋 OfferItem Model - Teklif Kalemleri (Line Items)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 📊 Database Columns (offer_items table)                                 │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * @property string $id UUID primary key
 * @property string $offer_id Teklif UUID (FK: offers)
 * @property string|null $service_id Hizmet UUID (FK: services) - opsiyonel
 * @property string $service_name Hizmet/ürün adı
 * @property string|null $description Kalem açıklaması
 * @property float $price Birim fiyat
 * @property string $currency Para birimi (TRY, USD, EUR)
 * @property int $duration Süre (gün/ay/yıl)
 * @property int $quantity Miktar/Adet
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 İlişkiler                                                            │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read Offer $offer           BelongsTo: Kalemin ait olduğu teklif
 * @property-read Service|null $service  BelongsTo: İlişkili hizmet (opsiyonel)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🧮 Computed Properties (Accessors)                                      │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read float $line_total      Satır toplamı (price * quantity)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 💼 İş Mantığı                                                           │
 * └─────────────────────────────────────────────────────────────────────────┘
 * OfferItem, teklifteki her bir SATIRI temsil eder:
 * - service_id: Mevcut Service'ten kopyalanabilir (opsiyonel)
 * - Manuel ekleme: service_id null, service_name/price manuel girilir
 * - line_total accessor: UI'da satır toplamını gösterir
 * - Teklif silindiğinde cascade delete (Offer::booted() içinde)
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class OfferItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'offer_id',
        'section_id',
        'service_id',
        'service_name',
        'description',
        'price',
        'currency',
        'duration',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration' => 'integer',
            'quantity' => 'integer',
        ];
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function section()
    {
        return $this->belongsTo(OfferSection::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getLineTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
