<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11 (ATOMIC)                                   ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: İki Faktörlü Doğrulama (2FA) Yönetimi                                                     ║
 * ║  🎯 ANA GÖREV: TOTP tabanlı iki faktörlü kimlik doğrulama kurulumu ve yönetimi                                  ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • enable(): 2FA etkinleştirme ve QR kod oluşturma                                                             ║
 * ║  • disable(): 2FA devre dışı bırakma                                                                           ║
 * ║  • confirmTwoFactor(): OTP doğrulama ve onaylama                                                               ║
 * ║  • Recovery Codes: Kurtarma kodları yönetimi (sub-component)                                                   ║
 * ║                                                                                                                  ║
 * ║  📦 PARTIAL YAPISI:                                                                                             ║
 * ║  • _two-factor-status.blade.php: Durum kartı ve enable/disable butonları                                       ║
 * ║  • _two-factor-qr.blade.php: QR kod modal ve manuel anahtar                                                    ║
 * ║  • _two-factor-verification.blade.php: OTP giriş formu                                                         ║
 * ║  • recovery-codes.blade.php: Kurtarma kodları (Livewire Component)                                             ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Fortify Feature Check: 2FA özelliği kontrolü                                                                ║
 * ║  • Secret Encryption: two_factor_secret şifrelenmiş saklanır                                                   ║
 * ║  • Confirmation Requirement: Opsiyonel OTP doğrulama zorunluluğu                                               ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\HttpFoundation\Response;

new
    #[Layout('components.layouts.app', ['title' => 'İki Faktörlü Doğrulama'])]
    class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    /**
     * Mount the component.
     * 🔐 Security: Aborts if 2FA feature is not enabled in Fortify config
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Enable two-factor authentication for the user.
     * 📢 Events: Shows modal with QR code
     */
    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();

        $this->showModal = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     * 🔐 Security: Decrypts the secret key for display
     */
    private function loadSetupData(): void
    {
        $user = auth()->user();

        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
    }

    /**
     * Confirm two-factor authentication for the user.
     * 🔐 Security: Validates OTP code before confirmation
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->closeModal();

        $this->twoFactorEnabled = true;
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     * 🔐 Security: Removes 2FA protection from account
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'showModal',
            'showVerificationStep',
        );

        $this->resetErrorBag();

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Get the current modal configuration state.
     * 🎯 Business Logic: Returns different config based on current step
     */
    public function getModalConfigProperty(): array
    {
        if ($this->twoFactorEnabled) {
            return [
                'title' => __('İki Faktörlü Doğrulama Etkinleştirildi'),
                'description' => __('İki faktörlü doğrulama artık etkinleştirildi. QR kodunu tarayın veya kurulum anahtarını doğrulayıcı uygulamanıza girin.'),
                'buttonText' => __('Kapat'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Doğrulama Kodunu Girin'),
                'description' => __('Doğrulayıcı uygulamanızdan 6 haneli kodu girin.'),
                'buttonText' => __('Devam Et'),
            ];
        }

        return [
            'title' => __('İki Faktörlü Doğrulamayı Etkinleştir'),
            'description' => __('İki faktörlü doğrulamayı etkinleştirmeyi tamamlamak için QR kodunu tarayın veya kurulum anahtarını doğrulayıcı uygulamanıza girin.'),
            'buttonText' => __('Devam Et'),
        ];
    }
} ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-skin-heading">İki Faktörlü Doğrulama</h1>
            <p class="text-sm text-skin-muted mt-1">Hesabınızın güvenliği için iki faktörlü doğrulama ayarlarını
                yönetin.</p>
        </div>

        {{-- SECTION: Status Card --}}
        @include('livewire.settings.two-factor._two-factor-status', [
            'twoFactorEnabled' => $twoFactorEnabled,
            'requiresConfirmation' => $requiresConfirmation
        ])
    </div>
        
    {{-- SECTION: QR Code Modal --}}
    @include('livewire.settings.two-factor._two-factor-qr', [
        'showModal' => $showModal,
        'showVerificationStep' => $showVerificationStep,
        'qrCodeSvg' => $qrCodeSvg,
        'manualSetupKey' => $manualSetupKey,
        'code' => $code
    ])
</section>