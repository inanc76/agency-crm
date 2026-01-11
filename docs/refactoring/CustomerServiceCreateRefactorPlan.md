# 🏗️ Customer & Service Create - Dosya Parçalama Planı
**Tarih:** 2026-01-10  
**Durum:** ADIM 1 - Mühendislik Dökümanı (Kod Parçalama Öncesi)  
**Hedef:** 930 satırlık ve 604 satırlık dosyaları Settings modeli gibi parçalara bölme planı

---

## 📊 Mevcut Durum Analizi

### 📁 Customer Create Module
**Dosya:** `resources/views/livewire/customers/create.blade.php`  
**Satır Sayısı:** 930 satır  
**Durum:** 🔴 Kritik (300 satır kuralını 3x aşıyor)

#### Dosya Yapısı:
```
1-439:   PHP Logic (Component Class)
440-930: Blade Template (UI)
```

#### Bölüm Analizi:
| Bölüm | Satır Aralığı | Satır Sayısı | Açıklama |
|-------|---------------|--------------|----------|
| Component Class | 1-439 | 439 | Livewire Component Logic |
| Back Button | 440-448 | 9 | Geri dönüş linki |
| Header | 449-502 | 54 | Başlık ve aksiyon butonları |
| Tab Navigation | 503-542 | 40 | Tab menüsü (View Mode) |
| Main Layout | 543-930 | 388 | Form kartları ve tab içerikleri |

---

### 📁 Service Create Module
**Dosya:** `resources/views/livewire/customers/services/create.blade.php`  
**Satır Sayısı:** 604 satır  
**Durum:** 🔴 Kritik (300 satır kuralını 2x aşıyor)

#### Dosya Yapısı:
```
1-296:   PHP Logic (Component Class)
297-604: Blade Template (UI)
```

#### Bölüm Analizi:
| Bölüm | Satır Aralığı | Satır Sayısı | Açıklama |
|-------|---------------|--------------|----------|
| Component Class | 1-296 | 296 | Livewire Component Logic |
| Back Button | 297-305 | 9 | Geri dönüş linki |
| Header | 306-358 | 53 | Başlık ve aksiyon butonları |
| Tab Navigation | 359-381 | 23 | Tab menüsü (View Mode) |
| Main Layout | 382-604 | 223 | Form kartları ve tab içerikleri |

---

## 🎯 Parçalama Stratejisi (Settings Modeli)

Settings modülünde kullandığımız yapıyı referans alıyoruz:

### Settings Modeli Örneği:
```
resources/views/livewire/settings/
├── panel.blade.php (Ana dosya - 150 satır)
└── partials/
    ├── _typography.blade.php
    ├── _inputs.blade.php
    ├── _buttons.blade.php
    ├── _cards.blade.php
    └── _tables.blade.php
```

---

## 📦 Customer Create - Parçalama Planı

### Hedef Yapı:
```
resources/views/livewire/customers/
├── create.blade.php (Ana dosya - ~200 satır)
├── _actions.php (Trait - Authorization & Business Logic)
├── _validation.php (Validation Rules)
└── partials/
    ├── _header.blade.php
    ├── _tab-navigation.blade.php
    ├── _basic-info-card.blade.php (Zaten var)
    ├── _address-card.blade.php (Zaten var)
    ├── _financial-card.blade.php (Zaten var)
    ├── _related-companies-card.blade.php (Zaten var)
    ├── _registration-info-card.blade.php (Zaten var)
    └── tabs/
        ├── _contacts-tab.blade.php
        ├── _assets-tab.blade.php
        ├── _services-tab.blade.php
        ├── _offers-tab.blade.php
        ├── _sales-tab.blade.php
        ├── _messages-tab.blade.php
        └── _notes-tab.blade.php
```

### Parçalama Detayları:

#### 1. `create.blade.php` (Ana Dosya - ~200 satır)
**İçerik:**
- Component Class (1-109): Mount, State Management
- Main Layout Structure (440-560): Container, includes
- Conditional Tab Rendering

**Satır Hedefi:** 200 satır

---

#### 2. `_actions.php` (Trait - ~150 satır)
**İçerik:**
- `save()` metodu (336-409)
- `toggleEditMode()` metodu (411-414) + Authorization
- `cancel()` metodu (416-423)
- `delete()` metodu (425-432) + Authorization
- `createNew()` metodu (435-438)
- `addRelatedCustomer()` metodu (186-191)
- `removeRelatedCustomer()` metodu (193-198)

**Satır Hedefi:** 150 satır

---

#### 3. `_validation.php` (Validation Rules - ~50 satır)
**İçerik:**
- Validation rules array (338-352)
- Custom validation messages
- Validation helper methods

**Satır Hedefi:** 50 satır

---

#### 4. `partials/_header.blade.php` (~50 satır)
**İçerik:**
- Back Button (443-448)
- Page Title (451-473)
- Action Buttons (474-501)

**Satır Hedefi:** 50 satır

---

#### 5. `partials/_tab-navigation.blade.php` (~40 satır)
**İçerik:**
- Tab Navigation (505-539)
- Active tab highlighting

**Satır Hedefi:** 40 satır

---

#### 6. `partials/tabs/_contacts-tab.blade.php` (~50 satır)
**İçerik:**
- Contacts Tab (562-610)
- Table structure
- Empty state

**Satır Hedefi:** 50 satır

---

#### 7. `partials/tabs/_assets-tab.blade.php` (~50 satır)
**İçerik:**
- Assets Tab (612-658)
- Table structure
- Empty state

**Satır Hedefi:** 50 satır

---

#### 8. `partials/tabs/_services-tab.blade.php` (~80 satır)
**İçerik:**
- Services Tab (661-743)
- Filter dropdown
- Table structure
- Empty state

**Satır Hedefi:** 80 satır

---

#### 9. `partials/tabs/_offers-tab.blade.php` (~80 satır)
**İçerik:**
- Offers Tab (746-830)
- Filter dropdown
- Table structure
- Empty state

**Satır Hedefi:** 80 satır

---

#### 10. `partials/tabs/_sales-tab.blade.php` (~50 satır)
**İçerik:**
- Sales Tab (832-880)
- Table structure
- Empty state

**Satır Hedefi:** 50 satır

---

#### 11. `partials/tabs/_messages-tab.blade.php` (~30 satır)
**İçerik:**
- Messages Tab (882-900)
- Empty state

**Satır Hedefi:** 30 satır

---

#### 12. `partials/tabs/_notes-tab.blade.php` (~30 satır)
**İçerik:**
- Notes Tab (902-920)
- Empty state

**Satır Hedefi:** 30 satır

---

### Toplam Satır Kontrolü:
| Dosya | Satır Sayısı | Durum |
|-------|--------------|-------|
| create.blade.php | 200 | ✅ <300 |
| _actions.php | 150 | ✅ <300 |
| _validation.php | 50 | ✅ <300 |
| _header.blade.php | 50 | ✅ <300 |
| _tab-navigation.blade.php | 40 | ✅ <300 |
| _contacts-tab.blade.php | 50 | ✅ <300 |
| _assets-tab.blade.php | 50 | ✅ <300 |
| _services-tab.blade.php | 80 | ✅ <300 |
| _offers-tab.blade.php | 80 | ✅ <300 |
| _sales-tab.blade.php | 50 | ✅ <300 |
| _messages-tab.blade.php | 30 | ✅ <300 |
| _notes-tab.blade.php | 30 | ✅ <300 |
| **TOPLAM** | **860** | ✅ |

---

## 📦 Service Create - Parçalama Planı

### Hedef Yapı:
```
resources/views/livewire/customers/services/
├── create.blade.php (Ana dosya - ~150 satır)
├── _actions.php (Trait - Authorization & Business Logic)
├── _validation.php (Validation Rules)
└── partials/
    ├── _header.blade.php
    ├── _tab-navigation.blade.php
    ├── _asset-selection-card.blade.php
    ├── _start-date-card.blade.php
    ├── _service-info-card.blade.php
    ├── _add-service-button.blade.php
    └── tabs/
        ├── _messages-tab.blade.php
        └── _notes-tab.blade.php
```

### Parçalama Detayları:

#### 1. `create.blade.php` (Ana Dosya - ~150 satır)
**İçerik:**
- Component Class (1-73): Mount, State Management
- Main Layout Structure (297-382): Container, includes
- Conditional Tab Rendering

**Satır Hedefi:** 150 satır

---

#### 2. `_actions.php` (Trait - ~120 satır)
**İçerik:**
- `save()` metodu (194-258) + Authorization
- `toggleEditMode()` metodu (280-283) + Authorization
- `cancel()` metodu (271-278)
- `delete()` metodu (285-294) + Authorization
- `addService()` metodu (105-120)
- `removeService()` metodu (122-128)
- `updatedCustomerId()` metodu (131-135)
- `updatedServices()` metodu (150-167)
- `loadServicesForIndex()` metodu (169-179)
- `updateServicePrice()` metodu (181-192)
- `calculateEndDate()` metodu (260-269)

**Satır Hedefi:** 120 satır

---

#### 3. `_validation.php` (Validation Rules - ~30 satır)
**İçerik:**
- Validation rules array (196-202)
- Custom validation messages
- Validation helper methods

**Satır Hedefi:** 30 satır

---

#### 4. `partials/_header.blade.php` (~50 satır)
**İçerik:**
- Back Button (300-305)
- Page Title (307-330)
- Action Buttons (332-357)

**Satır Hedefi:** 50 satır

---

#### 5. `partials/_tab-navigation.blade.php` (~25 satır)
**İçerik:**
- Tab Navigation (360-378)
- Active tab highlighting

**Satır Hedefi:** 25 satır

---

#### 6. `partials/_asset-selection-card.blade.php` (~45 satır)
**İçerik:**
- Asset Selection Card (388-430)
- Customer dropdown
- Asset dropdown

**Satır Hedefi:** 45 satır

---

#### 7. `partials/_start-date-card.blade.php` (~25 satır)
**İçerik:**
- Start Date Card (432-451)
- Date input

**Satır Hedefi:** 25 satır

---

#### 8. `partials/_service-info-card.blade.php` (~110 satır)
**İçerik:**
- Service Info Card (453-555)
- Category dropdown
- Service dropdown
- Status dropdown
- Price input
- Description textarea

**Satır Hedefi:** 110 satır

---

#### 9. `partials/_add-service-button.blade.php` (~10 satır)
**İçerik:**
- Add Service Button (557-564)

**Satır Hedefi:** 10 satır

---

#### 10. `partials/tabs/_messages-tab.blade.php` (~10 satır)
**İçerik:**
- Messages Tab (568-572)
- Empty state

**Satır Hedefi:** 10 satır

---

#### 11. `partials/tabs/_notes-tab.blade.php` (~10 satır)
**İçerik:**
- Notes Tab (575-579)
- Empty state

**Satır Hedefi:** 10 satır

---

### Toplam Satır Kontrolü:
| Dosya | Satır Sayısı | Durum |
|-------|--------------|-------|
| create.blade.php | 150 | ✅ <300 |
| _actions.php | 120 | ✅ <300 |
| _validation.php | 30 | ✅ <300 |
| _header.blade.php | 50 | ✅ <300 |
| _tab-navigation.blade.php | 25 | ✅ <300 |
| _asset-selection-card.blade.php | 45 | ✅ <300 |
| _start-date-card.blade.php | 25 | ✅ <300 |
| _service-info-card.blade.php | 110 | ✅ <300 |
| _add-service-button.blade.php | 10 | ✅ <300 |
| _messages-tab.blade.php | 10 | ✅ <300 |
| _notes-tab.blade.php | 10 | ✅ <300 |
| **TOPLAM** | **585** | ✅ |

---

## 🔐 Authorization Entegrasyonu

Her iki modülde de aşağıdaki yetki kontrolleri eklenecek:

### Customer Create:
```php
// Trait: _actions.php
public function toggleEditMode(): void
{
    if (!auth()->user()->can('customers.edit')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    $this->isViewMode = false;
}

public function delete(): void
{
    if (!auth()->user()->can('customers.delete')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    // ... delete logic
}
```

### Service Create:
```php
// Trait: _actions.php
public function toggleEditMode(): void
{
    if (!auth()->user()->can('services.edit')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    $this->isViewMode = false;
}

public function delete(): void
{
    if (!auth()->user()->can('services.delete')) {
        abort(403, 'Bu işlem için yetkiniz yok.');
    }
    // ... delete logic
}
```

---

## 🔗 N+1 Fix Entegrasyonu

### Customer Create - loadCustomerData():
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

### Service Create - loadServiceData():
```php
$service = Service::with(['customer', 'asset'])->findOrFail($this->serviceId);
```

### Service Create - save() (Bulk Insert):
```php
DB::transaction(function () use ($startDate) {
    $servicesToInsert = [];
    
    foreach ($this->services as $serviceData) {
        $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);
        
        $servicesToInsert[] = [
            'id' => Str::uuid()->toString(),
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
            // ... other fields
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    Service::insert($servicesToInsert);
});
```

---

## 📋 Uygulama Adımları (ADIM 2'de Yapılacak)

### Adım 1: Test Anayasası Kontrolü
- [ ] CustomerCreate.md test senaryoları gözden geçirildi
- [ ] ServiceCreate.md test senaryoları gözden geçirildi

### Adım 2: Trait Dosyaları Oluşturma
- [ ] `customers/_actions.php` oluşturuldu
- [ ] `customers/_validation.php` oluşturuldu
- [ ] `customers/services/_actions.php` oluşturuldu
- [ ] `customers/services/_validation.php` oluşturuldu

### Adım 3: Partial Dosyaları Oluşturma
- [ ] Customer partials oluşturuldu (12 dosya)
- [ ] Service partials oluşturuldu (11 dosya)

### Adım 4: Ana Dosya Refactor
- [ ] `customers/create.blade.php` refactor edildi
- [ ] `customers/services/create.blade.php` refactor edildi

### Adım 5: Authorization & N+1 Fix
- [ ] Authorization kontrolleri eklendi
- [ ] N+1 problemleri çözüldü

### Adım 6: Test Dashboard Güncelleme
- [ ] Test Dashboard'a yeni test senaryoları eklendi
- [ ] Tüm testler çalıştırıldı ve geçti

---

## 🎯 Başarı Kriterleri

✅ Tüm dosyalar 300 satırın altında  
✅ Authorization kontrolleri tüm kritik metodlarda  
✅ N+1 problemleri çözüldü  
✅ Test senaryolarının %100'ü geçti  
✅ Kod tekrarı minimize edildi  
✅ Modüler yapı korundu  

---

**Mimar Notu:** Bu plan ADIM 1'in çıktısıdır. ADIM 2'de bu plan uygulanacak ve kod parçalama işlemi gerçekleştirilecektir. Henüz hiçbir kod değişikliği yapılmadı, sadece mühendislik dökümanı hazırlandı.
