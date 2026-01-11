<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💰 Offer Model - Teklif Yönetimi ve Fiyatlandırma
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔑 UUID Strategy: ✅ ACTIVE (HasUuids trait)                            │
 * │    Primary Key: string (UUID v4)                                        │
 * │    Incrementing: false (implicit)                                       │
 * └─────────────────────────────────────────────────────────────────────────┘
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 📊 Database Columns (offers table)                                      │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property string $id                      UUID primary key
 * @property string $number                  Teklif numarası (örn: TKL-2026-001)
 * @property string $customer_id             Müşteri UUID (FK: customers)
 * @property string $status                  Teklif durumu (ReferenceData: DRAFT, SENT, ACCEPTED, REJECTED)
 * @property string|null $title              Teklif başlığı
 * @property string|null $description        Teklif açıklaması
 * @property float $total_amount             KDV dahil toplam tutar
 * @property float $original_amount          İndirim öncesi tutar
 * @property float $discount_percentage      İndirim yüzdesi (0-100)
 * @property float $discounted_amount        İndirim tutarı
 * @property string $currency                Para birimi (TRY, USD, EUR)
 * @property \Carbon\Carbon|null $valid_until Teklif geçerlilik tarihi
 * @property string|null $pdf_url            Oluşturulan PDF dosya yolu
 * @property string|null $tracking_token     Teklif takip token'ı (public link)
 * @property float $vat_rate                 KDV oranı (örn: 20.00)
 * @property float $vat_amount               KDV tutarı
 * @property \Carbon\Carbon $created_at      Kayıt oluşturma zamanı
 * @property \Carbon\Carbon $updated_at      Son güncelleme zamanı
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 Eloquent İlişkileri                                                  │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read Customer $customer
 *                BelongsTo: Teklifin ait olduğu müşteri
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<OfferItem> $items
 *                HasMany: Teklif kalemleri (hizmetler/ürünler)
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<OfferAttachment> $attachments
 *                HasMany: Teklife eklenen dosyalar (Minio'da saklanır)
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 💼 İş Mantığı Şerhi (Business Logic)                                    │
 * └─────────────────────────────────────────────────────────────────────────┘
 * Offer modeli, CRM'in FİNANSAL ÇEKIRDEĞI olarak kritik hesaplamalar içerir:
 * 
 * 1. **Fiyat Hesaplama Zinciri**:
 *    - original_amount: Tüm OfferItem'ların toplamı (items.sum(price * quantity))
 *    - discounted_amount: original_amount * (discount_percentage / 100)
 *    - vat_amount: (original_amount - discounted_amount) * (vat_rate / 100)
 *    - total_amount: original_amount - discounted_amount + vat_amount
 *    
 *    ⚠️ Bu hesaplamalar UI'da (Livewire) veya Service katmanında yapılır,
 *    model sadece sonuçları saklar (Single Source of Truth).
 * 
 * 2. **Lifecycle Management**:
 *    - DRAFT: Taslak, düzenlenebilir
 *    - SENT: Müşteriye gönderildi, tracking_token aktif
 *    - ACCEPTED: Müşteri onayladı → Sale kaydı oluşturulur
 *    - REJECTED: Müşteri reddetti
 * 
 * 3. **Cascade Deletion** (booted() method):
 *    Teklif silindiğinde:
 *    - attachments → Minio'dan dosyalar temizlenir (MinioService)
 *    - items → Teklif kalemleri silinir
 *    ⚠️ Bu işlem ATOMIC olmalıdır (DB transaction).
 * 
 * 4. **PDF Generation**:
 *    pdf_url, PdfService tarafından oluşturulur ve Minio'ya yüklenir.
 *    Teklif güncellendiğinde PDF yeniden oluşturulmalıdır.
 * 
 * 5. **Tracking Token**:
 *    Public link için benzersiz token (örn: /offers/track/{token}).
 *    Müşteri bu link ile teklifi görüntüleyip onaylayabilir.
 * 
 * 6. **Güvenlik**:
 *    - Teklif oluşturma/güncelleme: OFFER_CREATE/OFFER_UPDATE permission
 *    - Silme işlemi: OFFER_DELETE permission + Atomic Transaction
 *    - Public tracking: Token doğrulaması yeterli (auth gerekmez)
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Offer extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'id',
        'number',
        'customer_id',
        'status',
        'title',
        'description',
        'total_amount',
        'original_amount',
        'discount_percentage',
        'discounted_amount',
        'currency',
        'valid_until',
        'pdf_url',
        'tracking_token',
        'vat_rate',
        'vat_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discounted_amount' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'valid_until' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OfferItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(OfferAttachment::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Offer $offer) {
            // Delete attachments to trigger their deleting event for file cleanup
            $offer->attachments()->each(function ($attachment) {
                $attachment->delete();
            });

            // Allow OfferItem cascade (usually handled by DB or another hook)
            $offer->items()->delete();
        });
    }
}
