{{--
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🎯 IDENTITY CARD
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Partial: _tab-navigation.blade.php
Purpose: Customer View Mode Tab Navigation
Layer: UI Component (Blade Partial)
Dependencies: Livewire Component State ($activeTab, $counts)
Created: 2026-01-10
Refactored From: customers/create.blade.php (lines 504-542)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
--}}

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