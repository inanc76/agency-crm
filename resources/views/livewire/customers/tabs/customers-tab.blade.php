<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11 (ATOMIC)                                   ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Müşteri Listesi Tab                                                                       ║
 * ║  🎯 ANA GÖREV: Müşteri listeleme, filtreleme, arama ve toplu işlemler                                          ║
 * ║                                                                                                                  ║
 * ║  📦 PARTIAL YAPISI:                                                                                             ║
 * ║  • _customers-header.blade.php: Başlık, sayaç ve aksiyon butonları                                             ║
 * ║  • _customers-row.blade.php: Tablo satırı (avatar, şehir badge, count göstergeleri)                            ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Arama: ilike ile case-insensitive arama                                                                      ║
 * ║  • Harf Filtresi: A-Z ve 0-9 alfabetik filtreleme                                                              ║
 * ║  • Toplu Seçim: Checkbox ile çoklu müşteri seçimi ve toplu silme                                               ║
 * ║  • Sayfalama: Dinamik perPage ile pagination                                                                    ║
 * ║                                                                                                                  ║
 * ║  🔗 EXTERNAL COMPONENTS:                                                                                        ║
 * ║  • x-customer-management.filter-panel: Filtreleme paneli                                                        ║
 * ║  • x-customer-management.action-button: Yeni müşteri butonu                                                     ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\City;
use App\Services\ReferenceDataService;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';
    public string $letter = '';

    // Pagination & Selection
    public int $perPage = 25;
    public array $selected = [];
    public bool $selectAll = false;

    // Reset pagination when filtering
    public function updatedSearch() { $this->resetPage(); }
    public function updatedLetter() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        $this->selected = $value 
            ? $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray() 
            : [];
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        Customer::whereIn('id', $this->selected)->delete();
        $this->success('İşlem Başarılı', count($this->selected) . ' müşteri silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Customer::query()
            ->when($this->search, fn(Builder $q) => $q->where('name', 'ilike', '%' . $this->search . '%'))
            ->when($this->letter, function (Builder $q) {
                return $this->letter === '0-9' 
                    ? $q->whereRaw("name ~ '^[0-9]'") 
                    : $q->where('name', 'ilike', $this->letter . '%');
            })
            ->orderBy('name');
    }

    public function with(ReferenceDataService $service): array
    {
        $cities = City::all();
        $cityMap = [];
        $cityColorMap = [];
        $schemes = $service->getColorSchemes();
        $schemeCount = count($schemes);

        foreach ($cities as $c) {
            $cityMap[(string) $c->id] = $c->name;
            $colorIndex = crc32($c->name) % $schemeCount;
            $colorClass = "{$schemes[$colorIndex]['bg']} {$schemes[$colorIndex]['text']} {$schemes[$colorIndex]['border']}";
            $cityColorMap[(string) $c->id] = $colorClass;

            if ($c->plate_code) {
                $cityMap[(string) $c->plate_code] = $c->name;
                $cityMap[(int) $c->plate_code] = $c->name;
                $cityColorMap[(string) $c->plate_code] = $colorClass;
                $cityColorMap[(int) $c->plate_code] = $colorClass;
            }
        }

        return [
            'customers' => $this->getQuery()
                ->withCount(['contacts', 'assets', 'services', 'offers', 'sales', 'messages'])
                ->paginate($this->perPage),
            'cityMap' => $cityMap,
            'cityColorMap' => $cityColorMap,
        ];
    }
}; ?>

<div>
    {{-- SECTION: Header --}}
    @include('livewire.customers.tabs.partials._customers-header', [
        'selected' => $selected,
        'customers' => $customers
    ])

    {{-- SECTION: Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="true" :showAlphabet="true" :showStatus="false"
        categoryLabel="Tüm Kategoriler" statusLabel="Aktif" :letter="$letter" />

    {{-- SECTION: Data Table --}}
    @php
        $headers = [
            ['label' => '', 'sortable' => false, 'width' => '40px'],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Şehir', 'sortable' => true],
            ['label' => 'Kişiler', 'sortable' => false, 'align' => 'center'],
            ['label' => 'Varlıklar', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Hizmetler', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Teklifler', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Satışlar', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Mesajlar', 'sortable' => true, 'align' => 'center'],
        ];
    @endphp

    <div class="theme-card shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b"
                    style="background-color: var(--table-hover-bg); border-color: var(--card-border); color: var(--table-hover-text);">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th class="px-6 py-3 font-semibold text-skin-base {{ isset($header['align']) && $header['align'] == 'center' ? 'text-center' : '' }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        @include('livewire.customers.tabs.partials._customers-row', [
                            'customer' => $customer,
                            'cityMap' => $cityMap,
                            'cityColorMap' => $cityColorMap
                        ])
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-inbox" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz müşteri kaydı bulunmuyor</div>
                                    <div class="text-xs opacity-60 mt-1">Yeni müşteri ekleyerek başlayın</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- SECTION: Pagination --}}
        <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-skin-muted">Göster:</span>
                <select wire:model.live="perPage"
                    class="select select-xs bg-white border-skin-light text-xs w-18 h-8 min-h-0 focus:outline-none focus:border-slate-400">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>
            <div>{{ $customers->links() }}</div>
            <div class="text-[10px] text-skin-muted font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>