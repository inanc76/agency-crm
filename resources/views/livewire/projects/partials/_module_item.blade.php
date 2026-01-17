@props(['phaseIndex', 'moduleIndex', 'module' => [], 'isViewMode' => false, 'moduleStatuses' => []])

<div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg" wire:key="module-{{ $phaseIndex }}-{{ $moduleIndex }}">

    {{-- Module Number --}}
    <div
        class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium text-slate-600 flex-shrink-0">
        {{ $moduleIndex + 1 }}
    </div>

    {{-- Module Name & Dates --}}
    <div class="flex-1 flex items-center gap-3 overflow-hidden">
        <span class="text-sm font-medium text-slate-700 truncate">{{ $module['name'] }}</span>

        {{-- Status Badge --}}
        @php
            $status = collect($moduleStatuses)->firstWhere('id', $module['status_id'] ?? '');

            $statusColor = $status['color_class'] ?? 'bg-slate-100 text-slate-500 border-slate-200';
            $statusLabel = $status['display_label'] ?? 'Durum Yok';
        @endphp
        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border {{ $statusColor }}">
            {{ $statusLabel }}
        </span>

        @if(!empty($module['start_date']) || !empty($module['end_date']))
            <span
                class="text-xs text-slate-400 font-normal flex items-center gap-1 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-sm whitespace-nowrap">
                <x-mary-icon name="o-calendar" class="w-3 h-3" />
                {{ \Carbon\Carbon::parse($module['start_date'])->locale('tr')->translatedFormat('d F') }}
                →
                {{ \Carbon\Carbon::parse($module['end_date'])->locale('tr')->translatedFormat('d F') }}
            </span>
        @else
            <span class="text-xs text-slate-300 italic flex items-center gap-1 px-2">
                <x-mary-icon name="o-calendar" class="w-3 h-3" />
                Tarih Yok
            </span>
        @endif

        @if(!empty($module['estimated_hours']))
            <span
                class="text-xs text-blue-600 font-medium flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 shadow-sm whitespace-nowrap">
                <x-mary-icon name="o-clock" class="w-3 h-3" />
                {{ $module['estimated_hours'] }} Saat
            </span>
        @endif
    </div>

    {{-- Actions --}}
    @if(!$isViewMode)
        <div class="flex items-center gap-1">
            {{-- Edit --}}
            <button wire:click="openModuleModal({{ $phaseIndex }}, {{ $moduleIndex }})"
                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-white rounded-md transition-all" title="Düzenle">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
            </button>

            {{-- Delete --}}
            <button wire:click="removeModule({{ $phaseIndex }}, {{ $moduleIndex }})"
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-white rounded-md transition-all" title="Sil">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
            </button>
        </div>
    @endif
</div>