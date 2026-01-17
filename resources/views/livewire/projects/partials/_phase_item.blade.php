@props(['index', 'phase' => [], 'isViewMode' => false, 'phaseStatuses' => [], 'moduleStatuses' => []])

<div class="theme-card p-4 shadow-sm border-l-4" 
     style="border-left-color: {{ $phase['color'] ?? 'var(--primary-color)' }};"
     wire:key="phase-{{ $index }}">
    
    {{-- Phase Header Row --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
            {{-- Order Badge (Initials) --}}
            @php
                $words = explode(' ', $phase['name']);
                $initials = mb_substr($words[0] ?? '', 0, 1);
                if (count($words) > 1) {
                    $initials .= mb_substr($words[1] ?? '', 0, 1);
                }
                $initials = mb_strtoupper($initials);
            @endphp
            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs"
                 style="background-color: {{ $phase['color'] ?? 'var(--primary-color)' }};">
                {{ $initials }}
            </div>
            
            {{-- Phase Name --}}
            <div class="flex flex-col">
                <span class="text-base font-semibold text-slate-800">{{ $phase['name'] }}</span>
                @if(!empty($phase['description']))
                    <span class="text-xs text-slate-500 line-clamp-1">{{ $phase['description'] }}</span>
                @endif
            </div>
        </div>

        {{-- Phase Logic: Date Summary & Status Calculation --}}
        @php
            $modules = collect($phase['modules'] ?? []);
            $startDates = $modules->pluck('start_date')->filter();
            $endDates = $modules->pluck('end_date')->filter();
            
            // 1. Date Summary
            $dateSummary = null;
            if ($startDates->isNotEmpty() && $endDates->isNotEmpty()) {
                $minDate = \Carbon\Carbon::parse($startDates->min());
                $maxDate = \Carbon\Carbon::parse($endDates->max());
                $diffInDays = $minDate->diffInDays($maxDate) + 1;
                
                // Business Days (inclusive)
                $businessDays = 0;
                $period = \Carbon\CarbonPeriod::create($minDate, $maxDate);
                foreach ($period as $date) {
                    if (!$date->isWeekend()) $businessDays++;
                }
                $dateSummary = [
                    'start' => $minDate->locale('tr')->translatedFormat('d F'),
                    'end' => $maxDate->locale('tr')->translatedFormat('d F'),
                    'total' => $diffInDays,
                    'business' => $businessDays
                ];
            }

            // 2. Status Calculation
            // Map Phase Status Objects
            $pPlanned = collect($phaseStatuses)->firstWhere('key', 'phase_planned');
            $pInProgress = collect($phaseStatuses)->firstWhere('key', 'phase_in_progress');
            $pCompleted = collect($phaseStatuses)->firstWhere('key', 'phase_completed');
            $pCancelled = collect($phaseStatuses)->firstWhere('key', 'PHASE_CANCELED');
            
            // Map Module Status Keys (DB keys)
            $mInProgress = collect($moduleStatuses)->firstWhere('key', 'module_in_progress');
            $mCompleted = collect($moduleStatuses)->firstWhere('key', 'module_completed');
            $mCancelled = collect($moduleStatuses)->firstWhere('key', 'module_cancelled');
            
            $phaseStatus = $pPlanned; // Default
            
            if ($modules->isNotEmpty()) {
                $hasInProgress = $modules->contains('status_id', $mInProgress['id'] ?? 'xxx');
                $totalModules = $modules->count();
                $completedCount = $modules->where('status_id', $mCompleted['id'] ?? 'xxx')->count();
                $cancelledCount = $modules->where('status_id', $mCancelled['id'] ?? 'xxx')->count();
                
                if ($hasInProgress) {
                    $phaseStatus = $pInProgress;
                } elseif (($completedCount + $cancelledCount) === $totalModules) {
                    if ($cancelledCount === $totalModules) {
                         $phaseStatus = $pCancelled;
                    } else {
                         $phaseStatus = $pCompleted;
                    }
                }
            }
            
            $currentPhaseColor = $phaseStatus['color_class'] ?? 'bg-slate-100 text-slate-500 border-slate-200';
            $phaseLabel = $phaseStatus['display_label'] ?? 'Planlandı';
        @endphp

        <div class="ml-auto mr-4 flex items-center gap-2">
            @if($dateSummary)
                <div class="text-xs font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-md border border-slate-100 shadow-sm whitespace-nowrap">
                    {{ $dateSummary['start'] }} 
                    <span class="text-slate-300 mx-1">→</span> 
                    {{ $dateSummary['end'] }}
                    <span class="text-slate-300 mx-2">|</span>
                    <span class="text-slate-700">{{ $dateSummary['total'] }} Gün</span>
                    <span class="text-slate-400 ml-1">({{ $dateSummary['business'] }} iş günü)</span>
                </div>
            @endif
            
            {{-- Phase Status Badge --}}
            <div class="text-[10px] uppercase font-bold tracking-wider px-2 py-1.5 rounded-md border {{ $currentPhaseColor }} whitespace-nowrap">
                {{ $phaseLabel }}
            </div>
        </div>
        
        {{-- Actions --}}
        @if(!$isViewMode)
        <div class="flex items-center gap-1">
            <button wire:click="openPhaseModal({{ $index }})" 
                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                    title="Fazı Düzenle">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
            </button>
            
            <button wire:click="removePhase({{ $index }})" 
                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                    title="Fazı Sil">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
            </button>
        </div>
        @endif
    </div>
    
    {{-- Modules Section --}}
    <div class="mt-2 pl-11">
        <div class="border-t border-slate-100 pt-3">
             <div class="flex items-center justify-between mb-2">
            </div>
            
            <div class="space-y-2">
                @forelse($phase['modules'] ?? [] as $moduleIndex => $module)
                    @include('livewire.projects.partials._module_item', [
                        'phaseIndex' => $index,
                        'moduleIndex' => $moduleIndex,
                        'module' => $module,
                        'isViewMode' => $isViewMode,
                        'moduleStatuses' => $moduleStatuses
                    ])
                @empty
                    {{-- Empty state handled by button below --}}
                @endforelse

                @if(!$isViewMode)
                <button wire:click="openModuleModal({{ $index }})" 
                        class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 border-dashed px-3 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <x-mary-icon name="o-plus" class="w-3 h-3" />
                    Modül Ekle
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
