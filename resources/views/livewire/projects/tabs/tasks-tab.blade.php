<?php
/**
 * ✅ TASKS-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Functional API)
 *
 * Tüm projelerdeki işlerin merkezi listesi.
 * ---------------------------------------------------------
 */

use App\Models\ProjectTask;
use Livewire\Volt\Component;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'search' => '',
    'priorityFilter' => '',
]);

$tasks = computed(function () {
    return ProjectTask::query()
        ->with(['module.phase.project', 'users'])
        ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
        ->orderByDesc('created_at')
        ->limit(50)
        ->get();
});

$resetFilters = function () {
    $this->search = '';
    $this->priorityFilter = '';
};

?>

<div>
    {{-- Filter Panel --}}
    <div class="theme-card p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <x-mary-input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="İş ara..." 
                    icon="o-magnifying-glass"
                    class="!bg-white !border-gray-200"
                />
            </div>

            {{-- Priority Filter --}}
            <div class="w-48">
                <select wire:model.live="priorityFilter" class="theme-input w-full">
                    <option value="">Tüm Öncelikler</option>
                    <option value="urgent">🔴 Acil</option>
                    <option value="high">🟠 Yüksek</option>
                    <option value="medium">🟡 Orta</option>
                    <option value="low">🟢 Düşük</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <button wire:click="resetFilters" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-800">
                <x-mary-icon name="o-x-mark" class="w-5 h-5" />
            </button>

            {{-- New Task Button --}}
            <a href="{{ route('projects.tasks.create') }}" class="theme-btn-save px-4 py-2 flex items-center gap-2">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Görev</span>
            </a>
        </div>
    </div>

    {{-- Tasks Table --}}
    <div class="theme-card shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">İş Adı</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Proje</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Modül</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Öncelik</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Bitiş</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Atanan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($this->tasks as $task)
                    <tr wire:key="task-{{ $task->id }}" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-[var(--color-text-heading)]">{{ $task->name }}</div>
                            @if($task->description)
                                <div class="text-xs text-slate-500 truncate max-w-xs">{{ Str::limit($task->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $task->module?->phase?->project?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $task->module?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @switch($task->priority)
                                @case('urgent')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Acil</span>
                                    @break
                                @case('high')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Yüksek</span>
                                    @break
                                @case('medium')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Orta</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Düşük</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-slate-600">
                            {{ $task->due_date?->format('d.m.Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($task->users->count() > 0)
                                <div class="flex -space-x-2 justify-center">
                                    @foreach($task->users->take(3) as $user)
                                        <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium border-2 border-white"
                                            title="{{ $user->name }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($task->users->count() > 3)
                                        <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-xs font-medium border-2 border-white">
                                            +{{ $task->users->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Atanmadı</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <x-mary-icon name="o-clipboard-document-check" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                            <div class="font-medium text-slate-600">Henüz İş Yok</div>
                            <p class="text-sm text-slate-500">Proje oluşturup işler ekleyerek başlayın.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
