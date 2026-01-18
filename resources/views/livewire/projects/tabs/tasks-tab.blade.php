<?php
/**
 * ✅ TASKS-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-based)
 *
 * Tüm projelerdeki işlerin merkezi listesi.
 * ---------------------------------------------------------
 */

use App\Models\ProjectTask;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $search = '';

    public string $priorityFilter = '';

    public string $statusFilter = '';

    public array $priorities = [];

    public array $statuses = [];

    public ?string $project_id = null;

    public function mount()
    {
        $this->priorities = \App\Models\ReferenceItem::query()
            ->where('category_key', 'TASK_PRIORITY')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'key', 'display_label'])
            ->map(function ($item) {
                // Ensure key is lowercase for comparison if needed, or keep original
                // The task priority is stored as lowercase string in DB usually (e.g. 'urgent') based on previous code
                // Let's assume the ReferenceItem key might be UPPERCASE (e.g. 'URGENT')
                $item->key = strtolower($item->key);

                return $item;
            })
            ->toArray();

        $this->statuses = \App\Models\ReferenceItem::query()
            ->where('category_key', 'TASK_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'key', 'display_label'])
            ->toArray();
    }

    // Selection
    public array $selected = [];

    public bool $selectAll = false;

    public function updatedSelectAll($value)
    {
        $this->selected = $value
            ? $this->tasks->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        ProjectTask::whereIn('id', $this->selected)->delete();
        $this->success('İşlem Başarılı', count($this->selected) . ' görev silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    public function getTasksProperty()
    {
        return ProjectTask::query()
            ->with(['project', 'module', 'users', 'status', 'priority_item'])
            ->when($this->project_id, fn($q) => $q->where('project_id', $this->project_id))
            ->when($this->search, fn($q) => $q->where('name', 'LIKE', "%{$this->search}%")
                ->orWhere('description', 'LIKE', "%{$this->search}%"))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status_id', $this->statusFilter))
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }


    public function resetFilters()
    {
        $this->search = '';
        $this->priorityFilter = '';
        $this->statusFilter = '';
    }
}; ?>

<div>
    {{-- Filter Panel --}}
    <div class="theme-card p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Bulk Delete Button --}}
            {{-- Bulk Delete Button --}}
            @if(!empty($selected))
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected ?? []) }} görevi silmek istediğinize emin misiniz?"
                    class="theme-btn-delete px-3 py-2 flex items-center gap-2 text-sm">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    <span>{{ count($selected ?? []) }} Sil</span>
                </button>
            @endif

            {{-- Search --}}
            <div class="flex-grow max-w-[10rem] !bg-white rounded-lg">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Görev ara..."
                    icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                    style="background-color: white !important;" />
            </div>

            {{-- Priority Filter --}}
            <div class="w-40">
                <select wire:model.live="priorityFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Öncelikler</option>
                    @foreach($this->priorities as $priority)
                        <option value="{{ $priority['key'] }}">{{ $priority['display_label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="w-40">
                <select wire:model.live="statusFilter" class="select select-xs bg-white border-slate-200 w-full">
                    <option value="">Tüm Durumlar</option>
                    @foreach($this->statuses as $status)
                        <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- New Task Button --}}
            <a href="{{ route('projects.tasks.create', ['project' => $project_id]) }}"
                class="theme-btn-save px-4 py-2 flex items-center gap-2 ml-auto">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Görev</span>
            </a>
        </div>
    </div>

    {{-- Tasks Table --}}
    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        <th>Konu</th>
                        <th>Proje</th>
                        <th class="text-center">Öncelik</th>
                        <th class="text-center">Durum</th>
                        <th class="text-center">Atanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->tasks as $task)
                        <tr wire:key="task-{{ $task->id }}">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $task->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td onclick="window.location.href='{{ route('projects.tasks.edit', $task->id) }}'">
                                <div class="item-name mb-0.5">
                                    {{ $task->name }}
                                </div>
                                @if($task->description)
                                    <div class="text-[10px] opacity-50 truncate max-w-xs">
                                        {{ Str::limit($task->description, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td onclick="window.location.href='{{ route('projects.tasks.edit', $task->id) }}'">
                                <div class="opacity-70 text-xs">
                                    {{ $task->project?->name ?? $task->module?->phase?->project?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="text-center"
                                onclick="window.location.href='{{ route('projects.tasks.edit', $task->id) }}'">
                                @php
                                    $pItem = $task->priority_item;
                                    $label = $pItem->label ?? ucfirst($task->priority);
                                    $colorClass = $pItem->color_class ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $colorClass }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="text-center"
                                onclick="window.location.href='{{ route('projects.tasks.edit', $task->id) }}'">
                                @if($task->status)
                                    @php
                                        $sClasses = $task->status->color_class ?? 'bg-slate-100 text-slate-500 border-slate-200';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $sClasses }}">
                                        {{ $task->status->display_label }}
                                    </span>
                                @else
                                    <span class="text-xs opacity-40">-</span>
                                @endif
                            </td>
                            <td class="text-center"
                                onclick="window.location.href='{{ route('projects.tasks.edit', $task->id) }}'">
                                @if($task->users->count() > 0)
                                    <div class="flex -space-x-4 justify-center">
                                        @foreach($task->users->take(3) as $user)
                                            <div class="avatar-circle !w-7 !h-7 !text-[10px] !font-bold border-2 border-white ring-1 ring-slate-100"
                                                title="{{ $user->name }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($task->users->count() > 3)
                                            <div
                                                class="avatar-circle !w-7 !h-7 !text-[10px] !font-bold border-2 border-white ring-1 ring-slate-100 bg-slate-200">
                                                +{{ $task->users->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs opacity-40 italic">Atanmadı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-clipboard-document-check" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz İş Yok</div>
                                    <div class="text-xs opacity-60 mt-1">Proje oluşturup işler ekleyerek başlayın.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>