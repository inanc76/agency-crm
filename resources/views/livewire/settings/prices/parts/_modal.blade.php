<x-mary-modal wire:model="showModal" title="{{ $selectedId ? 'Fiyat Tanımı Düzenle' : 'Yeni Fiyat Tanımı Ekle' }}"
    class="backdrop-blur" box-class="!max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <x-mary-input label="Hizmet Adı *" placeholder="Örn: Premium Domain, SSL Sertifikası" wire:model="name" />
        </div>

        <x-mary-select label="Hizmet Kategorisi *" placeholder="Kategori Seçin" :options="$categories"
            option-value="key" option-label="display_label" wire:model="category" />

        <x-mary-select label="Hizmet Süresi *" placeholder="Süre Seçin" :options="$durations" option-value="key"
            option-label="display_label" wire:model="duration" />

        <x-mary-input label="Fiyat *" type="number" wire:model="price" />

        <x-mary-select label="Para Birimi *" placeholder="Para Birimi Seçin" :options="$currencies" option-value="key"
            option-label="display_label" wire:model="currency" />

        <div class="md:col-span-2">
            <x-mary-textarea label="Açıklama" placeholder="Hizmet detaylarını açıklayın..." rows="4"
                wire:model="description" />
        </div>

        <div class="md:col-span-2 flex items-center gap-3">
            <span class="text-sm font-medium {{ !$is_active ? 'text-rose-600' : 'text-slate-400' }}">Pasif</span>
            <x-mary-toggle wire:model="is_active" class="toggle-success" />
            <span class="text-sm font-medium {{ $is_active ? 'text-emerald-600' : 'text-slate-400' }}">Aktif</span>
        </div>
    </div>

    <x-slot:actions>
        <x-mary-button label="İptal" class="btn-ghost" wire:click="$set('showModal', false)" />
        <button type="button" class="theme-btn-save" wire:click="save">
            Kaydet
        </button>
    </x-slot:actions>
</x-mary-modal>