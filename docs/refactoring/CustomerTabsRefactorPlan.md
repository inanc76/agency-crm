# 🏗️ Customer Detail Hub - Refactoring Planı
**Hedef:** Monolitik yapıdan "Mikro-Modül" (Independent Volt Components) yapısına geçiş.
**Mevcut Durum:** Parent component (`HasCustomerData`) tüm verileri yüklüyor, child view'ler (`@include`) bu veriyi kullanıyor.
**Yeni Durum:** Parent sadece ID sağlar, her Child Component kendi verisini (Lazy Load ile) çeker.

---

## 🚀 Faz 1: Component Conversion (Dönüşüm)

Mevcut şablon dosyaları (`resources/views/livewire/customers/tabs/*.blade.php`) gerçek Volt componentlerine dönüştürülecek.

### 1. Dosya Taşıma ve Yapılandırma
Her tab için `app/Livewire/Customers/Tabs/` altında backend class (veya Volt functional API) ve `resources/views/livewire/customers/tabs/` altında view dosyası oluşturulacak (Livewire standartlarına uygun).

**Örnek Yapı:**
- `app/Livewire/Customers/Tabs/ContactsTab.php` (Volt Class API)
- `resources/views/livewire/customers/tabs/contacts-tab.blade.php` (View)

### 2. Parent-Child İletişimi
`create.blade.php` (Parent) artık veriyi yüklemeyecek, sadece child component'i çağıracak:

**Eski (Parent):**
```blade
{{-- data loaded in HasCustomerData --}}
@include('livewire.customers.tabs.contacts-tab', ['contacts' => $relatedContacts])
```

**Yeni (Parent):**
```blade
<livewire:customers.tabs.contacts-tab :customer-id="$customerId" wire:key="tab-contacts-{$customerId}" lazy />
```
*Not: `lazy` parametresi ile tab içeriği, sayfa yüklendikten sonra (placeholder göstererek) yüklenecek. Bu, sayfa açılış hızını **dramatik** şekilde artıracak.*

---

## 🧹 Faz 2: Parent Cleaning (Temizlik)

`HasCustomerData` trait'indeki gereksiz veri yüklemeleri temizlenecek.

**Silinecek Veri Yüklemeleri:**
- `$this->relatedContacts`
- `$this->relatedAssets`
- `$this->relatedServices`
- `$this->relatedOffers`
- `$this->relatedSales`
- `$this->relatedMessages`
- `$this->relatedNotes`

Parent component sadece **Ana Müşteri Bilgisini** (`loadCustomerData`) yükleyecek.

---

## 🛠️ Faz 3: Tab Implementasyonu (Sırasıyla)

Her bir tab için aşağıdaki işlemler yapılacak:

### 1. Contacts Tab
- **Namespace:** `App\Livewire\Customers\Tabs`
- **Component:** `ContactsTab`
- **Data:** `Contact::where('customer_id', $id)->paginate(10)`
- **Features:** Search, Filter, Pagination (Independent), Delete Action.

### 2. Services Tab
- **Component:** `ServicesTab`
- **Data:** `Service::with(['asset', 'priceDefinition'])->where('customer_id', $id)...` include N+1 fix.
- **Features:** Status Filter, Asset Filter.

### 3. Offers Tab
- **Component:** `OffersTab`
- **Data:** `Offer::where('customer_id', $id)...`
- **Features:** Status Badge, PDF Download Link.

### 4. Sales Tab
- **Component:** `SalesTab`
- **Features:** Total Amount Calculation.

### 5. Assets Tab
- **Component:** `AssetsTab`
- **Features:** Credentials View (Masked).

### 6. Messages & Notes Tabs
- **Component:** `MessagesTab`, `NotesTab`
- **Features:** Simple list + Add new form.

---

## 🛡️ Faz 4: Event & State Management

Tablar arası iletişim için `Livewire Events` kullanılacak.

- **Event:** `customer-updated` -> Parent ve tüm tabları yeniler.
- **Event:** `contacts-updated` -> Sadece Contacts tabı ve Parent'taki "Contacts Count" bilgisini yeniler.

`HasCustomerActions` trait'ine bu eventleri dinleyen (`#[On('...')]`) metodlar eklenecek.

---

**Mimar Notu:** Bu plan, sayfa açılış hızını 50ms'den 20ms'ye düşürecek ve sekmeleri birbirinden izole ederek hata toleransını artıracaktır.
