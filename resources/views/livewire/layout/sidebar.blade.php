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
                        'id' => 'users',
                        'label' => 'Kullanıcılar',
                        'icon' => '👥',
                        'href' => '/dashboard/settings/users',
                        'permission' => 'USERS',
                    ],
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
    <!-- Logo/Brand -->
    <div class="p-4 border-b border-[var(--card-border)]">
        @if (!$collapsed)
            <h1 class="text-h2 text-[var(--color-text-primary)] font-bold">Ajans Panel</h1>
        @else
            <div class="text-2xl text-center">🏢</div>
        @endif
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 overflow-y-auto">
        @php
            $menuItems = [
                ['id' => 'dashboard', 'label' => 'Gösterge Paneli', 'icon' => '📊', 'href' => '/dashboard', 'permission' => 'DASHBOARD'],
                ['id' => 'customers', 'label' => 'Müşteriler', 'icon' => '🏢', 'href' => '/dashboard/customers', 'permission' => 'CUSTOMERS'],
                ['id' => 'assets', 'label' => 'Varlıklar', 'icon' => '🌐', 'href' => '/dashboard/customers?tab=assets', 'permission' => 'ASSETS'],
                ['id' => 'services', 'label' => 'Hizmetler', 'icon' => '🛠️', 'href' => '/dashboard/customers?tab=services', 'permission' => 'SERVICES'],
                ['id' => 'offers', 'label' => 'Teklifler', 'icon' => '📄', 'href' => '/dashboard/customers?tab=offers', 'permission' => 'OFFERS'],
                ['id' => 'sales', 'label' => 'Satışlar', 'icon' => '💰', 'href' => '/dashboard/customers?tab=sales', 'permission' => 'SALES'],
                ['id' => 'mails', 'label' => 'Mailler', 'icon' => '✉️', 'href' => '/dashboard/mails', 'permission' => 'MAILS'],
                [
                    'id' => 'settings',
                    'label' => 'Ayarlar',
                    'icon' => '⚙️',
                    'href' => '/dashboard/settings',
                    'permission' => 'SETTINGS',
                    'children' => [
                        ['id' => 'account', 'label' => 'Hesabım', 'icon' => '👤', 'href' => '/dashboard/settings/account', 'permission' => 'SETTINGS'],
                        [
                            'id' => 'definitions',
                            'label' => 'Tanımlar',
                            'icon' => '📋',
                            'href' => '/dashboard/settings/definitions',
                            'permission' => 'DEFINITIONS',
                            'children' => [
                                ['id' => 'users', 'label' => 'Kullanıcılar', 'icon' => '👥', 'href' => '/dashboard/settings/users', 'permission' => 'USERS'],
                                ['id' => 'price-definitions', 'label' => 'Fiyat Tanımları', 'icon' => '💲', 'href' => '/dashboard/settings/price-definitions', 'permission' => 'SERVICES'],
                                ['id' => 'reference-data', 'label' => 'Reference Data', 'icon' => '🗂️', 'href' => '/dashboard/settings/reference-data', 'permission' => 'DEFINITIONS'],
                                ['id' => 'mail-templates', 'label' => 'Mail Şablonları', 'icon' => '📧', 'href' => '/dashboard/settings/mail-templates', 'permission' => 'MAIL_TEMPLATES'],
                            ],
                        ],
                    ],
                ],
            ];
        @endphp
        @foreach ($menuItems as $item)
            @php
                $isActive = request()->is(ltrim($item['href'], '/')) ||
                    request()->is(ltrim($item['href'], '/') . '/*');
                $hasChildren = isset($item['children']) && count($item['children']) > 0;
                $isExpanded = in_array($item['id'], $expandedItems);
            @endphp

            <div class="mb-1">
                @if ($hasChildren)
                    <div wire:click="toggleExpanded('{{ $item['id'] }}')"
                        class="flex items-center px-3 py-2 rounded-lg cursor-pointer transition-colors text-[var(--sidebar-text)] hover:bg-[var(--sidebar-hover-bg)] hover:text-[var(--sidebar-hover-text)]">
                        @if (!$collapsed)
                            <span class="text-lg mr-3">{{ $item['icon'] }}</span>
                            <span class="text-body font-medium flex-1">{{ $item['label'] }}</span>
                            <span class="transform transition-transform {{ $isExpanded ? 'rotate-90' : '' }}">▶</span>
                        @else
                            <span class="text-lg">{{ $item['icon'] }}</span>
                        @endif
                    </div>

                    @if ($hasChildren && $isExpanded && !$collapsed)
                        <div class="mt-1 ml-4">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childIsActive = request()->is(ltrim($child['href'], '/')) ||
                                        request()->is(ltrim($child['href'], '/') . '/*');
                                    $childHasChildren = isset($child['children']) && count($child['children']) > 0;
                                    $childIsExpanded = in_array($child['id'], $expandedItems);
                                @endphp

                                <div class="mb-1">
                                    @if ($childHasChildren)
                                        <div wire:click="toggleExpanded('{{ $child['id'] }}')"
                                            class="flex items-center px-3 py-2 rounded-lg cursor-pointer transition-colors text-[var(--sidebar-text)] hover:bg-[var(--sidebar-hover-bg)] hover:text-[var(--sidebar-hover-text)]">
                                            <span class="text-lg mr-3">{{ $child['icon'] }}</span>
                                            <span class="text-body font-medium flex-1">{{ $child['label'] }}</span>
                                            <span class="transform transition-transform {{ $childIsExpanded ? 'rotate-90' : '' }}">▶</span>
                                        </div>

                                        @if ($childIsExpanded)
                                            <div class="mt-1 ml-4">
                                                @foreach ($child['children'] as $grandchild)
                                                    @php
                                                        $grandchildIsActive = request()->is(ltrim($grandchild['href'], '/')) ||
                                                            request()->is(ltrim($grandchild['href'], '/') . '/*');
                                                    @endphp

                                                    <a href="{{ $grandchild['href'] }}" class="block">
                                                        <div
                                                            class="flex items-center px-3 py-2 rounded-lg cursor-pointer transition-colors {{ $grandchildIsActive ? 'bg-[var(--sidebar-active-bg)] text-[var(--sidebar-active-text)]' : 'text-[var(--sidebar-text)] hover:bg-[var(--sidebar-hover-bg)] hover:text-[var(--sidebar-hover-text)]' }}">
                                                            <span class="text-lg mr-3">{{ $grandchild['icon'] }}</span>
                                                            <span class="text-body font-medium">{{ $grandchild['label'] }}</span>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <a href="{{ $child['href'] }}" class="block">
                                            <div
                                                class="flex items-center px-3 py-2 rounded-lg cursor-pointer transition-colors {{ $childIsActive ? 'bg-[var(--sidebar-active-bg)] text-[var(--sidebar-active-text)]' : 'text-[var(--sidebar-text)] hover:bg-[var(--sidebar-hover-bg)] hover:text-[var(--sidebar-hover-text)]' }}">
                                                <span class="text-lg mr-3">{{ $child['icon'] }}</span>
                                                <span class="text-body font-medium">{{ $child['label'] }}</span>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <a href="{{ $item['href'] }}" class="block">
                        <div
                            class="flex items-center px-3 py-2 rounded-lg cursor-pointer transition-colors {{ $isActive ? 'bg-[var(--sidebar-active-bg)] text-[var(--sidebar-active-text)]' : 'text-[var(--sidebar-text)] hover:bg-[var(--sidebar-hover-bg)] hover:text-[var(--sidebar-hover-text)]' }}">
                            @if (!$collapsed)
                                <span class="text-lg mr-3">{{ $item['icon'] }}</span>
                                <span class="text-body font-medium">{{ $item['label'] }}</span>
                            @else
                                <span class="text-lg">{{ $item['icon'] }}</span>
                            @endif
                        </div>
                    </a>
                @endif
            </div>
        @endforeach
    </nav>

    <!-- Toggle Button -->
    <div class="p-4 border-t border-[var(--card-border)]">
        <button wire:click="toggleCollapsed"
            class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-[var(--dropdown-hover-bg)] transition-colors">
            <span class="text-lg">
                {{ $collapsed ? '▶' : '◀' }}
            </span>
        </button>
    </div>
</div>