{{--
═══════════════════════════════════════════════════════════════════════════
📊 CUSTOMERS TABLE ROW
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Müşteri listesi tablo satırı. Avatar, şehir badge ve count göstergeleri içerir.
📝 Kullanım Notu: $customer model instance, $cityMap ve $cityColorMap referans data olarak beklenir.
🔗 State Dependencies: $customer, $cityMap, $cityColorMap, $selected (bulk selection için)

--}}

@php
    $char = mb_substr($customer->name, 0, 1);
    $cityName = $cityMap[$customer->city_id] ?? 'Belirtilmedi';
    $cityColor = $cityColorMap[$customer->city_id] ?? 'bg-skin-hover text-skin-muted border-skin-light';
@endphp

<tr onclick="window.location.href='/dashboard/customers/{{ $customer->id }}'">
    {{-- Checkbox --}}
    <td onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Customer Name & Avatar --}}
    <td>
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="avatar-circle">
                    {{ $char }}
                </div>
            </div>
            <div>
                <div class="item-name">
                    {{ $customer->name }}
                </div>
            </div>
        </div>
    </td>

    {{-- City Badge --}}
    <td>
        @if($cityName !== 'Belirtilmedi')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $cityColor }}">
                {{ $cityName }}
            </span>
        @else
            <span class="text-xs opacity-40 italic">-</span>
        @endif
    </td>

    {{-- Count Badges --}}
    @foreach(['contacts', 'assets', 'services', 'offers', 'sales', 'messages'] as $relation)
        <td class="text-center">
            <span class="count-badge">
                {{ $customer->{$relation . '_count'} ?? 0 }}
            </span>
        </td>
    @endforeach
</tr>