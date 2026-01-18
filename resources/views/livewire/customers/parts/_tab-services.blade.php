{{-- 📝 Hizmetler Sekmesi (Bağımlılık: $relatedServices, $servicesStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Hizmetler</h2>
            <select wire:model.live="servicesStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
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
    @if($filteredServices->count() > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Hizmet Adı</th>
                        <th>Kategori</th>
                        <th class="text-center">Durum / Kalan Gün</th>
                        <th class="text-center">Bitiş</th>
                        <th class="text-right">Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredServices as $service)
                        @php
                            $endDate = $service->end_date;
                            $daysLeft = now()->diffInDays($endDate, false);
                            
                            $statusLabel = $service->status_item->label ?? $service->status ?? 'Pasif';
                            $statusClass = $service->status_item->color_class ?? 'bg-slate-100 text-slate-500';
                            $categoryLabel = $service->category_item->label ?? $service->service_category ?? '-';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/customers/services/{{ $service->id }}'">
                            <td class="item-name">{{ $service->service_name }}</td>
                            <td class="opacity-70">{{ $categoryLabel }}</td>
                            <td class="text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-cog-6-tooth" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">{{ $servicesStatusFilter ? 'Filtreye uygun hizmet bulunamadı' : 'Henüz hizmet kaydı bulunmuyor' }}</p>
        </div>
    @endif
</div>
