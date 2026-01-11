{{--
═══════════════════════════════════════════════════════════════════════════
🔢 TWO-FACTOR VERIFICATION (OTP INPUT)
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: OTP (One-Time Password) giriş formu.
TOTP uygulamasından alınan 6 haneli kodu doğrular.
📝 Kullanım Notu: flux:otp bileşeni kullanılır, 6 haneli kod gereklidir.
🔗 State Dependencies: $code (6 haneli string)

🔄 Actions:
- resetVerification: Geri butonu, QR kod ekranına döner
- confirmTwoFactor: Kodu doğrular ve 2FA aktifleştirir

--}}

<div class="space-y-6">
    <div class="flex flex-col items-center space-y-3 justify-center">
        <flux:otp name="code" wire:model="code" length="6" label="OTP Code" label:sr-only class="mx-auto" />
    </div>

    <div class="flex items-center space-x-3">
        <flux:button variant="outline" class="flex-1" wire:click="resetVerification">
            {{ __('Geri') }}
        </flux:button>

        <flux:button variant="primary" class="flex-1" wire:click="confirmTwoFactor"
            x-bind:disabled="$wire.code.length < 6">
            {{ __('Onayla') }}
        </flux:button>
    </div>
</div>