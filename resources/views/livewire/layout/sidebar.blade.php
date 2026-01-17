<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Sidebar Navigation Component                                                              ║
 * ║  🎯 ANA GÖREV: Ana navigasyon menüsü ve alt menü hiyerarşisi                                                    ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Collapsible Sidebar: $collapsed state ile açılır/kapanır menü                                                ║
 * ║  • Expandable Items: $expandedItems array ile alt menü kontrolü                                                 ║
 * ║  • Multi-level Hierarchy: 3 seviyeye kadar iç içe menü desteği                                                 ║
 * ║  • Active State Detection: request()->is() ile aktif sayfa tespiti                                             ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK (Permission-Based Visibility):                                                                     ║
 * ║  • Her menü öğesinde 'permission' anahtarı tanımlı (örn: CUSTOMERS, SETTINGS)                                  ║
 * ║  • TODO: @can directive ile yetki bazlı görünürlük implementasyonu                                              ║
 * ║  • Şu an tüm menü öğeleri görünür, yetki kontrolü eklenecek                                                     ║
 * ║                                                                                                                  ║
 * ║  📊 MENÜ HİYERARŞİSİ:                                                                                           ║
 * ║  ├── 📊 Gösterge Paneli (Dashboard)                                                                             ║
 * ║  ├── 🏢 Müşteriler (Customers)                                                                                   ║
 * ║  ├── 🌐 Varlıklar (Assets)                                                                                       ║
 * ║  ├── 🛠️ Hizmetler (Services)                                                                                     ║
 * ║  ├── 📄 Teklifler (Offers)                                                                                       ║
 * ║  ├── 💰 Satışlar (Sales)                                                                                         ║
 * ║  ├── ✉️ Mailler (Mails)                                                                                          ║
 * ║  └── ⚙️ Ayarlar (Settings)                                                                                       ║
 * ║       ├── 👤 Hesabım (Account)                                                                                   ║
 * ║       └── 📋 Tanımlar (Definitions)                                                                              ║
 * ║            ├── 👥 Kullanıcılar                                                                                   ║
 * ║            ├── 💲 Fiyat Tanımları                                                                                ║
 * ║            ├── 🗂️ Reference Data                                                                                 ║
 * ║            └── 📧 Mail Şablonları                                                                                ║
 * ║                                                                                                                  ║
 * ║  🎨 CSS VARİABLE KULLANIMI:                                                                                     ║
 * ║  • --sidebar-bg: Ana arka plan rengi                                                                            ║
 * ║  • --sidebar-text: Varsayılan metin rengi                                                                       ║
 * ║  • --sidebar-hover-bg / --sidebar-hover-text: Hover durumu                                                      ║
 * ║  • --sidebar-active-bg / --sidebar-active-text: Aktif sayfa durumu                                              ║
 * ║  • --sidebar-collapsed-width: Daraltılmış genişlik (CSS variable)                                               ║
 * ║                                                                                                                  ║
 * ║  📦 STATE YÖNETİMİ:                                                                                             ║
 * ║  • $collapsed (bool): Sidebar açık/kapalı durumu                                                                ║
 * ║  • $expandedItems (array): Açık olan alt menü ID'leri                                                           ║
 * ║                                                                                                                  ║
 * ║  🔧 MARYUI BİLEŞEN KULLANIMI:                                                                                   ║
 * ║  • Bu dosyada MaryUI kullanılmıyor, native HTML + Tailwind CSS                                                  ║
 * ║  • Emoji iconları kullanılıyor (Lucide/Heroicons yerine)                                                        ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use Livewire\Volt\Component;
use function Livewire\Volt\{state};

state(['collapsed' => false]);
state(['expandedItems' => ['settings', 'definitions']]);

$toggleCollapsed = function () {
    $this->collapsed = !$this->collapsed;
};

$toggleExpanded = function ($itemId) {
    if (in_array($itemId, $this->expandedItems)) {
        $this->expandedItems = array_filter($this->expandedItems, fn($id) => $id !== $itemId);
    } else {
        $this->expandedItems[] = $itemId;
    }
};

$menuItems = fn() => [
    [
        'id' => 'dashboard',
        'label' => 'Gösterge Paneli',
        'icon' => '📊',
        'href' => '/dashboard',
        'permission' => 'DASHBOARD',
    ],
    [
        'id' => 'customers',
        'label' => 'Müşteriler',
        'icon' => '🏢',
        'href' => '/dashboard/customers',
        'permission' => 'CUSTOMERS',
    ],
    [
        'id' => 'assets',
        'label' => 'Varlıklar',
        'icon' => '🌐',
        'href' => '/dashboard/customers?tab=assets',
        'permission' => 'ASSETS',
    ],
    [
        'id' => 'services',
        'label' => 'Hizmetler',
        'icon' => '🛠️',
        'href' => '/dashboard/customers?tab=services',
        'permission' => 'SERVICES',
    ],
    [
        'id' => 'offers',
        'label' => 'Teklifler',
        'icon' => '📄',
        'href' => '/dashboard/customers?tab=offers',
        'permission' => 'OFFERS',
    ],
    [
        'id' => 'sales',
        'label' => 'Satışlar',
        'icon' => '💰',
        'href' => '/dashboard/customers?tab=sales',
        'permission' => 'SALES',
    ],
    [
        'id' => 'mails',
        'label' => 'Mailler',
        'icon' => '✉️',
        'href' => '/dashboard/mails',
        'permission' => 'MAILS',
    ],
    [
        'id' => 'settings',
        'label' => 'Ayarlar',
        'icon' => '⚙️',
        'href' => '/dashboard/settings',
        'permission' => 'SETTINGS',
        'children' => [
            [
                'id' => 'account',
                'label' => 'Hesabım',
                'icon' => '👤',
                'href' => '/dashboard/settings/account',
                'permission' => 'SETTINGS',
            ],
            [
                'id' => 'definitions',
                'label' => 'Tanımlar',
                'icon' => '📋',
                'href' => '/dashboard/settings/definitions',
                'permission' => 'DEFINITIONS',
                'children' => [
                    [
                        'id' => 'price-definitions',
                        'label' => 'Fiyat Tanımları',
                        'icon' => '💲',
                        'href' => '/dashboard/settings/price-definitions',
                        'permission' => 'SERVICES',
                    ],
                    [
                        'id' => 'reference-data',
                        'label' => 'Reference Data',
                        'icon' => '🗂️',
                        'href' => '/dashboard/settings/reference-data',
                        'permission' => 'DEFINITIONS',
                    ],
                    [
                        'id' => 'mail-templates',
                        'label' => 'Mail Şablonları',
                        'icon' => '📧',
                        'href' => '/dashboard/settings/mail-templates',
                        'permission' => 'MAIL_TEMPLATES',
                    ],
                ],
            ],
        ],
    ],
];

?>

<div class="bg-[var(--sidebar-bg)] border-r border-[var(--card-border)] transition-all duration-300 flex flex-col {{ $collapsed ? 'w-[var(--sidebar-collapsed-width)]' : 'w-64' }}"
    style="color: var(--sidebar-text);">

    @include('livewire.layout.partials._sidebar-header', ['collapsed' => $collapsed])

    @include('livewire.layout.partials._sidebar-menu', ['collapsed' => $collapsed, 'expandedItems' => $expandedItems])

    @include('livewire.layout.partials._sidebar-footer', ['collapsed' => $collapsed])
</div>