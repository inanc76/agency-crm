<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🎨 PanelSetting Model - UI Tema ve Görünüm Ayarları
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * PanelSetting, sistemin TÜM UI TEMA AYARLARINI saklar:
 *
 * **Kategoriler:**
 * 1. **Branding**: site_name, logo_path, favicon_path, logo_scale
 * 2. **Typography**: font_family, base_text_color, heading_color, font sizes
 * 3. **Header & Menu**: header_bg_color, menu_bg_color, menu_text_color, borders
 * 4. **Sidebar**: sidebar_bg_color, sidebar_text_color, hover/active states
 * 5. **Inputs**: input_border_color, focus_ring_color, error states, padding, radius
 * 6. **Buttons**: Granular control (create, edit, delete, cancel, save) - bg, text, hover, border
 * 7. **Cards**: card_bg_color, card_border_color, card_border_radius, table_hover
 * 8. **Dashboard**: dashboard_card_bg_color, stats colors (1-3)
 * 9. **Avatar & Dropdowns**: gradient colors, notification badge
 *
 * **Kullanım:**
 * - Ayarlar sayfasında yönetilir (/dashboard/settings/panel)
 * - CSS variables olarak inject edilir (--card-bg-color, vb.)
 * - Tüm UI bileşenleri bu değişkenleri kullanır (Zero Hard-Coding)
 * - is_active: Tek bir kayıt aktif olmalı
 *
 * **Önemli:**
 * - 100+ alan içerir (granular control)
 * - Guarded: Tüm alanlar mass-assignable
 * - Değişiklikler anında UI'ya yansır (CSS variables)
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class PanelSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'site_name',
        'favicon_path',
        'logo_path',
        'logo_scale',
        'header_bg_color',
        'menu_bg_color',
        'menu_text_color',
        'header_icon_color',
        'header_border_color',
        'header_border_width',
        'is_active',
        // Typography
        'font_family',
        'page_bg_color',
        'base_text_color',
        'heading_color',
        'label_font_size',
        'input_font_size',
        'heading_font_size',
        'error_font_size',
        'helper_font_size',
        // Input & Validation
        'input_focus_ring_color',
        'input_border_color',
        'input_error_ring_color',
        'input_error_border_color',
        'input_error_text_color',
        'input_vertical_padding',
        'input_border_radius',
        // Buttons
        // Buttons - Granular Control
        'btn_create_bg_color',
        'btn_create_text_color',
        'btn_create_hover_color',
        'btn_create_border_color',
        'btn_edit_bg_color',
        'btn_edit_text_color',
        'btn_edit_hover_color',
        'btn_edit_border_color',
        'btn_delete_bg_color',
        'btn_delete_text_color',
        'btn_delete_hover_color',
        'btn_delete_border_color',
        'btn_cancel_bg_color',
        'btn_cancel_text_color',
        'btn_cancel_hover_color',
        'btn_cancel_border_color',
        'btn_save_bg_color',
        'btn_save_text_color',
        'btn_save_hover_color',
        'btn_save_border_color',

        'action_link_color',
        'active_tab_color',
        // Cards
        'card_bg_color',
        'card_border_color',
        'card_border_radius',
        'table_hover_bg_color',
        'table_hover_text_color',
        // List Card
        'list_card_bg_color',
        'list_card_border_color',
        'list_card_link_color',
        'list_card_hover_color',
        // Table Avatar
        'table_avatar_bg_color',
        'table_avatar_border_color',
        'table_avatar_text_color',
        // New Sidebar Settings
        'sidebar_bg_color',
        'sidebar_text_color',
        'sidebar_hover_bg_color',
        'sidebar_hover_text_color',
        'sidebar_active_item_bg_color',
        'sidebar_active_item_text_color',
        // New Header Active Items
        'header_active_item_bg_color',
        'header_active_item_text_color',
        // New Dashboard Colors
        'dashboard_card_bg_color',
        'dashboard_card_text_color',
        'dashboard_stats_1_color',
        'dashboard_stats_2_color',
        'dashboard_stats_3_color',
        // New User Menu / Header Dropdown
        'avatar_gradient_start_color',
        'avatar_gradient_end_color',
        'dropdown_header_bg_start_color',
        'dropdown_header_bg_end_color',
        'notification_badge_color',
        // PDF Template Settings
        'pdf_logo_path',
        'pdf_logo_height',
        'pdf_header_bg_color',
        'pdf_header_text_color',
        'pdf_font_family',
        'pdf_primary_color',
        'pdf_secondary_color',
        'pdf_footer_text',
        'pdf_discount_color',
        'pdf_total_color',
        'pdf_table_header_bg_color',
        'pdf_table_header_text_color',
        'pdf_limit_eager_loads', // Optional optimization
        // Existing PDF Fields
        'pdf_terms_conditions',
        'pdf_bank_details',
        'pdf_payment_terms',
        'pdf_notes',
        'pdf_show_logo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
