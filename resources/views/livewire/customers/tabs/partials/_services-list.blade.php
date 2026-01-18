{{--
SECTION: Services Data Table & Listing
Mimarın Notu: Bu bölüm hizmetlerin listelendiği tablo ve filtreleme sonuçlarını içerir.
İş Mantığı Şerhi: Service modeli ile konuşur, Customer ve Asset ilişkilerini kullanır.
Mühür Koruması: Table styling, hover effects ve pagination bileşenleri korunmalıdır.
--}}

{{-- Data Table --}}
@php
    $headers = [
        ['label' => '', 'sortable' => false, 'width' => '40px'],
        ['label' => 'Varlık / Hizmet', 'sortable' => true],
        ['label' => 'Hizmet Durumu', 'sortable' => false],
        ['label' => 'Kategori', 'sortable' => true],
        ['label' => 'Süre', 'sortable' => true],
        ['label' => 'Kalan Gün', 'sortable' => true],
        ['label' => 'Müşteri', 'sortable' => true],
        ['label' => 'Başlangıç', 'sortable' => true],
        ['label' => 'Bitiş', 'sortable' => true],
    ];
@endphp

<div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="agency-table">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="checkbox checkbox-xs rounded border-slate-300">
                    </th>
                    @foreach(array_slice($headers, 1) as $header)
                        <th class="{{ isset($header['align']) && $header['align'] == 'center' ? 'text-center' : '' }}">
                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    @php
                        $char = mb_substr($service->service_category, 0, 1);
                    @endphp
                    <tr onclick="window.location.href='/dashboard/customers/services/{{ $service->id }}'">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" wire:model.live="selected" value="{{ $service->id }}"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-circle">
                                        {{ $char }}
                                    </div>
                                </div>
                                <div>
                                    <div class="item-name">
                                        {{ $service->asset->name ?? 'Varlık Yok' }}
                                    </div>
                                    <div class="text-[11px] opacity-50">{{ $service->service_name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusLabel = $service->status_item->label ?? $service->status;
                                $statusClass = $service->status_item->color_class ?? 'bg-slate-50 text-slate-500 border border-slate-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            @php
                                $catLabel = $service->category_item->label ?? $service->service_category;
                                $catClass = $service->category_item->color_class ?? 'bg-slate-50 text-slate-500 border border-slate-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $catClass }}">
                                {{ $catLabel }}
                            </span>
                        </td>
                        <td class="text-xs opacity-60">
                            {{ $service->service_duration }} Yıl
                        </td>
                        <td class="text-center font-bold">
                            @if($service->end_date)
                                @php
                                    $days = now()->diffInDays($service->end_date, false);
                                @endphp
                                <span class="{{ $days < 0 ? 'text-red-500' : 'text-slate-600' }} text-xs">
                                    {{ (int) $days }} Gün
                                </span>
                            @else
                                <span class="opacity-30">-</span>
                            @endif
                        </td>
                        <td class="text-xs opacity-70">
                            {{ $service->customer->name ?? '-' }}
                        </td>
                        <td class="text-[10px] opacity-60 font-mono text-center">
                            {{ $service->start_date?->format('d.m.Y') ?? '-' }}
                        </td>
                        <td class="text-[10px] opacity-60 font-mono text-center">
                            {{ $service->end_date?->format('d.m.Y') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-skin-muted">
                            <div class="flex flex-col items-center justify-center">
                                <x-mary-icon name="o-archive-box" class="w-12 h-12 opacity-20 mb-4" />
                                <div class="font-medium">Henüz hizmet kaydı bulunmuyor</div>
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
            {{ $services->links() }}
        </div>

        <div class="text-[10px] text-skin-muted font-mono">
            {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
        </div>
    </div>
</div>