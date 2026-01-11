{{--
═══════════════════════════════════════════════════════════════════════════
📊 OFFERS TABLE ROW
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Teklif listesi tablo satırı. Avatar, statü badge, tarih ve fiyat bilgilerini içerir.
📝 Kullanım Notu: $offer model instance, $statusMap referans data olarak beklenir.
🔗 State Dependencies: $offer, $statusMap, $selected (bulk selection için)

--}}

@php
    $char = mb_substr($offer->title, 0, 1);
    $statusData = $statusMap[$offer->status] ?? null;
    $statusLabel = $statusData['label'] ?? $offer->status;
    $statusClass = $statusData['class'] ?? 'bg-skin-hover text-skin-muted border border-skin-light';
@endphp

<tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
    onclick="window.location.href='/dashboard/customers/offers/{{ $offer->id }}'">

    {{-- Checkbox --}}
    <td class="px-6 py-4" onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $offer->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Offer Title & Avatar --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-sm"
                    style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                    {{ $char }}
                </div>
            </div>
            <div>
                <div class="text-[13px] group-hover:opacity-80 transition-opacity" class="text-skin-heading">
                    {{ $offer->title }}
                </div>
                @if(!empty($offer->offer_no))
                    <div class="text-[11px] font-medium opacity-60">{{ $offer->offer_no }}</div>
                @endif
            </div>
        </div>
    </td>

    {{-- Status Badge --}}
    <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </td>

    {{-- Items Count --}}
    <td class="px-6 py-4 text-center">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px]"
            style="background-color: var(--card-bg); color: var(--color-text-heading); border: 1px solid var(--card-border);">
            {{ $offer->items_count ?? 0 }}
        </span>
    </td>

    {{-- Created Date --}}
    <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70">
        {{ $offer->created_at->format('d.m.Y') }}
    </td>

    {{-- Valid Until --}}
    <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70">
        {{ $offer->valid_until?->format('d.m.Y') ?? '-' }}
    </td>

    {{-- Customer --}}
    <td class="px-6 py-4 text-[13px] font-medium">
        {{ $offer->customer->name ?? '-' }}
    </td>

    {{-- Price --}}
    <td class="px-6 py-4 text-right text-[13px]" class="text-skin-heading">
        {{ number_format($offer->total_amount, 2) }} {{ $offer->currency }}
    </td>
</tr>