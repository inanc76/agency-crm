<div class="theme-card p-6 shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-mary-input label="Arama" placeholder="Hizmet adı veya açıklama..." icon="o-magnifying-glass"
            wire:model.live.debounce.300ms="search" />

        <x-mary-select label="Kategori" placeholder="Tüm Kategoriler" :options="$categories" option-value="key"
            option-label="display_label" wire:model.live="filterCategory" />

        <x-mary-select label="Hizmet Süresi" placeholder="Tüm Süreler" :options="$durations" option-value="key"
            option-label="display_label" wire:model.live="filterDuration" />

        <div class="flex items-end">
            <x-mary-button label="Filtreleri Temizle" icon="o-x-mark" class="btn-ghost" wire:click="clearFilters" />
        </div>
    </div>
</div>