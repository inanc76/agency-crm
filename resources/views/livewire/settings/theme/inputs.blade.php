<?php

use Livewire\Volt\Component;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;

    public string $input_focus_ring_color = '#6366f1';
    public string $input_border_color = '#cbd5e1';
    public string $input_error_ring_color = '#ef4444';
    public string $input_error_border_color = '#ef4444';
    public string $input_error_text_color = '#ef4444';
    public string $input_vertical_padding = '8px';
    public string $input_border_radius = '6px';

    public int $label_font_size = 14;
    public int $input_font_size = 16;
    public int $error_font_size = 12;
    public int $helper_font_size = 12;

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->input_focus_ring_color = $setting->input_focus_ring_color ?? '#6366f1';
            $this->input_border_color = $setting->input_border_color ?? '#cbd5e1';
            $this->input_error_ring_color = $setting->input_error_ring_color ?? '#ef4444';
            $this->input_error_border_color = $setting->input_error_border_color ?? '#ef4444';
            $this->input_error_text_color = $setting->input_error_text_color ?? '#ef4444';
            $this->input_vertical_padding = $setting->input_vertical_padding ?? '8px';
            $this->input_border_radius = $setting->input_border_radius ?? '6px';

            $this->label_font_size = $setting->label_font_size ?? 14;
            $this->input_font_size = $setting->input_font_size ?? 16;
            $this->error_font_size = $setting->error_font_size ?? 12;
            $this->helper_font_size = $setting->helper_font_size ?? 12;
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'input_focus_ring_color' => 'nullable|string',
            'input_border_color' => 'nullable|string',
            'input_error_ring_color' => 'nullable|string',
            'input_error_border_color' => 'nullable|string',
            'input_error_text_color' => 'nullable|string',
            'input_vertical_padding' => 'nullable|string',
            'input_border_radius' => 'nullable|string',

            'label_font_size' => 'required|integer|min:8|max:48',
            'input_font_size' => 'required|integer|min:8|max:48',
            'error_font_size' => 'required|integer|min:8|max:24',
            'helper_font_size' => 'required|integer|min:8|max:24',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Input Ayarları Kaydedildi');
    }
}; ?>

<div
    class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-pencil-square" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Input & Validation</span>
        </div>
        <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
            style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
            wire:click="save" spinner="save" />
    </div>

    <div class="space-y-6">
        {{-- Normal State --}}
        <div>
            <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Normal State</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Focus Ring Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="input_focus_ring_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="input_focus_ring_color" placeholder="#6366f1" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="input_border_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="input_border_color" placeholder="#cbd5e1" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Error State --}}
        <div>
            <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Error State</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Error Ring Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="input_error_ring_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="input_error_ring_color" placeholder="#ef4444" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Error Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="input_error_border_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="input_error_border_color" placeholder="#ef4444" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Error Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="input_error_text_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="input_error_text_color" placeholder="#ef4444" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Geometry & Fonts --}}
        <div>
            <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Input Geometry & Typography</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-mary-input label="Vertical Padding" wire:model="input_vertical_padding" hint="Örn: 8px" />
                <x-mary-input label="Border Radius" wire:model="input_border_radius" hint="Örn: 6px veya rounded-md" />
                <x-mary-input label="Label Font Size" wire:model="label_font_size" suffix="px" hint="Default: 14px" />
                <x-mary-input label="Input Font Size" wire:model="input_font_size" suffix="px" hint="Default: 16px" />
            </div>
        </div>

        {{-- Helper & Error Typography --}}
        <div>
            <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Validation Typography</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-mary-input label="Error Message Font Size" wire:model="error_font_size" suffix="px"
                    hint="Default: 12px" />
                <x-mary-input label="Helper Text Font Size" wire:model="helper_font_size" suffix="px"
                    hint="Default: 12px" />
            </div>
        </div>

        {{-- Combo Box Info --}}
        <div class="mt-6 border-t border-[var(--card-border)] pt-6">
            <x-mary-accordion>
                <x-mary-collapse name="combobox_info" separator>
                    <x-slot:heading>
                        <div class="flex items-center gap-3">
                            <x-mary-icon name="o-chevron-up-down" class="w-5 h-5 text-[var(--brand-primary)]" />
                            <span class="font-semibold text-skin-heading">Combo Box & Select Önizleme</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Preview --}}
                            <div
                                class="bg-[var(--dropdown-hover-bg)] rounded-lg p-4 border border-[var(--card-border)]">
                                <label
                                    class="block text-xs font-medium text-[var(--color-text-muted)] mb-2">Önizleme</label>
                                <div class="flex items-center gap-3">
                                    <select
                                        class="select select-sm bg-[var(--card-bg)] border-[var(--card-border)] text-xs w-40">
                                        <option>Tüm Kategoriler</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </x-slot:content>
                </x-mary-collapse>
            </x-mary-accordion>
        </div>
    </div>
</div>