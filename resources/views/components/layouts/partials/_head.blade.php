<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($theme?->site_name ?? 'MEDIACLICK') }}</title>

    {{-- Dynamic Favicon --}}
    @if($theme?->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $theme->favicon_path) }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            /* Global & Typography */
            --font-main:
                {{ $theme?->font_family ?? 'Inter' }}
                , sans-serif;
            --color-text-base:
                {{ $theme?->base_text_color ?? '#475569' }}
            ;
            --color-text-heading:
                {{ $theme?->heading_color ?? '#0f172a' }}
            ;

            /* Bridge Mappings requested by user */
            --primary-color:
                {{ $theme?->btn_create_bg_color ?? '#4f46e5' }}
            ;
            --error-color:
                {{ $theme?->input_error_text_color ?? '#ef4444' }}
            ;
            --table-hover: #f8fafc;
            /* Static fallback for now, or add to settings later */

            /* Inputs */
            --input-focus-ring:
                {{ $theme?->input_focus_ring_color ?? '#6366f1' }}
            ;
            --input-border:
                {{ $theme?->input_border_color ?? '#cbd5e1' }}
            ;
            --input-radius:
                {{ $theme?->input_border_radius ?? '6px' }}
            ;
            --input-padding-y:
                {{ $theme?->input_vertical_padding ?? '8px' }}
            ;

            /* Typography Font Sizes */
            --font-size-label:
                {{ ($theme?->label_font_size ?? 14) . 'px' }}
            ;
            --font-size-input:
                {{ ($theme?->input_font_size ?? 16) . 'px' }}
            ;
            --font-size-heading:
                {{ ($theme?->heading_font_size ?? 18) . 'px' }}
            ;
            --font-size-error:
                {{ ($theme?->error_font_size ?? 12) . 'px' }}
            ;
            --font-size-helper:
                {{ ($theme?->helper_font_size ?? 12) . 'px' }}
            ;

            /* Error States */
            --input-error-ring:
                {{ $theme?->input_error_ring_color ?? '#ef4444' }}
            ;
            --input-error-border:
                {{ $theme?->input_error_border_color ?? '#ef4444' }}
            ;
            --input-error-text:
                {{ $theme?->input_error_text_color ?? '#ef4444' }}
            ;

            /* Granular Buttons */
            /* Create / Primary */
            --btn-create-bg:
                {{ $theme?->btn_create_bg_color ?? '#4f46e5' }}
            ;
            --btn-create-text:
                {{ $theme?->btn_create_text_color ?? '#ffffff' }}
            ;
            --btn-create-hover:
                {{ $theme?->btn_create_hover_color ?? '#4338ca' }}
            ;
            --btn-create-border:
                {{ $theme?->btn_create_border_color ?? '#4f46e5' }}
            ;

            /* Edit */
            --btn-edit-bg:
                {{ $theme?->btn_edit_bg_color ?? '#f59e0b' }}
            ;
            --btn-edit-text:
                {{ $theme?->btn_edit_text_color ?? '#ffffff' }}
            ;
            --btn-edit-hover:
                {{ $theme?->btn_edit_hover_color ?? '#d97706' }}
            ;
            --btn-edit-border:
                {{ $theme?->btn_edit_border_color ?? '#f59e0b' }}
            ;

            /* Delete */
            --btn-delete-bg:
                {{ $theme?->btn_delete_bg_color ?? '#ef4444' }}
            ;
            --btn-delete-text:
                {{ $theme?->btn_delete_text_color ?? '#ffffff' }}
            ;
            --btn-delete-hover:
                {{ $theme?->btn_delete_hover_color ?? '#dc2626' }}
            ;
            --btn-delete-border:
                {{ $theme?->btn_delete_border_color ?? '#ef4444' }}
            ;

            /* Cancel */
            --btn-cancel-bg:
                {{ $theme?->btn_cancel_bg_color ?? '#94a3b8' }}
            ;
            --btn-cancel-text:
                {{ $theme?->btn_cancel_text_color ?? '#ffffff' }}
            ;
            --btn-cancel-hover:
                {{ $theme?->btn_cancel_hover_color ?? '#64748b' }}
            ;
            --btn-cancel-border:
                {{ $theme?->btn_cancel_border_color ?? '#94a3b8' }}
            ;

            /* Save */
            --btn-save-bg:
                {{ $theme?->btn_save_bg_color ?? '#10b981' }}
            ;
            --btn-save-text:
                {{ $theme?->btn_save_text_color ?? '#ffffff' }}
            ;
            --btn-save-hover:
                {{ $theme?->btn_save_hover_color ?? '#059669' }}
            ;
            --btn-save-border:
                {{ $theme?->btn_save_border_color ?? '#10b981' }}
            ;

            /* Actions */
            --action-link-color:
                {{ $theme?->action_link_color ?? '#4f46e5' }}
            ;
            --active-tab-color:
                {{ $theme?->active_tab_color ?? '#4f46e5' }}
            ;

            /* Cards */
            --card-bg:
                {{ $theme?->card_bg_color ?? '#eff4ff' }}
            ;
            --card-border:
                {{ $theme?->card_border_color ?? '#bfdbfe' }}
            ;
            --card-radius:
                {{ $theme?->card_border_radius ?? '12px' }}
            ;

            --table-hover-bg:
                {{ $theme?->table_hover_bg_color ?? '#f8fafc' }}
            ;
            --table-hover-text:
                {{ $theme?->table_hover_text_color ?? '#0f172a' }}
            ;

            /* Table Avatar */
            --table-avatar-bg:
                {{ $theme?->table_avatar_bg_color ?? '#f1f5f9' }}
            ;
            --table-avatar-border:
                {{ $theme?->table_avatar_border_color ?? '#e2e8f0' }}
            ;
            --table-avatar-text:
                {{ $theme?->table_avatar_text_color ?? '#475569' }}
            ;

            /* List Card */
            --list-card-bg:
                {{ $theme?->list_card_bg_color ?? '#ffffff' }}
            ;
            --list-card-border:
                {{ $theme?->list_card_border_color ?? '#e2e8f0' }}
            ;
            --list-card-link-color:
                {{ $theme?->list_card_link_color ?? '#4f46e5' }}
            ;
            --list-card-hover-bg:
                {{ $theme?->list_card_hover_color ?? '#f8fafc' }}
            ;

            /* Sidebar Settings */
            --sidebar-bg:
                {{ $theme?->sidebar_bg_color ?? '#3D3373' }}
            ;
            --sidebar-text:
                {{ $theme?->sidebar_text_color ?? '#ffffff' }}
            ;
            --sidebar-hover-bg:
                {{ $theme?->sidebar_hover_bg_color ?? '#4338ca' }}
            ;
            --sidebar-hover-text:
                {{ $theme?->sidebar_hover_text_color ?? '#ffffff' }}
            ;
            --sidebar-active-bg:
                {{ $theme?->sidebar_active_item_bg_color ?? '#4f46e5' }}
            ;
            --sidebar-active-text:
                {{ $theme?->sidebar_active_item_text_color ?? '#ffffff' }}
            ;

            /* Header Active Items */
            --header-active-bg:
                {{ $theme?->header_active_item_bg_color ?? '#ffffff' }}
            ;
            --header-active-text:
                {{ $theme?->header_active_item_text_color ?? '#4f46e5' }}
            ;

            /* Dashboard Colors */
            --dashboard-card-bg:
                {{ $theme?->dashboard_card_bg_color ?? '#eff4ff' }}
            ;
            --dashboard-card-text:
                {{ $theme?->dashboard_card_text_color ?? '#475569' }}
            ;
            --dashboard-stats-1:
                {{ $theme?->dashboard_stats_1_color ?? '#3b82f6' }}
            ;
            --dashboard-stats-2:
                {{ $theme?->dashboard_stats_2_color ?? '#14b8a6' }}
            ;
            --dashboard-stats-3:
                {{ $theme?->dashboard_stats_3_color ?? '#f59e0b' }}
            ;
            --dashboard-stats-4:
                {{ $theme?->dashboard_stats_4_color ?? '#8b5cf6' }}
            ;

            /* User Menu & Dropdown */
            --avatar-gradient-start:
                {{ $theme?->avatar_gradient_start_color ?? '#c084fc' }}
            ;
            --avatar-gradient-end:
                {{ $theme?->avatar_gradient_end_color ?? '#9333ea' }}
            ;
            --dropdown-header-start:
                {{ $theme?->dropdown_header_bg_start_color ?? '#f5f3ff' }}
            ;
            --dropdown-header-end:
                {{ $theme?->dropdown_header_bg_end_color ?? '#eef2ff' }}
            ;
            --notification-badge:
                {{ $theme?->notification_badge_color ?? '#ef4444' }}
            ;

            /* Page Background */
            --page-bg:
                {{ $theme?->page_bg_color ?? '#f8fafc' }}
            ;


            /* Theme variables are defined above - NO DaisyUI hijacking to prevent UI pollution */
        }

        /* Input Invalid State Shake & Color */
        .input:invalid,
        .input.is-invalid {
            --input-focus-ring: var(--error-color) !important;
            border-color: var(--error-color) !important;
        }

        body {
            font-family: var(--font-main);
            color: var(--color-text-base);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: var(--color-text-heading);
        }
    </style>
</head>