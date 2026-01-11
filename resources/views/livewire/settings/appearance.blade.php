<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Görünüm Ayarları'])]
    class extends Component {
    //
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-skin-heading">Görünüm Ayarları</h1>
            <p class="text-sm text-skin-muted mt-1">Hesabınız için görünüm tercihlerinizi güncelleyin.</p>
        </div>

        {{-- Main Card --}}
        <div class="theme-card p-6 shadow-sm">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-skin-light">
                <h2 class="text-sm font-medium text-skin-base">Tema Seçimi</h2>
            </div>

            {{-- Theme Selection --}}
            <div class="grid grid-cols-1 gap-6">
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun">{{ __('Açık Tema') }}</flux:radio>
                    <flux:radio value="dark" icon="moon">{{ __('Koyu Tema') }}</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">{{ __('Sistem') }}</flux:radio>
                </flux:radio.group>
                
                <div class="text-sm text-skin-muted">
                    <p>Sistem seçeneği, cihazınızın tema tercihini otomatik olarak takip eder.</p>
                </div>
            </div>
        </div>
    </div>
</div>
