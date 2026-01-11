<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🏢 Customer Model - Merkezi Müşteri Varlığı
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔑 UUID Strategy: ✅ ACTIVE (HasUuids trait)                            │
 * │    Primary Key: string (UUID v4)                                        │
 * │    Incrementing: false                                                  │
 * └─────────────────────────────────────────────────────────────────────────┘
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 📊 Database Columns (customers table)                                   │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property string $id                  UUID primary key
 * @property string $name                Müşteri adı (şirket/kişi)
 * @property string|null $title          Ünvan/Pozisyon
 * @property string|null $email          Ana e-posta adresi
 * @property \ArrayObject|null $emails   Çoklu e-posta dizisi (JSON)
 * @property string|null $phone          Ana telefon numarası
 * @property \ArrayObject|null $phones   Çoklu telefon dizisi (JSON)
 * @property string|null $address        Adres bilgisi
 * @property int|null $city_id           Şehir ID (FK: cities)
 * @property int|null $country_id        Ülke ID (FK: countries)
 * @property string|null $tax_number     Vergi numarası
 * @property string|null $tax_office     Vergi dairesi
 * @property string|null $website        Ana web sitesi
 * @property \ArrayObject|null $websites Çoklu web sitesi dizisi (JSON)
 * @property string|null $current_code   Cari hesap kodu
 * @property string|null $logo_url       Logo dosya yolu
 * @property string|null $customer_type  Müşteri tipi (ReferenceData)
 * @property \Carbon\Carbon $created_at  Kayıt oluşturma zamanı
 * @property \Carbon\Carbon $updated_at  Son güncelleme zamanı
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 Eloquent İlişkileri                                                  │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read \Illuminate\Database\Eloquent\Collection<Customer> $relatedCustomers
 *                BelongsToMany: İlişkili müşteriler (customer_relations pivot)
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Contact> $contacts
 *                HasMany: Müşteriye ait kişiler/kontaklar
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Asset> $assets
 *                HasMany: Müşteriye ait dijital varlıklar (domain, hosting vb.)
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Service> $services
 *                HasMany: Müşteriye sunulan aktif hizmetler
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Offer> $offers
 *                HasMany: Müşteriye gönderilen teklifler
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Sale> $sales
 *                HasMany: Müşteriden gerçekleşen satışlar
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Message> $messages
 *                HasMany: Müşteriyle yapılan mesaj/mail iletişimi
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Note> $notes
 *                HasMany: Müşteri hakkında tutulan notlar (polymorphic)
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 💼 İş Mantığı Şerhi (Business Logic)                                    │
 * └─────────────────────────────────────────────────────────────────────────┘
 * Customer, sistemin MERKEZI VARLIĞIdır. Tüm CRM operasyonları bu model
 * etrafında döner:
 * 
 * 1. **Çoklu İletişim Kanalları**: emails, phones, websites alanları
 *    AsArrayObject cast ile JSON olarak saklanır. UI'da dinamik input
 *    field'lar ile yönetilir.
 * 
 * 2. **İlişkili Müşteriler**: relatedCustomers() ile şirket grupları veya
 *    holding yapıları modellenebilir (self-referencing many-to-many).
 * 
 * 3. **Cascade İlişkiler**: Bir müşteri silindiğinde, ilişkili contacts,
 *    assets, services, offers, sales ve notes kayıtları da temizlenmelidir
 *    (DB foreign key constraints veya model events ile).
 * 
 * 4. **ReferenceData Entegrasyonu**: customer_type alanı, ReferenceItem
 *    tablosundan beslenir (örn: CORPORATE, INDIVIDUAL, GOVERNMENT).
 * 
 * 5. **Güvenlik**: Customer verisi GDPR/KVKK kapsamındadır. Silme ve
 *    güncelleme işlemlerinde authorization kontrolü zorunludur.
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Customer extends Model
{
    use HasUuids, HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'title',
        'email',
        'emails',
        'phone',
        'phones',
        'address',
        'city_id',
        'country_id',
        'tax_number',
        'tax_office',
        'website',
        'websites',
        'current_code',
        'logo_url',
        'customer_type'
    ];

    protected function casts(): array
    {
        return [
            'emails' => AsArrayObject::class,
            'phones' => AsArrayObject::class,
            'websites' => AsArrayObject::class,
        ];
    }



    public function relatedCustomers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_relations',
            'customer_id',
            'related_customer_id'
        )->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'entity_id')->where('entity_type', 'CUSTOMER');
    }
}
