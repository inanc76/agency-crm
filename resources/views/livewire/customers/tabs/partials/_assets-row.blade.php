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
    $typeData = $typeMap[$asset->type] ?? null;
    $typeLabel = $typeData['label'] ?? $asset->type ?? 'Diğer';
    $typeClass = $typeData['class'] ?? 'bg-skin-hover text-skin-muted border border-skin-light';
@endphp

<tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
    onclick="window.location.href='/dashboard/customers/assets/{{ $asset->id }}'">

    {{-- Checkbox --}}
    <td class="px-6 py-4" onclick="event.stopPropagation()">
        <input type="checkbox" wire:model.live="selected" value="{{ $asset->id }}"
            class="checkbox checkbox-xs rounded border-slate-300">
    </td>

    {{-- Asset Name & Avatar --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-sm"
                    style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                    {{ $char }}
                </div>
            </div>
            <div>
                <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                    style="color: var(--list-card-link-color);">
                    {{ $asset->name }}
                </div>
            </div>
        </div>
    </td>

    {{-- Type Badge --}}
    <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $typeClass }}">
            {{ $typeLabel }}
        </span>
    </td>

    {{-- URL --}}
    <td class="px-6 py-4 text-[12px]">
        @if($asset->url)
            <a href="{{ $asset->url }}" target="_blank" onclick="event.stopPropagation()" class="hover:underline"
                style="color: var(--action-link-color);">
                {{ Str::limit($asset->url, 40) }}
            </a>
        @else
            <span class="text-skin-muted italic">-</span>
        @endif
    </td>

    {{-- Customer --}}
    <td class="px-6 py-4 text-[13px]">
        {{ $asset->customer->name ?? '-' }}
    </td>

    {{-- Services Count --}}
    <td class="px-6 py-4 text-center">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px]"
            style="background-color: var(--card-bg); color: var(--color-text-heading); border: 1px solid var(--card-border);">
            {{ $asset->services_count ?? 0 }}
        </span>
    </td>

    {{-- Created Date --}}
    <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70">
        {{ $asset->created_at->format('d.m.Y') }}
    </td>
</tr>