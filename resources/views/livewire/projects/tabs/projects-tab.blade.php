<?php
/**
 * 🏗️ PROJECTS-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Functional API)
 *
 * Proje listesi ve filtreleme işlevleri.
 * ---------------------------------------------------------
 */

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceItem;
use Livewire\Volt\Component;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'search' => '',
    'statusFilter' => '',
    'typeFilter' => '',
]);

$projects = computed(function () {
    $searchTerm = trim($this->search);

    return Project::query()
        ->with(['customer', 'status', 'leader', 'phases.modules.status'])
        ->when($searchTerm, function ($q) use ($searchTerm) {
            $q->where(function ($sub) use ($searchTerm) {
                // ILIKE handles most case-insensitivity, but ILIKE with Turkish characters
                // can be tricky. Using a combined approach or ensuring the searchTerm
                // is used as-is with ILIKE is common in Laravel/PGSQL.
                $sub->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('project_id_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($c) use ($searchTerm) {
                    $c->where('name', 'LIKE', "%{$searchTerm}%");
                });
            });
        })
        ->when($this->statusFilter, fn($q) => $q->where('status_id', $this->statusFilter))
        ->when($this->typeFilter, fn($q) => $q->where('type_id', $this->typeFilter))
        ->orderByDesc('created_at')
        ->get();
});

$types = computed(function () {
    return ReferenceItem::where('category_key', 'PROJECT_TYPE')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get(['id', 'display_label']);
});

$statuses = computed(function () {
    return ReferenceItem::where('category_key', 'PROJECT_STATUS')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get(['id', 'display_label', 'key']);
});

$resetFilters = function () {
    $this->search = '';
    $this->statusFilter = '';
    $this->typeFilter = '';
};

?>

<div>
    {{-- Filter Panel --}}
    <div class="theme-card p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Search --}}
            <div class="flex-grow max-w-[10rem] !bg-white rounded-lg">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Proje ara..."
                    icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                    style="background-color: white !important;" />
            </div>

            {{-- Status Filter --}}
            <div class="w-40">
                <select wire:model.live="statusFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Durumlar</option>
                    @foreach($this->statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->display_label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type Filter --}}
            <div class="w-48">
                <select wire:model.live="typeFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Tipler</option>
                    @foreach($this->types as $type)
                        <option value="{{ $type->id }}">{{ $type->display_label }}</option>
                    @endforeach
                </select>
            </div>



            {{-- New Project Button --}}
            <a href="{{ route('projects.create') }}" class="theme-btn-save px-4 py-2 flex items-center gap-2 ml-auto">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Proje</span>
            </a>
        </div>
    </div>

    {{-- Projects List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->projects as $project)
            <a href="{{ route('projects.edit', $project->id) }}" wire:key="project-{{ $project->id }}"
                data-testid="project-card"
                class="theme-card p-5 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer group">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-3 min-w-0">
                    <div class="flex items-center gap-3 overflow-hidden min-w-0 flex-1 mr-2">
                        @if($project->customer?->logo_url)
                            <img src="{{ $project->customer->logo_url }}" alt=""
                                class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <x-mary-icon name="o-building-office" class="w-5 h-5 text-slate-400" />
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="text-xs text-slate-500 font-mono truncate">{{ $project->project_id_code }}</div>
                            <div
                                class="font-semibold text-[var(--color-text-heading)] group-hover:text-[var(--header-bg)] transition-colors truncate">
                                {{ $project->name }}
                            </div>
                        </div>
                    </div>
                    {{-- Customer Name in Top-Right --}}
                    @if($project->customer)
                        <div class="flex-shrink-0 max-w-[120px] ml-2">
                            <div class="text-[10px] text-slate-400 font-medium truncate bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100"
                                title="{{ $project->customer->name }}">
                                {{ $project->customer->name }}
                            </div>
                        </div>
                    @endif
                </div>


                {{-- Deadline --}}
                @if($project->target_end_date)
                    @php
                        $deadline = \Carbon\Carbon::parse($project->target_end_date)->startOfDay();
                        $isFrozen = !empty($project->completed_at);
                        $referenceDate = $isFrozen ? \Carbon\Carbon::parse($project->completed_at)->startOfDay() : \Carbon\Carbon::now()->startOfDay();

                        $diff = $referenceDate->diffInDays($deadline, false);

                        $businessDays = $referenceDate->diffInDaysFiltered(function (\Carbon\Carbon $date) {
                            return !$date->isWeekend();
                        }, $deadline);

                        $colorClass = 'text-green-600';
                        $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';

                        if ($isFrozen) {
                            if ($diff >= 0) {
                                $text = abs($diff) . ' Gün Erken Bitti (' . $businessDays . ' İş Günü)';
                                $colorClass = 'text-green-600';
                            } else {
                                $text = abs($diff) . ' Gün Gecikmeli Bitti (' . $businessDays . ' İş Günü)';
                                $colorClass = 'text-red-500';
                            }
                        } else {
                            if ($diff < 0) {
                                $colorClass = 'text-red-500';
                                $text = abs($diff) . ' Gün geçti (' . $businessDays . ' İş Günü)';
                            } elseif ($diff <= 7) {
                                $colorClass = 'text-orange-500';
                                $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';
                            }
                        }
                    @endphp
                    <div class="flex items-center gap-2 mb-3">
                        {{-- Status Badge Next to Deadline --}}
                        @if($project->status)
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap border
                                                                                                                                                                                                                                                             {{ $project->status->color_class ?? 'bg-slate-50 text-slate-600 border-slate-100' }}
                                                                                                                                                                                                                                                        ">
                                {{ $project->status->display_label }}
                            </span>
                        @endif

                        <div class="text-xs font-bold {{ $colorClass }} flex items-center">
                            <x-mary-icon name="o-clock" class="w-4 h-4 mr-1" />
                            {{ $text }}
                        </div>
                    </div>
                @endif

                {{-- Stats --}}
                <div class="flex items-center gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-1">
                        <x-mary-icon name="o-squares-2x2" class="w-4 h-4" />
                        <span>{{ $project->phases->count() }} Faz</span>
                    </div>
                    @if($project->leader)
                        <div class="flex items-center gap-1">
                            <x-mary-icon name="o-user" class="w-4 h-4" />
                            <span>{{ $project->leader->name }}</span>
                        </div>
                    @endif
                    @if($project->start_date)
                        <div class="flex items-center gap-1 ml-auto">
                            <x-mary-icon name="o-calendar" class="w-4 h-4" />
                            <span>{{ $project->start_date->format('d.m.Y') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Active Phases --}}
                @php
                    $activePhases = $project->phases->filter(function ($phase) {
                        return $phase->status && $phase->status->key === 'phase_in_progress';
                    });
                @endphp

                @if($activePhases->isNotEmpty())
                    <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-600">
                        @foreach($activePhases as $phase)
                            @php
                                $words = explode(' ', $phase->name);
                                $initials = mb_substr($words[0] ?? '', 0, 1);
                                if (count($words) > 1) {
                                    $initials .= mb_substr($words[1] ?? '', 0, 1);
                                }
                                $initials = mb_strtoupper($initials);
                            @endphp
                            <div class="flex items-center gap-2 mb-1.5 last:mb-0">
                                <div class="w-5 h-5 rounded flex items-center justify-center font-bold text-white text-[9px] flex-shrink-0"
                                    style="background-color: {{ $phase->custom_fields['color'] ?? 'var(--primary-color)' }};">
                                    {{ $initials }}
                                </div>
                                <span class="truncate font-medium">{{ $phase->name }}</span>
                                <span
                                    class="px-1.5 py-0.5 rounded text-[10px] font-medium border ml-auto {{ $phase->status->color_class ?? '' }}">
                                    {{ $phase->status->display_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </a>
        @empty
            <div class="col-span-3 theme-card p-12 text-center">
                <x-mary-icon name="o-folder-open" class="w-16 h-16 mx-auto mb-4 text-slate-300" />
                <h3 class="text-lg font-semibold text-slate-600 mb-2">Henüz Proje Yok</h3>
                <p class="text-slate-500 mb-4">İlk projenizi oluşturarak başlayın.</p>
                <a href="{{ route('projects.create') }}" class="theme-btn-save px-6 py-2 inline-flex items-center gap-2">
                    <x-mary-icon name="o-plus" class="w-5 h-5" />
                    Yeni Proje Oluştur
                </a>
            </div>
        @endforelse
    </div>
</div>