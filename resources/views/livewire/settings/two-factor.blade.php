<?php

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
    <div class="w-full lg:w-3/4 mx-auto">
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
            <p class="text-sm text-skin-muted mt-1">Hesabınızın güvenliği için iki faktörlü doğrulama ayarlarını yönetin.</p>
        </div>

        {{-- Main Card --}}
        <div class="theme-card p-6 shadow-sm">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-skin-light">
                <h2 class="text-sm font-medium text-skin-base">2FA Durumu</h2>
                @if ($twoFactorEnabled)
                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: var(--alert-success-bg); color: var(--alert-success-text); border: 1px solid var(--alert-success-border);">
                        Etkin
                    </span>
                @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: var(--alert-danger-bg); color: var(--alert-danger-text); border: 1px solid var(--alert-danger-border);">
                        Devre Dışı
                    </span>
                @endif
            </div>

            {{-- Content --}}
            <div class="grid grid-cols-1 gap-6" wire:cloak>
                @if ($twoFactorEnabled)
                    <div class="space-y-4">
                        <div class="p-4 rounded-lg" style="background-color: var(--alert-success-bg); border: 1px solid var(--alert-success-border);">
                            <p class="text-sm" style="color: var(--alert-success-text);">
                                {{ __('İki faktörlü doğrulama etkinleştirildiğinde, giriş sırasında telefonunuzdaki TOTP destekli uygulamadan alabileceğiniz güvenli, rastgele bir pin girmeniz istenecektir.') }}
                            </p>
                        </div>

                        <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />

                        <div class="flex justify-start">
                            <button wire:click="disable" class="theme-btn-delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <span>{{ __('2FA\'yı Devre Dışı Bırak') }}</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="p-4 rounded-lg" style="background-color: var(--alert-warning-bg); border: 1px solid var(--alert-warning-border);">
                            <p class="text-sm" style="color: var(--alert-warning-text);">
                                {{ __('İki faktörlü doğrulamayı etkinleştirdiğinizde, giriş sırasında güvenli bir pin girmeniz istenecektir. Bu pin telefonunuzdaki TOTP destekli bir uygulamadan alınabilir.') }}
                            </p>
                        </div>

                        <button wire:click="enable" class="theme-btn-save">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>{{ __('2FA\'yı Etkinleştir') }}</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal remains the same --}}

    <flux:modal name="two-factor-setup-modal" class="max-w-md md:min-w-md" @close="closeModal" wire:model="showModal">
        <div class="space-y-6">
            <div class="flex flex-col items-center space-y-4">
                <div
                    class="p-0.5 w-auto rounded-full border border-[var(--card-border)] dark:border-[var(--card-border)] bg-[var(--card-bg)] dark:bg-[var(--card-bg)] shadow-sm">
                    <div
                        class="p-2.5 rounded-full border border-[var(--card-border)] dark:border-[var(--card-border)] overflow-hidden bg-[var(--dropdown-hover-bg)] dark:bg-[var(--dropdown-hover-bg)] relative">
                        <div
                            class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-[var(--card-border)] dark:divide-[var(--card-border)] justify-around opacity-50">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <div
                            class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-[var(--card-border)] dark:divide-[var(--card-border)] justify-around opacity-50">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <flux:icon.qr-code class="relative z-20 dark:text-accent-foreground" />
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <flux:heading size="lg">{{ $this->modalConfig['title'] }}</flux:heading>
                    <flux:text>{{ $this->modalConfig['description'] }}</flux:text>
                </div>
            </div>

            @if ($showVerificationStep)
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
            @else
                @error('setupData')
                    <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}" />
                @enderror

                <div class="flex justify-center">
                    <div
                        class="relative w-64 overflow-hidden border rounded-lg border-[var(--card-border)] dark:border-[var(--card-border)] aspect-square">
                        @empty($qrCodeSvg)
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-[var(--card-bg)] dark:bg-[var(--card-bg)] animate-pulse">
                                <flux:icon.loading />
                            </div>
                        @else
                            <div class="flex items-center justify-center h-full p-4">
                                <div class="bg-white p-3 rounded">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>
                        @endempty
                    </div>
                </div>

                <div>
                    <flux:button :disabled="$errors->has('setupData')" variant="primary" class="w-full"
                        wire:click="showVerificationIfNecessary">
                        {{ $this->modalConfig['buttonText'] }}
                    </flux:button>
                </div>

                <div class="space-y-4">
                    <div class="relative flex items-center justify-center w-full">
                        <div
                            class="absolute inset-0 w-full h-px top-1/2 bg-[var(--card-border)] dark:bg-[var(--card-border)]">
                        </div>
                        <span
                            class="relative px-2 text-sm bg-[var(--card-bg)] dark:bg-[var(--card-bg)] text-[var(--color-text-base)] dark:text-[var(--color-text-muted)]">
                            {{ __('veya, kodu manuel olarak girin') }}
                        </span>
                    </div>

                    <div class="flex items-center space-x-2" x-data="{
                                copied: false,
                                async copy() {
                                    try {
                                        await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1500);
                                    } catch (e) {
                                        console.warn('Could not copy to clipboard');
                                    }
                                }
                            }">
                        <div class="flex items-stretch w-full border rounded-xl dark:border-[var(--card-border)]">
                            @empty($manualSetupKey)
                                <div
                                    class="flex items-center justify-center w-full p-3 bg-[var(--dropdown-hover-bg)] dark:bg-[var(--dropdown-hover-bg)]">
                                    <flux:icon.loading variant="mini" />
                                </div>
                            @else
                                <input type="text" readonly value="{{ $manualSetupKey }}"
                                    class="w-full p-3 bg-transparent outline-none text-skin-heading dark:text-skin-heading" />

                                <button @click="copy()"
                                    class="px-3 transition-colors border-l cursor-pointer border-[var(--card-border)] dark:border-[var(--card-border)]">
                                    <flux:icon.document-duplicate x-show="!copied" variant="outline"></flux:icon>
                                        <flux:icon.check x-show="copied" variant="solid" class="text-[var(--color-success)]">
                                            </flux:icon>
                                </button>
                            @endempty
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>
</section>