{{--
═══════════════════════════════════════════════════════════════════════════
🔐 TWO-FACTOR STATUS CARD
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: 2FA etkin/devre dışı durumunu gösteren ana kart bileşeni.
Recovery codes sub-component ile entegre çalışır.
📝 Kullanım Notu: $twoFactorEnabled boolean değeri ile durum kontrolü yapılır.
🔗 State Dependencies: $twoFactorEnabled, $requiresConfirmation

--}}

<div class="theme-card p-6 shadow-sm">
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-skin-light">
        <h2 class="text-sm font-medium text-skin-base">2FA Durumu</h2>
        @if ($twoFactorEnabled)
            <span class="px-2 py-1 text-xs font-medium rounded-full"
                style="background-color: var(--alert-success-bg); color: var(--alert-success-text); border: 1px solid var(--alert-success-border);">
                Etkin
            </span>
        @else
            <span class="px-2 py-1 text-xs font-medium rounded-full"
                style="background-color: var(--alert-danger-bg); color: var(--alert-danger-text); border: 1px solid var(--alert-danger-border);">
                Devre Dışı
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="grid grid-cols-1 gap-6" wire:cloak>
        @if ($twoFactorEnabled)
            <div class="space-y-4">
                <div class="p-4 rounded-lg"
                    style="background-color: var(--alert-success-bg); border: 1px solid var(--alert-success-border);">
                    <p class="text-sm" style="color: var(--alert-success-text);">
                        {{ __('İki faktörlü doğrulama etkinleştirildiğinde, giriş sırasında telefonunuzdaki TOTP destekli uygulamadan alabileceğiniz güvenli, rastgele bir pin girmeniz istenecektir.') }}
                    </p>
                </div>

                <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />

                <div class="flex justify-start">
                    <button wire:click="disable" class="theme-btn-delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span>{{ __('2FA\'yı Devre Dışı Bırak') }}</span>
                    </button>
                </div>
            </div>
        @else
            <div class="space-y-4">
                <div class="p-4 rounded-lg"
                    style="background-color: var(--alert-warning-bg); border: 1px solid var(--alert-warning-border);">
                    <p class="text-sm" style="color: var(--alert-warning-text);">
                        {{ __('İki faktörlü doğrulamayı etkinleştirdiğinizde, giriş sırasında güvenli bir pin girmeniz istenecektir. Bu pin telefonunuzdaki TOTP destekli bir uygulamadan alınabilir.') }}
                    </p>
                </div>

                <button wire:click="enable" class="theme-btn-save">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>{{ __('2FA\'yı Etkinleştir') }}</span>
                </button>
            </div>
        @endif
    </div>
</div>