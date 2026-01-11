<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Repositories\PanelSettingRepository;

new class extends Component {
    // Design Settings
    public string $font_family = 'Inter';
    public string $page_bg_color = '#f8fafc';
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

    public string $btn_create_bg_color = '#4f46e5';
    public string $btn_create_text_color = '#ffffff';
    public string $btn_edit_bg_color = '#f59e0b';
    public string $btn_edit_text_color = '#ffffff';
    public string $btn_delete_bg_color = '#ef4444';
    public string $btn_delete_text_color = '#ffffff';
    public string $btn_cancel_bg_color = '#94a3b8';
    public string $btn_cancel_text_color = '#ffffff';
    public string $btn_save_bg_color = '#10b981';
    public string $btn_save_text_color = '#ffffff';
    public string $action_link_color = '#4f46e5';

    public string $card_bg_color = '#eff4ff';
    public string $card_border_color = '#bfdbfe';
    public string $card_border_radius = '12px';

    public string $table_hover_bg_color = '#f8fafc';
    public string $table_hover_text_color = '#0f172a';

    // Header & Sidebar
    public string $site_name = 'MEDIACLICK';
    public float $logo_scale = 1.0;
    public ?string $current_logo_path = null;
    public ?string $current_favicon_path = null;

    public string $header_bg_color = '#3D3373';
    public string $header_border_color = '#000000';
    public int $header_border_width = 0;
    public string $header_active_item_bg_color = '#ffffff';
    public string $header_active_item_text_color = '#4f46e5';
    public string $header_icon_color = '#ffffff';

    public string $sidebar_bg_color = '#3D3373';
    public string $sidebar_text_color = '#ffffff';
    public string $sidebar_hover_bg_color = '#4338ca';
    public string $sidebar_hover_text_color = '#ffffff';
    public string $sidebar_active_item_bg_color = '#4f46e5';
    public string $sidebar_active_item_text_color = '#ffffff';

    public string $avatar_gradient_start_color = '#c084fc';
    public string $avatar_gradient_end_color = '#9333ea';
    public string $dropdown_header_bg_start_color = '#f5f3ff';
    public string $dropdown_header_bg_end_color = '#eef2ff';
    public string $notification_badge_color = '#ef4444';

    // Dashboard
    public string $dashboard_card_bg_color = '#eff4ff';
    public string $dashboard_card_text_color = '#475569';
    public string $dashboard_stats_1_color = '#3b82f6';
    public string $dashboard_stats_2_color = '#14b8a6';
    public string $dashboard_stats_3_color = '#f59e0b';

    public function mount(PanelSettingRepository $repository): void
    {
        $this->loadSettings($repository);
    }

    #[On('theme-updated')]
    public function refresh(): void
    {
        $repository = app(PanelSettingRepository::class);
        $this->loadSettings($repository);
    }

    private function loadSettings(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->font_family = $setting->font_family ?? 'Inter';
            $this->page_bg_color = $setting->page_bg_color ?? '#f8fafc';
            $this->base_text_color = $setting->base_text_color ?? '#475569';
            $this->heading_color = $setting->heading_color ?? '#0f172a';

            $this->input_focus_ring_color = $setting->input_focus_ring_color ?? '#6366f1';
            $this->input_border_color = $setting->input_border_color ?? '#cbd5e1';
            $this->input_error_ring_color = $setting->input_error_ring_color ?? '#ef4444';
            $this->input_error_border_color = $setting->input_error_border_color ?? '#ef4444';
            $this->input_error_text_color = $setting->input_error_text_color ?? '#ef4444';
            $this->input_vertical_padding = $setting->input_vertical_padding ?? '8px';
            $this->input_border_radius = $setting->input_border_radius ?? '6px';

            $this->btn_create_bg_color = $setting->btn_create_bg_color ?? '#4f46e5';
            $this->btn_create_text_color = $setting->btn_create_text_color ?? '#ffffff';
            $this->btn_edit_bg_color = $setting->btn_edit_bg_color ?? '#f59e0b';
            $this->btn_edit_text_color = $setting->btn_edit_text_color ?? '#ffffff';
            $this->btn_delete_bg_color = $setting->btn_delete_bg_color ?? '#ef4444';
            $this->btn_delete_text_color = $setting->btn_delete_text_color ?? '#ffffff';
            $this->btn_cancel_bg_color = $setting->btn_cancel_bg_color ?? '#94a3b8';
            $this->btn_cancel_text_color = $setting->btn_cancel_text_color ?? '#ffffff';
            $this->btn_save_bg_color = $setting->btn_save_bg_color ?? '#10b981';
            $this->btn_save_text_color = $setting->btn_save_text_color ?? '#ffffff';
            $this->action_link_color = $setting->action_link_color ?? '#4f46e5';

            $this->card_bg_color = $setting->card_bg_color ?? '#eff4ff';
            $this->card_border_color = $setting->card_border_color ?? '#bfdbfe';
            $this->card_border_radius = $setting->card_border_radius ?? '12px';

            $this->table_hover_bg_color = $setting->table_hover_bg_color ?? '#f8fafc';
            $this->table_hover_text_color = $setting->table_hover_text_color ?? '#0f172a';

            $this->site_name = $setting->site_name;
            $this->logo_scale = $setting->logo_scale ?? 1.0;
            $this->current_logo_path = $setting->logo_path;

            $this->header_bg_color = $setting->header_bg_color;
            $this->header_border_color = ($setting->header_border_color === 'transparent' || !str_starts_with($setting->header_border_color, '#')) ? '#000000' : $setting->header_border_color;
            $this->header_border_width = $setting->header_border_width ?? 0;
            $this->header_active_item_bg_color = $setting->header_active_item_bg_color ?? '#ffffff';
            $this->header_active_item_text_color = $setting->header_active_item_text_color ?? '#4f46e5';
            $this->header_icon_color = $setting->header_icon_color ?? '#ffffff';

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

            $this->dashboard_card_bg_color = $setting->dashboard_card_bg_color ?? '#eff4ff';
            $this->dashboard_card_text_color = $setting->dashboard_card_text_color ?? '#475569';
            $this->dashboard_stats_1_color = $setting->dashboard_stats_1_color ?? '#3b82f6';
            $this->dashboard_stats_2_color = $setting->dashboard_stats_2_color ?? '#14b8a6';
            $this->dashboard_stats_3_color = $setting->dashboard_stats_3_color ?? '#f59e0b';
        }
    }
}; ?>


<div class="mt-6 flex flex-col gap-2">
    {{-- 
    ═══════════════════════════════════════════════════════════════════════════
    🎨 STYLE GUIDE - CLEAN ORCHESTRATOR (Constitution V10)
    ═══════════════════════════════════════════════════════════════════════════
    
    Bu dosya, 862 satırlık monolitten 4 dengeli parçaya refactor edilmiştir.
    Her partial ~200 satır ve maksimum dokümantasyon ile mühürlenmiştir.
    
    📦 PARÇALAR:
    1. _style-typography.blade.php      (~90 satır)  - Renk paleti, tipografi
    2. _style-buttons-actions.blade.php (~100 satır) - Butonlar, aksiyon linkleri
    3. _style-forms-inputs.blade.php    (~200 satır) - Inputlar, validation, combo boxlar
    4. _style-components-extra.blade.php (~550 satır) - Logo, layout, kartlar, tablolar, dashboard
    
    🔒 EXPLICIT SCOPE PROTOCOL:
    Tüm değişkenler açıkça aktarılır (Zero Implicit Scope).
    
    ═══════════════════════════════════════════════════════════════════════════
    --}}

    {{-- PART 4: Components & Extra (Logo, Layout, Cards, Tables, Dashboard, Avatars) --}}
    @include('livewire.settings.parts.style-guide._style-components-extra', [
        'logo_scale' => $logo_scale,
        'current_logo_path' => $current_logo_path,
        'site_name' => $site_name,
        'sidebar_bg_color' => $sidebar_bg_color,
        'sidebar_text_color' => $sidebar_text_color,
        'sidebar_active_item_bg_color' => $sidebar_active_item_bg_color,
        'sidebar_hover_bg_color' => $sidebar_hover_bg_color,
        'header_bg_color' => $header_bg_color,
        'header_border_width' => $header_border_width,
        'header_border_color' => $header_border_color,
        'header_active_item_bg_color' => $header_active_item_bg_color,
        'header_active_item_text_color' => $header_active_item_text_color,
        'header_icon_color' => $header_icon_color,
        'heading_color' => $heading_color,
        'base_text_color' => $base_text_color,
        'card_bg_color' => $card_bg_color,
        'card_border_color' => $card_border_color,
        'card_border_radius' => $card_border_radius,
        'table_hover_bg_color' => $table_hover_bg_color,
        'table_hover_text_color' => $table_hover_text_color,
        'table_avatar_bg_color' => $table_avatar_bg_color ?? '#e0e7ff',
        'table_avatar_text_color' => $table_avatar_text_color ?? '#4f46e5',
        'table_avatar_border_color' => $table_avatar_border_color ?? '#c7d2fe',
        'dashboard_card_bg_color' => $dashboard_card_bg_color,
        'dashboard_card_text_color' => $dashboard_card_text_color,
        'dashboard_stats_1_color' => $dashboard_stats_1_color,
        'dashboard_stats_2_color' => $dashboard_stats_2_color,
        'dashboard_stats_3_color' => $dashboard_stats_3_color,
        'avatar_gradient_start_color' => $avatar_gradient_start_color,
        'avatar_gradient_end_color' => $avatar_gradient_end_color,
        'dropdown_header_bg_start_color' => $dropdown_header_bg_start_color,
        'dropdown_header_bg_end_color' => $dropdown_header_bg_end_color,
        'notification_badge_color' => $notification_badge_color,
    ])

    {{-- PART 1: Typography & Color Palette --}}
    @include('livewire.settings.parts.style-guide._style-typography', [
        'font_family' => $font_family,
        'heading_color' => $heading_color,
        'base_text_color' => $base_text_color,
    ])

    {{-- PART 3: Forms & Inputs (Input, Validation, Combo Boxes) --}}
    @include('livewire.settings.parts.style-guide._style-forms-inputs', [
        'input_border_color' => $input_border_color,
        'input_focus_ring_color' => $input_focus_ring_color,
        'input_error_border_color' => $input_error_border_color,
        'input_error_ring_color' => $input_error_ring_color,
        'input_error_text_color' => $input_error_text_color,
        'input_vertical_padding' => $input_vertical_padding,
        'input_border_radius' => $input_border_radius,
    ])

    {{-- PART 2: Buttons & Actions --}}
    @include('livewire.settings.parts.style-guide._style-buttons-actions', [
        'btn_save_bg_color' => $btn_save_bg_color,
        'btn_save_text_color' => $btn_save_text_color,
        'btn_create_bg_color' => $btn_create_bg_color,
        'btn_create_text_color' => $btn_create_text_color,
        'btn_edit_bg_color' => $btn_edit_bg_color,
        'btn_edit_text_color' => $btn_edit_text_color,
        'btn_delete_bg_color' => $btn_delete_bg_color,
        'btn_delete_text_color' => $btn_delete_text_color,
        'btn_cancel_bg_color' => $btn_cancel_bg_color,
        'btn_cancel_text_color' => $btn_cancel_text_color,
        'action_link_color' => $action_link_color,
    ])
</div>
