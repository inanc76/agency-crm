<x-layouts.app title="Dashboard">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gösterge Paneli</h1>
            <p class="text-gray-600">Hoş geldiniz! İşte sisteminizdeki genel bakış.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Müşteriler Card -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Aktif</span>
                </div>
                <h2 class="text-2xl font-bold mb-1">Müşteriler</h2>
                <p class="text-4xl font-extrabold mb-2">7</p>
                <p class="text-blue-100 text-sm">Toplam müşteri sayısı</p>
            </div>

            <!-- Hizmetler Card -->
            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Aktif</span>
                </div>
                <h2 class="text-2xl font-bold mb-1">Hizmetler</h2>
                <p class="text-4xl font-extrabold mb-2">9</p>
                <p class="text-emerald-100 text-sm">Aktif hizmet sayısı</p>
            </div>

            <!-- Varlıklar Card -->
            <div
                class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Toplam</span>
                </div>
                <h2 class="text-2xl font-bold mb-1">Varlıklar</h2>
                <p class="text-4xl font-extrabold mb-2">5</p>
                <p class="text-amber-100 text-sm">Toplam varlık sayısı</p>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl shadow-sm border border-purple-100 p-8">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Hoş Geldiniz!</h2>
                    <p class="text-gray-700 mb-4">
                        Laravel layout migrasyonu başarıyla tamamlandı. Yeni tasarımınız hazır!
                    </p>
                    <button
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        Başlayın
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>