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
                <flux:input name="email" :label="__('Email address')" type="email" required autofocus
                    autocomplete="email" placeholder="Email adresinizi girin"
                    style="border: none; background: rgba(0,0,0,0.03); color: var(--heading-color);"
                    class="input-modern w-full px-4 py-3 rounded-xl transition-all hover:border hover:border-[var(--input-border)] focus:bg-white focus:border focus:border-[var(--input-border)] focus:ring-2 focus:ring-[var(--input-focus-ring)]" />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" placeholder="Şifrenizi girin" viewable
                    style="border: none; background: rgba(0,0,0,0.03); color: var(--heading-color);"
                    class="input-modern w-full px-4 py-3 rounded-xl transition-all hover:border hover:border-[var(--input-border)] focus:bg-white focus:border focus:border-[var(--input-border)] focus:ring-2 focus:ring-[var(--input-focus-ring)]" />

                @if (Route::has('password.request'))
                    <div class="text-right mt-2">
                        <flux:link class="text-sm font-medium hover:opacity-80 transition-opacity text-skin-primary"
                            :href="route('password.request')" wire:navigate>
                            Şifrenizi mi unuttunuz?
                        </flux:link>
                    </div>
                @endif
            </div>


            <!-- Login Button -->
            <div class="pt-2 flex justify-center">
                <button type="submit"
                    class="theme-btn-action max-w-xs px-12 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200"
                    data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>



    </div>
</x-layouts.auth>