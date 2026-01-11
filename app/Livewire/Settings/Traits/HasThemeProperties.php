<?php

namespace App\Livewire\Settings\Traits;

use Livewire\WithFileUploads;
use Mary\Traits\Toast;

trait HasThemeProperties
{
    use WithFileUploads, Toast;

    public string $activeTab = 'theme';

    public string $site_name = 'MEDIACLICK';
    public $favicon;
    public $logo;
    public float $logo_scale = 1.0;
    public string $header_bg_color = '#3D3373';
    public string $menu_bg_color = '#3D3373';
    public string $menu_text_color = '#ffffff';
    public string $header_icon_color = '#ffffff';
    public string $header_border_color = '#000000';
    public int $header_border_width = 0;

    public ?string $current_favicon_path = null;
    public ?string $current_logo_path = null;

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

    public string $table_avatar_bg_color = '#f1f5f9';
    public string $table_avatar_border_color = '#e2e8f0';
    public string $table_avatar_text_color = '#475569';

    // Sidebar Settings
    public string $sidebar_bg_color = '#3D3373';
    public string $sidebar_text_color = '#ffffff';
    public string $sidebar_hover_bg_color = '#4338ca';
    public string $sidebar_hover_text_color = '#ffffff';
    public string $sidebar_active_item_bg_color = '#4f46e5';
    public string $sidebar_active_item_text_color = '#ffffff';

    // Header Settings
    public string $header_active_item_bg_color = '#ffffff';
    public string $header_active_item_text_color = '#4f46e5';

    // Dashboard Settings
    public string $dashboard_card_bg_color = '#eff4ff';
    public string $dashboard_card_text_color = '#475569';
    public string $dashboard_stats_1_color = '#3b82f6';
    public string $dashboard_stats_2_color = '#14b8a6';
    public string $dashboard_stats_3_color = '#f59e0b';

    // User Menu Settings
    public string $avatar_gradient_start_color = '#c084fc';
    public string $avatar_gradient_end_color = '#9333ea';
    public string $dropdown_header_bg_start_color = '#f5f3ff';
    public string $dropdown_header_bg_end_color = '#eef2ff';
    public string $notification_badge_color = '#ef4444';
}
