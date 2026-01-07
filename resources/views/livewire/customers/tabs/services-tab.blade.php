<?php

use Livewire\Volt\Component;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $services = Service::query()
            ->with(['customer', 'asset'])
            ->when($this->search, function (Builder $query) {
                $query->where('service_name', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("service_name ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('service_name', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->orderBy('service_name')
            ->get();

        return [
            'services' => $services,
        ];
    }
}; ?>

{{-- Hizmetler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Hizmetler</h2>
            <p class="text-sm text-gray-500">Müşteriye ait hizmetleri görüntüleyin ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500"><span class="font-medium"
                    style="color: var(--btn-save-bg);">Aktif</span>
                {{ $services->count() }} hizmet</span>
            <x-customer-management.action-button label="Yeni Hizmet" href="#" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="true" :showAlphabet="true" categoryLabel="Tüm Kategoriler"
        statusLabel="Aktif" :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Varlık / Hizmet', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Süre', 'sortable' => true],
            ['label' => 'Başlangıç', 'sortable' => true],
            ['label' => 'Bitiş', 'sortable' => true],
            ['label' => 'Kalan Gün', 'sortable' => true],
            ['label' => 'Hizmet Durumu', 'sortable' => false],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz hizmet kaydı bulunmuyor">
        @foreach($services as $service)
            @php
                $char = mb_substr($service->service_name, 0, 1);
            @endphp
            <tr class="group hover:bg-slate-50/80 transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $service->customer_id }}?tab=services'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div>
                            <div class="font-bold text-slate-700 text-[13px] group-hover:text-blue-600 transition-colors">
                                {{ $service->asset->name ?? 'Varlık Yok' }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">{{ $service->service_name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">
                    {{ $service->customer->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-500">
                    {{ $service->service_duration }}
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500 font-mono text-center">
                    {{ $service->start_date?->format('d.m.Y') ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500 font-mono text-center">
                    {{ $service->end_date?->format('d.m.Y') ?? '-' }}
                </td>
                <td class="px-6 py-4 text-center font-bold text-slate-500">
                    @if($service->end_date)
                        @php
                            $days = now()->diffInDays($service->end_date, false);
                        @endphp
                        <span class="{{ $days < 0 ? 'text-error' : 'text-slate-500' }}">
                            {{ (int) $days }} Gün
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4">
                    <x-customer-management.status-badge :status="$service->status ?? 'active'" />
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>