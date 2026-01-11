<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💸 Sale Model - Gerçekleşen Satışlar
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $customer_id     Müşteri UUID (FK: customers)
 * @property string|null $offer_id   İlişkili teklif UUID (FK: offers) - opsiyonel
 * @property float $amount           Satış tutarı
 * @property string $currency        Para birimi (TRY, USD, EUR)
 * @property \Carbon\Carbon $sale_date Satış tarihi
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Customer $customer BelongsTo: Satışın müşterisi
 * @property-read Offer|null $offer  BelongsTo: İlişkili teklif (varsa)
 * 
 * Sale, ONAYLANMIŞ tekliflerin satışa dönüştüğü kayıtlardır:
 * - Offer durumu ACCEPTED olduğunda Sale kaydı oluşturulur
 * - offer_id: Teklif üzerinden gelen satışlar için
 * - offer_id null: Direkt satışlar için
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Sale extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'offer_id',
        'amount',
        'currency',
        'sale_date'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sale_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
