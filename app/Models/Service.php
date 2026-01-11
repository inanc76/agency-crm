<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasBlameable;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🔧 Service Model - Müşteri Hizmet Abonelikleri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 📊 Database Columns (services table)                                    │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property string $id                      UUID primary key
 * @property string $customer_id             Müşteri UUID (FK: customers)
 * @property string|null $asset_id           İlişkili varlık UUID (FK: assets)
 * @property string|null $price_definition_id Fiyat tanımı UUID (FK: price_definitions)
 * @property string $service_name            Hizmet adı
 * @property string|null $service_category   Hizmet kategorisi (ReferenceData)
 * @property int|null $service_duration      Hizmet süresi (gün/ay/yıl)
 * @property float $service_price            Hizmet fiyatı
 * @property string $service_currency        Para birimi (TRY, USD, EUR)
 * @property \Carbon\Carbon|null $start_date Başlangıç tarihi
 * @property \Carbon\Carbon|null $end_date   Bitiş tarihi
 * @property string|null $description        Hizmet açıklaması
 * @property bool $is_active                 Aktiflik durumu
 * @property string|null $status             Hizmet durumu (ReferenceData)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 İlişkiler                                                            │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read Customer $customer         BelongsTo: Hizmetin müşterisi
 * @property-read Asset|null $asset          BelongsTo: İlişkili varlık (domain, hosting)
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 💼 İş Mantığı                                                           │
 * └─────────────────────────────────────────────────────────────────────────┘
 * Service, RECURRING (tekrarlayan) hizmetleri temsil eder:
 * - Hosting, domain, bakım abonelikleri
 * - start_date/end_date ile süre yönetimi
 * - is_active: Manuel aktif/pasif kontrolü
 * - Yenileme: end_date yaklaştığında otomatik bildirim (cron job)
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasBlameable;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'asset_id',
        'price_definition_id',
        'service_name',
        'service_category',
        'service_duration',
        'service_price',
        'service_currency',
        'start_date',
        'end_date',
        'description',
        'is_active',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'service_price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
