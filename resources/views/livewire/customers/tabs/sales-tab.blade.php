<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $customerId = '';
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

        Sale::whereIn('id', $this->selected)->delete();

        $this->success('İşlem Başarılı', count($this->selected) . ' satış silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Sale::query()
            ->with(['customer', 'offer'])
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->when($this->search, function (Builder $query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereHas('customer', function ($q) {
                        $q->whereRaw("name ~ '^[0-9]'");
                    });
                } else {
                    $query->whereHas('customer', function ($q) {
                        $q->where('name', 'ilike', $this->letter . '%');
                    });
                }
            })
            ->orderBy('sale_date', 'desc');
    }

    public function with(): array
    {
        return [
            'sales' => $this->getQuery()->paginate($this->perPage),
        ];
    }
}; ?>

{{-- Satışlar Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-skin-heading">Satışlar</h2>
            <p class="text-sm text-skin-muted">Tüm satış kayıtlarını görüntüleyin ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} satışı silmek istediğinize emin misiniz?"
                    class="btn-danger-outline">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Seçilileri Sil ({{ count($selected) }})
                </button>
            @endif

            <span class="text-sm opacity-60">{{ $sales->total() }} satış</span>
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="false" :showAlphabet="true" statusLabel="Duruma Göre Filtrele"
        :letter="$letter">

    </x-customer-management.filter-panel>

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => '', 'sortable' => false, 'width' => '40px'],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Teklif No', 'sortable' => false],
            ['label' => 'Tutar', 'sortable' => true, 'align' => 'right'],
            ['label' => 'Satış Tarihi', 'sortable' => true],
            ['label' => 'Kayıt Tarihi', 'sortable' => true],
        ];
    @endphp

    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-skin-light">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th
                                class="px-6 py-3 font-semibold text-skin-base {{ isset($header['align']) && $header['align'] == 'center' ? 'text-center' : (isset($header['align']) && $header['align'] == 'right' ? 'text-right' : '') }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        @php
                            $char = mb_substr($sale->customer->name ?? '?', 0, 1);
                        @endphp
                        <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                            onclick="window.location.href='/dashboard/customers/{{ $sale->customer_id }}?tab=sales'">
                            <td class="px-6 py-4" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $sale->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-sm font-semibold"
                                            style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                                            {{ $char }}
                                        </div>
                                    </div>
                                    <div class="text-[13px] group-hover:opacity-80 transition-opacity font-medium"
                                        style="color: var(--list-card-link-color);">
                                        {{ $sale->customer->name ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-skin-muted font-mono">
                                {{ $sale->offer->number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-skin-base text-[13px]">
                                {{ number_format($sale->amount, 2) }} {{ $sale->currency }}
                            </td>
                            <td class="px-6 py-4 text-[12px] text-skin-muted font-mono text-center">
                                {{ $sale->sale_date?->format('d.m.Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 italic text-skin-muted text-[11px] text-center">
                                {{ $sale->created_at?->format('d.m.Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-currency-dollar" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz satış kaydı bulunmuyor</div>
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
                {{ $sales->links() }}
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>