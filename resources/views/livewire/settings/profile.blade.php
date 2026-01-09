<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'new_password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Update password if provided
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        // Clear password fields after successful update
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Hesabım')" :subheading="__('Profil bilgilerinizi ve şifrenizi güncelleyin')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" label="Ad Soyad" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" label="Email" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !text-[var(--brand-success)]">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="border-t border-[var(--card-border)] pt-6">
                <h3 class="text-sm font-medium text-[var(--color-text-heading)] mb-4">Şifre Değiştir</h3>
                <div class="space-y-4">
                    <flux:input wire:model="new_password" label="Yeni Şifre" type="password"
                        autocomplete="new-password" />
                    <flux:input wire:model="new_password_confirmation" label="Yeni Şifre (Tekrar)" type="password"
                        autocomplete="new-password" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end w-full">
                    <button type="submit" wire:loading.attr="disabled" class="theme-btn-save w-full justify-center">
                        <svg wire:loading wire:target="updateProfileInformation" class="w-4 h-4 animate-spin"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Kaydet</span>
                    </button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Kaydedildi.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>