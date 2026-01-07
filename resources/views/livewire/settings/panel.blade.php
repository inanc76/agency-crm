<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Tema Ayarları'])]
    class extends Component {
    use Toast;
    use WithFileUploads;

    public string $site_name = 'MEDIACLICK';
    public $favicon;
    public $logo;
    public float $logo_scale = 1.0;
    public string $header_bg_color = '#3D3373';
    public string $menu_bg_color = '#3D3373';
    public string $menu_text_color = '#ffffff';
    public string $header_icon_color = '#ffffff';
    public string $header_border_color = 'transparent';
    public int $header_border_width = 0;

    public ?string $current_favicon_path = null;
    public ?string $current_logo_path = null;

    // Design Settings
    public string $font_family = 'Inter';
    public string $base_text_color = '#475569';
    public string $heading_color = '#0f172a';

    public string $input_focus_ring_color = '#6366f1';
    public string $input_border_color = '#cbd5e1';
    public string $input_error_ring_color = '#ef4444';
    public string $input_error_border_color = '#ef4444';
    public string $input_error_text_color = '#ef4444';
    public string $input_vertical_padding = '0.5rem';
    public string $input_border_radius = '0.375rem';

    // Buttons - Granular
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

    public string $card_bg_color = '#eff4ff';
    public string $card_border_color = '#bfdbfe';
    public string $card_border_radius = '0.75rem';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();

        if ($setting) {
            $this->site_name = $setting->site_name;
            $this->logo_scale = $setting->logo_scale ?? 1.0;
            $this->header_bg_color = $setting->header_bg_color;
            $this->menu_bg_color = str_starts_with($setting->menu_bg_color, '#') ? $setting->menu_bg_color : '#3D3373';
            $this->menu_text_color = $setting->menu_text_color;
            $this->header_icon_color = $setting->header_icon_color ?? '#ffffff';
            $this->header_border_color = ($setting->header_border_color === 'transparent' || !str_starts_with($setting->header_border_color, '#')) ? '#000000' : $setting->header_border_color;
            $this->header_border_width = $setting->header_border_width ?? 0;
            $this->current_favicon_path = $setting->favicon_path;
            $this->current_logo_path = $setting->logo_path;

            // Load Design Settings with defaults
            $this->font_family = $setting->font_family ?? 'Inter';
            $this->base_text_color = $setting->base_text_color ?? '#475569';
            $this->heading_color = $setting->heading_color ?? '#0f172a';

            $this->input_focus_ring_color = $setting->input_focus_ring_color ?? '#6366f1';
            $this->input_border_color = $setting->input_border_color ?? '#cbd5e1';
            $this->input_error_ring_color = $setting->input_error_ring_color ?? '#ef4444';
            $this->input_error_border_color = $setting->input_error_border_color ?? '#ef4444';
            $this->input_error_text_color = $setting->input_error_text_color ?? '#ef4444';
            $this->input_vertical_padding = $setting->input_vertical_padding ?? '0.5rem';
            $this->input_border_radius = $setting->input_border_radius ?? '0.375rem';

            // Load Granular Button Settings
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

            $this->card_bg_color = $setting->card_bg_color ?? '#eff4ff';
            $this->card_border_color = $setting->card_border_color ?? '#bfdbfe';
            $this->card_border_radius = $setting->card_border_radius ?? '0.75rem';
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'site_name' => 'required|string|max:255',
            'favicon' => 'nullable|file|mimes:ico,png|max:512',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
            'logo_scale' => 'required|numeric|in:1,1.5,2',
            'header_bg_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'menu_bg_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'menu_text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_icon_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_border_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_border_width' => 'required|integer|min:0|max:20',

            'font_family' => 'nullable|string|max:255',
            'base_text_color' => 'nullable|string',
            'heading_color' => 'nullable|string',

            'input_focus_ring_color' => 'nullable|string',
            'input_border_color' => 'nullable|string',
            'input_error_ring_color' => 'nullable|string',
            'input_error_border_color' => 'nullable|string',
            'input_error_text_color' => 'nullable|string',
            'input_vertical_padding' => 'nullable|string',
            'input_border_radius' => 'nullable|string',

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

            'card_bg_color' => 'nullable|string',
            'card_border_color' => 'nullable|string',
            'card_border_radius' => 'nullable|string',
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

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="w-full lg:w-3/4 mx-auto pb-20">
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
            <h1 class="text-2xl font-bold text-gray-900">Tema Ayarları</h1>
            <p class="text-sm text-slate-500 mt-1">Uygulamanın görünümünü ve tema renklerini özelleştirin.</p>
        </div>

        {{-- Header Appearance Card --}}
        @include('livewire.settings.parts.header-appearance')

        {{-- Basic Design Card --}}
        @include('livewire.settings.parts.basic-design')

    </div>
</div>