{{-- 📝 Teklifler Sekmesi (Bağımlılık: $relatedOffers, $offersStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Teklifler</h2>
            <select wire:model.live="offersStatusFilter" class="select select-xs bg-white border-slate-200">
                <option value="">Tüm Durumlar</option>
                @foreach($offerStatuses as $status)
                    <option value="{{ $status['key'] }}">{{ $status['display_label'] }}</option>
                @endforeach
            </select>
        </div>
        <x-customer-management.action-button label="Yeni Teklif" href="/dashboard/customers/offers/create?customer={{ $customerId }}" />
    </div>

    @php
        $filteredOffers = collect($relatedOffers)->when($offersStatusFilter, function ($collection) {
            return $collection->where('status', $this->offersStatusFilter);
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
                        <th>Teklif Başlığı</th>
                        <th class="text-center">Tarih</th>
                        <th class="text-center">Geçerlilik</th>
                        <th class="text-right">Toplam Tutar</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredOffers as $offer)
                        @php
                            $validUntil = $offer->valid_until;
                            $daysLeft = $validUntil ? now()->diffInDays($validUntil, false) : null;
                            
                            $statusLabel = $offer->status_item->label ?? $offer->status ?? 'Taslak';
                            $statusClass = $offer->status_item->color_class ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/customers/offers/{{ $offer->id }}'">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td class="item-name">{{ $offer->title }}</td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $offer->created_at->format('d.m.Y') }}</td>
                            <td class="text-center">
                                @if($daysLeft === null)
                                    <span class="text-xs opacity-40">-</span>
                                @elseif($daysLeft < 0)
                                    <span class="text-[10px] font-bold text-red-500">
                                        {{ abs((int)$daysLeft) }} gün geçti
                                    </span>
                                @elseif($daysLeft <= 7)
                                    <span class="text-[10px] font-bold text-amber-500">
                                        {{ (int)$daysLeft }} gün kaldı
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-tight">
                                        {{ (int)$daysLeft }} gün
                                    </span>
                                @endif
                            </td>
                            <td class="text-right font-bold text-slate-700">
                                {{ number_format($offer->total_amount, 2) }} {{ $offer->currency }}</td>
                            <td class="text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-document-text" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">{{ $offersStatusFilter ? 'Filtreye uygun teklif bulunamadı' : 'Henüz teklif kaydı bulunmuyor' }}</div>
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
                {{ $filteredOffers->count() }} kayıt listelendi
            </div>
        </div>
    </div>
</div>
