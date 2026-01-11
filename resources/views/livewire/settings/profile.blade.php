<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Profil Ayarları'])]
    class extends Component {
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
            <h1 class="text-2xl font-bold text-skin-heading">Profil Ayarları</h1>
            <p class="text-sm text-skin-muted mt-1">Hesap bilgilerinizi ve şifrenizi güncelleyin.</p>
        </div>

        {{-- Main Card --}}
        <div class="theme-card p-6 shadow-sm">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-skin-light">
                <h2 class="text-sm font-medium text-skin-base">Hesap Bilgileri</h2>
            </div>

            {{-- Form --}}
            <form wire:submit="updateProfileInformation">
                <div class="grid grid-cols-1 gap-6">
                    {{-- Name --}}
                    <div>
                        <x-mary-input label="Ad Soyad" wire:model="name" required autofocus autocomplete="name" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-mary-input label="E-posta Adresi" type="email" wire:model="email" required autocomplete="email" />
                        
                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                            <div class="mt-3 p-3 rounded-lg" style="background-color: var(--alert-warning-bg); border: 1px solid var(--alert-warning-border);">
                                <p class="text-sm" style="color: var(--alert-warning-text);">
                                    {{ __('E-posta adresiniz doğrulanmamış.') }}
                                    <button type="button" wire:click.prevent="resendVerificationNotification" 
                                            class="underline hover:no-underline font-medium">
                                        {{ __('Doğrulama e-postasını tekrar göndermek için tıklayın.') }}
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm font-medium" style="color: var(--brand-success);">
                                        {{ __('E-posta adresinize yeni bir doğrulama bağlantısı gönderildi.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Password Section --}}
                    <div class="pt-6 border-t border-skin-light">
                        <h3 class="text-sm font-medium text-skin-heading mb-4">Şifre Değiştir</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-mary-password label="Yeni Şifre" wire:model="new_password" autocomplete="new-password" />
                            </div>
                            <div>
                                <x-mary-password label="Yeni Şifre (Tekrar)" wire:model="new_password_confirmation" autocomplete="new-password" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="pt-6 mt-6 border-t border-skin-light flex justify-end gap-3">
                    <button type="submit" wire:loading.attr="disabled" class="theme-btn-save">
                        <svg wire:loading wire:target="updateProfileInformation" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg wire:loading.remove wire:target="updateProfileInformation" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Profili Güncelle</span>
                    </button>
                </div>

                {{-- Success Message --}}
                <x-action-message class="mt-4" on="profile-updated">
                    <div class="p-3 rounded-lg" style="background-color: var(--alert-success-bg); border: 1px solid var(--alert-success-border);">
                        <p class="text-sm font-medium" style="color: var(--alert-success-text);">
                            {{ __('Profil bilgileriniz başarıyla güncellendi.') }}
                        </p>
                    </div>
                </x-action-message>
            </form>
        </div>
    </div>
</div>