<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Şifre Değiştir'])]
    class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

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
            <h1 class="text-2xl font-bold text-skin-heading">Şifre Değiştir</h1>
            <p class="text-sm text-skin-muted mt-1">Hesabınızın güvenliği için güçlü bir şifre kullandığınızdan emin olun.</p>
        </div>

        {{-- Main Card --}}
        <div class="theme-card p-6 shadow-sm">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-skin-light">
                <h2 class="text-sm font-medium text-skin-base">Şifre Güncelleme</h2>
            </div>

            {{-- Form --}}
            <form wire:submit="updatePassword">
                <div class="grid grid-cols-1 gap-6">
                    {{-- Current Password --}}
                    <div>
                        <x-mary-password label="Mevcut Şifre" wire:model="current_password" required autocomplete="current-password" />
                    </div>

                    {{-- New Password Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-mary-password label="Yeni Şifre" wire:model="password" required autocomplete="new-password" />
                        </div>
                        <div>
                            <x-mary-password label="Yeni Şifre (Tekrar)" wire:model="password_confirmation" required autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="pt-6 mt-6 border-t border-skin-light flex justify-end gap-3">
                    <button type="submit" wire:loading.attr="disabled" class="theme-btn-save">
                        <svg wire:loading wire:target="updatePassword" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg wire:loading.remove wire:target="updatePassword" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Şifreyi Güncelle</span>
                    </button>
                </div>

                {{-- Success Message --}}
                <x-action-message class="mt-4" on="password-updated">
                    <div class="p-3 rounded-lg" style="background-color: var(--alert-success-bg); border: 1px solid var(--alert-success-border);">
                        <p class="text-sm font-medium" style="color: var(--alert-success-text);">
                            {{ __('Şifreniz başarıyla güncellendi.') }}
                        </p>
                    </div>
                </x-action-message>
            </form>
        </div>
    </div>
</div>