<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use Toast;
    use WithFileUploads;

    public string $site_name = 'MEDIACLICK';
    public $favicon;
    public $logo;
    public float $logo_scale = 1.0;

    public ?string $current_favicon_path = null;
    public ?string $current_logo_path = null;

    // Header Color Settings
    public string $header_bg_color = '#3D3373';
    public string $header_border_color = '#000000';
    public int $header_border_width = 0;
    public string $header_active_item_bg_color = '#ffffff';
    public string $header_active_item_text_color = '#4f46e5';
    public string $header_icon_color = '#ffffff';

    // Sidebar Settings
    public string $sidebar_bg_color = '#3D3373';
    public string $sidebar_text_color = '#ffffff';
    public string $sidebar_hover_bg_color = '#4338ca';
    public string $sidebar_hover_text_color = '#ffffff';
    public string $sidebar_active_item_bg_color = '#4f46e5';
    public string $sidebar_active_item_text_color = '#ffffff';

    // User Menu & Dropdown
    public string $avatar_gradient_start_color = '#c084fc';
    public string $avatar_gradient_end_color = '#9333ea';
    public string $dropdown_header_bg_start_color = '#f5f3ff';
    public string $dropdown_header_bg_end_color = '#eef2ff';
    public string $notification_badge_color = '#ef4444';

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->site_name = $setting->site_name;
            $this->logo_scale = $setting->logo_scale ?? 1.0;
            $this->header_bg_color = $setting->header_bg_color;
            // Fix legacy transparent value
            $this->header_border_color = ($setting->header_border_color === 'transparent' || !str_starts_with($setting->header_border_color, '#')) ? '#000000' : $setting->header_border_color;
            $this->header_border_width = $setting->header_border_width ?? 0;
            $this->header_icon_color = $setting->header_icon_color ?? '#ffffff';

            $this->current_favicon_path = $setting->favicon_path;
            $this->current_logo_path = $setting->logo_path;

            $this->header_active_item_bg_color = $setting->header_active_item_bg_color ?? '#ffffff';
            $this->header_active_item_text_color = $setting->header_active_item_text_color ?? '#4f46e5';

            $this->sidebar_bg_color = $setting->sidebar_bg_color ?? '#3D3373';
            $this->sidebar_text_color = $setting->sidebar_text_color ?? '#ffffff';
            $this->sidebar_hover_bg_color = $setting->sidebar_hover_bg_color ?? '#4338ca';
            $this->sidebar_hover_text_color = $setting->sidebar_hover_text_color ?? '#ffffff';
            $this->sidebar_active_item_bg_color = $setting->sidebar_active_item_bg_color ?? '#4f46e5';
            $this->sidebar_active_item_text_color = $setting->sidebar_active_item_text_color ?? '#ffffff';

            $this->avatar_gradient_start_color = $setting->avatar_gradient_start_color ?? '#c084fc';
            $this->avatar_gradient_end_color = $setting->avatar_gradient_end_color ?? '#9333ea';
            $this->dropdown_header_bg_start_color = $setting->dropdown_header_bg_start_color ?? '#f5f3ff';
            $this->dropdown_header_bg_end_color = $setting->dropdown_header_bg_end_color ?? '#eef2ff';
            $this->notification_badge_color = $setting->notification_badge_color ?? '#ef4444';
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
            'header_border_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_border_width' => 'required|integer|min:0|max:20',
            'header_active_item_bg_color' => 'nullable|string',
            'header_active_item_text_color' => 'nullable|string',
            'header_icon_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            'sidebar_bg_color' => 'nullable|string',
            'sidebar_text_color' => 'nullable|string',
            'sidebar_hover_bg_color' => 'nullable|string',
            'sidebar_hover_text_color' => 'nullable|string',
            'sidebar_active_item_bg_color' => 'nullable|string',
            'sidebar_active_item_text_color' => 'nullable|string',

            'avatar_gradient_start_color' => 'nullable|string',
            'avatar_gradient_end_color' => 'nullable|string',
            'dropdown_header_bg_start_color' => 'nullable|string',
            'dropdown_header_bg_end_color' => 'nullable|string',
            'notification_badge_color' => 'nullable|string',
        ]);

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        $this->dispatch('theme-updated');
        $this->success('Header ve Görünüm Ayarları Kaydedildi');
    }
}; ?>

<div
    class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
        <h2 class="text-sm font-medium text-skin-heading">Header & Sidebar Görünüm Ayarları</h2>
        <x-mary-button label="Kaydet" icon="o-check" class="btn-sm text-white"
            style="background-color: var(--btn-save-bg); border-color: var(--btn-save-border);" wire:click="save"
            spinner="save" />
    </div>

    {{-- Accordion Sections --}}
    <div class="flex flex-col gap-2">
        @include('livewire.settings.theme.parts.logo')
        @include('livewire.settings.theme.parts.sidebar')
        @include('livewire.settings.theme.parts.topbar')
    </div>
</div>