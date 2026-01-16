# 📝 Notlar Modülü - Entegrasyon Kılavuzu

## 🎯 Genel Bakış

Notlar modülü, sistemdeki tüm varlıklara (Project, Task, Customer, Contact, Asset, Service, Offer) not ekleme imkanı sağlar. Polymorphic yapıda tasarlanmıştır ve görünürlük kontrolü içerir.

## 📊 Veritabanı Yapısı

### `notes` Tablosu
- `id` (UUID): Primary key
- `content` (TEXT): Not içeriği
- `author_id` (UUID): Notu yazan kullanıcı (FK: users)
- `entity_type` (STRING): Varlık tipi (CUSTOMER, PROJECT, PROJECT_TASK, CONTACT, ASSET, SERVICE, OFFER)
- `entity_id` (UUID): Varlık ID'si
- `created_at`, `updated_at`

### `note_user` Pivot Tablosu
- `note_id` (UUID): Not ID'si (FK: notes)
- `user_id` (UUID): Kullanıcı ID'si (FK: users)
- Composite Primary Key: `[note_id, user_id]`

## 🔧 Kullanım

### 1. Entity Detay Sayfalarına Entegrasyon

Her entity'nin detay sayfasında "Notlar" tab'ı ekleyin:

#### Örnek: Proje Detay Sayfası

```php
// resources/views/livewire/projects/show.blade.php

<x-mary-tabs wire:model="activeTab">
    <x-mary-tab name="overview" label="Genel Bakış" icon="o-home">
        @livewire('projects.tabs.overview-tab', ['projectId' => $project->id])
    </x-mary-tab>
    
    <x-mary-tab name="notes" label="Notlar ({{ $project->notes()->count() }})" icon="o-document-text">
        @livewire('shared.notes-tab', [
            'entityType' => 'PROJECT',
            'entityId' => $project->id
        ])
    </x-mary-tab>
</x-mary-tabs>
```

#### Örnek: Müşteri Detay Sayfası

```php
// resources/views/livewire/customers/show.blade.php

<x-mary-tab name="notes" label="Notlar ({{ $customer->notes()->count() }})" icon="o-document-text">
    @livewire('shared.notes-tab', [
        'entityType' => 'CUSTOMER',
        'entityId' => $customer->id
    ])
</x-mary-tab>
```

### 2. Entity Type Değerleri

Her entity için kullanılacak `entity_type` değerleri:

| Entity | entity_type |
|--------|-------------|
| Proje | `PROJECT` |
| Görev | `PROJECT_TASK` |
| Müşteri | `CUSTOMER` |
| Kişi | `CONTACT` |
| Varlık | `ASSET` |
| Hizmet | `SERVICE` |
| Teklif | `OFFER` |

### 3. Model İlişkileri

Tüm entity modellerine `notes()` ilişkisi eklenmiştir:

```php
/**
 * Entity'ye ait notlar (Polymorphic)
 */
public function notes()
{
    return $this->hasMany(Note::class, 'entity_id')
        ->where('entity_type', 'CUSTOMER') // Entity tipine göre değişir
        ->orderBy('created_at', 'desc');
}
```

## 🎨 UI Bileşenleri

### Not Kartı Özellikleri
- ✅ Yazar bilgisi (avatar + isim)
- ✅ Oluşturulma tarihi (human-readable)
- ✅ Düzenleme tarihi göstergesi
- ✅ Not içeriği (whitespace-pre-wrap)
- ✅ Görünürlük bilgisi (kaç kişi görebilir)
- ✅ Düzenle/Sil butonları (sadece yazar için)

### Not Ekleme Modalı
- ✅ Geniş metin alanı (max 10.000 karakter)
- ✅ Karakter sayacı
- ✅ Kullanıcı seçim listesi (checkbox'lar)
- ✅ Avatar'lı kullanıcı kartları
- ✅ Seçili kullanıcı sayısı göstergesi
- ✅ Loading state

## 🔐 Yetkilendirme

### Görünürlük Kuralları
1. **Yazar**: Notu yazan kullanıcı her zaman görebilir
2. **Seçili Kullanıcılar**: `note_user` pivot tablosunda tanımlı kullanıcılar görebilir
3. **Diğerleri**: Notu göremez

### İşlem Yetkileri
- **Oluşturma**: Tüm kullanıcılar not ekleyebilir
- **Düzenleme**: Sadece not yazarı düzenleyebilir
- **Silme**: Sadece not yazarı silebilir
- **Görüntüleme**: Yazar + seçili kullanıcılar

## 📝 Örnek Kullanım Senaryoları

### Senaryo 1: Proje Notları
```
Proje Yöneticisi → Proje Detay → Notlar Tab → Not Ekle
- İçerik: "Müşteri toplantısında yeni özellik talep edildi"
- Görünürlük: Proje ekibi üyeleri (3 kişi)
```

### Senaryo 2: Müşteri Notları
```
Satış Temsilcisi → Müşteri Detay → Notlar Tab → Not Ekle
- İçerik: "Müşteri fiyat konusunda hassas, indirim bekliyor"
- Görünürlük: Satış ekibi + Yönetici (5 kişi)
```

### Senaryo 3: Görev Notları
```
Geliştirici → Görev Detay → Notlar Tab → Not Ekle
- İçerik: "API entegrasyonu için test ortamı gerekli"
- Görünürlük: Teknik ekip (4 kişi)
```

## 🚀 Kurulum Adımları

### 1. Migration'ı Çalıştırın
```bash
php artisan migrate --path=database/migrations/2024_01_01_000017_create_note_user_table.php
```

### 2. Model İlişkilerini Kontrol Edin
Tüm entity modellerinde `notes()` ilişkisinin olduğundan emin olun.

### 3. Tab Entegrasyonu
Her entity'nin detay sayfasına notes tab'ını ekleyin (yukarıdaki örneklere bakın).

### 4. Test Edin
```bash
# Not oluşturma testi
php artisan tinker
>>> $user = User::first();
>>> $customer = Customer::first();
>>> $note = Note::create([
...     'content' => 'Test notu',
...     'author_id' => $user->id,
...     'entity_type' => 'CUSTOMER',
...     'entity_id' => $customer->id,
... ]);
>>> $note->visibleTo()->attach([$user->id]);
>>> $customer->notes()->count(); // 1 olmalı
```

## 🎯 URL Yapısı

Notlar tab'ına erişim URL'leri:

1. **Proje**: `/dashboard/projects/{projectId}?tab=notes`
2. **Görev**: `/dashboard/projects/tasks/{taskId}?tab=notes`
3. **Müşteri**: `/dashboard/customers/{customerId}?tab=notes`
4. **Kişi**: `/dashboard/customers/contacts/{contactId}?tab=notes`
5. **Varlık**: `/dashboard/customers/assets/{assetId}?tab=notes`
6. **Hizmet**: `/dashboard/customers/services/{serviceId}?tab=notes`
7. **Teklif**: `/dashboard/customers/offers/{offerId}?tab=notes`

## 🔍 Özellikler

### ✅ Tamamlanan
- [x] Polymorphic Note modeli
- [x] Görünürlük kontrolü (note_user pivot)
- [x] HasNoteActions trait
- [x] Shared notes-tab component
- [x] Not ekleme/düzenleme modalı
- [x] Not listesi (kartlar)
- [x] Yazar kontrolü (düzenle/sil)
- [x] Empty state
- [x] Loading states
- [x] Validation
- [x] Model ilişkileri (7 entity)

### 🚧 Gelecek Geliştirmeler
- [ ] Not arama/filtreleme
- [ ] Not etiketleri (tags)
- [ ] Mention (@kullanıcı) özelliği
- [ ] Not bildirimleri
- [ ] Not export (PDF/Excel)
- [ ] Not şablonları
- [ ] Zengin metin editörü (rich text)
- [ ] Dosya ekleme (attachments)

## 📚 Dosya Yapısı

```
app/
├── Models/
│   └── Note.php                                    # Note modeli
├── Livewire/
│   └── Traits/
│       └── HasNoteActions.php                      # Not CRUD trait
database/
└── migrations/
    ├── 2024_01_01_000016_create_notes_table.php   # Notes tablosu
    └── 2024_01_01_000017_create_note_user_table.php # Pivot tablo
resources/
└── views/
    └── livewire/
        └── shared/
            ├── notes-tab.blade.php                 # Ana tab component
            └── notes/
                └── partials/
                    ├── _notes-actions.blade.php    # Aksiyon bar
                    ├── _notes-list.blade.php       # Not listesi
                    └── _modal-note.blade.php       # Not modalı
```

## 🎨 Tema Uyumluluğu

Notlar modülü, projenin mevcut tema sistemini kullanır:
- `text-skin-base`, `text-skin-muted`
- `border-skin-light`
- `theme-btn-primary`, `theme-btn-cancel`, `theme-btn-save`
- `hover:bg-[var(--list-card-hover-bg)]`

## 🐛 Sorun Giderme

### Not görünmüyor
- `loadNotes()` metodunun çağrıldığından emin olun
- `entity_type` ve `entity_id` değerlerinin doğru olduğunu kontrol edin
- Kullanıcının görünürlük listesinde olduğunu kontrol edin

### Modal açılmıyor
- `showNoteModal` property'sinin tanımlı olduğundan emin olun
- `HasNoteActions` trait'inin kullanıldığından emin olun

### Validation hatası
- `noteContent` max 10.000 karakter
- `noteVisibleTo` en az 1 kullanıcı seçilmeli

## 📞 Destek

Sorularınız için:
- Dokümantasyon: `docs/notes-module-integration.md`
- Kod örnekleri: `resources/views/livewire/shared/notes-tab.blade.php`
- Trait: `app/Livewire/Traits/HasNoteActions.php`
