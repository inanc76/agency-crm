<?php

use Livewire\Volt\Component;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $sales = Sale::query()
            ->with(['customer', 'offer'])
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
            ->orderBy('sale_date', 'desc')
            ->get();

        return [
            'sales' => $sales,
        ];
    }
}; ?>

{{-- Satışlar Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Satışlar</h2>
            <p class="text-sm text-gray-500">Tüm satış kayıtlarını görüntüleyin ve yönetin</p>
        </div>
        <x-customer-management.action-button label="Yeni Satış" href="#" />
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="false" :showAlphabet="true" statusLabel="Duruma Göre Filtrele"
        :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Teklif No', 'sortable' => false],
            ['label' => 'Tutar', 'sortable' => true, 'align' => 'right'],
            ['label' => 'Satış Tarihi', 'sortable' => true],
            ['label' => 'Kayıt Tarihi', 'sortable' => true],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz satış kaydı bulunmuyor">
        @foreach($sales as $sale)
            @php
                $char = mb_substr($sale->customer->name ?? '?', 0, 1);
            @endphp
            <tr class="group hover:bg-slate-50/80 transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $sale->customer_id }}?tab=sales'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div class="font-bold text-slate-700 text-[13px] group-hover:text-blue-600 transition-colors">
                            {{ $sale->customer->name ?? '-' }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-500 font-mono">
                    {{ $sale->offer->offer_no ?? '-' }}
                </td>
                <td class="px-6 py-4 text-right font-bold text-slate-700 text-[13px]">
                    {{ number_format($sale->amount, 2) }} {{ $sale->currency }}
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500 font-mono text-center">
                    {{ $sale->sale_date?->format('d.m.Y') ?? '-' }}
                </td>
                <td class="px-6 py-4 italic text-slate-400 text-[11px] text-center">
                    {{ $sale->created_at?->format('d.m.Y H:i') ?? '-' }}
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>