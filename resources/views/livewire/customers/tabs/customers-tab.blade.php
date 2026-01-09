<?php

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
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedLetter()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Customer::whereIn('id', $this->selected)->delete();

        $this->success('İşlem Başarılı', count($this->selected) . ' müşteri silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Customer::query()
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'ilike', '%' . $this->search . '%');
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("name ~ '^[0-9]'");
                } else {
                    $query->where('name', 'ilike', $this->letter . '%');
                }
            })
            ->orderBy('name');
    }

    public function with(ReferenceDataService $service): array
    {
        // Safe City Lookup for mixed types (UUID vs Plate Code)
        $cities = City::all();
        $cityMap = [];
        $cityColorMap = [];
        $schemes = $service->getColorSchemes();
        $schemeCount = count($schemes);

        foreach ($cities as $c) {
            $cityMap[(string) $c->id] = $c->name; // UUID key

            // Deterministic color generation based on city name
            // Use simple hashing to pick a color index
            $colorIndex = crc32($c->name) % $schemeCount;
            $scheme = $schemes[$colorIndex];
            $colorClass = "{$scheme['bg']} {$scheme['text']} {$scheme['border']}";

            $cityColorMap[(string) $c->id] = $colorClass;

            if ($c->plate_code) {
                // Handle plate code with and without leading zero just in case
                $cityMap[(string) $c->plate_code] = $c->name;
                $cityMap[(int) $c->plate_code] = $c->name; // For integer lookups (17)

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

{{-- Müşteriler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold" class="text-skin-heading">Müşteriler</h2>
            <p class="text-sm opacity-60">Tüm müşterilerinizi görüntüleyin ve
                yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} müşteriyi silmek istediğinize emin misiniz?"
                    class="btn-danger-outline">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Seçilileri Sil ({{ count($selected) }})
                </button>
            @endif

            <span class="text-sm opacity-60">
                <span class="font-medium" style="color: var(--btn-save-bg);">Aktif</span>
                {{ $customers->total() }} müşteri</span>
            <x-customer-management.action-button label="Yeni Müşteri" href="/dashboard/customers/create" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="true" :showAlphabet="true" :showStatus="false"
        categoryLabel="Tüm Kategoriler" statusLabel="Aktif" :letter="$letter">
    </x-customer-management.filter-panel>

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => '', 'sortable' => false, 'width' => '40px'], // Checkbox column
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
                            <th
                                class="px-6 py-3 font-semibold text-skin-base {{ isset($header['align']) && $header['align'] == 'center' ? 'text-center' : '' }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        @php
                            $char = mb_substr($customer->name, 0, 1);
                        @endphp
                        <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                            onclick="window.location.href='/dashboard/customers/{{ $customer->id }}'">
                            <td class="px-6 py-4" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <x-mary-avatar placeholder="{{ $char }}"
                                            class="!w-9 !h-9 font-semibold text-xs shadow-sm"
                                            style="background-color: var(--table-avatar-bg); border: 1px solid var(--table-avatar-border); color: var(--table-avatar-text);" />
                                    </div>
                                    <div>
                                        <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                                            style="color: var(--list-card-link-color);">
                                            {{ $customer->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $cityName = $cityMap[$customer->city_id] ?? 'Belirtilmedi';
                                    $cityColor = $cityColorMap[$customer->city_id] ?? 'bg-skin-hover text-skin-muted border-skin-light';
                                @endphp
                                @if($cityName !== 'Belirtilmedi')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $cityColor }}">
                                        {{ $cityName }}
                                    </span>
                                @else
                                    <span class="text-skin-muted italic text-xs">-</span>
                                @endif
                            </td>

                            {{-- Counts Columns --}}
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->contacts_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->assets_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->services_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->offers_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->sales_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                                    {{ $customer->messages_count ?? 0 }}
                                </span>
                            </td>
                        </tr>
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

        {{-- Pagination --}}
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

            <div>
                {{ $customers->links() }}
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>