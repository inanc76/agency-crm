<?php

namespace App\Livewire\Settings\Traits;

use App\Repositories\PanelSettingRepository;
use Illuminate\Support\Facades\Cache;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasThemeActions Trait (UI Theme Management)                                               ║
 * ║  🎯 ANA GÖREV: Panel görünüm ayarlarının yüklenmesi ve kaydedilmesi                                            ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • mountHasThemeSettings(): Tüm tema değişkenlerini DB'den yükler ve fallback'ler uygular                       ║
 * ║  • saveThemeSettings(): Validasyondan geçen tema verilerini kaydeder ve cache'i temizler                        ║
 * ║  • resetThemeToDefaults(): Tüm ayarları varsayılan değerlere sıfırlar                                           ║
 * ║                                                                                                                  ║
 * ║  🎨 RENK YÖNETİMİ STRATEJİSİ:                                                                                   ║
 * ║  • Fallback Pattern: Her renk için null-coalescing (??) ile varsayılan değer                                   ║
 * ║  • Transparent Guard: 'transparent' değerleri hex'e çevrilir (header_border_color örneği)                      ║
 * ║  • Format Validation: str_starts_with('#') ile hex format kontrolü                                              ║
 * ║                                                                                                                  ║
 * ║  🔗 CSS VARIABLE MAPPING:                                                                                       ║
 * ║  • $xxx_bg_color      → --xxx-bg      (Arka plan renkleri)                                                     ║
 * ║  • $xxx_text_color    → --xxx-text    (Yazı renkleri)                                                          ║
 * ║  • $xxx_border_color  → --xxx-border  (Kenarlık renkleri)                                                      ║
 * ║  • $xxx_hover_color   → --xxx-hover   (Hover durumu renkleri)                                                  ║
 * ║                                                                                                                  ║
 * ║  🌈 COLOR-MIX KULLANIMI (Dashboard Stats):                                                                      ║
 * ║  • Dashboard stats kartlarında "color-mix(in srgb, $color, white 90%)" kullanılır                              ║
 * ║  • Bu teknik: Ana renkten %90 beyazla karıştırarak pastel ton oluşturur                                        ║
 * ║  • Örnek: #3b82f6 (mavi) → color-mix ile açık mavi arka plan üretilir                                          ║
 * ║                                                                                                                  ║
 * ║  📦 TRAIT BAĞIMLILIKLARI:                                                                                       ║
 * ║  • HasThemeProperties: Tüm public property tanımları                                                           ║
 * ║  • HasThemeValidation: themeRules() validasyon kuralları                                                       ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK:                                                                                                   ║
 * ║  • Cache Invalidation: Kaydetme sonrası 'theme_settings' cache'i temizlenir                                    ║
 * ║  • Repository Pattern: Doğrudan DB erişimi yerine PanelSettingRepository kullanılır                            ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasThemeActions
{
    public function mountHasThemeSettings(): void
    {
        $repository = app(PanelSettingRepository::class);
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

            $this->table_avatar_bg_color = $setting->table_avatar_bg_color ?? '#f1f5f9';
            $this->table_avatar_border_color = $setting->table_avatar_border_color ?? '#e2e8f0';
            $this->table_avatar_text_color = $setting->table_avatar_text_color ?? '#475569';

            // Load New Settings
            $this->sidebar_bg_color = $setting->sidebar_bg_color ?? '#3D3373';
            $this->sidebar_text_color = $setting->sidebar_text_color ?? '#ffffff';
            $this->sidebar_hover_bg_color = $setting->sidebar_hover_bg_color ?? '#4338ca';
            $this->sidebar_hover_text_color = $setting->sidebar_hover_text_color ?? '#ffffff';
            $this->sidebar_active_item_bg_color = $setting->sidebar_active_item_bg_color ?? '#4f46e5';
            $this->sidebar_active_item_text_color = $setting->sidebar_active_item_text_color ?? '#ffffff';

            $this->header_active_item_bg_color = $setting->header_active_item_bg_color ?? '#ffffff';
            $this->header_active_item_text_color = $setting->header_active_item_text_color ?? '#4f46e5';

            $this->dashboard_card_bg_color = $setting->dashboard_card_bg_color ?? '#eff4ff';
            $this->dashboard_card_text_color = $setting->dashboard_card_text_color ?? '#475569';
            $this->dashboard_stats_1_color = $setting->dashboard_stats_1_color ?? '#3b82f6';
            $this->dashboard_stats_2_color = $setting->dashboard_stats_2_color ?? '#14b8a6';
            $this->dashboard_stats_3_color = $setting->dashboard_stats_3_color ?? '#f59e0b';

            $this->avatar_gradient_start_color = $setting->avatar_gradient_start_color ?? '#c084fc';
            $this->avatar_gradient_end_color = $setting->avatar_gradient_end_color ?? '#9333ea';
            $this->dropdown_header_bg_start_color = $setting->dropdown_header_bg_start_color ?? '#f5f3ff';
            $this->dropdown_header_bg_end_color = $setting->dropdown_header_bg_end_color ?? '#eef2ff';
            $this->notification_badge_color = $setting->notification_badge_color ?? '#ef4444';
        }
    }

    public function saveThemeSettings(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate($this->themeRules());

        $repository->saveSettings($data);

        Cache::forget('theme_settings');

        $this->success('Ayarlar Kaydedildi', 'Tema ayarları başarıyla güncellendi. Tasarım tüm sisteme uygulandı.');
    }

    public function resetThemeToDefaults(): void
    {
        $repository = app(PanelSettingRepository::class);
        $repository->resetToDefaults();

        $this->mountHasThemeSettings();
        $this->success('Varsayılana Döndürüldü', 'Tema ayarları varsayılan değerlere sıfırlandı.');
    }
}
