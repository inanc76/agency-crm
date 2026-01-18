<?php

use Livewire\Volt\Component;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;

    public string $card_bg_color = '#eff4ff';
    public string $card_border_color = '#bfdbfe';
    public string $card_border_radius = '12px';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->card_bg_color = $setting->card_bg_color ?? '#eff4ff';
            $this->card_border_color = $setting->card_border_color ?? '#bfdbfe';
            $this->card_border_radius = $setting->card_border_radius ?? '12px';
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'card_bg_color' => 'nullable|string',
            'card_border_color' => 'nullable|string',
            'card_border_radius' => 'nullable|string',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Kart Ayarları Kaydedildi');
    }
}; ?>

<div class="space-y-6">
    {{-- Kart & Konteyner --}}
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-[var(--brand-primary)]" />
                <span class="font-semibold text-skin-heading">Kart & Konteyner Ayarları</span>
            </div>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="save" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Card Background</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="card_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="card_bg_color" placeholder="#eff4ff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Card Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="card_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="card_border_color" placeholder="#bfdbfe" class="flex-1" />
                </div>
            </div>
            <div>
                <x-mary-input label="Border Radius" wire:model.live="card_border_radius" hint="Örn: 12px" />
            </div>
        </div>
    </div>
</div>