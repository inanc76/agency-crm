{{--
📦 Module Form Partial
----------------------
Dinamik modül satırı: Ad, açıklama

Props: $phaseIndex, $moduleIndex, $module, $isViewMode
--}}

@props(['phaseIndex', 'moduleIndex', 'module' => [], 'isViewMode' => false])

<div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg" wire:key="module-{{ $phaseIndex }}-{{ $moduleIndex }}">

    {{-- Module Number --}}
    <div
        class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium text-slate-600 flex-shrink-0">
        {{ $moduleIndex + 1 }}
    </div>

    {{-- Module Name --}}
    <input type="text" wire:model="phases.{{ $phaseIndex }}.modules.{{ $moduleIndex }}.name" placeholder="Modül adı..."
        class="flex-1 input text-sm" @if($isViewMode) disabled @endif />

    {{-- Delete Button --}}
    @if(!$isViewMode)
        <button wire:click="removeModule({{ $phaseIndex }}, {{ $moduleIndex }})"
            class="p-1 text-red-400 hover:text-red-600 transition-colors" title="Modülü Sil">
            <x-mary-icon name="o-x-mark" class="w-4 h-4" />
        </button>
    @endif
</div>