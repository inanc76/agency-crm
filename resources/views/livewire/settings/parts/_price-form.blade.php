{{--
📝 PRICE FORM PARTIAL
---------------------------------------------------------
MİMARIN NOTU: Fiyat tanımlarının oluşturulma ve güncellenme formudur.
Buradaki veri girişi doğrudan teklif ve hizmet modüllerini besler.

BAĞLANTILAR:
- wire:model="name": Hizmet adı (Unique veya açıklayıcı olmalı)
- wire:model="price": Fiyat değeri (Hesaplamaların temeli)
- wire:model="currency": Para birimi (TRY, USD, EUR)
- $categories, $durations, $currencies: Dropdown kaynakları.

VALIDASYON UYARISI:
- price: numeric|min:0 (Negatif fiyat girilemez)
- currency: required (Para birimi olmadan işlem yapılamaz)
---------------------------------------------------------
--}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <x-mary-input label="Hizmet Adı *" placeholder="Örn: Premium Domain, SSL Sertifikası" wire:model="name" />
    </div>

    <x-mary-select label="Hizmet Kategorisi *" placeholder="Kategori Seçin" :options="$categories" option-value="key"
        option-label="display_label" wire:model="category" />

    <x-mary-select label="Hizmet Süresi *" placeholder="Süre Seçin" :options="$durations" option-value="key"
        option-label="display_label" wire:model="duration" />

    <x-mary-input label="Fiyat *" type="number" step="0.01" wire:model="price" />

    <x-mary-select label="Para Birimi *" placeholder="Para Birimi Seçin" :options="$currencies" option-value="key"
        option-label="display_label" wire:model="currency" />

    <div class="md:col-span-2">
        <x-mary-textarea label="Açıklama" placeholder="Hizmet detaylarını açıklayın..." rows="4"
            wire:model="description" />
    </div>

    <div class="md:col-span-2 flex items-center gap-3">
        <span
            class="text-sm font-medium {{ !$is_active ? 'text-[var(--color-danger)]' : 'text-[var(--color-text-muted)]' }}">Pasif</span>
        <x-mary-toggle wire:model="is_active" class="toggle-success" />
        <span
            class="text-sm font-medium {{ $is_active ? 'text-[var(--color-success)]' : 'text-[var(--color-text-muted)]' }}">Aktif</span>
    </div>
</div>