<x-layouts.auth>
    <div class="space-y-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold mb-1 text-skin-heading">Giriş Yap</h2>
            <p class="text-sm text-skin-base">Hesabınıza erişmek için bilgilerinizi girin</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <flux:input name="email" :label="__('Email address')" value="admin@mediaclick.com.tr" type="email"
                    required autofocus autocomplete="email" placeholder="admin@mediaclick.com.tr"
                    class="input-modern w-full px-4 py-3 rounded-xl border-2 border-[var(--card-border)] focus:border-[var(--color-focus)] focus:ring-4 focus:ring-[var(--brand-primary)]/20 transition-all" />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" value="admin" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable
                    class="input-modern w-full px-4 py-3 rounded-xl border-2 border-[var(--card-border)] focus:border-[var(--color-focus)] focus:ring-4 focus:ring-[var(--brand-primary)]/20 transition-all" />

                @if (Route::has('password.request'))
                    <div class="text-right mt-2">
                        <flux:link class="text-sm font-medium hover:opacity-80 transition-opacity text-skin-primary"
                            :href="route('password.request')" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    </div>
                @endif
            </div>


            <!-- Login Button -->
            <div class="pt-2">
                <flux:button variant="primary" type="submit"
                    class="w-full py-3 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 bg-skin-primary"
                    data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>



    </div>
</x-layouts.auth>