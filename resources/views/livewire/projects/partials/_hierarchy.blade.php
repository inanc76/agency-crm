<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] flex items-center gap-2">
            <x-mary-icon name="o-squares-2x2" class="w-5 h-5" />
            Proje Hiyerarşisi
        </h3>
        @if(!$isViewMode)
        <button wire:click="openPhaseModal" 
                class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
            <x-mary-icon name="o-plus" class="w-4 h-4" />
            Faz Ekle
        </button>
        @endif
    </div>

    {{-- Phases List --}}
    <div class="space-y-4">
        @forelse($phases as $index => $phase)
            {{-- Assuming _phase-form will be moved to partials as well --}}
            @include('livewire.projects.partials._phase_item', [
                'index' => $index,
                'phase' => $phase,
                'isViewMode' => $isViewMode
            ])
        @empty
            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                <x-mary-icon name="o-cube-transparent" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                <p class="text-slate-500 mb-2">Henüz faz eklenmedi</p>
                <p class="text-xs text-slate-400">"Faz Ekle" butonuna tıklayarak proje aşamalarını tanımlayın</p>
            </div>
        @endforelse
    </div>
</div>
