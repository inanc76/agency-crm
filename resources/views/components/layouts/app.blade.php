<!DOCTYPE html>
<html lang="tr" data-theme="light">

@php
    $panelSettings = app(\App\Repositories\PanelSettingRepository::class)->getActiveSetting();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($panelSettings?->site_name ?? 'MEDIACLICK') }}</title>

    {{-- Dynamic Favicon --}}
    @if($panelSettings?->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $panelSettings->favicon_path) }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans bg-slate-50 text-gray-900 antialiased">
    {{-- Header - Dynamic colors from panel settings --}}
    <header
        style="background-color: {{ $panelSettings?->header_bg_color ?? '#3D3373' }}; border-bottom: {{ ($panelSettings?->header_border_width ?? 0) }}px solid {{ $panelSettings?->header_border_color ?? 'transparent' }}">
        <div class="flex items-center h-16 px-6">
            {{-- Logo --}}
            <div class="flex items-center w-64">
                @if($panelSettings?->logo_path)
                    @php
                        $baseHeight = 32; // h-8 = 32px
                        $logoScale = $panelSettings->logo_scale ?? 1.0;
                        $scaledHeight = $baseHeight * $logoScale;
                    @endphp
                    <img src="{{ asset('storage/' . $panelSettings->logo_path) }}"
                        alt="{{ $panelSettings->site_name ?? 'MEDIACLICK' }}" style="height: {{ $scaledHeight }}px"
                        class="object-contain">
                @else
                    <h1 class="text-xl font-bold text-white">{{ $panelSettings?->site_name ?? 'MEDIACLICK' }}</h1>
                @endif
            </div>

            {{-- Navigation Tabs - Dynamic colors --}}
            <div class="flex items-center px-4">
                <div class="flex backdrop-blur-sm rounded-full p-1.5 border border-white/20"
                    style="background-color: {{ $panelSettings?->menu_bg_color ?? 'rgba(255, 255, 255, 0.1)' }}">
                    <a href="/dashboard"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard') ? 'bg-white text-[#3D3373]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard') ? 'color: ' . ($panelSettings?->menu_text_color ?? '#ffffff') : '' }}">
                        Dashboard
                    </a>
                    <a href="/dashboard/customers"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard/customers*') ? 'bg-white text-[#3D3373]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard/customers*') ? 'color: ' . ($panelSettings?->menu_text_color ?? '#ffffff') : '' }}">
                        Müşteriler
                    </a>
                    <a href="/dashboard/settings"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request()->is('dashboard/settings*') ? 'bg-white text-[#3D3373]' : 'hover:bg-white/10' }}"
                        style="{{ !request()->is('dashboard/settings*') ? 'color: ' . ($panelSettings?->menu_text_color ?? '#ffffff') : '' }}">
                        Ayarlar
                    </a>
                </div>
            </div>

            {{-- Right Side - Notification + User --}}
            <div class="flex items-center space-x-4 ml-auto">
                {{-- Notification Bell --}}
                <button class="relative p-2 hover:bg-white/10 rounded-lg transition-colors"
                    style="color: {{ $panelSettings?->header_icon_color ?? '#ffffff' }}">
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
                        <div class="w-8 h-8 bg-purple-400 rounded-full flex items-center justify-center">
                            <span class="text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                        <div class="text-left hidden md:block">
                            <div class="text-sm font-medium leading-tight"
                                style="color: {{ $panelSettings?->header_icon_color ?? '#ffffff' }}">
                                {{ auth()->user()->name ?? 'Kullanıcı' }}
                            </div>
                            <div class="text-xs leading-tight"
                                style="color: {{ $panelSettings?->header_icon_color ?? '#ffffff' }}; opacity: 0.8">
                                {{ auth()->user()->email ?? '' }}
                            </div>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24"
                            style="color: {{ $panelSettings?->header_icon_color ?? '#ffffff' }}; opacity: 0.7">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50 overflow-hidden"
                        style="display: none;">

                        <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                            <p class="text-sm text-gray-900 font-medium truncate">
                                {{ auth()->user()->name ?? 'Kullanıcı' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors">
                                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-purple-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Ayarları
                            </a>
                        </div>

                        <div class="border-t border-gray-100 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="mr-3 h-4 w-4 text-red-500 group-hover:text-red-600" fill="none"
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