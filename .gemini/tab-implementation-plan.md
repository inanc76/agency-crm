# Tab Yapısı Ekleme Implementation Plan

## Genel Yaklaşım
Contact detay sayfasındaki tab yapısını örnek alarak, Assets, Services ve Offers sayfalarına benzer tab yapıları eklenecek.

## Ortak Değişiklikler (Her 3 sayfa için)

### 1. Backend (PHP) Değişiklikleri
Her dosyanın PHP bölümüne eklenecekler:
```php
// State Management
public bool $isViewMode = false;
public string $activeTab = 'info'; // veya ilgili ilk tab adı
```

### 2. Tab Navigation HTML Yapısı
`@if($isViewMode)` bloğundan sonra, form alanlarından önce eklenecek:
```blade
{{-- Tab Navigation --}}
@if($isViewMode)
    <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
        <button wire:click="$set('activeTab', 'info')" 
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
            [TAB_ADI]
        </button>
        <!-- Diğer tablar -->
    </div>
@else
    <div class="mb-8"></div>
@endif
```

### 3. Tab Content Wrapper
Mevcut form içeriğini `@if($activeTab === 'info')` ile sarmalama:
```blade
@if($activeTab === 'info')
    <div class="space-y-6">
        <!-- Mevcut form içeriği -->
    </div>
@endif

@if($activeTab === 'notes')
    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
        <div class="font-medium">Henüz not bulunmuyor</div>
    </div>
@endif
```

---

## Sayfa 1: Assets Detail (/dashboard/customers/assets/{asset})
**Dosya:** `/Users/volkaninanc/agency-crm/resources/views/livewire/customers/assets/create.blade.php`

### Tablar:
1. **Varlık Bilgileri** (activeTab: 'info')
2. **Notlar (0)** (activeTab: 'notes')

### Değişiklikler:
1. PHP bölümüne `public string $activeTab = 'info';` ekle
2. Header'dan sonra tab navigation ekle:
   - Tab 1: "Varlık Bilgileri"
   - Tab 2: "Notlar (0)"
3. Mevcut form içeriğini `@if($activeTab === 'info')` ile sarmala
4. Notes tab için placeholder ekle

---

## Sayfa 2: Services Detail (/dashboard/customers/services/{service})
**Dosya:** `/Users/volkaninanc/agency-crm/resources/views/livewire/customers/services/create.blade.php`

### Tablar:
1. **Hizmet Bilgileri** (activeTab: 'info')
2. **Notlar (0)** (activeTab: 'notes')

### Değişiklikler:
1. PHP bölümüne `public string $activeTab = 'info';` ekle
2. Header'dan sonra tab navigation ekle:
   - Tab 1: "Hizmet Bilgileri"
   - Tab 2: "Notlar (0)"
3. Mevcut form içeriğini `@if($activeTab === 'info')` ile sarmala
4. Notes tab için placeholder ekle

---

## Sayfa 3: Offers Detail (/dashboard/customers/offers/{offer})
**Dosya:** `/Users/volkaninanc/agency-crm/resources/views/livewire/customers/offers/create.blade.php`

### Tablar:
1. **Teklif Bilgileri** (activeTab: 'info')
2. **Mesajlar (0)** (activeTab: 'messages')
3. **Notlar (0)** (activeTab: 'notes')
4. **İndirmeler (0)** (activeTab: 'downloads')

### Değişiklikler:
1. PHP bölümüne `public string $activeTab = 'info';` ekle
2. Header'dan sonra tab navigation ekle:
   - Tab 1: "Teklif Bilgileri"
   - Tab 2: "Mesajlar (0)"
   - Tab 3: "Notlar (0)"
   - Tab 4: "İndirmeler (0)"
3. Mevcut form içeriğini `@if($activeTab === 'info')` ile sarmala
4. Diğer tablar için placeholder'lar ekle:
   - Messages: chat-bubble-left-right icon
   - Notes: document-text icon
   - Downloads: arrow-down-tray icon

---

## Uygulama Sırası
1. Assets sayfası (en basit - 2 tab)
2. Services sayfası (basit - 2 tab)
3. Offers sayfası (kompleks - 4 tab)

## Notlar
- Tab yapısı sadece `$isViewMode = true` olduğunda görünür
- Yeni kayıt oluştururken tablar gösterilmez
- Her tab için placeholder içerik eklenecek (ileride doldurulacak)
- Tab sayıları şu an sabit (0) - ileride dinamik hale getirilebilir
