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

<tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
    onclick="window.location.href='/dashboard/customers/{{ $customer->id }}'">

    {{-- Checkbox --}}
    <td class="px-6 py-4" onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Customer Name & Avatar --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <x-mary-avatar placeholder="{{ $char }}" class="!w-9 !h-9 font-semibold text-xs shadow-sm"
                    style="background-color: var(--table-avatar-bg); border: 1px solid var(--table-avatar-border); color: var(--table-avatar-text);" />
            </div>
            <div>
                <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                    style="color: var(--list-card-link-color);">
                    {{ $customer->name }}
                </div>
            </div>
        </div>
    </td>

    {{-- City Badge --}}
    <td class="px-6 py-4">
        @if($cityName !== 'Belirtilmedi')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $cityColor }}">
                {{ $cityName }}
            </span>
        @else
            <span class="text-skin-muted italic text-xs">-</span>
        @endif
    </td>

    {{-- Count Badges --}}
    @foreach(['contacts', 'assets', 'services', 'offers', 'sales', 'messages'] as $relation)
        <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] border"
                style="background-color: var(--card-bg); color: var(--color-text-heading); border-color: var(--card-border);">
                {{ $customer->{$relation . '_count'} ?? 0 }}
            </span>
        </td>
    @endforeach
</tr>