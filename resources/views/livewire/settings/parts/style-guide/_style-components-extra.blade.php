{{--
═══════════════════════════════════════════════════════════════════════════
🧩 STYLE GUIDE PART 4: COMPONENTS & EXTRA ELEMENTS (ORCHESTRATOR)
═══════════════════════════════════════════════════════════════════════════

📦 PACKAGE: resources/views/livewire/settings/parts/style-guide
📄 FILE: _style-components-extra.blade.php
🏗️ CONSTITUTION: V11 (ATOMIC)

💼 İş Mantığı Şerhi: Bu dosya, alt parçaların (Logo, Layout, Cards/Tables, Dashboard Visuals)
birleştirildiği merkezi yönetim noktasıdır.
📝 Kullanım Notu: Tüm alt parçalar @include ile ve explicit scope ile dahil edilir.

--}}

<div class="space-y-4">
    {{-- 1. Logo & Branding --}}
    @include('livewire.settings.parts.style-guide._style-logo-branding', [
        'logo_scale' => $logo_scale,
        'current_logo_path' => $current_logo_path,
        'site_name' => $site_name
    ])

    {{-- 2. Layout Elements --}}
    @include('livewire.settings.parts.style-guide._style-layout-elements', [
        'sidebar_bg_color' => $sidebar_bg_color,
        'sidebar_text_color' => $sidebar_text_color,
        'sidebar_active_item_bg_color' => $sidebar_active_item_bg_color,
        'header_bg_color' => $header_bg_color,
        'header_border_width' => $header_border_width,
        'header_border_color' => $header_border_color,
        'header_active_item_bg_color' => $header_active_item_bg_color,
        'header_active_item_text_color' => $header_active_item_text_color,
        'header_icon_color' => $header_icon_color
    ])

    {{-- 3. Cards & Tables --}}
    @include('livewire.settings.parts.style-guide._style-cards-tables', [
        'card_bg_color' => $card_bg_color,
        'card_border_color' => $card_border_color,
        'card_border_radius' => $card_border_radius,
        'heading_color' => $heading_color,
        'base_text_color' => $base_text_color,
        'table_hover_bg_color' => $table_hover_bg_color,
        'table_hover_text_color' => $table_hover_text_color,
        'table_avatar_bg_color' => $table_avatar_bg_color,
        'table_avatar_text_color' => $table_avatar_text_color,
        'table_avatar_border_color' => $table_avatar_border_color,
        'table_header_bg_color' => $table_header_bg_color,
        'table_header_text_color' => $table_header_text_color,
        'table_divide_color' => $table_divide_color,
        'table_item_name_size' => $table_item_name_size,
        'table_item_name_weight' => $table_item_name_weight,
        'list_card_link_color' => $list_card_link_color ?? '#4f46e5'
    ])

    {{-- 4. Dashboard Visuals --}}
    @include('livewire.settings.parts.style-guide._style-dashboard-visuals', [
        'table_avatar_bg_color' => $table_avatar_bg_color,
        'table_avatar_text_color' => $table_avatar_text_color,
        'table_avatar_border_color' => $table_avatar_border_color,
        'avatar_gradient_start_color' => $avatar_gradient_start_color,
        'avatar_gradient_end_color' => $avatar_gradient_end_color,
        'dashboard_card_bg_color' => $dashboard_card_bg_color,
        'dashboard_card_text_color' => $dashboard_card_text_color,
        'dashboard_stats_1_color' => $dashboard_stats_1_color,
        'dashboard_stats_2_color' => $dashboard_stats_2_color,
        'dashboard_stats_3_color' => $dashboard_stats_3_color,
        'notification_badge_color' => $notification_badge_color,
        'dropdown_header_bg_start_color' => $dropdown_header_bg_start_color,
        'dropdown_header_bg_end_color' => $dropdown_header_bg_end_color
    ])
</div>