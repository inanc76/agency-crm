<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
