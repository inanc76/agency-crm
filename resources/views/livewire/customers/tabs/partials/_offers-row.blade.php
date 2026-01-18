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
    $statusLabel = $offer->status_item->display_label ?? $offer->status;
    $statusClass = $offer->status_item->metadata['color_class'] ?? 'bg-skin-hover text-skin-muted border border-skin-light';
@endphp

<tr onclick="window.location.href='/dashboard/customers/offers/{{ $offer->id }}'">

    {{-- Checkbox --}}
    <td onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $offer->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Offer Title & Avatar --}}
    <td>
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="avatar-circle">
                    {{ $char }}
                </div>
            </div>
            <div>
                <div class="item-name">
                    {{ $offer->title }}
                </div>
                @if(!empty($offer->offer_no))
                    <div class="text-[10px] font-medium opacity-50">{{ $offer->offer_no }}</div>
                @endif
            </div>
        </div>
    </td>

    {{-- Status Badge --}}
    <td>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </td>

    {{-- Items Count --}}
    <td class="text-center">
        <span class="count-badge">
            {{ $offer->items_count ?? 0 }}
        </span>
    </td>

    {{-- Created Date --}}
    <td class="text-[11px] font-mono text-center opacity-60">
        {{ $offer->created_at->format('d.m.Y') }}
    </td>

    {{-- Valid Until --}}
    <td class="text-[11px] font-mono text-center opacity-60">
        {{ $offer->valid_until?->format('d.m.Y') ?? '-' }}
    </td>

    {{-- Customer --}}
    <td class="text-xs opacity-80 font-medium">
        {{ $offer->customer->name ?? '-' }}
    </td>

    {{-- Price --}}
    <td class="text-right text-xs font-bold text-slate-700">
        {{ number_format($offer->total_amount, 2) }} {{ $offer->currency }}
    </td>
</tr>