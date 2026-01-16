<?php

/**
 * ✅ TASK CREATE/EDIT COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-Based API)
 * V10 CONSTITUTION COMPLIANT - Decomposed & Filter Fixed
 * ---------------------------------------------------------
 */

use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use App\Models\User;
use App\Services\ReferenceDataService;
use App\Repositories\ProjectRepository;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    protected ReferenceDataService $refService;
    protected ProjectRepository $projectRepo;

    public function boot(ReferenceDataService $refService, ProjectRepository $projectRepo)
    {
        $this->refService = $refService;
        $this->projectRepo = $projectRepo;
    }

    public function getColorClasses($id)
    {
        return $this->refService->getColorClasses($id);
    }

    // Route Model Binding & State
    public ?ProjectTask $task = null;
    public ?string $customer_id = null;
    public ?string $project_id = null;
    public ?string $assigned_by = null;
    public array $assigned_to = [];
    public ?string $priority_id = null;
    public ?string $status_id = null;
    public string $name = '';
    public string $description = '';

    // Dropdowns
    public array $customers = [];
    public array $projects = [];
    public array $users = [];
    public array $priorities = [];
    public array $statuses = [];

    // UI State
    public bool $isViewMode = false;
    public bool $isAdmin = false;
    public string $activeTab = 'task_info';

    public function mount(?ProjectTask $task = null): void
    {
        $this->task = $task ?? new ProjectTask;
        $this->isAdmin = auth()->user()?->role?->name === 'admin';
        $this->assigned_by = auth()->id();

        // Handle Query String for Tab
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['task_info', 'reports', 'notes'])) {
            $this->activeTab = $tab;
        }

        $this->customers = Customer::query()->orderBy('name')->get(['id', 'name'])->toArray();
        $this->users = User::query()->orderBy('name')->get(['id', 'name'])->toArray();

        // Pre-select via Query String (e.g. from Project Detail)
        if (!$this->task->id && request()->has('project')) {
            $projectId = request()->query('project');
            $project = Project::find($projectId);
            if ($project) {
                $this->customer_id = $project->customer_id;
                $this->project_id = $project->id;
                // Load projects for this customer to populate dropdown
                $this->loadProjects();
            }
        }

        $this->priorities = ReferenceItem::query()->where('category_key', 'TASK_PRIORITY')->where('is_active', true)->orderBy('sort_order')->get(['id', 'key', 'display_label', 'metadata'])->toArray();
        $this->statuses = ReferenceItem::query()->where('category_key', 'TASK_STATUS')->where('is_active', true)->orderBy('sort_order')->get(['id', 'key', 'display_label', 'metadata'])->toArray();

        // Defaults
        if (empty($this->status_id) && !empty($this->statuses)) {
            $defaultStatus = collect($this->statuses)->firstWhere('key', 'new') ?? collect($this->statuses)->firstWhere('key', 'open') ?? $this->statuses[0];
            $this->status_id = $defaultStatus['id'];
        }
        $this->priority_id = collect($this->priorities)->firstWhere('key', 'NORMAL')['id'] ?? null;

        if ($this->task?->id) {
            $this->isViewMode = true;
            $this->loadTaskData();
        }
    }

    public function loadTaskData(): void
    {
        $this->task->load(['project.customer', 'users']);
        $this->customer_id = $this->task->project?->customer_id;
        $this->project_id = $this->task->project_id;
        $this->assigned_to = $this->task->users->pluck('id')->toArray();
        $this->name = $this->task->name;
        $this->description = $this->task->description ?? '';
        $this->status_id = $this->task->status_id;

        if ($this->customer_id)
            $this->loadProjects();

        $priorityKey = strtoupper($this->task->priority ?? 'normal');
        $this->priority_id = collect($this->priorities)->firstWhere('key', $priorityKey)['id'] ?? $this->priority_id;
        $this->assigned_by = $this->task->custom_fields['assigned_by'] ?? auth()->id();
    }

    public function updatedCustomerId(): void
    {
        $this->project_id = null;
        $this->loadProjects();
    }

    public function loadProjects(): void
    {
        if (!$this->customer_id) {
            $this->projects = [];
            return;
        }

        // V10 FIX: Use repository and allow draft/on-hold projects
        $this->projects = $this->projectRepo->getSelectableProjectsForCustomer($this->customer_id)
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => "[{$p->project_id_code}] {$p->name}",
            ])->toArray();
    }

    public function save(): void
    {
        $data = $this->validate([
            'customer_id' => 'required|uuid',
            'project_id' => 'required|uuid',
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'uuid',
            'priority_id' => 'required|uuid',
            'status_id' => 'required|uuid',
            'name' => 'required|min:3|max:255',
        ]);

        $priorityKey = strtolower(collect($this->priorities)->firstWhere('id', $this->priority_id)['key'] ?? 'normal');

        $taskData = [
            'project_id' => $this->project_id,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $priorityKey,
            'status_id' => $this->status_id,
            'custom_fields' => ['assigned_by' => $this->assigned_by],
        ];

        DB::transaction(function () use ($taskData) {
            if ($this->task?->id) {
                $this->task->update($taskData);
                $this->task->users()->syncWithPivotValues($this->assigned_to, ['assigned_at' => now()]);
            } else {
                $task = ProjectTask::create($taskData);
                $task->users()->attach(array_fill_keys($this->assigned_to, ['assigned_at' => now()]));
            }
        });

        $this->dispatch('toast', type: 'success', message: 'Görev başarıyla ' . ($this->task?->id ? 'güncellendi' : 'oluşturuldu') . '.');

        if (!$this->task?->id) {
            $this->redirect(route('projects.index', ['tab' => 'tasks']), navigate: true);
        } else {
            $this->isViewMode = true;
        }
    }

    public function updatedActiveTab($value)
    {
        if ($this->task?->id) {
            $this->dispatch('url-changed', url: route('projects.tasks.edit', ['task' => $this->task->id, 'tab' => $value]));
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = !$this->isViewMode;
    }

    public function delete(): void
    {
        if ($this->task?->id) {
            $this->task->delete();
            $this->dispatch('toast', type: 'success', message: 'Görev başarıyla silindi.');
            $this->redirect(route('projects.index', ['tab' => 'tasks']), navigate: true);
        }
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('projects.index', ['tab' => 'tasks']) }}"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Görev Listesi</span>
        </a>

        @include('livewire.projects.tasks.parts._header')

        {{-- TABS NAVIGATION --}}
        @if($task?->id)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                @php
                    $tabs = [
                        'task_info' => 'Görev Bilgileri',
                        'reports' => 'Raporlar',
                        'notes' => 'Notlar',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <button wire:click="$set('activeTab', '{{ $key }}')"
                        class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === $key ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}"
                        onclick="history.pushState(null, '', '{{ route('projects.tasks.edit', ['task' => $task->id, 'tab' => $key]) }}')">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- TAB CONTENT --}}
        <div>
            {{-- Tab 1: Görev Bilgileri --}}
            <div x-show="$wire.activeTab === 'task_info'">
                <div class="grid grid-cols-12 gap-6">
                    @include('livewire.projects.tasks.parts._form-left')
                    @include('livewire.projects.tasks.parts._sidebar')
                </div>
            </div>

            {{-- Tab 2: Raporlar --}}
            @if($task?->id)
                <div x-show="$wire.activeTab === 'reports'" style="display: none;">
                    <livewire:projects.tabs.reports-tab :task_id="$task->id" wire:key="task-reports-{{ $task->id }}" />
                </div>

                {{-- Tab 3: Notlar --}}
                <div x-show="$wire.activeTab === 'notes'" style="display: none;">
                    <livewire:projects.tabs.notes-tab :task_id="$task->id" wire:key="task-notes-{{ $task->id }}" />
                </div>
            @endif
        </div>
    </div>
</div>