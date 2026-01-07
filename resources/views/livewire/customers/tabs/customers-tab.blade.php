<?php

use Livewire\Volt\Component;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $customers = Customer::query()
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
            ->orderBy('name')
            ->get();

        return [
            'customers' => $customers,
        ];
    }
}; ?>

{{-- Müşteriler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Müşteriler</h2>
            <p class="text-sm text-gray-500">Tüm müşterilerinizi görüntüleyin ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500"><span class="font-medium"
                    style="color: var(--btn-save-bg);">Aktif</span>
                {{ $customers->count() }} müşteri</span>
            <x-customer-management.action-button label="Yeni Müşteri" href="/dashboard/customers/create" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="true" :showAlphabet="true" categoryLabel="Tüm Kategoriler"
        statusLabel="Aktif" :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Şehir', 'sortable' => true],
            ['label' => 'Telefon', 'sortable' => false],
            ['label' => 'Varlık', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Hizmet', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Teklif', 'sortable' => true, 'align' => 'center'],
            ['label' => 'Satış', 'sortable' => true, 'align' => 'center'],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz müşteri kaydı bulunmuyor">
        @foreach($customers as $customer)
            @php
                $char = mb_substr($customer->name, 0, 1);
            @endphp
            <tr class="group hover:bg-slate-50/80 transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $customer->id }}'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div>
                            <div
                                class="font-bold text-slate-700 text-[13px] group-hover:text-[var(--btn-primary-bg)] transition-colors">
                                {{ $customer->name }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">{{ $customer->customer_type }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">
                    <div class="flex items-center gap-1.5 text-slate-500 italic">
                        <x-mary-icon name="o-map-pin" class="w-3.5 h-3.5 opacity-50" />
                        {{ $customer->city_id ?? 'Belirtilmedi' }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div
                        class="text-[12px] text-slate-500 font-mono tracking-tight bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100 inline-block">
                        {{ $customer->phone ?? '-' }}
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span
                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold">
                        0
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span
                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold">
                        0
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] font-bold border"
                        style="background-color: color-mix(in srgb, var(--btn-primary-bg), white 90%); color: var(--btn-primary-bg); border-color: color-mix(in srgb, var(--btn-primary-bg), white 80%);">
                        0
                    </span>
                </td>
                <td class="px-6 py-4 text-center font-bold text-[13px]" style="color: var(--btn-save-bg);">
                    0
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>

</div>

</div>