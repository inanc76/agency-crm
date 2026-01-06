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
            'menu_bg_color' => 'rgba(255, 255, 255, 0.1)',
            'menu_text_color' => '#ffffff',
            'header_icon_color' => '#ffffff',
            'header_border_color' => 'transparent',
            'header_border_width' => 0,
        ]);
    }
}
