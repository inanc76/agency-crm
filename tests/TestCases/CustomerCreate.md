# 🧪 Customer Create Module - Test Anayasası
**Dosya:** `resources/views/livewire/customers/create.blade.php` (930 satır)  
**Tarih:** 2026-01-10  
**Durum:** Kritik Bölge - Authorization & N+1 Odaklı Test Senaryoları

---

## 📋 Test Kategorileri

### 🔐 A. Authorization Tests (Yetki Kontrolleri) - 10 Senaryo

#### T01: Yetkisiz Kullanıcı Erişim Engeli
- **Amaç:** Müşteri oluşturma yetkisi olmayan kullanıcı `/dashboard/customers/create` sayfasına erişemez.
- **Beklenen:** 403 Forbidden veya redirect to dashboard.
- **Kritiklik:** 🔴 Yüksek

#### T02: Yetkili Kullanıcı Erişim İzni
- **Amaç:** `customers.create` yetkisi olan kullanıcı sayfaya erişebilir.
- **Beklenen:** 200 OK, form görüntülenir.
- **Kritiklik:** 🟢 Düşük

#### T03: Müşteri Görüntüleme Yetkisi (View Mode)
- **Amaç:** `customers.view` yetkisi olmayan kullanıcı `/dashboard/customers/{id}` sayfasına erişemez.
- **Beklenen:** 403 veya redirect.
- **Kritiklik:** 🔴 Yüksek

#### T04: Müşteri Düzenleme Yetkisi (Edit Mode)
- **Amaç:** `customers.edit` yetkisi olmayan kullanıcı "Düzenle" butonuna basınca hata alır.
- **Beklenen:** `toggleEditMode()` çağrısı yetki kontrolü yapar, 403.
- **Kritiklik:** 🔴 Yüksek

#### T05: Müşteri Silme Yetkisi
- **Amaç:** `customers.delete` yetkisi olmayan kullanıcı "Sil" butonuna basınca hata alır.
- **Beklenen:** `delete()` metodu yetki kontrolü yapar, 403.
- **Kritiklik:** 🔴 Yüksek

#### T06: İlişkili Firma Ekleme Yetkisi
- **Amaç:** `customers.edit` yetkisi olmayan kullanıcı ilişkili firma ekleyemez.
- **Beklenen:** `addRelatedCustomer()` yetki kontrolü yapar.
- **Kritiklik:** 🟡 Orta

#### T07: Logo Yükleme Yetkisi
- **Amaç:** `customers.edit` yetkisi olmayan kullanıcı logo yükleyemez.
- **Beklenen:** `save()` metodu logo yükleme işlemini yetki kontrolü ile yapar.
- **Kritiklik:** 🟡 Orta

#### T08: Tab Erişim Yetkisi (Contacts, Assets, Services)
- **Amaç:** `contacts.view`, `assets.view`, `services.view` yetkisi olmayan kullanıcı ilgili tabları göremez.
- **Beklenen:** Tab'lar gizlenir veya "Yetki yok" mesajı gösterilir.
- **Kritiklik:** 🟡 Orta

#### T09: Toplu Veri Görüntüleme Yetkisi
- **Amaç:** `customers.view` yetkisi olmayan kullanıcı müşteri listesine erişemez.
- **Beklenen:** `/dashboard/customers?tab=customers` sayfası 403 döner.
- **Kritiklik:** 🔴 Yüksek

#### T10: Müşteri Oluşturma Sonrası Redirect Yetkisi
- **Amaç:** Müşteri oluşturulduktan sonra `/dashboard/customers/{id}` sayfasına yönlendirme yapılır, yetki kontrolü yapılır.
- **Beklenen:** Yetki yoksa redirect to dashboard.
- **Kritiklik:** 🟡 Orta

---

### 🔗 B. N+1 Query Tests (Eager Loading Kontrolleri) - 15 Senaryo

#### T11: Customer Load - Related Customers N+1
- **Amaç:** `loadCustomerData()` metodu `relatedCustomers` ilişkisini eager loading ile yükler.
- **Kod:** `Customer::with('relatedCustomers')->findOrFail($this->customerId);` (Satır 132)
- **Beklenen:** 1 query (Customer + relatedCustomers).
- **Kritiklik:** 🔴 Yüksek

#### T12: Customer Load - Contacts N+1
- **Amaç:** `loadCustomerData()` metodu `contacts` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->contacts()->orderBy('name')->get()` (Satır 167)
- **Beklenen:** 1 query (contacts).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('contacts')->findOrFail($this->customerId);`

#### T13: Customer Load - Assets N+1
- **Amaç:** `loadCustomerData()` metodu `assets` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->assets()->orderBy('name')->get()` (Satır 168)
- **Beklenen:** 1 query (assets).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('assets')->findOrFail($this->customerId);`

#### T14: Customer Load - Services N+1
- **Amaç:** `loadCustomerData()` metodu `services` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->services()->orderBy('created_at', 'desc')->get()` (Satır 169)
- **Beklenen:** 1 query (services).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('services')->findOrFail($this->customerId);`

#### T15: Customer Load - Offers N+1
- **Amaç:** `loadCustomerData()` metodu `offers` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->offers()->orderBy('created_at', 'desc')->get()` (Satır 170)
- **Beklenen:** 1 query (offers).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('offers')->findOrFail($this->customerId);`

#### T16: Customer Load - Sales N+1
- **Amaç:** `loadCustomerData()` metodu `sales` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->sales()->orderBy('created_at', 'desc')->get()` (Satır 171)
- **Beklenen:** 1 query (sales).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('sales')->findOrFail($this->customerId);`

#### T17: Customer Load - Messages N+1
- **Amaç:** `loadCustomerData()` metodu `messages` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->messages()->orderBy('created_at', 'desc')->get()` (Satır 172)
- **Beklenen:** 1 query (messages).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('messages')->findOrFail($this->customerId);`

#### T18: Customer Load - Notes N+1
- **Amaç:** `loadCustomerData()` metodu `notes` ilişkisini eager loading ile yükler.
- **Kod:** `$customer->notes()->orderBy('created_at', 'desc')->get()` (Satır 173)
- **Beklenen:** 1 query (notes).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::with('notes')->findOrFail($this->customerId);`

#### T19: Customer Load - Counts N+1
- **Amaç:** `loadCustomerData()` metodu `counts` array'ini eager loading ile yükler.
- **Kod:** `$customer->contacts()->count()` (Satır 157-163)
- **Beklenen:** 1 query (withCount).
- **Kritiklik:** 🔴 Yüksek
- **Fix:** `Customer::withCount(['contacts', 'assets', 'services', 'offers', 'sales', 'messages', 'notes'])->findOrFail($this->customerId);`

#### T20: Mount - Customers Load N+1
- **Amaç:** `mount()` metodu `existingCustomers` listesini eager loading ile yükler.
- **Kod:** `Customer::orderBy('name')->get(['id', 'name'])` (Satır 96)
- **Beklenen:** 1 query (customers).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T21: Mount - Countries Load N+1
- **Amaç:** `mount()` metodu `countries` listesini eager loading ile yükler.
- **Kod:** `DB::table('countries')->where('is_active', true)->get()` (Satır 88)
- **Beklenen:** 1 query (countries).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T22: Mount - Cities Load N+1
- **Amaç:** `loadCities()` metodu `cities` listesini eager loading ile yükler.
- **Kod:** `DB::table('cities')->where('is_active', true)->get()` (Satır 202)
- **Beklenen:** 1 query (cities).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T23: Mount - Reference Items Load N+1
- **Amaç:** `mount()` metodu `customerTypes` listesini eager loading ile yükler.
- **Kod:** `ReferenceItem::where('category_key', 'CUSTOMER_TYPE')->get()` (Satır 80)
- **Beklenen:** 1 query (reference items).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T24: Save - Related Customers Sync N+1
- **Amaç:** `save()` metodu `relatedCustomers` ilişkisini sync ile günceller.
- **Kod:** `$customer->relatedCustomers()->sync($this->related_customers);` (Satır 400)
- **Beklenen:** 1 query (sync).
- **Kritiklik:** 🟡 Orta

#### T25: Tab Switch - Filtered Services N+1
- **Amaç:** Services tab'ında filtreleme yapılırken N+1 problemi oluşmaz.
- **Kod:** `collect($relatedServices)->when($servicesStatusFilter, ...)` (Satır 679)
- **Beklenen:** 1 query (services).
- **Kritiklik:** 🟡 Orta

---

### ✅ C. Validation Tests (Doğrulama Testleri) - 10 Senaryo

#### T26: Required Fields - Name
- **Amaç:** `name` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The name field is required."
- **Kritiklik:** 🟡 Orta

#### T27: Required Fields - Country
- **Amaç:** `country_id` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The country id field is required."
- **Kritiklik:** 🟡 Orta

#### T28: Required Fields - City
- **Amaç:** `city_id` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The city id field is required."
- **Kritiklik:** 🟡 Orta

#### T29: Email Format Validation
- **Amaç:** `emails.*` alanı geçersiz email formatında olamaz.
- **Beklenen:** Validation error: "The emails.0 must be a valid email address."
- **Kritiklik:** 🟡 Orta

#### T30: Website URL Validation
- **Amaç:** `websites.*` alanı geçersiz URL formatında olamaz.
- **Beklenen:** Validation error: "The websites.0 must be a valid URL."
- **Kritiklik:** 🟡 Orta

#### T31: Logo File Size Validation
- **Amaç:** `logo` dosyası 5MB'dan büyük olamaz.
- **Beklenen:** Validation error: "The logo must not be greater than 5120 kilobytes."
- **Kritiklik:** 🟡 Orta

#### T32: Logo File Type Validation
- **Amaç:** `logo` dosyası sadece image formatında olabilir.
- **Beklenen:** Validation error: "The logo must be an image."
- **Kritiklik:** 🟡 Orta

#### T33: Max Email Count
- **Amaç:** En fazla 3 email adresi eklenebilir.
- **Beklenen:** `addEmail()` metodu 3. email'den sonra çalışmaz.
- **Kritiklik:** 🟢 Düşük

#### T34: Max Related Customers Count
- **Amaç:** En fazla 10 ilişkili firma eklenebilir.
- **Beklenen:** `addRelatedCustomer()` metodu 10. firmadan sonra çalışmaz.
- **Kritiklik:** 🟢 Düşük

#### T35: Phone Number Normalization
- **Amaç:** Telefon numarası sadece rakam, + ve boşluk içerebilir.
- **Beklenen:** `normalizePhone()` metodu geçersiz karakterleri temizler.
- **Kritiklik:** 🟢 Düşük

---

### 🔄 D. Business Logic Tests (İş Mantığı Testleri) - 5 Senaryo

#### T36: Website URL Normalization
- **Amaç:** Website URL'si otomatik olarak `https://` ile başlar.
- **Beklenen:** `normalizeUrl('example.com')` => `https://example.com`
- **Kritiklik:** 🟢 Düşük

#### T37: Title Case Formatting
- **Amaç:** `name`, `title`, `tax_office`, `address` alanları Title Case formatında kaydedilir.
- **Beklenen:** `formatTitleCase('deneme')` => `Deneme`
- **Kritiklik:** 🟢 Düşük

#### T38: Default Customer Type
- **Amaç:** Yeni müşteri oluşturulurken varsayılan `customer_type` atanır.
- **Beklenen:** `initNewCustomer()` metodu `is_default=true` olan ReferenceItem'ı seçer.
- **Kritiklik:** 🟢 Düşük

#### T39: Default Country (Türkiye)
- **Amaç:** Yeni müşteri oluşturulurken varsayılan ülke "Türkiye" olarak seçilir.
- **Beklenen:** `initNewCustomer()` metodu `countries` array'inden "Türkiye"yi seçer.
- **Kritiklik:** 🟢 Düşük

#### T40: Default City (İstanbul)
- **Amaç:** Yeni müşteri oluşturulurken varsayılan şehir "İstanbul" olarak seçilir.
- **Beklenen:** `initNewCustomer()` metodu `cities` array'inden "İstanbul"u seçer.
- **Kritiklik:** 🟢 Düşük

---

## 🛠️ Kritik Yamalar (Quick Fixes)

### 🔧 Fix 1: N+1 Problem - loadCustomerData()
**Satır:** 132  
**Mevcut Kod:**
```php
$customer = Customer::with('relatedCustomers')->findOrFail($this->customerId);
```

**Yeni Kod:**
```php
$customer = Customer::with([
    'relatedCustomers',
    'contacts',
    'assets',
    'services',
    'offers',
    'sales',
    'messages',
    'notes'
])->withCount([
    'contacts',
    'assets',
    'services',
    'offers',
    'sales',
    'messages',
    'notes'
])->findOrFail($this->customerId);
```

**Etki:** 8 ayrı query yerine 1 query (N+1 çözüldü).

---

### 🔧 Fix 2: Authorization - toggleEditMode()
**Satır:** 411  
**Mevcut Kod:**
```php
public function toggleEditMode(): void
{
    $this->isViewMode = false;
}
```

**Yeni Kod:**
```php
public function toggleEditMode(): void
{
    // Authorization Check
    if (!auth()->user()->can('customers.edit')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    
    $this->isViewMode = false;
}
```

**Etki:** Yetkisiz kullanıcı düzenleme moduna geçemez.

---

### 🔧 Fix 3: Authorization - delete()
**Satır:** 425  
**Mevcut Kod:**
```php
public function delete(): void
{
    if ($this->customerId) {
        Customer::findOrFail($this->customerId)->delete();
        $this->success('Müşteri Silindi', 'Müşteri kaydı başarıyla silindi.');
        $this->redirect('/dashboard/customers?tab=customers');
    }
}
```

**Yeni Kod:**
```php
public function delete(): void
{
    // Authorization Check
    if (!auth()->user()->can('customers.delete')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    
    if ($this->customerId) {
        Customer::findOrFail($this->customerId)->delete();
        $this->success('Müşteri Silindi', 'Müşteri kaydı başarıyla silindi.');
        $this->redirect('/dashboard/customers?tab=customers');
    }
}
```

**Etki:** Yetkisiz kullanıcı müşteri silemez.

---

## 📊 Test Özeti

| Kategori | Senaryo Sayısı | Kritiklik |
|----------|----------------|-----------|
| Authorization | 10 | 🔴 Yüksek |
| N+1 Query | 15 | 🔴 Yüksek |
| Validation | 10 | 🟡 Orta |
| Business Logic | 5 | 🟢 Düşük |
| **TOPLAM** | **40** | - |

---

## 🎯 Öncelik Sırası

1. **N+1 Fix (T11-T19):** Öncelikli - Performance kritik
2. **Authorization Fix (T01-T10):** Öncelikli - Security kritik
3. **Validation Tests (T26-T35):** Orta - Kullanıcı deneyimi
4. **Business Logic Tests (T36-T40):** Düşük - Fonksiyonel doğruluk

---

**Mimar Notu:** Bu test anayasası, Customer Create modülünün "Zırhlı" hale getirilmesi için gerekli tüm senaryoları kapsar. Kod parçalama öncesi bu testlerin yazılması ve geçmesi zorunludur.
