<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

use Livewire\Attributes\Url;

new
    #[Layout('components.layouts.app', ['title' => 'Tema Ayarları'])]
    class extends Component {
    use Toast;
    use WithFileUploads;



    #[Url(as: 'tab')]
    public string $activeTab = 'style-guide';

    // Branding moved to Header Component

    // Design Settings moved to Atomic Components

    // Sidebar & Header moved to Header Component

    // Dashboard Settings
    public string $dashboard_card_bg_color = '#eff4ff';
    public string $dashboard_card_text_color = '#475569';
    public string $dashboard_stats_1_color = '#3b82f6';
    public string $dashboard_stats_2_color = '#14b8a6';
    public string $dashboard_stats_3_color = '#f59e0b';

    // User Menu moved to Header Component

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();

        if ($setting) {
            // Branding data loading moved to Header Component

            // Design settings are now loaded in atomic components

            // Sidebar & Header data loading moved to Header Component

            $this->dashboard_card_bg_color = $setting->dashboard_card_bg_color ?? '#eff4ff';
            $this->dashboard_card_text_color = $setting->dashboard_card_text_color ?? '#475569';
            $this->dashboard_stats_1_color = $setting->dashboard_stats_1_color ?? '#3b82f6';
            $this->dashboard_stats_2_color = $setting->dashboard_stats_2_color ?? '#14b8a6';
            $this->dashboard_stats_3_color = $setting->dashboard_stats_3_color ?? '#f59e0b';

            // User Menu data loading moved to Header Component
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            // Branding validation moved to Header Component

            // Validation for design settings handled in atomic components

            // Sidebar & Header validation moved to Header Component

            'dashboard_card_bg_color' => 'nullable|string',
            'dashboard_card_text_color' => 'nullable|string',
            'dashboard_stats_1_color' => 'nullable|string',
            'dashboard_stats_2_color' => 'nullable|string',
            'dashboard_stats_3_color' => 'nullable|string',

            // User Menu validation moved to Header Component
        ]);

        $repository->saveSettings($data);

        Cache::forget('theme_settings');

        $this->success('Ayarlar Kaydedildi', 'Tema ayarları başarıyla güncellendi. Tasarım tüm sisteme uygulandı.');
    }

    public function resetToDefaults(): void
    {
        $repository = app(PanelSettingRepository::class);
        $repository->resetToDefaults();

        $this->mount($repository);
        $this->success('Varsayılana Döndürüldü', 'Tema ayarları varsayılan değerlere sıfırlandı.');
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto pb-20">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Tema & Tasarım Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Uygulamanın görünümünü özelleştirin ve canlı tasarım rehberini
                inceleyin.</p>
        </div>

        <x-mary-tabs wire:model="activeTab" class="bg-transparent">
            {{-- Tasarım Rehberi (Default First) --}}
            <x-mary-tab name="style-guide" icon="o-swatch">
                <x-slot:label>
                    <span class="font-semibold">Tasarım Rehberi</span>
                </x-slot:label>

                <livewire:settings.style-guide lazy />
            </x-mary-tab>

            {{-- Tema Ayarları --}}
            <x-mary-tab name="theme" icon="o-adjustments-horizontal">
                <x-slot:label>
                    <span class="font-semibold">Tema Ayarları</span>
                </x-slot:label>
                <div class="mt-6 space-y-6">
                    {{-- Header Appearance Card (Atomic & Lazy) --}}
                    <livewire:settings.theme.header lazy />

                    {{-- Basic Design Card (Atomic & Lazy) --}}
                    <div class="space-y-6">
                        <livewire:settings.theme.typography lazy />
                        <livewire:settings.theme.inputs lazy />
                        <livewire:settings.theme.buttons lazy />
                        <livewire:settings.theme.cards lazy />
                        <livewire:settings.theme.tables lazy />
                    </div>
                </div>
            </x-mary-tab>


        </x-mary-tabs>
    </div>
</div>