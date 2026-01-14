<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .input-modern {
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .credentials-box {
            background: rgba(255, 255, 255, 0.9);
            border: 2px dashed rgba(0, 0, 0, 0.2);
        }

        .copy-text {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-text:hover {
            background: rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="min-h-screen antialiased overflow-hidden">
    {{-- Solid Yellow Background --}}
    @php
        $panelSettings = app(\App\Repositories\PanelSettingRepository::class)->getActiveSetting();
        $bgColor = $panelSettings->header_bg_color ?? '#f6df11';
    @endphp
    <div class="fixed inset-0" style="background-color: {{ $bgColor }};"></div>

    {{-- Grid Pattern Overlay --}}
    <div class="fixed inset-0 opacity-10"
        style="background-image: linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    {{-- Main Content --}}
    <div class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md animate-fade-in-up">
            {{-- Logo Section --}}
            <div class="text-center mb-8">
                @if($panelSettings && $panelSettings->logo_path)
                    <div class="inline-block mb-4">
                        <img src="{{ asset('storage/' . $panelSettings->logo_path) }}"
                            alt="{{ $panelSettings->site_name ?? config('app.name') }}"
                            class="h-24 w-auto mx-auto drop-shadow-xl"
                            style="transform: scale({{ $panelSettings->logo_scale ?? 1 }});">
                    </div>
                @else
                    <div class="inline-block mb-4 p-4 bg-white/30 backdrop-blur-sm rounded-2xl">
                        <x-app-logo-icon class="h-16 w-16 text-black drop-shadow-lg" />
                    </div>
                @endif

                <h1 class="text-3xl font-bold text-black mb-2 drop-shadow-sm">
                    {{ $panelSettings->site_name ?? config('app.name', 'MEDIACLICK') }}
                </h1>
                <p class="text-black/70 text-sm">Hoş geldiniz, lütfen giriş yapın</p>
            </div>

            {{-- Login Card --}}
            <div class="theme-card shadow-2xl p-8">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="text-center mt-6">
                <p class="text-black/50 text-sm">© {{ date('Y') }} {{ $panelSettings->site_name ?? 'MEDIACLICK' }}. Tüm
                    hakları saklıdır.</p>
            </div>

            {{-- Test Credentials (Compact, below footer) --}}
            <div class="mt-4 flex items-center justify-center gap-3 text-[11px]">
                <span class="text-black/40">Test:</span>
                <button type="button"
                    onclick="navigator.clipboard.writeText('admin@mediaclick.com.tr'); this.querySelector('.copied').classList.remove('hidden'); setTimeout(() => this.querySelector('.copied').classList.add('hidden'), 1500);"
                    class="flex items-center gap-1.5 bg-white/50 hover:bg-white/80 px-2 py-1 rounded-md transition-all group">
                    <span class="font-mono font-medium text-black/70">admin@mediaclick.com.tr</span>
                    <svg class="w-3.5 h-3.5 text-black/40 group-hover:text-black/70" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span class="copied hidden text-green-600 text-[10px]">✓</span>
                </button>
                <span class="text-black/30">|</span>
                <button type="button"
                    onclick="navigator.clipboard.writeText('admin'); this.querySelector('.copied').classList.remove('hidden'); setTimeout(() => this.querySelector('.copied').classList.add('hidden'), 1500);"
                    class="flex items-center gap-1.5 bg-white/50 hover:bg-white/80 px-2 py-1 rounded-md transition-all group">
                    <span class="font-mono font-medium text-black/70">admin</span>
                    <svg class="w-3.5 h-3.5 text-black/40 group-hover:text-black/70" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span class="copied hidden text-green-600 text-[10px]">✓</span>
                </button>
            </div>
        </div>
    </div>

    @fluxScripts
</body>

</html>