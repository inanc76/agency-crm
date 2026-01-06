<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        @keyframes gradient-shift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

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

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 15s ease infinite;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .input-modern {
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2);
        }
    </style>
</head>

<body class="min-h-screen antialiased overflow-hidden">
    <!-- Animated Gradient Background -->
    <div class="fixed inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 animate-gradient"></div>

    <!-- Decorative Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-float"
            style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-pink-400/10 rounded-full blur-3xl animate-float"
            style="animation-delay: -6s;"></div>
    </div>

    <!-- Main Content -->
    <div class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md animate-fade-in-up">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                @php
                    $panelSettings = app(\App\Repositories\PanelSettingRepository::class)->getActiveSetting();
                @endphp

                @if($panelSettings && $panelSettings->logo_path)
                    <div class="inline-block mb-4">
                        <img src="{{ asset('storage/' . $panelSettings->logo_path) }}"
                            alt="{{ $panelSettings->site_name ?? config('app.name') }}"
                            class="h-20 w-auto mx-auto drop-shadow-2xl"
                            style="transform: scale({{ $panelSettings->logo_scale ?? 1 }});">
                    </div>
                @else
                    <div class="inline-block mb-4 p-4 bg-white/20 backdrop-blur-sm rounded-2xl">
                        <x-app-logo-icon class="h-12 w-12 text-white drop-shadow-lg" />
                    </div>
                @endif

                <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">
                    {{ $panelSettings->site_name ?? config('app.name', 'MEDIACLICK') }}
                </h1>
                <p class="text-white/80 text-sm">Hoş geldiniz, lütfen giriş yapın</p>
            </div>

            <!-- Login Card -->
            <div class="glass-card rounded-3xl shadow-2xl p-8">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-white/60 text-sm">© {{ date('Y') }} {{ $panelSettings->site_name ?? 'MEDIACLICK' }}. Tüm
                    hakları saklıdır.</p>
            </div>
        </div>
    </div>

    @fluxScripts
</body>

</html>