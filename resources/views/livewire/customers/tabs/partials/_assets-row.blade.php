{{--
═══════════════════════════════════════════════════════════════════════════
📊 ASSETS TABLE ROW
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Varlık listesi tablo satırı. Avatar, tip badge, URL ve müşteri bilgilerini içerir.
📝 Kullanım Notu: $asset model instance ve $typeMap referans data olarak beklenir.
🔗 State Dependencies: $asset, $typeMap, $selected (bulk selection için)

--}}

@php
    $char = mb_substr($asset->name, 0, 1);
    $typeLabel = $asset->type_item->label ?? $asset->type ?? 'Diğer';
    $typeClass = $asset->type_item->color_class ?? 'bg-skin-hover text-skin-muted border border-skin-light';
@endphp

<tr onclick="window.location.href='/dashboard/customers/assets/{{ $asset->id }}'">

    {{-- Checkbox --}}
    <td onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $asset->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Asset Name & Avatar --}}
    <td>
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="avatar-circle">
                    {{ $char }}
                </div>
            </div>
            <div class="item-name">
                {{ $asset->name }}
            </div>
        </div>
    </td>

    {{-- Type Badge --}}
    <td>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeClass }}">
            {{ $typeLabel }}
        </span>
    </td>

    {{-- Müşteri --}}
    <td class="text-xs opacity-80 font-medium">
        {{ $asset->customer->name ?? '-' }}
    </td>

    {{-- URL --}}
    <td class="text-xs">
        @if($asset->url)
            <a href="{{ $asset->url }}" target="_blank" onclick="event.stopPropagation()"
                class="hover:underline text-indigo-600">
                {{ Str::limit($asset->url, 40) }}
            </a>
        @else
            <span class="opacity-30 italic text-[11px]">-</span>
        @endif
    </td>

    {{-- Services Count --}}
    <td class="text-center">
        <span class="count-badge">
            {{ $asset->services_count ?? 0 }}
        </span>
    </td>

    {{-- Created Date --}}
    <td class="text-[11px] font-mono text-center opacity-60">
        {{ $asset->created_at->format('d.m.Y') }}
    </td>
</tr>