<?php

use Livewire\Volt\Component;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $offers = Offer::query()
            ->with('customer')
            ->when($this->search, function (Builder $query) {
                $query->where('title', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("title ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('title', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'offers' => $offers,
        ];
    }
}; ?>

{{-- Teklifler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold" style="color: var(--color-text-heading);">Teklifler</h2>
            <p class="text-sm opacity-60" style="color: var(--color-text-base);">Tüm müşteri tekliflerini görüntüleyin
                ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm opacity-60" style="color: var(--color-text-base);">{{ $offers->count() }} teklif</span>
            <x-customer-management.action-button label="Yeni Teklif" href="#" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="false" :showAlphabet="true" statusLabel="Duruma Göre Filtrele"
        :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Teklif Başlığı', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Teklif Tarihi', 'sortable' => true],
            ['label' => 'Geçerlilik Tarihi', 'sortable' => true],
            ['label' => 'Durum', 'sortable' => false],
            ['label' => 'Hizmet Sayısı', 'sortable' => false, 'align' => 'center'],
            ['label' => 'Son Fiyat', 'sortable' => true, 'align' => 'right'],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz teklif kaydı bulunmuyor">
        @foreach($offers as $offer)
            @php
                $char = mb_substr($offer->title, 0, 1);
            @endphp
            <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $offer->customer_id }}?tab=offers'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div>
                            <div class="font-bold text-[13px] group-hover:opacity-80 transition-opacity"
                                style="color: var(--color-text-heading);">
                                {{ $offer->title }}
                            </div>
                            <div class="text-[11px] font-medium opacity-60" style="color: var(--color-text-base);">
                                {{ $offer->offer_no ?? 'No Belirtilmedi' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] font-medium" style="color: var(--color-text-base);">
                    {{ $offer->customer->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70" style="color: var(--color-text-base);">
                    {{ $offer->offer_date?->format('d.m.Y') ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70" style="color: var(--color-text-base);">
                    {{ $offer->valid_until?->format('d.m.Y') ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <x-customer-management.status-badge :status="$offer->status ?? 'draft'" />
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] font-bold"
                        style="background-color: var(--card-bg); color: var(--color-text-heading); border: 1px solid var(--card-border);">
                        0
                    </span>
                </td>
                <td class="px-6 py-4 text-right font-bold text-[13px]" style="color: var(--color-text-heading);">
                    {{ number_format($offer->total_amount, 2) }} {{ $offer->currency }}
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>