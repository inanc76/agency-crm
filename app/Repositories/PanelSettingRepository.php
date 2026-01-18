<?php

namespace App\Repositories;

use App\Models\PanelSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PanelSettingRepository
{
    public function getActiveSetting(): ?PanelSetting
    {
        return PanelSetting::where('is_active', true)->first();
    }

    public function saveSettings(array $data): PanelSetting
    {
        return DB::transaction(function () use ($data) {
            // 1. Find the currently active setting to update it directly
            // We use lockForUpdate to prevent race conditions
            $setting = PanelSetting::where('is_active', true)->first();

            // 2. If no active setting found, try to find ANY setting or create new
            if (! $setting) {
                $setting = PanelSetting::latest()->first() ?? new PanelSetting;
            }

            // Handle favicon upload
            if (isset($data['favicon']) && $data['favicon']) {
                // Delete old favicon if exists
                if ($setting->favicon_path) {
                    Storage::disk('public')->delete($setting->favicon_path);
                }

                $faviconPath = $data['favicon']->store('panel', 'public');
                $data['favicon_path'] = $faviconPath;
                unset($data['favicon']);
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo']) {
                // Delete old logo if exists
                if ($setting->logo_path) {
                    Storage::disk('public')->delete($setting->logo_path);
                }

                $logoPath = $data['logo']->store('panel', 'public');
                $data['logo_path'] = $logoPath;
                unset($data['logo']);
            }

            // Set as active
            $data['is_active'] = true;

            $setting->fill($data);
            $setting->save();

            // 3. Ensure Singleton: Deactivate all OTHER settings
            PanelSetting::where('id', '!=', $setting->id)->update(['is_active' => false]);

            return $setting;
        });
    }

    public function resetToDefaults(): PanelSetting
    {
        return $this->saveSettings([
            'site_name' => 'MEDIACLICK',
            'logo_scale' => 1.0,
            'header_bg_color' => '#3D3373',
            'menu_bg_color' => '#3D3373',
            'menu_text_color' => '#ffffff',
            'header_icon_color' => '#ffffff',
            'header_border_color' => '#000000',
            'header_border_width' => 0,

            // Design Defaults
            'font_family' => 'Inter',
            'page_bg_color' => '#f8fafc',
            'base_text_color' => '#475569',
            'heading_color' => '#0f172a',
            'label_font_size' => 14,
            'input_font_size' => 16,
            'heading_font_size' => 18,
            'error_font_size' => 12,
            'helper_font_size' => 12,

            'input_focus_ring_color' => '#6366f1',
            'input_border_color' => '#cbd5e1',
            'input_error_ring_color' => '#ef4444',
            'input_error_border_color' => '#ef4444',
            'input_error_text_color' => '#ef4444',
            'input_vertical_padding' => '8px',
            'input_border_radius' => '6px',

            'btn_create_bg_color' => '#4f46e5',
            'btn_create_text_color' => '#ffffff',
            'btn_create_hover_color' => '#4338ca',
            'btn_create_border_color' => '#4f46e5',

            'btn_edit_bg_color' => '#f59e0b',
            'btn_edit_text_color' => '#ffffff',
            'btn_edit_hover_color' => '#d97706',
            'btn_edit_border_color' => '#f59e0b',

            'btn_delete_bg_color' => '#ef4444',
            'btn_delete_text_color' => '#ffffff',
            'btn_delete_hover_color' => '#dc2626',
            'btn_delete_border_color' => '#ef4444',

            'btn_cancel_bg_color' => '#94a3b8',
            'btn_cancel_text_color' => '#ffffff',
            'btn_cancel_hover_color' => '#64748b',
            'btn_cancel_border_color' => '#94a3b8',

            'btn_save_bg_color' => '#10b981',
            'btn_save_text_color' => '#ffffff',
            'btn_save_hover_color' => '#059669',
            'btn_save_border_color' => '#10b981',

            'action_link_color' => '#4f46e5',
            'active_tab_color' => '#4f46e5',

            'card_bg_color' => '#eff4ff',
            'card_border_color' => '#bfdbfe',
            'card_border_radius' => '12px',
            'table_hover_bg_color' => '#f8fafc',
            'table_hover_text_color' => '#0f172a',

            'list_card_bg_color' => '#ffffff',
            'list_card_border_color' => '#e2e8f0',
            'list_card_link_color' => '#4f46e5',
            'list_card_hover_color' => '#f8fafc',

            'table_avatar_bg_color' => '#f1f5f9',
            'table_avatar_border_color' => '#e2e8f0',
            'table_avatar_text_color' => '#475569',

            'table_header_bg_color' => '#f8fafc',
            'table_header_text_color' => '#1e293b',
            'table_divide_color' => '#f1f5f9',
            'table_item_name_size' => '13px',
            'table_item_name_weight' => '500',

            // Sidebar Defaults
            'sidebar_bg_color' => '#3D3373',
            'sidebar_text_color' => '#ffffff',
            'sidebar_hover_bg_color' => '#4338ca',
            'sidebar_hover_text_color' => '#ffffff',
            'sidebar_active_item_bg_color' => '#4f46e5',
            'sidebar_active_item_text_color' => '#ffffff',

            // Header defaults
            'header_active_item_bg_color' => '#ffffff',
            'header_active_item_text_color' => '#4f46e5',

            // Dashboard Defaults
            'dashboard_card_bg_color' => '#eff4ff',
            'dashboard_card_text_color' => '#475569',
            'dashboard_stats_1_color' => '#3b82f6',
            'dashboard_stats_2_color' => '#14b8a6',
            'dashboard_stats_3_color' => '#f59e0b',

            // User Menu Defaults
            'avatar_gradient_start_color' => '#c084fc',
            'avatar_gradient_end_color' => '#9333ea',
            'dropdown_header_bg_start_color' => '#f5f3ff',
            'dropdown_header_bg_end_color' => '#eef2ff',
            'notification_badge_color' => '#ef4444',
        ]);
    }
}
