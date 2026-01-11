{{-- 📝 Teklifler Sekmesi (Bağımlılık: $relatedOffers, $offersStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Teklifler</h2>
            <select wire:model.live="offersStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
                <option value="">Tüm Durumlar</option>
                <option value="DRAFT">Taslak</option>
                <option value="SENT">Gönderildi</option>
                <option value="ACCEPTED">Kabul Edildi</option>
                <option value="REJECTED">Reddedildi</option>
            </select>
        </div>
        <a href="/dashboard/customers/offers/create?customer={{ $customerId }}"
            class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
            + Yeni Teklif
        </a>
    </div>
    @php
        $filteredOffers = collect($relatedOffers)->when($offersStatusFilter, function ($collection) {
            return $collection->where('status', $this->offersStatusFilter);
        });
    @endphp
    @if($filteredOffers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Teklif Başlığı</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Tarih</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Kalan Gün</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Tutar</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredOffers as $offer)
                        @php
                            $validUntil = \Carbon\Carbon::parse($offer['valid_until']);
                            $daysLeft = now()->diffInDays($validUntil, false);
                            $statusColors = [
                                'DRAFT' => 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]',
                                'SENT' => 'bg-[var(--brand-primary)]/10 text-[var(--brand-primary)]',
                                'ACCEPTED' => 'bg-[var(--color-success)]/10 text-[var(--color-success)]',
                                'REJECTED' => 'bg-[var(--color-danger)]/10 text-[var(--color-danger)]',
                            ];
                            $statusLabels = ['DRAFT' => 'Taslak', 'SENT' => 'Gönderildi', 'ACCEPTED' => 'Kabul', 'REJECTED' => 'Ret'];
                        @endphp
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                            onclick="window.location.href='/dashboard/customers/offers/{{ $offer['id'] }}'">
                            <td class="py-3 px-2 font-medium">{{ $offer['title'] }}</td>
                            <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                {{ \Carbon\Carbon::parse($offer['created_at'])->format('d.m.Y') }}</td>
                            <td class="py-3 px-2 text-center">
                                @if($daysLeft < 0)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                        {{ abs((int)$daysLeft) }} gün geçti
                                    </span>
                                @elseif($daysLeft <= 7)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                        {{ (int)$daysLeft }} gün
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                        {{ (int)$daysLeft }} gün
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-right font-medium">
                                {{ number_format($offer['total_amount'], 2) }} {{ $offer['currency'] }}</td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$offer['status']] ?? 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                    {{ $statusLabels[$offer['status']] ?? $offer['status'] }}
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
