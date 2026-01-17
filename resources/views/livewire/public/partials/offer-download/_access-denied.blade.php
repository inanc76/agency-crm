{{--
🚫 PARTIAL: Access Denied / Expired State
---------------------------------------------------------------------
Teklif indirilemez durumda olduğunda gösterilen ekran.

@security-note [Triggers]:
Bu ekran şu durumlarda tetiklenir ($isBlocked = true):
1. Geçerlilik Tarihi Dolmuşsa (valid_until < now). 2. Yönetici panelinden "Süresi dolsa bile indirilebilsin" ayarı
    KAPALI ise. 3. Teklif statüsü iptal/reddedilmiş ise (Logic dependent). ÖZELLİKLER: - Yeni Teklif İste Modalı (Modal
    Interaction). - İletişim formu validasyonu. ---------------------------------------------------------------------
    --}} {{-- BLOCKED / EXPIRED STATE --}} <div class="flex flex-col items-center">
    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-6 text-gray-400">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>

    <h2 class="text-xl font-bold text-gray-900 mb-3">Teklif Geçerlilik Süresi Doldu</h2>
    <p class="text-gray-500 text-sm mb-8 leading-relaxed max-w-md mx-auto">
        Teklif geçerlilik süresi dolduğu için indirilemez.<br>
        Yeni bir teklif almak için lütfen aşağıdaki butonu kullanınız.
    </p>

    @if($formSent)
        <div class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-xl flex items-center gap-3 w-full">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">Talebiniz başarıyla iletildi. En kısa sürede size dönüş yapacağız.</span>
        </div>
    @else
        <button wire:click="$set('showRequestModal', true)"
            class="w-full max-w-sm bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] cursor-pointer">
            YENİ TEKLİF İSTE
        </button>
    @endif
    </div>

    {{-- Custom Modal --}}
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity"
            x-data x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative transform transition-all"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Yeni Teklif İste</h3>
                    <button wire:click="$set('showRequestModal', false)"
                        class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="requestNewOffer" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Firma
                            Adı</label>
                        <input type="text" wire:model="company_name"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                        @error('company_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Adı
                            Soyadı</label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Telefon</label>
                            <input type="text" wire:model="phone"
                                class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                            @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">E-Posta</label>
                            <input type="email" wire:model="email"
                                class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Notunuz</label>
                        <textarea wire:model="note" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm resize-none"></textarea>
                        @error('note') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                            GÖNDER
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif