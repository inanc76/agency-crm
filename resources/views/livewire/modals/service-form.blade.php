<?php
/**
 * 🛡️ SERVICE FORM MODAL (ORCHESTRA SHELL)
 * ---------------------------------------------------------
 * ARCHITECTURE: MVVM (Model-View-ViewModel) through Livewire Volt.
 * LOGIC HOOK: App\Livewire\Customers\Services\Traits\HasServiceActions.
 * UI DESIGN: Two-Part Balanced Architecture (parts/service/).
 * SECURITY: Restricted to authorized users via web.php 'can' middleware.
 * ---------------------------------------------------------
 */

use Livewire\Volt\Component;
use App\Livewire\Customers\Services\Traits\HasServiceActions;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use HasServiceActions;
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=services"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Hizmet Listesi</span>
        </a>

        {{-- Header Section --}}
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
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Hizmet</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $serviceId }}</span>
                    @else
                        <p class="text-sm opacity-60">
                            {{ count($services) }} hizmet kaydı oluşturun
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu hizmeti silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $serviceId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
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

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Hizmet Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5">
                @if($activeTab === 'info')
                    @include('livewire.modals.parts.service._service-core', [
                        'customer_id' => $customer_id,
                        'asset_id' => $asset_id,
                        'start_date' => $start_date,
                        'isViewMode' => $isViewMode,
                        'customers' => $customers,
                        'assets' => $assets
                    ])

                    <div class="mt-6">
                        @include('livewire.modals.parts.service._service-pricing-logic', [
                            'services' => $services,
                            'categories' => $categories,
                            'isViewMode' => $isViewMode
                        ])
                    </div>
                @endif

                @if($activeTab === 'messages')
                    <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                        <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz mesaj bulunmuyor</div>
                    </div>
                @endif

                @if($activeTab === 'notes')
                    <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz not bulunmuyor</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                <div class="theme-card p-6 shadow-sm text-center">
                    <h3 class="text-sm font-bold text-skin-heading mb-4">Hizmet Görseli</h3>
                    <div class="w-32 h-32 mx-auto border-2 border-dashed border-[var(--card-border)] rounded-lg flex items-center justify-center mb-4 bg-[var(--card-bg)] overflow-hidden">
                        @php
                            $svcName = $services[0]['service_name'] ?? 'H';
                            $initials = mb_substr($svcName, 0, 1) ?: 'H';
                        @endphp
                        <div class="w-full h-full flex items-center justify-center bg-[var(--dropdown-hover-bg)] text-[var(--icon-muted)] font-bold text-5xl uppercase">
                            {{ $initials }}
                        </div>
                    </div>
                    <div class="text-[10px] text-[var(--color-text-muted)]">PNG, JPG, GIF (Max 5MB)</div>
                </div>
            </div>
        </div>
    </div>
</div>