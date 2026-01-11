{{--
@component: _tabs.blade.php
@section: Tab Navigasyonu
@description: Sayfa içi sekmeler arası geçişi sağlar.
@params: $activeTab (string: 'info'|'messages'|'notes'|'downloads'), $isViewMode (bool)
@events: $set('activeTab', 'info'), $set('activeTab', 'messages'), $set('activeTab', 'notes'), $set('activeTab',
'downloads')
--}}
@if($isViewMode)
    <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
        <button wire:click="$set('activeTab', 'info')"
            class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
            Teklif Bilgileri
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
        <button wire:click="$set('activeTab', 'downloads')"
            class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            style="{{ $activeTab === 'downloads' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
            İndirmeler (0)
        </button>
    </div>
@else
    <div class="mb-8"></div>
@endif