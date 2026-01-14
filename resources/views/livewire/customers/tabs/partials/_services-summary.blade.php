{{--
SECTION: Services Summary & Statistics
Mimarın Notu: Bu bölüm hizmetler sekmesinin üst kısmında yer alan özet kartları ve istatistikleri içerir.
İş Mantığı Şerhi: Service modeli ile konuşur, ReferenceDataService trait'i kullanır.
Mühür Koruması: MaryUI card bileşenleri ve theme-card CSS sınıfları korunmalıdır.
--}}

{{-- Header with Action Button --}}
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-semibold text-skin-heading">Hizmetler</h2>
        <p class="text-sm text-skin-muted">Müşteriye ait hizmetleri görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-4">
        @if(count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="Seçili {{ count($selected) }} hizmeti silmek istediğinize emin misiniz?"
                class="btn-danger-outline">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Seçilileri Sil ({{ count($selected) }})
            </button>
        @endif

        <span class="text-sm text-skin-muted">
            <span class="font-medium" style="color: var(--btn-save-bg);">Aktif</span>
            {{ $services->total() }} hizmet
        </span>
        <x-customer-management.action-button label="Yeni Hizmet" href="{{ route('customers.services.create') }}" />
    </div>
</div>

{{-- Filter Panel --}}
<x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
    <div class="flex flex-wrap items-center gap-3">
        <div class="w-36">
            <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Kategoriler']] + $categoryOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                option-label="display_label" option-value="id" wire:model.live="categoryFilter"
                class="select-sm !bg-white !border-gray-200 text-xs" />
        </div>
        <div class="w-36">
            <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Durumlar']] + $statusOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()" option-label="display_label"
                option-value="id" wire:model.live="statusFilter" class="select-sm !bg-white !border-gray-200 text-xs"
                style="background-color: white !important;" />
        </div>

        <div class="flex-grow max-w-[9rem]">
            <x-mary-input placeholder="Ara..." icon="o-magnifying-glass"
                class="input-sm !bg-white !border-gray-200 text-xs" style="background-color: white !important;"
                wire:model.live.debounce.300ms="search" />
        </div>

        <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
            <x-mary-button label="0-9" wire:click="$set('letter', '0-9')"
                class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-1.5" />
            <x-mary-button label="Tümü" wire:click="$set('letter', '')"
                class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-1.5" />
            <div class="divider divider-horizontal mx-0 h-4"></div>
            @foreach(range('A', 'Z') as $char)
                <x-mary-button :label="$char" wire:click="$set('letter', '{{ $char }}')"
                    class="btn-ghost btn-xs font-medium text-[10px] {{ $letter === $char ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover min-w-[20px] !px-0.5" />
            @endforeach
        </div>
    </div>
</x-mary-card>