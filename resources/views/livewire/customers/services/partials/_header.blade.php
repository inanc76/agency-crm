{{--
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🎯 IDENTITY CARD
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Partial: _header.blade.php
Purpose: Service Create/Edit Page Header with Action Buttons
Layer: UI Component (Blade Partial)
Dependencies: Livewire Component State
Created: 2026-01-10
Refactored From: customers/services/create.blade.php (lines 300-358)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
--}}

{{-- Back Button --}}
<a href="/dashboard/customers?tab=services"
    class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
    <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
    <span class="text-sm font-medium">Hizmet Listesi</span>
</a>

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
            @if($isViewMode)
                {{ $services[0]['service_name'] ?? 'Hizmet' }}
            @elseif($serviceId)
                Düzenle: {{ $services[0]['service_name'] ?? 'Hizmet' }}
            @else
                Yeni Hizmet Ekle
            @endif
        </h1>
        <div class="flex items-center gap-2 mt-1">
            @if($isViewMode)
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Hizmet</span>
                <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $serviceId }}</span>
            @else
                <p class="text-sm opacity-60 text-skin-base">
                    {{ count($services) }} hizmet kaydı oluşturun
                </p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        @if($isViewMode)
            <button type="button" wire:click="delete" wire:confirm="Bu hizmeti silmek istediğinize emin misiniz?"
                wire:key="btn-delete-{{ $serviceId }}" class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Sil
            </button>
            <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $serviceId }}"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $serviceId ?: 'new' }}"
                class="theme-btn-cancel">
                İptal
            </button>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                wire:key="btn-save-{{ $serviceId ?: 'new' }}" class="theme-btn-save">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($serviceId) Güncelle @else Kaydet @endif
            </button>
        @endif
    </div>
</div>