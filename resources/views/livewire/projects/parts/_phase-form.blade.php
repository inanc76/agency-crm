{{-- 
    📊 Phase Form Partial
    ---------------------
    Dinamik faz satırı: Ad, tarih, renk ve modül ekleme
    
    Props: $index, $phase, $isViewMode
--}}

@props(['index', 'phase' => [], 'isViewMode' => false])

<div class="theme-card p-4 shadow-sm border-l-4" 
     style="border-left-color: {{ $phase['color'] ?? 'var(--primary-color)' }};"
     wire:key="phase-{{ $index }}">
    
    {{-- Phase Header --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-sm"
                 style="background-color: {{ $phase['color'] ?? 'var(--primary-color)' }};">
                {{ $index + 1 }}
            </div>
            <input 
                type="text" 
                wire:model="phases.{{ $index }}.name" 
                placeholder="Faz adı..."
                class="input text-lg font-semibold border-0 bg-transparent focus:ring-0 p-0"
                @if($isViewMode) disabled @endif
            />
        </div>
        
        @if(!$isViewMode)
        <button wire:click="removePhase({{ $index }})" 
                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                title="Fazı Sil">
            <x-mary-icon name="o-trash" class="w-4 h-4" />
        </button>
        @endif
    </div>
    
    {{-- Phase Details --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Başlangıç</label>
            <div class="relative group">
                <input 
                    type="date" 
                    wire:model="phases.{{ $index }}.start_date"
                    class="input w-full text-sm bg-slate-50 cursor-not-allowed text-slate-400"
                    disabled
                />
                <div class="absolute hidden group-hover:block bottom-full left-0 mb-2 p-2 bg-slate-800 text-white text-xs rounded shadow-lg z-10 w-48">
                    Modüllerden otomatik hesaplanır
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Bitiş</label>
             <div class="relative group">
                <input 
                    type="date" 
                    wire:model="phases.{{ $index }}.end_date"
                    class="input w-full text-sm bg-slate-50 cursor-not-allowed text-slate-400"
                    disabled
                />
                <div class="absolute hidden group-hover:block bottom-full left-0 mb-2 p-2 bg-slate-800 text-white text-xs rounded shadow-lg z-10 w-48">
                    Modüllerden otomatik hesaplanır
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Renk</label>
            <input 
                type="color" 
                wire:model="phases.{{ $index }}.color"
                class="h-10 w-full rounded-lg cursor-pointer"
                @if($isViewMode) disabled @endif
            />
        </div>
    </div>
    
    {{-- Modules Section --}}
    <div class="mt-4 pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-600">Modüller</span>
            @if(!$isViewMode)
            <button wire:click="addModule({{ $index }})" 
                    class="text-xs px-2 py-1 text-blue-600 hover:bg-blue-50 rounded-lg flex items-center gap-1">
                <x-mary-icon name="o-plus" class="w-3 h-3" />
                Modül Ekle
            </button>
            @endif
        </div>
        
        <div class="space-y-2">
            @forelse($phase['modules'] ?? [] as $moduleIndex => $module)
                @include('livewire.projects.parts._module-form', [
                    'phaseIndex' => $index,
                    'moduleIndex' => $moduleIndex,
                    'module' => $module,
                    'isViewMode' => $isViewMode
                ])
            @empty
                <div class="text-center py-4 text-slate-400 text-sm">
                    <x-mary-icon name="o-cube" class="w-6 h-6 mx-auto mb-1" />
                    Henüz modül eklenmedi
                </div>
            @endforelse
        </div>
    </div>
</div>
