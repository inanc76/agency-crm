<?php

use Livewire\Volt\Component;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;

    public string $font_family = 'Inter';
    public string $page_bg_color = '#f8fafc';
    public string $base_text_color = '#475569';
    public string $heading_color = '#0f172a';
    public int $heading_font_size = 18;

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->font_family = $setting->font_family ?? 'Inter';
            $this->page_bg_color = $setting->page_bg_color ?? '#f8fafc';
            $this->base_text_color = $setting->base_text_color ?? '#475569';
            $this->heading_color = $setting->heading_color ?? '#0f172a';
            $this->heading_font_size = $setting->heading_font_size ?? 18;
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'font_family' => 'required|string',
            'page_bg_color' => 'nullable|string',
            'base_text_color' => 'required|string',
            'heading_color' => 'nullable|string',
            'heading_font_size' => 'required|integer|min:8|max:72',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Tipografi Ayarları Kaydedildi');
    }
}; ?>

<div
    class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-language" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Global Tipografi</span>
        </div>
        <x-mary-button label="Kaydet" icon="o-check" class="btn-sm text-white"
            style="background-color: var(--btn-save-bg); border-color: var(--btn-save-border);" wire:click="save"
            spinner="save" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-3">
            <x-mary-input label="Font Family" wire:model="font_family"
                hint="Sistemin tamamında kullanılacak ana font (Örn: Inter, Geist, Plus Jakarta Sans)" />
        </div>
        <div>
            <label class="block text-sm font-medium text-skin-base mb-2">Sayfa Arka Plan Rengi</label>
            <div class="flex items-center gap-2">
                <input type="color" wire:model.live="page_bg_color"
                    class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                <x-mary-input wire:model.live="page_bg_color" placeholder="#f8fafc" class="flex-1" />
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-skin-base mb-2">Base Text Color</label>
            <div class="flex items-center gap-2">
                <input type="color" wire:model.live="base_text_color"
                    class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                <x-mary-input wire:model.live="base_text_color" placeholder="#475569" class="flex-1" />
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-skin-base mb-2">Heading Color</label>
            <div class="flex items-center gap-2">
                <input type="color" wire:model.live="heading_color"
                    class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                <x-mary-input wire:model.live="heading_color" placeholder="#0f172a" class="flex-1" />
            </div>
        </div>
        <div>
            <x-mary-input label="Form Başlığı (H2) Font Boyutu" wire:model="heading_font_size" suffix="px"
                hint="Default: 18px" />
        </div>
    </div>
</div>