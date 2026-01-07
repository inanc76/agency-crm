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
            // Deactivate all existing settings
            PanelSetting::query()->update(['is_active' => false]);

            // Get or create the active setting
            $setting = PanelSetting::where('is_active', false)->first() ?? new PanelSetting();

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
            'base_text_color' => '#475569',
            'heading_color' => '#0f172a',

            'input_focus_ring_color' => '#6366f1',
            'input_border_color' => '#cbd5e1',
            'input_error_ring_color' => '#ef4444',
            'input_error_border_color' => '#ef4444',
            'input_error_text_color' => '#ef4444',
            'input_vertical_padding' => '0.5rem',
            'input_border_radius' => '0.375rem',

            'primary_button_bg_color' => '#4f46e5',
            'primary_button_text_color' => '#ffffff',
            'primary_button_hover_color' => '#4338ca',

            'secondary_button_border_color' => '#e2e8f0',
            'secondary_button_text_color' => '#475569',

            'action_link_color' => '#4f46e5',

            'card_bg_color' => '#eff4ff',
            'card_border_color' => '#bfdbfe',
            'card_border_radius' => '0.75rem',
        ]);
    }
}
