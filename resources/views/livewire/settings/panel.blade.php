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

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();

        if ($setting) {
            $this->site_name = $setting->site_name;
            $this->logo_scale = $setting->logo_scale ?? 1.0;
            $this->header_bg_color = $setting->header_bg_color;
            // Normalize RGBA to Hex to prevent validation errors
            $this->menu_bg_color = str_starts_with($setting->menu_bg_color, '#') ? $setting->menu_bg_color : '#3D3373';
            $this->menu_text_color = $setting->menu_text_color;
            $this->header_icon_color = $setting->header_icon_color ?? '#ffffff';
            // Normalize "transparent" to Hex for consistency with picker
            $this->header_border_color = ($setting->header_border_color === 'transparent' || !str_starts_with($setting->header_border_color, '#')) ? '#000000' : $setting->header_border_color;
            $this->header_border_width = $setting->header_border_width ?? 0;
            $this->current_favicon_path = $setting->favicon_path;
            $this->current_logo_path = $setting->logo_path;
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
        ]);

        $repository->saveSettings($data);

        $this->success('Ayarlar Kaydedildi', 'Tema ayarları başarıyla güncellendi. Değişiklikleri görmek için sayfayı yenileyin.');
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
    <div class="w-full lg:w-3/4 mx-auto">
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

        {{-- Main Card --}}
        <div class="bg-[#eff4ff] border border-[#bfdbfe] rounded-xl shadow-sm p-6">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <h2 class="text-sm font-medium text-slate-700">Header Görünüm Ayarları</h2>
            </div>

            {{-- Accordion Sections --}}
            <div class="flex flex-col gap-2">
                {{-- Accordion 1: Logo --}}
                <x-mary-collapse name="group1" group="settings" separator
                    class="bg-white border border-slate-200 shadow-sm rounded-lg">
                    <x-slot:heading>
                        <div class="flex items-center gap-3">
                            <x-mary-icon name="o-photo" class="w-5 h-5 text-blue-500" />
                            <span class="font-semibold text-slate-700">Logo Ayarları</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start py-2">
                            {{-- Site Name --}}
                            <div class="lg:col-span-3">
                                <x-mary-input label="Site Adı" wire:model="site_name"
                                    hint="Logo yüklenmediğinde gözükür" />
                            </div>

                            {{-- Logo --}}
                            <div class="lg:col-span-6">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                                <div class="flex items-center gap-2">
                                    @if($current_logo_path)
                                        <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Current Logo"
                                            class="h-10 object-contain border border-slate-200 rounded p-1 bg-white">
                                    @endif
                                    <div class="flex-1">
                                        <x-mary-file wire:model="logo" accept=".png,.jpg,.jpeg,.svg"
                                            hint="PNG, JPG veya SVG, max 2MB" />
                                    </div>
                                    <div class="flex gap-1">
                                        <button type="button" wire:click="$set('logo_scale', 1)"
                                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1 ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-700' }}">
                                            1x
                                        </button>
                                        <button type="button" wire:click="$set('logo_scale', 1.5)"
                                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1.5 ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-700' }}">
                                            1.5x
                                        </button>
                                        <button type="button" wire:click="$set('logo_scale', 2)"
                                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 2 ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-700' }}">
                                            2x
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Favicon --}}
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                                <div class="flex items-center gap-2">
                                    @if($current_favicon_path)
                                        <img src="{{ asset('storage/' . $current_favicon_path) }}" alt="Current Favicon"
                                            class="w-8 h-8 object-contain border border-slate-200 rounded p-1">
                                    @endif
                                    <div class="flex-1">
                                        <x-mary-file wire:model="favicon" accept=".ico,.png"
                                            hint="ICO veya PNG, max 512KB" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot:content>
                </x-mary-collapse>

                {{-- Accordion 2: Menü --}}
                <x-mary-collapse name="group2" group="settings" separator
                    class="bg-white border border-slate-200 shadow-sm rounded-lg">
                    <x-slot:heading>
                        <div class="flex items-center gap-3">
                            <x-mary-icon name="o-bars-3" class="w-5 h-5 text-emerald-500" />
                            <span class="font-semibold text-slate-700">Menü Renk Ayarları</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-2">
                            {{-- Header Background Color --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Header Arka Plan
                                    Rengi</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="header_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="header_bg_color" placeholder="#3D3373" class="flex-1" />
                                </div>
                            </div>

                            {{-- Menu Background Color --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Menü Arka Plan
                                    Rengi</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="menu_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="menu_bg_color" placeholder="#3D3373" class="flex-1" />
                                </div>
                            </div>

                            {{-- Menu Text Color --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Menü Yazı Rengi</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="menu_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="menu_text_color" placeholder="#ffffff" class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </x-slot:content>
                </x-mary-collapse>

                {{-- Accordion 3: Kenarlık ve Yazı Rengi --}}
                <x-mary-collapse name="group3" group="settings" separator
                    class="bg-white border border-slate-200 shadow-sm rounded-lg">
                    <x-slot:heading>
                        <div class="flex items-center gap-3">
                            <x-mary-icon name="o-swatch" class="w-5 h-5 text-purple-500" />
                            <span class="font-semibold text-slate-700">Kenarlık ve Yazı Rengi</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
                            {{-- Header Icon Color --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Header İkon ve Yazı
                                    Rengi</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="header_icon_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="header_icon_color" placeholder="#ffffff" class="flex-1"
                                        hint="Sağ taraftaki bildirim zili ve kullanıcı adı rengi" />
                                </div>
                            </div>

                            {{-- Header Border --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Header Alt Kenarlık</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Renk</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" wire:model.live="header_border_color"
                                                class="w-10 h-10 rounded border border-slate-200 cursor-pointer">
                                            <x-mary-input wire:model.live="header_border_color" placeholder="#000000"
                                                class="flex-1" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Kalınlık (px)</label>
                                        <x-mary-input type="number" wire:model.live="header_border_width"
                                            placeholder="0" min="0" max="20" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot:content>
                </x-mary-collapse>
            </div>

            {{-- Footer Actions --}}
            <div class="pt-6 mt-6 border-t border-slate-200 flex justify-end gap-3">
                <x-mary-button label="Ayarları Kaydet"
                    class="btn-primary bg-emerald-500 hover:bg-emerald-600 border-none text-white" wire:click="save"
                    spinner="save" />
            </div>
        </div>
    </div>
</div>