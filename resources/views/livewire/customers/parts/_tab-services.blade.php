{{-- 📝 Hizmetler Sekmesi (Bağımlılık: $relatedServices, $servicesStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Hizmetler</h2>
            <select wire:model.live="servicesStatusFilter" class="select select-xs bg-white border-slate-200">
                <option value="">Tüm Durumlar</option>
                @foreach($serviceStatuses as $status)
                    <option value="{{ $status['key'] }}">{{ $status['display_label'] }}</option>
                @endforeach
            </select>
        </div>
        <x-customer-management.action-button label="Yeni Hizmet" href="/dashboard/customers/services/create?customer={{ $customerId }}" />
    </div>

    @php
        $filteredServices = collect($relatedServices)->when($servicesStatusFilter, function ($collection) {
            return $collection->where('status', $this->servicesStatusFilter);
        });
    @endphp

    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" disabled
                                class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                        </th>
                        <th>Hizmet Adı</th>
                        <th>Kategori</th>
                        <th class="text-center">Durum / Kalan Gün</th>
                        <th class="text-center">Bitiş</th>
                        <th class="text-right">Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredServices as $service)
                        @php
                            $endDate = $service->end_date;
                            $daysLeft = now()->diffInDays($endDate, false);
                            
                            $statusLabel = $service->status_item->label ?? $service->status ?? 'Pasif';
                            $statusClass = $service->status_item->color_class ?? 'bg-slate-100 text-slate-500';
                            $categoryLabel = $service->category_item->label ?? $service->service_category ?? '-';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/customers/services/{{ $service->id }}'">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td class="item-name">{{ $service->service_name }}</td>
                            <td class="opacity-70">{{ $categoryLabel }}</td>
                            <td class="text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($daysLeft < 0)
                                        <span class="text-[9px] font-bold text-red-500">
                                            {{ abs((int)$daysLeft) }} gün geçti
                                        </span>
                                    @elseif($daysLeft <= 30)
                                        <span class="text-[9px] font-bold text-amber-500">
                                            {{ (int)$daysLeft }} gün kaldı
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $endDate ? $endDate->format('d.m.Y') : '-' }}</td>
                            <td class="text-right font-bold text-slate-700">
                                {{ number_format($service->service_price, 2) }}
                                {{ $service->service_currency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-cog-6-tooth" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">{{ $servicesStatusFilter ? 'Filtreye uygun hizmet bulunamadı' : 'Henüz hizmet kaydı bulunmuyor' }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-skin-muted">Göster:</span>
                <div class="px-2 py-1 border border-skin-light rounded text-xs bg-white">25</div>
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ $filteredServices->count() }} kayıt listelendi
            </div>
        </div>
    </div>
</div>