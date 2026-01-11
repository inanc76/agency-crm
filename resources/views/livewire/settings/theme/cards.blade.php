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

    public string $table_hover_bg_color = '#f8fafc';
    public string $table_hover_text_color = '#0f172a';

    public string $list_card_bg_color = '#ffffff';
    public string $list_card_border_color = '#e2e8f0';
    public string $list_card_link_color = '#4f46e5';
    public string $list_card_hover_color = '#f8fafc';

    public string $table_avatar_bg_color = '#f1f5f9';
    public string $table_avatar_border_color = '#e2e8f0';
    public string $table_avatar_text_color = '#475569';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->card_bg_color = $setting->card_bg_color ?? '#eff4ff';
            $this->card_border_color = $setting->card_border_color ?? '#bfdbfe';
            $this->card_border_radius = $setting->card_border_radius ?? '12px';

            $this->table_hover_bg_color = $setting->table_hover_bg_color ?? '#f8fafc';
            $this->table_hover_text_color = $setting->table_hover_text_color ?? '#0f172a';

            $this->list_card_bg_color = $setting->list_card_bg_color ?? '#ffffff';
            $this->list_card_border_color = $setting->list_card_border_color ?? '#e2e8f0';
            $this->list_card_link_color = $setting->list_card_link_color ?? '#4f46e5';
            $this->list_card_hover_color = $setting->list_card_hover_color ?? '#f8fafc';

            $this->table_avatar_bg_color = $setting->table_avatar_bg_color ?? '#f1f5f9';
            $this->table_avatar_border_color = $setting->table_avatar_border_color ?? '#e2e8f0';
            $this->table_avatar_text_color = $setting->table_avatar_text_color ?? '#475569';
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'card_bg_color' => 'nullable|string',
            'card_border_color' => 'nullable|string',
            'card_border_radius' => 'nullable|string',

            'table_hover_bg_color' => 'nullable|string',
            'table_hover_text_color' => 'nullable|string',

            'list_card_bg_color' => 'nullable|string',
            'list_card_border_color' => 'nullable|string',
            'list_card_link_color' => 'nullable|string',
            'list_card_hover_color' => 'nullable|string',

            'table_avatar_bg_color' => 'nullable|string',
            'table_avatar_border_color' => 'nullable|string',
            'table_avatar_text_color' => 'nullable|string',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Kart & Tablo Ayarları Kaydedildi');
    }
}; ?>

<div class="space-y-6">
    {{-- Kart & Konteyner --}}
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-[var(--brand-primary)]" />
                <span class="font-semibold text-skin-heading">Kart & Konteyner</span>
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

    {{-- Tablo Ayarları --}}
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-table-cells" class="w-5 h-5 text-[var(--brand-primary)]" />
                <span class="font-semibold text-skin-base">Tablo & Liste Ayarları</span>
            </div>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="save" />
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Row Hover Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="table_hover_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="table_hover_bg_color" placeholder="#f8fafc" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Row Hover Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="table_hover_text_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="table_hover_text_color" placeholder="#0f172a" class="flex-1" />
                    </div>
                </div>
            </div>

            {{-- Table Avatar Settings --}}
            <div class="border-t border-[var(--card-border)] pt-6 mt-6">
                <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Table Avatar Styling</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Background</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_bg_color" placeholder="#f1f5f9"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_border_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_border_color" placeholder="#e2e8f0"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_text_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_text_color" placeholder="#475569"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- List Card Settings --}}
            <div class="border-t border-[var(--card-border)] pt-6 mt-6">
                <h3 class="text-sm font-semibold text-skin-heading mb-3 block">List Card Styling</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">List Card Background</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="list_card_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="list_card_bg_color" placeholder="#ffffff" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">List Card Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="list_card_border_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="list_card_border_color" placeholder="#e2e8f0"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">List Card Link Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="list_card_link_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="list_card_link_color" placeholder="#4f46e5" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">List Card Hover Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="list_card_hover_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="list_card_hover_color" placeholder="#f8fafc"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>