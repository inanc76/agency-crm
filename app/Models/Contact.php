<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasBlameable;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 👤 Contact Model - Müşteri İletişim Kişileri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔑 UUID Strategy: ⚠️ MANUAL (No HasUuids trait)                        │
 * │    Primary Key: string (UUID must be set manually)                      │
 * │    Incrementing: false                                                  │
 * │    ⚠️ NOT: UUID generation UI/Service katmanında yapılmalıdır          │
 * └─────────────────────────────────────────────────────────────────────────┘
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 📊 Database Columns (contacts table)                                    │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property string $id                      UUID primary key (manuel)
 * @property string $customer_id             Müşteri UUID (FK: customers)
 * @property string $name                    Kişi adı soyadı
 * @property string|null $email              Ana e-posta adresi
 * @property \ArrayObject|null $emails       Çoklu e-posta dizisi (JSON)
 * @property string|null $phone              Ana telefon numarası
 * @property \ArrayObject|null $phones       Çoklu telefon dizisi (JSON)
 * @property string|null $position           Pozisyon/Ünvan
 * @property string|null $status             Kişi durumu (ReferenceData: ACTIVE, INACTIVE, LEFT)
 * @property string|null $gender             Cinsiyet (ReferenceData: MALE, FEMALE, OTHER)
 * @property \Carbon\Carbon|null $birth_date Doğum tarihi
 * @property \ArrayObject|null $social_profiles Sosyal medya profilleri (JSON: {linkedin, twitter, ...})
 * @property \ArrayObject|null $extensions   Ek bilgiler (JSON: özel alanlar)
 * @property \Carbon\Carbon $created_at      Kayıt oluşturma zamanı
 * @property \Carbon\Carbon $updated_at      Son güncelleme zamanı
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 🔗 Eloquent İlişkileri                                                  │
 * └─────────────────────────────────────────────────────────────────────────┘
 * @property-read Customer $customer
 *                BelongsTo: Kişinin bağlı olduğu müşteri
 * 
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ 💼 İş Mantığı Şerhi (Business Logic)                                    │
 * └─────────────────────────────────────────────────────────────────────────┘
 * Contact modeli, Customer'a bağlı GERÇEK KİŞİLERİ temsil eder:
 * 
 * 1. **Çoklu İletişim Kanalları**:
 *    - emails, phones: AsArrayObject cast ile JSON array
 *    - UI'da dinamik input field'lar (örn: "E-posta Ekle" butonu)
 *    - Her bir kanal için primary (ana) ve secondary (yedek) ayrımı yapılabilir
 * 
 * 2. **Sosyal Medya Entegrasyonu**:
 *    - social_profiles: {linkedin: "url", twitter: "handle", ...}
 *    - UI'da icon'larla gösterilir, direkt link açılır
 * 
 * 3. **Genişletilebilir Yapı**:
 *    - extensions: Müşteri bazlı özel alanlar (örn: "Favori Kahve", "Doğum Günü Hediyesi")
 *    - ReferenceData ile yönetilebilir
 * 
 * 4. **Lifecycle Management**:
 *    - ACTIVE: Aktif çalışan
 *    - INACTIVE: Geçici olarak pasif (izin, hastalık)
 *    - LEFT: Şirketten ayrıldı (soft delete yerine status kullanımı)
 * 
 * 5. **GDPR/KVKK Uyumluluğu**:
 *    - Kişisel veri içerir (doğum tarihi, cinsiyet, iletişim bilgileri)
 *    - Silme/güncelleme işlemlerinde authorization + audit log zorunlu
 *    - Müşteri silindiğinde Contact kayıtları da temizlenmelidir (cascade)
 * 
 * 6. **UUID Generation**:
 *    ⚠️ HasUuids trait KULLANILMADIĞI için, UUID generation:
 *    - Livewire component'te: $this->id = Str::uuid();
 *    - Service katmanında: $contact->id = Str::uuid();
 *    - Repository'de: Explicit UUID assignment
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Contact extends Model
{
    use HasFactory, SoftDeletes, HasBlameable;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'name',
        'email',
        'emails',
        'phone',
        'phones',
        'position',
        'status',
        'gender',
        'birth_date',
        'social_profiles',
        'extensions'
    ];

    protected function casts(): array
    {
        return [
            'emails' => AsArrayObject::class,
            'phones' => AsArrayObject::class,
            'social_profiles' => AsArrayObject::class,
            'extensions' => AsArrayObject::class,
            'birth_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
