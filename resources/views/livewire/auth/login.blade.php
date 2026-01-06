<x-layouts.auth>
    <div class="space-y-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">Giriş Yap</h2>
            <p class="text-sm text-gray-600">Hesabınıza erişmek için bilgilerinizi girin</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <flux:input name="email" :label="__('Email address')" value="admin@mediaclick.com.tr" type="email"
                    required autofocus autocomplete="email" placeholder="admin@mediaclick.com.tr"
                    class="input-modern w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all" />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" value="admin" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable
                    class="input-modern w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all" />

                @if (Route::has('password.request'))
                    <div class="text-right mt-2">
                        <flux:link class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                            :href="route('password.request')" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    </div>
                @endif
            </div>


            <!-- Login Button -->
            <div class="pt-2">
                <flux:button variant="primary" type="submit" 
                    class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200" 
                    data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>



    </div>
</x-layouts.auth>