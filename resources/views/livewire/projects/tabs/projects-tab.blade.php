<?php
/**
 * 🏗️ PROJECTS-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Functional API)
 * 
 * Proje listesi ve filtreleme işlevleri.
 * ---------------------------------------------------------
 */

use Livewire\Volt\Component;
use function Livewire\Volt\{state, computed, mount};
use App\Models\Project;
use App\Models\Customer;
use App\Models\ReferenceItem;

state([
    'search' => '',
    'statusFilter' => '',
    'customerFilter' => '',
]);

$projects = computed(function () {
    return Project::query()
        ->with(['customer', 'status', 'leader', 'phases'])
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('project_id_code', 'like', "%{$this->search}%"))
        ->when($this->statusFilter, fn($q) => $q->where('status_id', $this->statusFilter))
        ->when($this->customerFilter, fn($q) => $q->where('customer_id', $this->customerFilter))
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
                <select wire:model.live="statusFilter" class="theme-input w-full">
                    <option value="">Tüm Durumlar</option>
                    @foreach($this->statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->display_label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Customer Filter --}}
            <div class="w-56">
                <select wire:model.live="customerFilter" class="theme-input w-full">
                    <option value="">Tüm Müşteriler</option>
                    @foreach($this->customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            <button wire:click="resetFilters" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-800">
                <x-mary-icon name="o-x-mark" class="w-5 h-5" />
            </button>

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
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($project->customer?->logo_url)
                                <img src="{{ $project->customer->logo_url }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                    <x-mary-icon name="o-building-office" class="w-5 h-5 text-slate-400" />
                                </div>
                            @endif
                            <div>
                                <div class="text-xs text-slate-500 font-mono">{{ $project->project_id_code }}</div>
                                <div
                                    class="font-semibold text-[var(--color-text-heading)] group-hover:text-[var(--header-bg)] transition-colors">
                                    {{ $project->name }}
                                </div>
                            </div>
                        </div>
                        {{-- Status Badge --}}
                        @if($project->status)
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($project->status->key === 'project_active') bg-blue-100 text-blue-700
                                                @elseif($project->status->key === 'project_completed') bg-green-100 text-green-700
                                                @elseif($project->status->key === 'project_cancelled') bg-red-100 text-red-700
                                                @elseif($project->status->key === 'project_on_hold') bg-yellow-100 text-yellow-700
                                                @else bg-slate-100 text-slate-600
                                                @endif
                                            ">
                                {{ $project->status->display_label }}
                            </span>
                        @endif
                    </div>

                {{-- Customer --}}
                <div class="text-sm text-slate-600 mb-3">
                    <x-mary-icon name="o-building-office-2" class="w-4 h-4 inline-block mr-1" />
                    {{ $project->customer?->name ?? 'Müşteri Yok' }}
                </div>

            {{-- Deadline --}}
            @if($project->target_end_date)
                @php
                    $deadline = \Carbon\Carbon::parse($project->target_end_date)->startOfDay();
                    $today = \Carbon\Carbon::now()->startOfDay();
                    $diff = $today->diffInDays($deadline, false);

                    $businessDays = $today->diffInDaysFiltered(function (\Carbon\Carbon $date) {
                        return !$date->isWeekend();
                    }, $deadline);

                    $colorClass = 'text-green-600';
                    $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';

                    if ($diff < 0) {
                        $colorClass = 'text-red-500';
                        $text = abs($diff) . ' Gün geçti (' . $businessDays . ' İş Günü)';
                    } elseif ($diff <= 7) {
                        $colorClass = 'text-orange-500';
                        $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';
                    }
                @endphp
                <div class="text-xs font-bold mb-3 {{ $colorClass }}">
                    <x-mary-icon name="o-clock" class="w-4 h-4 inline-block mr-1" />
                    {{ $text }}
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