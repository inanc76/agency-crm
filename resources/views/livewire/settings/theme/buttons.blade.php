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
            <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Buton & Aksiyon Parametreleri</span>
        </div>
        <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
            style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
            wire:click="save" spinner="save" />
    </div>

    <div class="space-y-8 py-4">
        {{--
        SECTION: Standard Buttons
        Includes: Primary, Save, Edit, Delete
        --}}
        @include('livewire.parts.buttons._btn-standard', [
            'btn_create_bg_color' => $btn_create_bg_color,
            'btn_create_text_color' => $btn_create_text_color,
            'btn_create_hover_color' => $btn_create_hover_color,
            'btn_create_border_color' => $btn_create_border_color,
            'btn_save_bg_color' => $btn_save_bg_color,
            'btn_save_text_color' => $btn_save_text_color,
            'btn_save_hover_color' => $btn_save_hover_color,
            'btn_save_border_color' => $btn_save_border_color,
            'btn_edit_bg_color' => $btn_edit_bg_color,
            'btn_edit_text_color' => $btn_edit_text_color,
            'btn_edit_hover_color' => $btn_edit_hover_color,
            'btn_edit_border_color' => $btn_edit_border_color,
            'btn_delete_bg_color' => $btn_delete_bg_color,
            'btn_delete_text_color' => $btn_delete_text_color,
            'btn_delete_hover_color' => $btn_delete_hover_color,
            'btn_delete_border_color' => $btn_delete_border_color,
        ])

        {{-- 
        SECTION: Outline & Ghost Buttons 
        Includes: Cancel, Action Links
        --}}
        @include('livewire.parts.buttons._btn-outline-ghost', [
            'btn_cancel_bg_color' => $btn_cancel_bg_color,
            'btn_cancel_text_color' => $btn_cancel_text_color,
            'btn_cancel_hover_color' => $btn_cancel_hover_color,
            'btn_cancel_border_color' => $btn_cancel_border_color,
            'action_link_color' => $action_link_color,
            'active_tab_color' => $active_tab_color,
        ])

        {{-- 
        SECTION: Special Icons & Badges 
        Includes: Icon buttons, circular buttons (Future)
        --}}
        @include('livewire.parts.buttons._btn-special-icons')
    </div>
</div>