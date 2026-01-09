<!DOCTYPE html>
<html lang="tr" data-theme="light">

@php
    // Use Shared Theme Settings (Cached in Provider)
    // Fallback to empty object/null handled by optional chaining
    $theme = $themeSettings ?? null;
@endphp

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

<body class="font-sans antialiased text-gray-900" style="background-color: var(--page-bg);">
    {{-- Header - Dynamic colors from theme settings --}}
    <header
        style="background-color: {{ $theme?->header_bg_color ?? '#3D3373' }}; border-bottom: {{ ($theme?->header_border_width ?? 0) }}px solid {{ $theme?->header_border_color ?? 'transparent' }}">
        <div class="flex items-center h-16 px-6">
            {{-- Logo --}}
            <div class="flex items-center w-64">
                @if($theme?->logo_path)
                    @php
                        $baseHeight = 32; // h-8 = 32px
                        $logoScale = $theme->logo_scale ?? 1.0;
                        $scaledHeight = $baseHeight * $logoScale;
                    @endphp
                    <img src="{{ asset('storage/' . $theme->logo_path) }}" alt="{{ $theme->site_name ?? 'MEDIACLICK' }}"
                        style="height: {{ $scaledHeight }}px" class="object-contain">
                @else
                    <h1 class="text-xl font-bold text-white">{{ $theme?->site_name ?? 'MEDIACLICK' }}</h1>
                @endif
            </div>

            {{-- Navigation Tabs - Dynamic colors --}}
            <div class="flex items-center px-4">
                <div class="flex backdrop-blur-sm rounded-full p-1.5 border border-white/20"
                    style="background-color: {{ $theme?->menu_bg_color ?? 'rgba(255, 255, 255, 0.1)' }}">
                    <a href="/dashboard"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard') ? 'color: ' . ($theme?->menu_text_color ?? '#ffffff') : '' }}">
                        Dashboard
                    </a>
                    <a href="/dashboard/customers"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard/customers*') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard/customers*') ? 'color: ' . ($theme?->menu_text_color ?? '#ffffff') : '' }}">
                        Müşteriler
                    </a>
                    <a href="/dashboard/settings"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard/settings*') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard/settings*') ? 'color: ' . ($theme?->menu_text_color ?? '#ffffff') : '' }}">
                        Ayarlar
                    </a>
                </div>
            </div>

            {{-- Right Side - Notification + User --}}
            <div class="flex items-center space-x-4 ml-auto">
                {{-- Notification Bell --}}
                <button class="relative p-2 hover:bg-white/10 rounded-lg transition-colors"
                    style="color: {{ $theme?->header_icon_color ?? '#ffffff' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                {{-- User Info --}}
                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors cursor-pointer focus:outline-none">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                            style="background-color: var(--avatar-bg);">
                            <span class="text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                        <div class="text-left hidden md:block">
                            <div class="text-sm font-medium leading-tight"
                                style="color: {{ $theme?->header_icon_color ?? '#ffffff' }}">
                                {{ auth()->user()->name ?? 'Kullanıcı' }}
                            </div>
                            <div class="text-xs leading-tight"
                                style="color: {{ $theme?->header_icon_color ?? '#ffffff' }}; opacity: 0.8">
                                {{ auth()->user()->email ?? '' }}
                            </div>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24"
                            style="color: {{ $theme?->header_icon_color ?? '#ffffff' }}; opacity: 0.7">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-56 rounded-xl shadow-lg py-1 z-50 overflow-hidden"
                        style="display: none; background-color: var(--dropdown-bg); border: 1px solid var(--dropdown-border);">

                        <div class="px-4 py-3 border-b"
                            style="border-color: var(--dropdown-border); background-color: var(--dropdown-header-bg);">
                            <p class="text-sm font-medium truncate" style="color: var(--dropdown-text);">
                                {{ auth()->user()->name ?? 'Kullanıcı' }}
                            </p>
                            <p class="text-xs truncate" style="color: var(--dropdown-text-muted);">
                                {{ auth()->user()->email ?? '' }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="group flex items-center px-4 py-2 text-sm transition-colors"
                               
                                onmouseover="this.style.backgroundColor='var(--dropdown-item-hover)'; this.style.color='var(--dropdown-item-hover-text)';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-text-base)';">
                                <svg class="mr-3 h-4 w-4" style="color: var(--dropdown-icon-muted);" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Ayarları
                            </a>
                        </div>

                        <div class="border-t py-1" style="border-color: var(--dropdown-border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="group flex w-full items-center px-4 py-2 text-sm transition-colors"
                                    style="color: var(--dropdown-danger-text);"
                                    onmouseover="this.style.backgroundColor='var(--dropdown-danger-hover)';"
                                    onmouseout="this.style.backgroundColor='transparent';">
                                    <svg class="mr-3 h-4 w-4" style="color: var(--dropdown-danger-icon);" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <x-mary-toast />
    @livewireScripts
</body>

</html>