<?php
/**
 * 🛡️ CONTACT FORM MODAL (ORCHESTRA SHELL)
 * ---------------------------------------------------------
 * ARCHITECTURE: MVVM (Model-View-ViewModel) through Livewire Volt.
 * LOGIC HOOK: App\Livewire\Customers\Contacts\Traits\HasContactActions.
 * UI DESIGN: Ultra-Atomic Structure (Divided into parts/contact/).
 * SECURITY: Restricted to authorized users via web.php 'can' middleware.
 * ---------------------------------------------------------
 */
use App\Livewire\Customers\Contacts\Traits\HasContactActions;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use HasContactActions;
    use Toast;
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=contacts"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Kişi Listesi</span>
        </a>

        {{-- Header Section --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name }}
                    @elseif($contactId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Kişi Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Kişi</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $contactId }}</span>
                    @else
                        <p class="text-sm opacity-60">Yeni kişi bilgilerini girin</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu kişiyi silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $contactId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $contactId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $contactId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $contactId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($contactId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Kişi Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout: Full Width --}}
        <div>
            {{-- Content --}}
            <div>
                @if($activeTab === 'info')
                    <div class="space-y-6">
                        @include('livewire.modals.parts.contact._personal-info', [
                            'name' => $name,
                            'customer_id' => $customer_id,
                            'status' => $status,
                            'gender' => $gender,
                            'position' => $position,
                            'isViewMode' => $isViewMode,
                            'customers' => $customers,
                            'genders' => $genders
                        ])

                        @include('livewire.modals.parts.contact._communication', [
                            'emails' => $emails,
                            'phones' => $phones,
                            'isViewMode' => $isViewMode
                        ])

                        @include('livewire.modals.parts.contact._social-profiles', [
                            'social_profiles' => $social_profiles,
                            'isViewMode' => $isViewMode
                        ])

                        @include('livewire.modals.parts.contact._other-details', [
                            'birth_date' => $birth_date,
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
                    @if($contactId)
                        @livewire('shared.notes-tab', [
                            'entityType' => 'CONTACT',
                            'entityId' => $contactId
                        ], key('notes-tab-' . $contactId))
                    @else
                        <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                            <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                            <div class="font-medium">Kişiyi kaydedin, ardından not ekleyebilirsiniz</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
