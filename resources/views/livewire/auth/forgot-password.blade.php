<x-layouts.auth>
    <div class="space-y-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold mb-1 text-skin-heading">Şifremi Unuttum</h2>
            <p class="text-sm text-skin-base">E-posta adresinizi girin, şifre sıfırlama bağlantısı gönderelim</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <flux:input name="email" :label="__('Email Address')" type="email" required autofocus
                    placeholder="Email adresinizi girin"
                    style="border: none; background: rgba(0,0,0,0.03); color: var(--heading-color);"
                    class="input-modern w-full px-4 py-3 rounded-xl transition-all hover:border hover:border-[var(--input-border)] focus:bg-white focus:border focus:border-[var(--input-border)] focus:ring-2 focus:ring-[var(--input-focus-ring)]" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2 flex justify-center">
                <button type="submit"
                    class="theme-btn-action w-full max-w-xs py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200"
                    data-test="email-password-reset-link-button">
                    Şifre Sıfırlama Bağlantısı Gönder
                </button>
            </div>
        </form>

        <div class="text-center text-sm" style="color: var(--base-text);">
            <span>veya </span>
            <flux:link :href="route('login')" wire:navigate
                class="font-medium hover:opacity-80 transition-opacity text-skin-primary">giriş sayfasına dön
            </flux:link>
        </div>
    </div>
</x-layouts.auth>