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

    public string $activeTab = 'theme';

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
    public string $input_vertical_padding = '8px';
    public string $input_border_radius = '6px';

    public int $label_font_size = 14;
    public int $input_font_size = 16;
    public int $heading_font_size = 18;
    public int $error_font_size = 12;
    public int $helper_font_size = 12;

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
    public string $active_tab_color = '#4f46e5';

    public string $card_bg_color = '#eff4ff';
    public string $card_border_color = '#bfdbfe';
    public string $card_border_radius = '12px';

    public string $table_hover_bg_color = '#f8fafc';
    public string $table_hover_text_color = '#0f172a';

    public string $list_card_bg_color = '#ffffff';
    public string $list_card_border_color = '#e2e8f0';
    public string $list_card_link_color = '#4f46e5';
    public string $list_card_hover_color = '#f8fafc';

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
            $this->input_vertical_padding = $setting->input_vertical_padding ?? '8px';
            $this->input_border_radius = $setting->input_border_radius ?? '6px';

            $this->label_font_size = $setting->label_font_size ?? 14;
            $this->input_font_size = $setting->input_font_size ?? 16;
            $this->heading_font_size = $setting->heading_font_size ?? 18;
            $this->error_font_size = $setting->error_font_size ?? 12;
            $this->helper_font_size = $setting->helper_font_size ?? 12;

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
            $this->active_tab_color = $setting->active_tab_color ?? '#4f46e5';

            $this->card_bg_color = $setting->card_bg_color ?? '#eff4ff';
            $this->card_border_color = $setting->card_border_color ?? '#bfdbfe';
            $this->card_border_radius = $setting->card_border_radius ?? '12px';

            $this->table_hover_bg_color = $setting->table_hover_bg_color ?? '#f8fafc';
            $this->table_hover_text_color = $setting->table_hover_text_color ?? '#0f172a';

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

            'label_font_size' => 'required|integer|min:8|max:48',
            'input_font_size' => 'required|integer|min:8|max:48',
            'heading_font_size' => 'required|integer|min:8|max:72',
            'error_font_size' => 'required|integer|min:8|max:24',
            'helper_font_size' => 'required|integer|min:8|max:24',

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

            'card_bg_color' => 'nullable|string',
            'card_border_color' => 'nullable|string',
            'card_border_radius' => 'nullable|string',

            'table_hover_bg_color' => 'nullable|string',
            'table_hover_text_color' => 'nullable|string',

            'list_card_bg_color' => 'nullable|string',
            'list_card_border_color' => 'nullable|string',
            'list_card_link_color' => 'nullable|string',
            'list_card_hover_color' => 'nullable|string',
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
    <div class="w-full lg:w-4/5 mx-auto pb-20">
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
            <x-mary-tab name="theme" icon="o-adjustments-horizontal">
                <x-slot:label>
                    <span class="font-semibold">Tema Ayarları</span>
                </x-slot:label>
                <div class="mt-6 space-y-6">
                    {{-- Header Appearance Card --}}
                    @include('livewire.settings.parts.header-appearance')

                    {{-- Basic Design Card --}}
                    @include('livewire.settings.parts.basic-design')
                </div>
            </x-mary-tab>

            <x-mary-tab name="style-guide" icon="o-swatch">
                <x-slot:label>
                    <span class="font-semibold">Tasarım Rehberi</span>
                </x-slot:label>

                <div class="mt-6 flex flex-col gap-2">
                    {{-- 1. Logo Ayarları Önizleme --}}
                    <x-mary-collapse name="preview1" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-photo" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Logo Ayarları Önizleme</span>
                                </div>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--logo-scale:
                                    {{ $logo_scale }}</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div
                                class="p-6 bg-slate-100 rounded-xl flex items-center justify-center border border-dashed border-slate-300">
                                <div class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm w-full max-w-md">
                                    @if($current_logo_path)
                                        <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Logo"
                                            class="object-contain" style="height: {{ 2.5 * $logo_scale }}rem">
                                    @else
                                        <span
                                            class="text-xl font-bold tracking-tight text-slate-800">{{ $site_name }}</span>
                                    @endif
                                    <div class="h-4 w-px bg-slate-200 mx-2"></div>
                                    <span class="text-sm font-medium text-slate-500">Uygulama Önizleme</span>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 2. Menü Renk Ayarları Önizleme --}}
                    <x-mary-collapse name="preview2" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-bars-3" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Menü Renk Ayarları Önizleme</span>
                                </div>
                                <div class="flex gap-2">
                                    <span
                                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--header-bg</span>
                                    <span
                                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--menu-bg</span>
                                </div>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="space-y-4">
                                <div class="p-4 rounded-xl border border-slate-200 relative"
                                    style="background-color: {{ $header_bg_color }}">
                                    <span class="absolute top-2 right-2 text-[8px] font-mono opacity-50"
                                        style="color: {{ $menu_text_color }}">--header-bg</span>
                                    <div class="flex items-center justify-between">
                                        <span style="color: {{ $menu_text_color }}">Header Alanı</span>
                                        <div class="flex gap-2">
                                            <div class="w-8 h-8 rounded-full"
                                                style="background-color: {{ $menu_text_color }}; opacity: 0.2;"></div>
                                            <div class="w-8 h-8 rounded-full"
                                                style="background-color: {{ $menu_text_color }}; opacity: 0.2;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl border border-slate-200 flex relative"
                                    style="background-color: {{ $menu_bg_color }}">
                                    <span class="absolute top-2 right-2 text-[8px] font-mono opacity-50"
                                        style="color: {{ $menu_text_color }}">--menu-bg</span>
                                    <div class="space-y-2 w-full">
                                        <div class="h-2 w-3/4 rounded"
                                            style="background-color: {{ $menu_text_color }}; opacity: 0.6;"></div>
                                        <div class="h-2 w-1/2 rounded"
                                            style="background-color: {{ $menu_text_color }}; opacity: 0.4;"></div>
                                        <div class="h-2 w-2/3 rounded"
                                            style="background-color: {{ $menu_text_color }}; opacity: 0.4;"></div>
                                    </div>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 3. Kenarlık ve Yazı Rengi Önizleme --}}
                    <x-mary-collapse name="preview3" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-swatch" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Kenarlık ve Yazı Rengi Önizleme</span>
                                </div>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--header-icon-color</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="p-6 rounded-xl border-t-0"
                                style="background-color: {{ $header_bg_color }}; border-bottom: {{ $header_border_width }}px solid {{ $header_border_color }}">
                                <div class="flex items-center justify-end gap-6"
                                    style="color: {{ $header_icon_color }}">
                                    <div class="flex flex-col items-center gap-1">
                                        <x-mary-icon name="o-bell" class="w-6 h-6" />
                                        <span class="text-[8px] font-mono opacity-50">--header-icon-color</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold">John Doe</span>
                                        <x-mary-icon name="o-chevron-down" class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <p class="text-xs text-slate-500 italic">* Alt Kenarlık: {{ $header_border_width }}px
                                </p>
                                <span class="text-[9px] font-mono text-slate-400">border-bottom:
                                    var(--header-border-width) solid var(--header-border-color)</span>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 4. Global Tipografi Önizleme --}}
                    <x-mary-collapse name="preview4" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-language" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Global Tipografi Önizleme</span>
                                </div>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">font-family:
                                    {{ $font_family }}</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="p-6 rounded-xl border border-slate-100 bg-white"
                                style="font-family: {{ $font_family }}">
                                <div class="flex items-baseline gap-4 mb-2">
                                    <h1 class="text-3xl font-bold" style="color: {{ $heading_color }}">H1: Başlık Seviye
                                        1</h1>
                                    <span class="text-[10px] font-mono text-slate-400">--color-text-heading</span>
                                </div>
                                <h2 class="text-2xl font-semibold mb-2" style="color: {{ $heading_color }}">H2: Başlık
                                    Seviye 2</h2>
                                <h3 class="text-xl font-medium mb-4" style="color: {{ $heading_color }}">H3: Başlık
                                    Seviye 3</h3>
                                <div class="relative pt-4 border-t border-slate-50">
                                    <span
                                        class="absolute -top-2.5 left-4 bg-white px-2 text-[10px] font-mono text-slate-400">--color-text-base</span>
                                    <p class="leading-relaxed" style="color: {{ $base_text_color }}">
                                        Bu paragraf metni, ayarlar sayfasından seçilen ana metin (base text) rengini ve
                                        seçilen font ailesini kullanmaktadır.
                                        Sistemdeki tüm uzun metinler ve açıklamalar bu biçimde görünecektir.
                                    </p>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 5. Input & Validation Önizleme --}}
                    <x-mary-collapse name="preview5" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-pencil-square" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Input & Validation Önizleme</span>
                                </div>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--input-radius:
                                    {{ $input_border_radius }}</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4">
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-slate-700">Normal & Focus
                                            State</label>
                                        <span class="text-[9px] font-mono text-slate-400">--input-border</span>
                                    </div>
                                    <input type="text" placeholder="Focus durumunu test edin..."
                                        class="w-full px-4 py-2 border transition-all duration-200 outline-none" style="border-color: {{ $input_border_color }}; 
                                                  border-radius: {{ $input_border_radius }}; 
                                                  padding-top: {{ $input_vertical_padding }}; 
                                                  padding-bottom: {{ $input_vertical_padding }};">
                                    <p class="text-[10px] font-mono text-slate-500 mt-2">Focus ring: <span
                                            class="inline-block w-3 h-3 rounded-full align-middle"
                                            style="background-color: {{ $input_focus_ring_color }}"></span> <span
                                            class="text-slate-400">--input-focus-ring</span></p>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium"
                                            style="color: {{ $input_error_text_color }}">Error State</label>
                                        <span class="text-[9px] font-mono text-slate-400">--error-color</span>
                                    </div>
                                    <input type="text" value="Hatalı veri girişi"
                                        class="w-full px-4 py-2 border transition-all duration-200 outline-none" style="border-color: {{ $input_error_border_color }}; 
                                                  border-radius: {{ $input_border_radius }}; 
                                                  padding-top: {{ $input_vertical_padding }}; 
                                                  padding-bottom: {{ $input_vertical_padding }};
                                                  box-shadow: 0 0 0 2px {{ $input_error_ring_color }}40;">
                                    <p class="text-[10px] font-mono mt-1" style="color: {{ $input_error_text_color }}">
                                        --input-error-text</p>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 6. Buton & Aksiyon Parametreleri Önizleme --}}
                    <x-mary-collapse name="preview6" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Buton & Aksiyon Parametreleri
                                        Önizleme</span>
                                </div>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">Isolated
                                    Design System</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div
                                class="p-6 bg-white rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-3 gap-6">
                                <div class="flex flex-col items-center gap-2">
                                    <button class="theme-btn-save w-full justify-center">
                                        <x-mary-icon name="o-check" class="w-4 h-4" /> <span>Kaydet</span>
                                    </button>
                                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-save</span>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <button class="theme-btn-action w-full justify-center">
                                        <x-mary-icon name="o-plus" class="w-4 h-4" /> <span>Yeni Ekle</span>
                                    </button>
                                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-action</span>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <button class="theme-btn-edit w-full justify-center">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" /> <span>Düzenle</span>
                                    </button>
                                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-edit</span>
                                </div>
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <button class="theme-btn-delete w-full justify-center">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" /> <span>Sil</span>
                                    </button>
                                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-delete</span>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <button class="theme-btn-cancel w-full justify-center">
                                        <span>İptal</span>
                                    </button>
                                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-cancel</span>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="h-10 flex items-center">
                                        <a href="#" class="text-sm font-semibold underline"
                                            style="color: {{ $action_link_color }}">Detayları Gör</a>
                                    </div>
                                    <span class="text-[9px] font-mono text-slate-400">--action-link-color</span>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 7. Kart & Konteyner Önizleme --}}
                    <x-mary-collapse name="preview7" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Kart & Konteyner Önizleme</span>
                                </div>
                                <div class="flex gap-2">
                                    <span
                                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">.theme-card</span>
                                    <span
                                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--card-radius:
                                        {{ $card_border_radius }}</span>
                                </div>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="p-10 bg-slate-200/50 rounded-2xl border border-dashed border-slate-300">
                                <div class="transition-all duration-300 shadow-xl relative" style="background-color: {{ $card_bg_color }}; 
                                            border: 1px solid {{ $card_border_color }}; 
                                            border-radius: {{ $card_border_radius }};">
                                    <span
                                        class="absolute -top-3 -right-3 bg-white border border-slate-200 text-[9px] font-mono px-2 py-1 rounded shadow-sm">.theme-card</span>
                                    {{-- Card Header Preview --}}
                                    <div class="px-6 py-4 border-b flex justify-between items-center"
                                        style="border-color: {{ $card_border_color }}">
                                        <h4 class="font-bold text-lg" style="color: {{ $heading_color }}">Kart Başlığı
                                        </h4>
                                        <span class="text-[8px] font-mono text-slate-400">--card-border</span>
                                    </div>

                                    {{-- Card Body Preview --}}
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <p class="text-sm leading-relaxed max-w-[70%]"
                                                style="color: {{ $base_text_color }}">
                                                Bu alan, seçtiğiniz <strong>Kart Arka Planı</strong> ve <strong>Kenarlık
                                                    Rengi</strong> ile şekillenir.
                                            </p>
                                            <span class="text-[8px] font-mono text-slate-400">--card-bg</span>
                                        </div>

                                        <div class="mt-6 flex gap-3">
                                            <div class="h-8 w-24 rounded shadow-sm opacity-50"
                                                style="background-color: var(--btn-save-bg);"></div>
                                            <div class="h-8 w-24 rounded shadow-sm opacity-50"
                                                style="background-color: var(--btn-cancel-bg);"></div>
                                        </div>
                                    </div>

                                    {{-- Card Footer Preview --}}
                                    <div class="px-6 py-3 bg-black/5 rounded-b-[inherit]"
                                        style="border-top: 1px solid {{ $card_border_color }}">
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] uppercase tracking-wider font-bold opacity-40"
                                                style="color: {{ $base_text_color }}">Önizleme Modu</span>
                                            <div class="flex -space-x-2">
                                                <div class="w-6 h-6 rounded-full border-2 border-white bg-indigo-400">
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 border-white bg-emerald-400">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div
                                    class="p-3 bg-white rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-200 transition-colors">
                                    <span class="text-[10px] text-slate-400 block mb-1 font-mono">--card-bg</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded border border-slate-200"
                                            style="background-color: {{ $card_bg_color }}"></div>
                                        <code class="text-xs font-mono text-slate-600">{{ $card_bg_color }}</code>
                                    </div>
                                </div>
                                <div
                                    class="p-3 bg-white rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-200 transition-colors">
                                    <span class="text-[10px] text-slate-400 block mb-1 font-mono">--card-border</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded border border-slate-200"
                                            style="background-color: {{ $card_border_color }}"></div>
                                        <code class="text-xs font-mono text-slate-600">{{ $card_border_color }}</code>
                                    </div>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>

                    {{-- 8. Tablo Hover Önizleme --}}
                    <x-mary-collapse name="preview8" group="previews" separator
                        class="bg-white border border-slate-200 shadow-sm rounded-lg">
                        <x-slot:heading>
                            <div class="flex items-center justify-between w-full pr-4">
                                <div class="flex items-center gap-3">
                                    <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
                                    <span class="font-semibold text-slate-700">Tablo Hover Önizleme</span>
                                </div>
                                <div class="flex gap-2">
                                    <span
                                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--table-hover-bg</span>
                                </div>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="p-4 bg-white rounded-xl border border-slate-100">
                                <table class="table w-full">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-slate-400">
                                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                                Örnek Sütun 1</th>
                                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                                Örnek Sütun 2</th>
                                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                                Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-slate-50 transition-colors duration-200">
                                            <td class="py-3 px-4 text-sm">Satır Verisi A-1</td>
                                            <td class="py-3 px-4 text-sm">Satır Verisi A-2</td>
                                            <td class="py-3 px-4 text-sm"><span
                                                    class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">AKTİF</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-slate-50 transition-colors duration-200">
                                            <td class="py-3 px-4 text-sm">Satır Verisi B-1</td>
                                            <td class="py-3 px-4 text-sm">Satır Verisi B-2</td>
                                            <td class="py-3 px-4 text-sm"><span
                                                    class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">BEKLEMEDE</span>
                                            </td>
                                        </tr>
                                        <tr class="transition-colors duration-200">
                                            <td class="py-3 px-4 text-sm">Satır Verisi C-1</td>
                                            <td class="py-3 px-4 text-sm">Satır Verisi C-2</td>
                                            <td class="py-3 px-4 text-sm"><span
                                                    class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold">İPTAL</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div
                                    class="mt-4 p-3 bg-slate-50 rounded-lg flex items-center justify-between border border-dashed border-slate-200">
                                    <span class="text-xs text-slate-500 italic">* Satırların üzerine gelerek hover
                                        efektini test edebilirsiniz.</span>
                                    <div class="flex gap-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded border border-slate-200"
                                                style="background-color: {{ $table_hover_bg_color }}"></div>
                                            <span class="text-[10px] font-mono text-slate-400">BG</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded border border-slate-200"
                                                style="background-color: {{ $table_hover_text_color }}"></div>
                                            <span class="text-[10px] font-mono text-slate-400">TEXT</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-mary-collapse>
                </div>
            </x-mary-tab>
        </x-mary-tabs>
    </div>
</div>