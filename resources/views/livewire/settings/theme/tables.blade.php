<?php

use Livewire\Volt\Component;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;

    public string $table_hover_bg_color = '#f8fafc';
    public string $table_hover_text_color = '#0f172a';

    public string $table_avatar_bg_color = '#f1f5f9';
    public string $table_avatar_border_color = '#e2e8f0';
    public string $table_avatar_text_color = '#475569';

    public string $table_header_bg_color = '#f8fafc';
    public string $table_header_text_color = '#1e293b';
    public string $table_divide_color = '#f1f5f9';
    public string $table_item_name_size = '13px';
    public string $table_item_name_weight = '500';

    public string $list_card_bg_color = '#ffffff';
    public string $list_card_border_color = '#e2e8f0';
    public string $list_card_link_color = '#4f46e5';
    public string $list_card_hover_color = '#f8fafc';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->table_hover_bg_color = $setting->table_hover_bg_color ?? '#f8fafc';
            $this->table_hover_text_color = $setting->table_hover_text_color ?? '#0f172a';

            $this->table_avatar_bg_color = $setting->table_avatar_bg_color ?? '#f1f5f9';
            $this->table_avatar_border_color = $setting->table_avatar_border_color ?? '#e2e8f0';
            $this->table_avatar_text_color = $setting->table_avatar_text_color ?? '#475569';

            $this->table_header_bg_color = $setting->table_header_bg_color ?? '#f8fafc';
            $this->table_header_text_color = $setting->table_header_text_color ?? '#1e293b';
            $this->table_divide_color = $setting->table_divide_color ?? '#f1f5f9';
            $this->table_item_name_size = $setting->table_item_name_size ?? '13px';
            $this->table_item_name_weight = $setting->table_item_name_weight ?? '500';

            $this->list_card_bg_color = $setting->list_card_bg_color ?? '#ffffff';
            $this->list_card_border_color = $setting->list_card_border_color ?? '#e2e8f0';
            $this->list_card_link_color = $setting->list_card_link_color ?? '#4f46e5';
            $this->list_card_hover_color = $setting->list_card_hover_color ?? '#f8fafc';
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'table_hover_bg_color' => 'nullable|string',
            'table_hover_text_color' => 'nullable|string',
            'table_avatar_bg_color' => 'nullable|string',
            'table_avatar_border_color' => 'nullable|string',
            'table_avatar_text_color' => 'nullable|string',
            'table_header_bg_color' => 'nullable|string',
            'table_header_text_color' => 'nullable|string',
            'table_divide_color' => 'nullable|string',
            'table_item_name_size' => 'nullable|string',
            'table_item_name_weight' => 'nullable|string',
            'list_card_bg_color' => 'nullable|string',
            'list_card_border_color' => 'nullable|string',
            'list_card_link_color' => 'nullable|string',
            'list_card_hover_color' => 'nullable|string',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Tablo Ayarları Kaydedildi');
    }
}; ?>

<div class="space-y-6">
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-table-cells" class="w-5 h-5 text-[var(--brand-primary)]" />
                <span class="font-semibold text-skin-heading">Tablo Genel Ayarları</span>
            </div>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="save" />
        </div>

        <div class="space-y-8">
            {{-- Header Settings --}}
            <div>
                <h3 class="text-sm font-semibold text-skin-heading mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-[var(--brand-primary)] rounded-full"></div>
                    Tablo Başlığı (Thead)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Başlık Arkaplanı</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_header_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_header_bg_color" placeholder="#f8fafc"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Başlık Yazı Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_header_text_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_header_text_color" placeholder="#1e293b"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row & Hover Settings --}}
            <div class="border-t border-[var(--card-border)] pt-6">
                <h3 class="text-sm font-semibold text-skin-heading mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-[var(--brand-primary)] rounded-full"></div>
                    Satır & Hover Ayarları
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Satır Hover Arkaplan</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_hover_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_hover_bg_color" placeholder="#f8fafc" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Satır Hover Yazı Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_hover_text_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_hover_text_color" placeholder="#0f172a"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Ayırıcı Çizgi Rengi
                            (Divide)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_divide_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_divide_color" placeholder="#f1f5f9" class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Item Name Styling --}}
            <div class="border-t border-[var(--card-border)] pt-6">
                <h3 class="text-sm font-semibold text-skin-heading mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-[var(--brand-primary)] rounded-full"></div>
                    İsim & Metin Stil Ayarları
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-mary-input label="İsim Yazı Boyutu" wire:model.live="table_item_name_size" hint="Örn: 13px" />
                    <x-mary-select label="İsim Yazı Kalınlığı" wire:model.live="table_item_name_weight" :options="[
        ['id' => '400', 'name' => 'Normal (400)'],
        ['id' => '500', 'name' => 'Medium (500)'],
        ['id' => '600', 'name' => 'Semi-Bold (600)'],
        ['id' => '700', 'name' => 'Bold (700)'],
    ]" />
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">İsim/Link Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="list_card_link_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="list_card_link_color" placeholder="#4f46e5" class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Avatar & Badges --}}
            <div class="border-t border-[var(--card-border)] pt-6">
                <h3 class="text-sm font-semibold text-skin-heading mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-[var(--brand-primary)] rounded-full"></div>
                    Avatar & Sayı Rozetleri
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Arkaplan</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_bg_color" placeholder="#f1f5f9"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Kenarlık</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_border_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_border_color" placeholder="#e2e8f0"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Yazı Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_avatar_text_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="table_avatar_text_color" placeholder="#475569"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- List Card Settings (Secondary Table Style) --}}
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-queue-list" class="w-5 h-5 text-[var(--brand-primary)]" />
                <span class="font-semibold text-skin-heading">Liste Kartı Ayarları (Alternatif)</span>
            </div>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="save" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Liste Kartı Arkaplan</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="list_card_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="list_card_bg_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Liste Kartı Kenarlık</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="list_card_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="list_card_border_color" placeholder="#e2e8f0" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Liste Kartı Hover Rengi</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="list_card_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="list_card_hover_color" placeholder="#f8fafc" class="flex-1" />
                </div>
            </div>
        </div>
    </div>
</div>