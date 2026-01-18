{{-- 📝 Teklifler Sekmesi (Bağımlılık: $relatedOffers, $offersStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Teklifler</h2>
            <select wire:model.live="offersStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
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
    @if($filteredOffers->count() > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Teklif Başlığı</th>
                        <th class="text-center">Tarih</th>
                        <th class="text-center">Geçerlilik</th>
                        <th class="text-right">Toplam Tutar</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredOffers as $offer)
                        @php
                            $validUntil = $offer->valid_until;
                            $daysLeft = $validUntil ? now()->diffInDays($validUntil, false) : null;
                            
                            $statusLabel = $offer->status_item->label ?? $offer->status ?? 'Taslak';
                            $statusClass = $offer->status_item->color_class ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/customers/offers/{{ $offer->id }}'">
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
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">{{ $offersStatusFilter ? 'Filtreye uygun teklif bulunamadı' : 'Henüz teklif kaydı bulunmuyor' }}</p>
        </div>
    @endif
</div>
