<x-layouts.app title="Dashboard">
    <div class="space-y-6 max-w-7xl mx-auto mt-[30px]">
        <!-- Page Header -->
        <div class="theme-card p-6">
            <h1 class="text-3xl font-bold mb-2 text-skin-heading">Gösterge Paneli</h1>
            <p class="text-skin-base">Hoş geldiniz! İşte sisteminizdeki genel bakış.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Müşteriler Card -->
            <div class="theme-card p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-1), white 90%); color: var(--dashboard-stats-1);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium px-3 py-1 rounded-full"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-1), white 90%); color: var(--dashboard-stats-1);">Aktif</span>
                </div>
                <h2 class="text-2xl font-bold mb-1 text-skin-heading">Müşteriler</h2>
                <p class="text-4xl font-extrabold mb-2 text-skin-heading">7</p>
                <p class="text-sm text-skin-base">Toplam müşteri sayısı</p>
            </div>

            <!-- Hizmetler Card -->
            <div class="theme-card p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-2), white 90%); color: var(--dashboard-stats-2);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium px-3 py-1 rounded-full"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-2), white 90%); color: var(--dashboard-stats-2);">Aktif</span>
                </div>
                <h2 class="text-2xl font-bold mb-1 text-skin-heading">Hizmetler</h2>
                <p class="text-4xl font-extrabold mb-2 text-skin-heading">9</p>
                <p class="text-sm text-skin-base">Aktif hizmet sayısı</p>
            </div>

            <!-- Varlıklar Card -->
            <div class="theme-card p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-3), white 90%); color: var(--dashboard-stats-3);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium px-3 py-1 rounded-full"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-3), white 90%); color: var(--dashboard-stats-3);">Toplam</span>
                </div>
                <h2 class="text-2xl font-bold mb-1 text-skin-heading">Varlıklar</h2>
                <p class="text-4xl font-extrabold mb-2 text-skin-heading">5</p>
                <p class="text-sm text-skin-base">Toplam varlık sayısı</p>
            </div>

            <!-- Teklifler Card -->
            <div class="theme-card p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-4), white 90%); color: var(--dashboard-stats-4);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium px-3 py-1 rounded-full"
                        style="background-color: color-mix(in srgb, var(--dashboard-stats-4), white 90%); color: var(--dashboard-stats-4);">Gönderildi</span>
                </div>
                <h2 class="text-2xl font-bold mb-1 text-skin-heading">Teklifler</h2>
                <p class="text-4xl font-extrabold mb-2 text-skin-heading">
                    {{ \App\Models\Offer::where('status', 'SENT')->count() }}</p>
                <p class="text-sm text-skin-base">Gönderilen teklif sayısı</p>
            </div>
        </div>
    </div>
</x-layouts.app>