<?php

namespace App\Models;

use App\Traits\HasBlameable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💰 Offer Model - Teklif Yönetimi ve Fiyatlandırma
 * ═══════════════════════════════════════════════════════════════════════════
 *
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
 *
 * @property string $id UUID primary key
 * @property string $number Teklif numarası (örn: TKL-2026-001)
 * @property string $customer_id Müşteri UUID (FK: customers)
 * @property string $status Teklif durumu (ReferenceData: DRAFT, SENT, ACCEPTED, REJECTED)
 * @property string|null $title Teklif başlığı
 * @property string|null $description Teklif açıklaması
 * @property float $total_amount KDV dahil toplam tutar
 * @property float $original_amount İndirim öncesi tutar
 * @property float $discount_percentage İndirim yüzdesi (0-100)
 * @property float $discounted_amount İndirim tutarı
 * @property string $currency Para birimi (TRY, USD, EUR)
 * @property \Carbon\Carbon|null $valid_until Teklif geçerlilik tarihi
 * @property string|null $pdf_url Oluşturulan PDF dosya yolu
 * @property string|null $tracking_token Teklif takip token'ı (public link)
 * @property float $vat_rate KDV oranı (örn: 20.00)
 * @property float $vat_amount KDV tutarı
 * @property bool $is_pdf_downloadable PDF indirilebilir mi?
 * @property bool $is_attachments_downloadable Ekler indirilebilir mi?
 * @property bool $is_downloadable_after_expiry Süresi dolunca indirilebilir mi?
 * @property \Carbon\Carbon $created_at Kayıt oluşturma zamanı
 * @property \Carbon\Carbon $updated_at Son güncelleme zamanı
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 Eloquent İlişkileri                                                  │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read Customer $customer
 *                BelongsTo: Teklifin ait olduğu müşteri
 * @property-read \Illuminate\Database\Eloquent\Collection<OfferItem> $items
 *                HasMany: Teklif kalemleri (hizmetler/ürünler)
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
 *    🛡️ Audit: SoftDeletes + Blameable aktif
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Offer extends Model
{
    use HasBlameable, HasFactory, HasUuids, SoftDeletes;

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
        'is_pdf_downloadable',
        'is_attachments_downloadable',
        'is_downloadable_after_expiry',
        'selected_introduction_files',
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
            'is_pdf_downloadable' => 'boolean',
            'is_attachments_downloadable' => 'boolean',
            'is_attachments_downloadable' => 'boolean',
            'is_downloadable_after_expiry' => 'boolean',
            'selected_introduction_files' => 'array',
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

    public function sections()
    {
        return $this->hasMany(OfferSection::class)->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->hasMany(OfferAttachment::class);
    }

    /**
     * Get the reference item for the current status.
     */
    public function status_item(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'status', 'key')
            ->where('category_key', 'OFFER_STATUS');
    }

    /**
     * Get the reference item for the current currency.
     */
    public function currency_item(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'currency', 'key')
            ->where('category_key', 'CURRENCY');
    }

    /**
     * Get the reference item for the current VAT rate.
     * Note: Since the DB stores a decimal, we find the matching reference item.
     */
    public function vat_item()
    {
        return ReferenceItem::where('category_key', 'VAT_RATES')
            ->get()
            ->first(function ($item) {
                // Try from metadata if available
                if (isset($item->metadata['rate'])) {
                    return (float) $item->metadata['rate'] === (float) $this->vat_rate;
                }
                // Fallback to parsing display_label or key
                if (preg_match('/(\d+)/', $item->display_label, $matches)) {
                    return (float) $matches[1] === (float) $this->vat_rate;
                }
                return false;
            });
    }

    protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            if (empty($offer->tracking_token)) {
                $offer->tracking_token = \Illuminate\Support\Str::uuid();
            }
            // Defaults
            if (is_null($offer->is_pdf_downloadable))
                $offer->is_pdf_downloadable = true;
            if (is_null($offer->is_attachments_downloadable))
                $offer->is_attachments_downloadable = true;
        });

        static::deleting(function (Offer $offer) {
            // Delete attachments to trigger their deleting event for file cleanup
            $offer->attachments()->each(function ($attachment) {
                $attachment->delete();
            });

            // Delete sections which will cascade (if configured) or handle manually
            $offer->sections()->each(function ($section) {
                $section->delete();
            });

            // Allow OfferItem cascade (usually handled by DB or another hook)
            $offer->items()->delete();
        });
    }

    /**
     * Teklife ait notlar (Polymorphic)
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'entity_id')
            ->where('entity_type', 'OFFER')
            ->orderBy('created_at', 'desc');
    }
}
