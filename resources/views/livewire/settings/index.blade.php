<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Ayarlar'])]
    class extends Component {
    public string $search = '';

    public function cards(): array
    {
        $allCards = [
            [
                'title' => 'Aktivite Logları',
                'subtitle' => 'Sistem aktivitelerini ve loglarını görüntüleyin',
                'icon' => 'o-list-bullet',
                'color' => 'bg-amber-50 text-amber-500',
                'link' => '#'
            ],
            [
                'title' => 'Değişken Yönetimi',
                'subtitle' => 'Sistem değişkenlerini ve referans verilerini yönetin',
                'icon' => 'o-tag',
                'color' => 'bg-rose-50 text-rose-500',
                'link' => route('settings.variables')
            ],
            [
                'title' => 'Depolama Ayarları',
                'subtitle' => 'Minio (S3) depolama alanı bağlantı ayarları',
                'icon' => 'o-archive-box',
                'color' => 'bg-pink-50 text-pink-500',
                'link' => route('settings.storage')
            ],
            [
                'title' => 'Fiyat Tanımları',
                'subtitle' => 'Hizmet fiyat tanımlarını oluşturun ve düzenleyin',
                'icon' => 'o-banknotes',
                'color' => 'bg-yellow-50 text-yellow-600',
                'link' => route('settings.prices')
            ],
            [
                'title' => 'Kullanıcılar',
                'subtitle' => 'Sistem kullanıcılarını ve rollerini yönetin',
                'icon' => 'o-users',
                'color' => 'bg-orange-50 text-orange-500',
                'link' => route('users.index')
            ],
            [
                'title' => 'Mail Ayarları',
                'subtitle' => 'SMTP ve Mailgun ayarlarını yapılandırın',
                'icon' => 'o-cog-6-tooth',
                'color' => 'bg-purple-50 text-purple-500',
                'link' => route('settings.mail')
            ],
            [
                'title' => 'Mail Şablonları',
                'subtitle' => 'E-posta şablonlarını oluşturun ve yönetin',
                'icon' => 'o-envelope',
                'color' => 'bg-orange-50 text-orange-500',
                'link' => '#'
            ],
            [
                'title' => 'Tema Ayarları',
                'subtitle' => 'Tema renklerini ve görünümü özelleştirin',
                'icon' => 'o-adjustments-horizontal',
                'color' => 'bg-blue-50 text-blue-600',
                'link' => '/dashboard/settings/panel'
            ],
            [
                'title' => 'Teklif Şablonu',
                'subtitle' => 'Teklif ve İndirme Sayfası ayarlarını özelleştirin',
                'icon' => 'o-document-text',
                'color' => 'bg-blue-50 text-blue-500',
                'link' => route('settings.pdf-template')
            ],
            [
                'title' => 'Veri Aktarımı',
                'subtitle' => 'Verileri toplu olarak içe ve dışa aktarın',
                'icon' => 'o-arrow-up-tray',
                'color' => 'bg-orange-50 text-orange-500',
                'link' => '#'
            ],
            [
                'title' => 'Zamanlanmış Görevler',
                'subtitle' => 'Otomatik görevleri ve zamanlamalarını yönetin',
                'icon' => 'o-clock',
                'color' => 'bg-pink-50 text-pink-500',
                'link' => '#'
            ],
            [
                'title' => 'Güvenlik',
                'subtitle' => 'İki faktörlü doğrulama ve güvenlik ayarları',
                'icon' => 'o-shield-check',
                'color' => 'bg-green-50 text-green-600',
                'link' => route('two-factor.show')
            ],
        ];

        if (empty($this->search)) {
            return $allCards;
        }

        return array_filter($allCards, function ($card) {
            return str_contains(strtolower($card['title']), strtolower($this->search)) ||
                str_contains(strtolower($card['subtitle']), strtolower($this->search));
        });
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-skin-heading">Ayarlar</h1>
                <p class="text-sm opacity-60 mt-1">Sistem ayarlarını ve
                    konfigürasyonları yönetin</p>
            </div>
            <div class="w-full md:w-72">
                <x-mary-input placeholder="Ayarlarda ara..." icon="o-magnifying-glass"
                    class="!bg-white border-gray-200 h-11" wire:model.live.debounce.300ms="search" />
            </div>
        </div>

        {{-- Settings Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($this->cards() as $card)
                <a href="{{ $card['link'] }}" class="group block">
                    <x-mary-card shadow
                        class="theme-card shadow-sm hover:shadow-md transition-all duration-300 h-[140px] overflow-hidden">
                        <div class="flex items-start gap-3 h-full relative p-2">
                            {{-- Icon Box --}}
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $card['color'] }} transition-transform duration-300 group-hover:scale-110">
                                <x-mary-icon :name="$card['icon']" class="w-6 h-6" />
                            </div>

                            {{-- Text Content --}}
                            <div class="flex-grow pt-0.5">
                                <h3
                                    class="font-bold text-[15px] group-hover:opacity-80 transition-opacity mb-1.5 text-skin-heading">
                                    {{ $card['title'] }}
                                </h3>
                                <p class="text-[12px] leading-relaxed line-clamp-2 pr-4 opacity-70">
                                    {{ $card['subtitle'] }}
                                </p>
                            </div>

                            {{-- Corner Arrow --}}
                            <div class="absolute bottom-0 right-0 p-2">
                                <x-mary-icon name="o-arrow-right"
                                    class="w-4 h-4 opacity-20 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"
                                    style="color: var(--primary-color);" />
                            </div>
                        </div>
                    </x-mary-card>
                </a>
            @empty
                <div
                    class="col-span-full py-12 flex flex-col items-center justify-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                    <x-mary-icon name="o-magnifying-glass-circle" class="w-12 h-12 mb-3 opacity-20" />
                    <p class="font-medium">Aradığınız kriterlere uygun ayar bulunamadı.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>