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
    'customerFilter' => '',
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
                $sub->where('name', 'ILIKE', "%{$searchTerm}%")
                    ->orWhere('project_id_code', 'ILIKE', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($c) use ($searchTerm) {
                        $c->where('name', 'ILIKE', "%{$searchTerm}%");
                    });
            });
        })
        ->when($this->statusFilter, fn ($q) => $q->where('status_id', $this->statusFilter))
        ->when($this->customerFilter, fn ($q) => $q->where('customer_id', $this->customerFilter))
        ->orderByDesc('created_at')
        ->get();
});

$customers = computed(function () {
    return Customer::orderBy('name')->get(['id', 'name']);
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
    $this->customerFilter = '';
};

?>

<div>
    {{-- Filter Panel --}}
    <div class="theme-card p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Proje ara..."
                    icon="o-magnifying-glass" class="!bg-white !border-gray-200" />
            </div>

            {{-- Status Filter --}}
            <div class="w-48">
                <select wire:model.live="statusFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Durumlar</option>
                    @foreach($this->statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->display_label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Customer Filter --}}
            <div class="w-56">
                <select wire:model.live="customerFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Müşteriler</option>
                    @foreach($this->customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>



            {{-- New Project Button --}}
            <a href="{{ route('projects.create') }}" class="theme-btn-save px-4 py-2 flex items-center gap-2">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Proje</span>
            </a>
        </div>
    </div>

    {{-- Projects List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->projects as $project)
            <a href="{{ route('projects.edit', $project->id) }}" wire:key="project-{{ $project->id }}"
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
                                                                                                                                                            @if($project->status->key === 'project_active') bg-blue-50 text-blue-600 border-blue-100
                                                                                                                                                            @elseif($project->status->key === 'project_completed') bg-green-50 text-green-600 border-green-100
                                                                                                                                                            @elseif($project->status->key === 'project_cancelled') bg-red-50 text-red-600 border-red-100
                                                                                                                                                            @elseif($project->status->key === 'project_on_hold') bg-yellow-50 text-yellow-600 border-yellow-100
                                                                                                                                                            @else bg-slate-50 text-slate-600 border-slate-100
                                                                                                                                                            @endif
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
                    @php
                        // Phase Color Map (Matching _phase-form.blade.php)
                        $statusColors = [
                            'blue' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'green' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'red' => 'bg-red-100 text-red-700 border-red-200',
                            'rose' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'amber' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'orange' => 'bg-orange-100 text-orange-700 border-orange-200',
                            'gray' => 'bg-slate-100 text-slate-500 border-slate-200',
                            'slate' => 'bg-slate-100 text-slate-500 border-slate-200',
                            'teal' => 'bg-teal-100 text-teal-700 border-teal-200',
                            'cyan' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                            'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                            'purple' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'pink' => 'bg-pink-100 text-pink-700 border-pink-200',
                            'yellow' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        ];
                    @endphp
                    <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-600">
                        @foreach($activePhases as $phase)
                            @php
                                $words = explode(' ', $phase->name);
                                $initials = mb_substr($words[0] ?? '', 0, 1);
                                if (count($words) > 1) {
                                    $initials .= mb_substr($words[1] ?? '', 0, 1);
                                }
                                $initials = mb_strtoupper($initials);

                                // Get status color from metadata
                                $rawColor = $phase->status->metadata['color'] ?? 'gray';
                                $statusClass = $statusColors[$rawColor] ?? $statusColors['gray'];
                            @endphp
                            <div class="flex items-center gap-2 mb-1.5 last:mb-0">
                                <div class="w-5 h-5 rounded flex items-center justify-center font-bold text-white text-[9px] flex-shrink-0"
                                    style="background-color: {{ $phase->custom_fields['color'] ?? 'var(--primary-color)' }};">
                                    {{ $initials }}
                                </div>
                                <span class="truncate font-medium">{{ $phase->name }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium border ml-auto {{ $statusClass }}">
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