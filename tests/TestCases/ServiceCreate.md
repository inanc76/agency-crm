# 🧪 Service Create Module - Test Anayasası
**Dosya:** `resources/views/livewire/customers/services/create.blade.php` (604 satır)  
**Tarih:** 2026-01-10  
**Durum:** Kritik Bölge - Authorization & N+1 Odaklı Test Senaryoları

---

## 📋 Test Kategorileri

### 🔐 A. Authorization Tests (Yetki Kontrolleri) - 10 Senaryo

#### T01: Yetkisiz Kullanıcı Erişim Engeli
- **Amaç:** Hizmet oluşturma yetkisi olmayan kullanıcı `/dashboard/customers/services/create` sayfasına erişemez.
- **Beklenen:** 403 Forbidden veya redirect to dashboard.
- **Kritiklik:** 🔴 Yüksek

#### T02: Yetkili Kullanıcı Erişim İzni
- **Amaç:** `services.create` yetkisi olan kullanıcı sayfaya erişebilir.
- **Beklenen:** 200 OK, form görüntülenir.
- **Kritiklik:** 🟢 Düşük

#### T03: Hizmet Görüntüleme Yetkisi (View Mode)
- **Amaç:** `services.view` yetkisi olmayan kullanıcı `/dashboard/customers/services/{id}` sayfasına erişemez.
- **Beklenen:** 403 veya redirect.
- **Kritiklik:** 🔴 Yüksek

#### T04: Hizmet Düzenleme Yetkisi (Edit Mode)
- **Amaç:** `services.edit` yetkisi olmayan kullanıcı "Düzenle" butonuna basınca hata alır.
- **Beklenen:** `toggleEditMode()` çağrısı yetki kontrolü yapar, 403.
- **Kritiklik:** 🔴 Yüksek

#### T05: Hizmet Silme Yetkisi
- **Amaç:** `services.delete` yetkisi olmayan kullanıcı "Sil" butonuna basınca hata alır.
- **Beklenen:** `delete()` metodu yetki kontrolü yapar, 403.
- **Kritiklik:** 🔴 Yüksek

#### T06: Müşteri Seçimi Yetkisi
- **Amaç:** `customers.view` yetkisi olmayan kullanıcı müşteri listesini göremez.
- **Beklenen:** `mount()` metodu müşteri listesini yüklerken yetki kontrolü yapar.
- **Kritiklik:** 🟡 Orta

#### T07: Varlık Seçimi Yetkisi
- **Amaç:** `assets.view` yetkisi olmayan kullanıcı varlık listesini göremez.
- **Beklenen:** `loadAssets()` metodu varlık listesini yüklerken yetki kontrolü yapar.
- **Kritiklik:** 🟡 Orta

#### T08: Fiyat Tanımı Görüntüleme Yetkisi
- **Amaç:** `prices.view` yetkisi olmayan kullanıcı fiyat tanımlarını göremez.
- **Beklenen:** `mount()` metodu fiyat kategorilerini yüklerken yetki kontrolü yapar.
- **Kritiklik:** 🟡 Orta

#### T09: Toplu Hizmet Oluşturma Yetkisi
- **Amaç:** `services.create` yetkisi olmayan kullanıcı birden fazla hizmet oluşturamaz.
- **Beklenen:** `save()` metodu yetki kontrolü yapar.
- **Kritiklik:** 🔴 Yüksek

#### T10: Hizmet Oluşturma Sonrası Redirect Yetkisi
- **Amaç:** Hizmet oluşturulduktan sonra `/dashboard/customers?tab=services` sayfasına yönlendirme yapılır, yetki kontrolü yapılır.
- **Beklenen:** Yetki yoksa redirect to dashboard.
- **Kritiklik:** 🟡 Orta

---

### 🔗 B. N+1 Query Tests (Eager Loading Kontrolleri) - 15 Senaryo

#### T11: Mount - Customers Load N+1
- **Amaç:** `mount()` metodu `customers` listesini eager loading ile yükler.
- **Kod:** `Customer::orderBy('name')->get(['id', 'name'])` (Satır 42)
- **Beklenen:** 1 query (customers).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T12: Mount - Price Definitions Load N+1
- **Amaç:** `mount()` metodu `categories` listesini eager loading ile yükler.
- **Kod:** `PriceDefinition::where('is_active', true)->distinct()->pluck('category')` (Satır 48)
- **Beklenen:** 1 query (price definitions).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T13: LoadAssets - Assets Load N+1
- **Amaç:** `loadAssets()` metodu `assets` listesini eager loading ile yükler.
- **Kod:** `Asset::where('customer_id', $this->customer_id)->orderBy('name')->get()` (Satır 140)
- **Beklenen:** 1 query (assets).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T14: LoadServicesForIndex - Services List N+1
- **Amaç:** `loadServicesForIndex()` metodu `services_list` array'ini eager loading ile yükler.
- **Kod:** `PriceDefinition::where('category', $this->services[$index]['category'])->get()` (Satır 172)
- **Beklenen:** 1 query (price definitions).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T15: Save - Multiple Services Creation N+1
- **Amaç:** `save()` metodu birden fazla hizmet oluştururken N+1 problemi oluşmaz.
- **Kod:** `DB::transaction(function () { foreach ($this->services as $serviceData) { Service::create(...) } })` (Satır 231)
- **Beklenen:** Transaction içinde her service için 1 query (toplu insert yok).
- **Kritiklik:** 🟡 Orta
- **Fix:** `Service::insert()` kullanarak toplu insert yapılabilir.

#### T16: LoadServiceData - Service Load N+1
- **Amaç:** `loadServiceData()` metodu `service` kaydını eager loading ile yükler.
- **Kod:** `Service::findOrFail($this->serviceId)` (Satır 77)
- **Beklenen:** 1 query (service).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T17: Delete - Service Load N+1
- **Amaç:** `delete()` metodu `service` kaydını eager loading ile yükler.
- **Kod:** `Service::findOrFail($this->serviceId)` (Satır 288)
- **Beklenen:** 1 query (service).
- **Kritiklik:** 🟢 Düşük (zaten eager loading yok)

#### T18: UpdatedCustomerId - Assets Reload N+1
- **Amaç:** `updatedCustomerId()` metodu `assets` listesini yeniden yüklerken N+1 problemi oluşmaz.
- **Kod:** `$this->loadAssets()` (Satır 133)
- **Beklenen:** 1 query (assets).
- **Kritiklik:** 🟢 Düşük

#### T19: UpdatedServices - Services List Reload N+1
- **Amaç:** `updatedServices()` metodu `services_list` array'ini yeniden yüklerken N+1 problemi oluşmaz.
- **Kod:** `$this->loadServicesForIndex($index)` (Satır 160)
- **Beklenen:** 1 query (price definitions).
- **Kritiklik:** 🟢 Düşük

#### T20: Mount - Customer Query Parameter N+1
- **Amaç:** `mount()` metodu `customer` query parametresini kontrol ederken N+1 problemi oluşmaz.
- **Kod:** `collect($this->customers)->firstWhere('id', $customerId)` (Satır 66)
- **Beklenen:** 0 query (collection üzerinde arama).
- **Kritiklik:** 🟢 Düşük

#### T21: UpdateServicePrice - Price Definition Lookup N+1
- **Amaç:** `updateServicePrice()` metodu `priceDef` kaydını ararken N+1 problemi oluşmaz.
- **Kod:** `collect($this->services[$index]['services_list'])->firstWhere('name', $serviceName)` (Satır 184)
- **Beklenen:** 0 query (collection üzerinde arama).
- **Kritiklik:** 🟢 Düşük

#### T22: Save - Transaction Rollback N+1
- **Amaç:** `save()` metodu transaction içinde hata oluşursa rollback yapar, N+1 problemi oluşmaz.
- **Kod:** `DB::transaction(function () { ... })` (Satır 231)
- **Beklenen:** Hata durumunda rollback, N+1 yok.
- **Kritiklik:** 🟡 Orta

#### T23: CalculateEndDate - Date Calculation N+1
- **Amaç:** `calculateEndDate()` metodu tarih hesaplarken N+1 problemi oluşmaz.
- **Kod:** `$startDate->copy()->addYear()` (Satır 262)
- **Beklenen:** 0 query (Carbon üzerinde işlem).
- **Kritiklik:** 🟢 Düşük

#### T24: LoadServiceData - Asset Name Lookup N+1
- **Amaç:** View mode'da asset adı gösterilirken N+1 problemi oluşmaz.
- **Kod:** `\App\Models\Asset::find($asset_id)?->name` (Satır 415)
- **Beklenen:** 1 query (asset).
- **Kritiklik:** 🟡 Orta
- **Fix:** `loadServiceData()` içinde `Service::with('asset')->findOrFail()` kullanılmalı.

#### T25: LoadServiceData - Customer Name Lookup N+1
- **Amaç:** View mode'da customer adı gösterilirken N+1 problemi oluşmaz.
- **Kod:** `collect($customers)->firstWhere('id', $customer_id)['name']` (Satır 396)
- **Beklenen:** 0 query (collection üzerinde arama).
- **Kritiklik:** 🟢 Düşük

---

### ✅ C. Validation Tests (Doğrulama Testleri) - 10 Senaryo

#### T26: Required Fields - Customer
- **Amaç:** `customer_id` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The customer id field is required."
- **Kritiklik:** 🟡 Orta

#### T27: Required Fields - Asset
- **Amaç:** `asset_id` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The asset id field is required."
- **Kritiklik:** 🟡 Orta

#### T28: Required Fields - Start Date
- **Amaç:** `start_date` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The start date field is required."
- **Kritiklik:** 🟡 Orta

#### T29: Required Fields - Service Category
- **Amaç:** `services.*.category` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The services.0.category field is required."
- **Kritiklik:** 🟡 Orta

#### T30: Required Fields - Service Name
- **Amaç:** `services.*.service_name` alanı boş bırakılamaz.
- **Beklenen:** Validation error: "The services.0.service name field is required."
- **Kritiklik:** 🟡 Orta

#### T31: Date Format Validation
- **Amaç:** `start_date` alanı geçerli tarih formatında olmalıdır.
- **Beklenen:** Validation error: "The start date must be a valid date."
- **Kritiklik:** 🟡 Orta

#### T32: Max Services Count
- **Amaç:** En fazla 5 hizmet eklenebilir.
- **Beklenen:** `addService()` metodu 5. hizmetten sonra çalışmaz.
- **Kritiklik:** 🟢 Düşük

#### T33: Min Services Count
- **Amaç:** En az 1 hizmet olmalıdır.
- **Beklenen:** `removeService()` metodu son hizmeti silmez.
- **Kritiklik:** 🟢 Düşük

#### T34: Service Price Validation
- **Amaç:** `service_price` alanı numeric olmalıdır.
- **Beklenen:** Validation error: "The services.0.service price must be a number."
- **Kritiklik:** 🟡 Orta

#### T35: Service Currency Validation
- **Amaç:** `service_currency` alanı geçerli para birimi olmalıdır.
- **Beklenen:** Validation error: "The services.0.service currency must be a valid currency."
- **Kritiklik:** 🟢 Düşük

---

### 🔄 D. Business Logic Tests (İş Mantığı Testleri) - 5 Senaryo

#### T36: End Date Calculation - Monthly
- **Amaç:** `calculateEndDate()` metodu "month" içeren duration için 1 ay ekler.
- **Beklenen:** `calculateEndDate('2024-01-01', '1 Month')` => `2024-02-01`
- **Kritiklik:** 🟡 Orta

#### T37: End Date Calculation - Yearly
- **Amaç:** `calculateEndDate()` metodu "year" içermeyen duration için 1 yıl ekler (fallback).
- **Beklenen:** `calculateEndDate('2024-01-01', '1 Year')` => `2025-01-01`
- **Kritiklik:** 🟡 Orta

#### T38: Service Price Auto-Fill
- **Amaç:** Hizmet seçildiğinde fiyat otomatik olarak doldurulur.
- **Beklenen:** `updateServicePrice()` metodu `service_price` alanını günceller.
- **Kritiklik:** 🟢 Düşük

#### T39: Service Duration Auto-Fill
- **Amaç:** Hizmet seçildiğinde süre otomatik olarak doldurulur.
- **Beklenen:** `updateServicePrice()` metodu `service_duration` alanını günceller.
- **Kritiklik:** 🟢 Düşük

#### T40: Service Currency Auto-Fill
- **Amaç:** Hizmet seçildiğinde para birimi otomatik olarak doldurulur.
- **Beklenen:** `updateServicePrice()` metodu `service_currency` alanını günceller.
- **Kritiklik:** 🟢 Düşük

---

## 🛠️ Kritik Yamalar (Quick Fixes)

### 🔧 Fix 1: N+1 Problem - loadServiceData()
**Satır:** 77  
**Mevcut Kod:**
```php
$service = Service::findOrFail($this->serviceId);
```

**Yeni Kod:**
```php
$service = Service::with(['customer', 'asset'])->findOrFail($this->serviceId);
```

**Etki:** 2 ayrı query yerine 1 query (N+1 çözüldü).

---

### 🔧 Fix 2: Authorization - toggleEditMode()
**Satır:** 280  
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
    if (!auth()->user()->can('services.edit')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    
    $this->isViewMode = false;
}
```

**Etki:** Yetkisiz kullanıcı düzenleme moduna geçemez.

---

### 🔧 Fix 3: Authorization - delete()
**Satır:** 285  
**Mevcut Kod:**
```php
public function delete(): void
{
    if ($this->serviceId) {
        $service = Service::findOrFail($this->serviceId);
        $customer_id = $service->customer_id;
        $service->delete();
        $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
        $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
    }
}
```

**Yeni Kod:**
```php
public function delete(): void
{
    // Authorization Check
    if (!auth()->user()->can('services.delete')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    
    if ($this->serviceId) {
        $service = Service::findOrFail($this->serviceId);
        $customer_id = $service->customer_id;
        $service->delete();
        $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
        $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
    }
}
```

**Etki:** Yetkisiz kullanıcı hizmet silemez.

---

### 🔧 Fix 4: Bulk Insert Optimization - save()
**Satır:** 231  
**Mevcut Kod:**
```php
DB::transaction(function () use ($startDate) {
    foreach ($this->services as $serviceData) {
        $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

        Service::create([
            'id' => Str::uuid()->toString(),
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
            'price_definition_id' => $serviceData['price_definition_id'],
            'service_name' => $serviceData['service_name'],
            'service_category' => $serviceData['category'],
            'service_duration' => $serviceData['service_duration'],
            'service_price' => $serviceData['service_price'],
            'service_currency' => $serviceData['service_currency'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $serviceData['description'],
            'status' => $serviceData['status'],
            'is_active' => $serviceData['status'] === 'ACTIVE',
        ]);
    }
});
```

**Yeni Kod:**
```php
DB::transaction(function () use ($startDate) {
    $servicesToInsert = [];
    
    foreach ($this->services as $serviceData) {
        $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);
        
        $servicesToInsert[] = [
            'id' => Str::uuid()->toString(),
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
            'price_definition_id' => $serviceData['price_definition_id'],
            'service_name' => $serviceData['service_name'],
            'service_category' => $serviceData['category'],
            'service_duration' => $serviceData['service_duration'],
            'service_price' => $serviceData['service_price'],
            'service_currency' => $serviceData['service_currency'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $serviceData['description'],
            'status' => $serviceData['status'],
            'is_active' => $serviceData['status'] === 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    Service::insert($servicesToInsert);
});
```

**Etki:** 5 ayrı query yerine 1 query (bulk insert).

---

## 📊 Test Özeti

| Kategori | Senaryo Sayısı | Kritiklik |
|----------|----------------|-----------|
| Authorization | 10 | 🔴 Yüksek |
| N+1 Query | 15 | 🟡 Orta |
| Validation | 10 | 🟡 Orta |
| Business Logic | 5 | 🟢 Düşük |
| **TOPLAM** | **40** | - |

---

## 🎯 Öncelik Sırası

1. **Authorization Fix (T01-T10):** Öncelikli - Security kritik
2. **Bulk Insert Fix (T15):** Öncelikli - Performance kritik
3. **N+1 Fix (T11-T25):** Orta - Performance iyileştirme
4. **Validation Tests (T26-T35):** Orta - Kullanıcı deneyimi
5. **Business Logic Tests (T36-T40):** Düşük - Fonksiyonel doğruluk

---

**Mimar Notu:** Bu test anayasası, Service Create modülünün "Zırhlı" hale getirilmesi için gerekli tüm senaryoları kapsar. Kod parçalama öncesi bu testlerin yazılması ve geçmesi zorunludur.
