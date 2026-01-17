<x-layouts.guest title="Şifre Belirle">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" 
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        
        <div class="max-w-md w-full space-y-8">
            {{-- Header --}}
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center mb-6">
                    <x-mary-icon name="o-key" class="w-8 h-8 text-purple-600" />
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">
                    Şifrenizi Belirleyin
                </h2>
                <p class="text-purple-100">
                    Merhaba {{ $user->name }}, hesabınız için güvenli bir şifre oluşturun
                </p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <form method="POST" action="{{ route('setup-password.store') }}" class="space-y-6">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- User Info --}}
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-semibold text-sm">
                                        {{ $user->initials() }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                @if($user->title)
                                    <p class="text-xs text-gray-400">{{ $user->title }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Password Field --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Yeni Şifre
                        </label>
                        <x-mary-input 
                            name="password" 
                            type="password" 
                            placeholder="En az 8 karakter"
                            class="w-full"
                            required />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Şifre Tekrarı
                        </label>
                        <x-mary-input 
                            name="password_confirmation" 
                            type="password" 
                            placeholder="Şifrenizi tekrar girin"
                            class="w-full"
                            required />
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Requirements --}}
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Şifre Gereksinimleri:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li class="flex items-center">
                                <x-mary-icon name="o-check-circle" class="w-4 h-4 mr-2" />
                                En az 8 karakter uzunluğunda
                            </li>
                            <li class="flex items-center">
                                <x-mary-icon name="o-check-circle" class="w-4 h-4 mr-2" />
                                Büyük ve küçük harf içermeli
                            </li>
                            <li class="flex items-center">
                                <x-mary-icon name="o-check-circle" class="w-4 h-4 mr-2" />
                                En az bir rakam içermeli
                            </li>
                        </ul>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                            <x-mary-icon name="o-key" class="w-5 h-5 mr-2" />
                            Şifremi Oluştur
                        </button>
                    </div>

                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <x-mary-icon name="o-exclamation-triangle" class="w-5 h-5 text-red-400 mr-2" />
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Hata oluştu:</h3>
                                    <ul class="mt-1 text-sm text-red-700">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>

                {{-- Help Text --}}
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        Şifrenizi oluşturduktan sonra otomatik olarak sisteme giriş yapacaksınız.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center">
                <p class="text-sm text-purple-100">
                    Sorun mu yaşıyorsunuz? 
                    <a href="mailto:{{ config('mail.from.address') }}" class="font-medium text-white hover:text-purple-200 underline">
                        Destek ekibiyle iletişime geçin
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>