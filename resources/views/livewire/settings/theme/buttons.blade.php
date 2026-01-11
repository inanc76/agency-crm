<?php

use Livewire\Volt\Component;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;

    public string $btn_create_bg_color = '#4f46e5';
    public string $btn_create_text_color = '#ffffff';
    public string $btn_create_hover_color = '#4338ca';
    public string $btn_create_border_color = '#4f46e5';

    public string $btn_edit_bg_color = '#f59e0b';
    public string $btn_edit_text_color = '#ffffff';
    public string $btn_edit_hover_color = '#d97706';
    public string $btn_edit_border_color = '#f59e0b';

    public string $btn_delete_bg_color = '#ef4444';
    public string $btn_delete_text_color = '#ffffff';
    public string $btn_delete_hover_color = '#dc2626';
    public string $btn_delete_border_color = '#ef4444';

    public string $btn_cancel_bg_color = '#94a3b8';
    public string $btn_cancel_text_color = '#ffffff';
    public string $btn_cancel_hover_color = '#64748b';
    public string $btn_cancel_border_color = '#94a3b8';

    public string $btn_save_bg_color = '#10b981';
    public string $btn_save_text_color = '#ffffff';
    public string $btn_save_hover_color = '#059669';
    public string $btn_save_border_color = '#10b981';

    public string $action_link_color = '#4f46e5';
    public string $active_tab_color = '#4f46e5';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->btn_create_bg_color = $setting->btn_create_bg_color ?? '#4f46e5';
            $this->btn_create_text_color = $setting->btn_create_text_color ?? '#ffffff';
            $this->btn_create_hover_color = $setting->btn_create_hover_color ?? '#4338ca';
            $this->btn_create_border_color = $setting->btn_create_border_color ?? '#4f46e5';

            $this->btn_edit_bg_color = $setting->btn_edit_bg_color ?? '#f59e0b';
            $this->btn_edit_text_color = $setting->btn_edit_text_color ?? '#ffffff';
            $this->btn_edit_hover_color = $setting->btn_edit_hover_color ?? '#d97706';
            $this->btn_edit_border_color = $setting->btn_edit_border_color ?? '#f59e0b';

            $this->btn_delete_bg_color = $setting->btn_delete_bg_color ?? '#ef4444';
            $this->btn_delete_text_color = $setting->btn_delete_text_color ?? '#ffffff';
            $this->btn_delete_hover_color = $setting->btn_delete_hover_color ?? '#dc2626';
            $this->btn_delete_border_color = $setting->btn_delete_border_color ?? '#ef4444';

            $this->btn_cancel_bg_color = $setting->btn_cancel_bg_color ?? '#94a3b8';
            $this->btn_cancel_text_color = $setting->btn_cancel_text_color ?? '#ffffff';
            $this->btn_cancel_hover_color = $setting->btn_cancel_hover_color ?? '#64748b';
            $this->btn_cancel_border_color = $setting->btn_cancel_border_color ?? '#94a3b8';

            $this->btn_save_bg_color = $setting->btn_save_bg_color ?? '#10b981';
            $this->btn_save_text_color = $setting->btn_save_text_color ?? '#ffffff';
            $this->btn_save_hover_color = $setting->btn_save_hover_color ?? '#059669';
            $this->btn_save_border_color = $setting->btn_save_border_color ?? '#10b981';

            $this->action_link_color = $setting->action_link_color ?? '#4f46e5';
            $this->active_tab_color = $setting->active_tab_color ?? '#4f46e5';
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'btn_create_bg_color' => 'nullable|string',
            'btn_create_text_color' => 'nullable|string',
            'btn_create_hover_color' => 'nullable|string',
            'btn_create_border_color' => 'nullable|string',

            'btn_edit_bg_color' => 'nullable|string',
            'btn_edit_text_color' => 'nullable|string',
            'btn_edit_hover_color' => 'nullable|string',
            'btn_edit_border_color' => 'nullable|string',

            'btn_delete_bg_color' => 'nullable|string',
            'btn_delete_text_color' => 'nullable|string',
            'btn_delete_hover_color' => 'nullable|string',
            'btn_delete_border_color' => 'nullable|string',

            'btn_cancel_bg_color' => 'nullable|string',
            'btn_cancel_text_color' => 'nullable|string',
            'btn_cancel_hover_color' => 'nullable|string',
            'btn_cancel_border_color' => 'nullable|string',

            'btn_save_bg_color' => 'nullable|string',
            'btn_save_text_color' => 'nullable|string',
            'btn_save_hover_color' => 'nullable|string',
            'btn_save_border_color' => 'nullable|string',

            'action_link_color' => 'nullable|string',
            'active_tab_color' => 'nullable|string',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Buton Ayarları Kaydedildi');
    }
}; ?>

<div
    class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-indigo-500" />
            <span class="font-semibold text-skin-heading">Buton & Aksiyon Parametreleri</span>
        </div>
        <x-mary-button label="Kaydet" icon="o-check" class="btn-sm text-white"
            style="background-color: var(--btn-save-bg); border-color: var(--btn-save-border);" wire:click="save"
            spinner="save" />
    </div>

    <div class="space-y-8 py-4">
        {{-- 1. Ekle (Create) Button --}}
        <div>
            <h3
                class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                <x-mary-icon name="o-plus-circle" class="w-4 h-4 text-[var(--brand-primary)]" /> Ekle Butonu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_create_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="btn_create_bg_color" placeholder="#4f46e5" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_create_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_create_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_create_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_create_hover_color" placeholder="#4338ca" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_create_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_create_border_color" placeholder="#4f46e5" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Kaydet (Save) Button --}}
        <div>
            <h3
                class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                <x-mary-icon name="o-check-circle" class="w-4 h-4" style="color: var(--btn-save-bg);" /> Kaydet Butonu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_save_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="btn_save_bg_color" placeholder="#10b981" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_save_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_save_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_save_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_save_hover_color" placeholder="#059669" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_save_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_save_border_color" placeholder="#10b981" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Düzenle (Edit) Button --}}
        <div>
            <h3
                class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-[var(--color-warning)]" /> Düzenle Butonu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_edit_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="btn_edit_bg_color" placeholder="#f59e0b" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_edit_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_edit_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_edit_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_edit_hover_color" placeholder="#d97706" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_edit_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_edit_border_color" placeholder="#f59e0b" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Sil (Delete) Button --}}
        <div>
            <h3
                class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                <x-mary-icon name="o-trash" class="w-4 h-4 text-[var(--color-danger)]" /> Sil Butonu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_delete_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="btn_delete_bg_color" placeholder="#ef4444" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_delete_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_delete_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_delete_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_delete_hover_color" placeholder="#dc2626" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_delete_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_delete_border_color" placeholder="#ef4444" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. İptal (Cancel) Button --}}
        <div>
            <h3
                class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                <x-mary-icon name="o-x-circle" class="w-4 h-4 text-[var(--color-text-muted)]" /> İptal Butonu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_cancel_bg_color"
                            class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="btn_cancel_bg_color" placeholder="#94a3b8" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_cancel_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_cancel_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_cancel_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_cancel_hover_color" placeholder="#64748b" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="btn_cancel_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="btn_cancel_border_color" placeholder="#94a3b8" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Links --}}
        <div>
            <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2">Diğer Aksiyonlar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Link Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="action_link_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="action_link_color" placeholder="#4f46e5" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Active Tab Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="active_tab_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="active_tab_color" placeholder="#4f46e5" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>