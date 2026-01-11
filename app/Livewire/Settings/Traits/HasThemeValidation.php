<?php

namespace App\Livewire\Settings\Traits;

trait HasThemeValidation
{
    protected function themeRules(): array
    {
        return [
            'site_name' => 'required|min:3',
            'favicon' => 'nullable|image|max:1024',
            'logo' => 'nullable|image|max:2048',
            'logo_scale' => 'nullable|numeric|min:0.1|max:3.0',
            'header_bg_color' => 'required|string',
            'menu_bg_color' => 'required|string',
            'menu_text_color' => 'required|string',
            'header_icon_color' => 'required|string',
            'header_border_color' => 'required|string',
            'header_border_width' => 'required|integer|min:0|max:10',

            'font_family' => 'required|string',
            'page_bg_color' => 'required|string',
            'base_text_color' => 'required|string',
            'heading_color' => 'required|string',

            'input_focus_ring_color' => 'required|string',
            'input_border_color' => 'required|string',
            'input_error_ring_color' => 'required|string',
            'input_error_border_color' => 'required|string',
            'input_error_text_color' => 'required|string',
            'input_vertical_padding' => 'required|string',
            'input_border_radius' => 'required|string',

            'label_font_size' => 'required|integer|min:8|max:24',
            'input_font_size' => 'required|integer|min:8|max:24',
            'heading_font_size' => 'required|integer|min:8|max:48',
            'error_font_size' => 'required|integer|min:8|max:20',
            'helper_font_size' => 'required|integer|min:8|max:20',

            'btn_create_bg_color' => 'required|string',
            'btn_create_text_color' => 'required|string',
            'btn_create_hover_color' => 'required|string',
            'btn_create_border_color' => 'required|string',

            'btn_edit_bg_color' => 'required|string',
            'btn_edit_text_color' => 'required|string',
            'btn_edit_hover_color' => 'required|string',
            'btn_edit_border_color' => 'required|string',

            'btn_delete_bg_color' => 'required|string',
            'btn_delete_text_color' => 'required|string',
            'btn_delete_hover_color' => 'required|string',
            'btn_delete_border_color' => 'required|string',

            'btn_cancel_bg_color' => 'required|string',
            'btn_cancel_text_color' => 'required|string',
            'btn_cancel_hover_color' => 'required|string',
            'btn_cancel_border_color' => 'required|string',

            'btn_save_bg_color' => 'required|string',
            'btn_save_text_color' => 'required|string',
            'btn_save_hover_color' => 'required|string',
            'btn_save_border_color' => 'required|string',

            'action_link_color' => 'required|string',
            'active_tab_color' => 'required|string',

            'card_bg_color' => 'required|string',
            'card_border_color' => 'required|string',
            'card_border_radius' => 'required|string',

            'table_hover_bg_color' => 'required|string',
            'table_hover_text_color' => 'required|string',

            'list_card_bg_color' => 'required|string',
            'list_card_border_color' => 'required|string',
            'list_card_link_color' => 'required|string',
            'list_card_hover_color' => 'required|string',

            'table_avatar_bg_color' => 'required|string',
            'table_avatar_border_color' => 'required|string',
            'table_avatar_text_color' => 'required|string',

            'sidebar_bg_color' => 'required|string',
            'sidebar_text_color' => 'required|string',
            'sidebar_hover_bg_color' => 'required|string',
            'sidebar_hover_text_color' => 'required|string',
            'sidebar_active_item_bg_color' => 'required|string',
            'sidebar_active_item_text_color' => 'required|string',

            'header_active_item_bg_color' => 'required|string',
            'header_active_item_text_color' => 'required|string',

            'dashboard_card_bg_color' => 'required|string',
            'dashboard_card_text_color' => 'required|string',
            'dashboard_stats_1_color' => 'required|string',
            'dashboard_stats_2_color' => 'required|string',
            'dashboard_stats_3_color' => 'required|string',

            'avatar_gradient_start_color' => 'required|string',
            'avatar_gradient_end_color' => 'required|string',
            'dropdown_header_bg_start_color' => 'required|string',
            'dropdown_header_bg_end_color' => 'required|string',
            'notification_badge_color' => 'required|string',
        ];
    }
}
