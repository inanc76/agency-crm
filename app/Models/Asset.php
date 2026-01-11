<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🌐 Asset Model - Müşteri Dijital Varlıkları
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $customer_id     Müşteri UUID (FK: customers)
 * @property string|null $type       Varlık tipi (ReferenceData: DOMAIN, HOSTING, SSL, etc.)
 * @property string|null $name       Varlık adı (örn: example.com)
 * @property string|null $url        Varlık URL'i
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Customer $customer BelongsTo: Varlığın sahibi müşteri
 * 
 * Asset, müşteriye ait dijital varlıkları (domain, hosting, SSL sertifikası)
 * temsil eder. Service modeli ile ilişkilendirilebilir.
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Asset extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'type',
        'name',
        'url'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
