{{-- Back Button --}}
<a href="/dashboard/customers?tab=customers"
    class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
    <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
    <span class="text-sm font-medium">Müşteri Listesi</span>
</a>

{{-- Header with Action Buttons --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
            @if($isViewMode)
                {{ $name ?: 'Müşteri Bilgileri' }}
            @elseif($customerId)
                Düzenle: {{ $name }}
            @else
                Yeni Müşteri Ekle
            @endif
        </h1>
        <div class="flex items-center gap-2 mt-1">
            @if($isViewMode)
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Müşteri</span>
                <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $customerId }}</span>
            @else
                <p class="text-sm opacity-60 text-skin-base">
                    Yeni müşteri bilgilerini girin
                </p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($isViewMode)
            {{-- View Mode Actions --}}
            <button type="button" wire:click="delete" wire:confirm="Bu müşteriyi silmek istediğinize emin misiniz?"
                wire:key="btn-delete-{{ $customerId }}" class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Sil
            </button>
            <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $customerId }}"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            {{-- Edit Mode Actions --}}
            <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $customerId ?: 'new' }}"
                class="theme-btn-cancel">
                İptal
            </button>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                wire:key="btn-save-{{ $customerId ?: 'new' }}" class="theme-btn-save">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($customerId) Güncelle @else Kaydet @endif
            </button>
        @endif
    </div>
</div>

{{-- Tab Navigation --}}
@if($isViewMode)
    <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
        <button wire:click="$set('activeTab', 'info')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'info' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Müşteri Bilgileri
        </button>
        <button wire:click="$set('activeTab', 'contacts')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'contacts' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Kişiler ({{ $counts['contacts'] }})
        </button>
        <button wire:click="$set('activeTab', 'assets')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'assets' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Varlıklar ({{ $counts['assets'] }})
        </button>
        <button wire:click="$set('activeTab', 'services')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'services' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Hizmetler ({{ $counts['services'] }})
        </button>
        <button wire:click="$set('activeTab', 'offers')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'offers' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Teklifler ({{ $counts['offers'] }})
        </button>
        <button wire:click="$set('activeTab', 'sales')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'sales' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Satışlar ({{ $counts['sales'] }})
        </button>
        <button wire:click="$set('activeTab', 'messages')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'messages' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Mesajlar ({{ $counts['messages'] }})
        </button>
        <button wire:click="$set('activeTab', 'notes')"
            class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'notes' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
            Notlar ({{ $counts['notes'] }})
        </button>
    </div>
@else
    <div class="mb-8"></div>
@endif