<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
}; ?>

<div class="py-6 space-y-6 border shadow-sm rounded-xl border-[var(--card-border)] dark:border-[var(--card-border)]"
    wire:cloak x-data="{ showRecoveryCodes: false }">
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <flux:icon.lock-closed variant="outline" class="size-4" />
            <flux:heading size="lg" level="3">{{ __('2FA Kurtarma Kodları') }}</flux:heading>
        </div>
        <flux:text variant="subtle">
            {{ __('Kurtarma kodları, 2FA cihazınızı kaybetmeniz durumunda erişimi yeniden kazanmanızı sağlar. Bunları güvenli bir şifre yöneticisinde saklayın.') }}
        </flux:text>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:button x-show="!showRecoveryCodes" icon="eye" icon:variant="outline" variant="primary"
                @click="showRecoveryCodes = true;" aria-expanded="false" aria-controls="recovery-codes-section">
                {{ __('Kurtarma Kodlarını Göster') }}
            </flux:button>

            <flux:button x-show="showRecoveryCodes" icon="eye-slash" icon:variant="outline" variant="primary"
                @click="showRecoveryCodes = false" aria-expanded="true" aria-controls="recovery-codes-section">
                {{ __('Kurtarma Kodlarını Gizle') }}
            </flux:button>

            @if (filled($recoveryCodes))
                <flux:button x-show="showRecoveryCodes" icon="arrow-path" variant="filled"
                    wire:click="regenerateRecoveryCodes">
                    {{ __('Kodları Yeniden Oluştur') }}
                </flux:button>
            @endif
        </div>

        <div x-show="showRecoveryCodes" x-transition id="recovery-codes-section" class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes">
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <flux:callout variant="danger" icon="x-circle" heading="{{$message}}" />
                @enderror

                @if (filled($recoveryCodes))
                    <div class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-[var(--dropdown-hover-bg)] dark:bg-[var(--card-bg)]"
                        role="list" aria-label="Recovery codes">
                        @foreach($recoveryCodes as $code)
                            <div role="listitem" class="select-text" wire:loading.class="opacity-50 animate-pulse">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <flux:text variant="subtle" class="text-xs">
                        {{ __('Her kurtarma kodu hesabınıza erişmek için bir kez kullanılabilir ve kullanıldıktan sonra kaldırılır. Daha fazlasına ihtiyacınız varsa, yukarıdaki Kodları Yeniden Oluştur\'a tıklayın.') }}
                    </flux:text>
                @endif
            </div>
        </div>
    </div>
</div>