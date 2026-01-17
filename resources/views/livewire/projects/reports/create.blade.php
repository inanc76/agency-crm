<?php
/**
 * ✅ REPORT CREATE/EDIT COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-Based API)
 * V10 CONSTITUTION COMPLIANT - Decomposed & Task Link Added
 * ---------------------------------------------------------
 */

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\ReferenceItem;
use App\Models\Service;
use App\Repositories\ProjectReportRepository;
use App\Repositories\TaskRepository;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    protected ProjectReportRepository $reportRepo;
    protected TaskRepository $taskRepo;

    public function boot(ProjectReportRepository $reportRepo, TaskRepository $taskRepo)
    {
        $this->reportRepo = $reportRepo;
        $this->taskRepo = $taskRepo;
    }

    public ?ProjectReport $report = null;
    public ?string $customer_id = null;
    public string $report_type = '';
    public ?string $service_category_key = null;
    public ?string $project_id = null;
    public ?string $service_id = null;
    public ?string $task_id = null;

    public array $reportLines = [], $modalLines = [];
    public bool $showReportModal = false, $isViewMode = false;
    public array $customers = [], $categories = [], $projects = [], $services = [], $inProgressTasks = [];
    public array $allProjects = []; // Store all fetched projects
    public array $hourOptions = [], $minuteOptions = [0, 15, 30, 45];
    public ?Project $selectedProject = null;
    public ?int $assignedHours = null;

    // Report Creation Context
    public string $creation_target = 'PROJECT'; // PROJECT | TASK

    // Date Editing
    public string $report_date = '';
    public bool $isDateEditing = false;

    public function mount(?ProjectReport $report = null): void
    {
        $this->report = $report ?? new ProjectReport;
        $this->hourOptions = range(0, 10);
        $this->customers = Customer::query()->orderBy('name')->get(['id', 'name'])->toArray();

        // Pre-select via Query String
        if (!$this->report->exists && request()->has('project')) {
            $projectId = request()->query('project');
            $project = Project::find($projectId);
            if ($project) {
                $this->customer_id = $project->customer_id;
                $this->report_type = 'PROJECT';
                $this->project_id = $project->id;
                $this->report_date = now()->format('Y-m-d'); // Default to today
                $this->loadRelatedItems();
                $this->updatedProjectId();
            }
        } else {
            $this->report_date = now()->format('Y-m-d'); // Default to today
        }

        if ($this->report?->exists) {
            $this->isViewMode = true;
            $this->loadReportData();
        }
    }

    public function loadReportData(): void
    {
        $this->customer_id = $this->report->customer_id;
        $this->report_type = $this->report->report_type;
        $this->project_id = $this->report->project_id;
        $this->service_id = $this->report->service_id;
        $this->task_id = $this->report->task_id;
        if ($this->report->service_id)
            $this->service_category_key = $this->report->service?->service_category;

        $this->reportLines[] = [
            'id' => $this->report->id,
            'hours' => $this->report->hours,
            'minutes' => $this->report->minutes,
            'content' => $this->report->content,
            'content' => $this->report->content,
            'user_name' => $this->report->creator?->name ?? 'Sistem',
        ];

        $this->report_date = $this->report->created_at->format('Y-m-d');

        if ($this->customer_id)
            $this->loadRelatedItems();
        if ($this->project_id)
            $this->updatedProjectId();
    }

    public function updatedCustomerId(): void
    {
        $this->project_id = null;
        $this->service_id = null;
        $this->task_id = null;
        $this->loadRelatedItems();
    }

    public function loadRelatedItems(): void
    {
        if (!$this->customer_id) {
            $this->projects = $this->services = $this->inProgressTasks = $this->allProjects = [];
            return;
        }

        // Fetch projects with type
        $this->allProjects = Project::with('type')
            ->where('customer_id', $this->customer_id)
            ->orderBy('name')
            ->get(['id', 'name', 'type_id'])
            ->map(fn($p) => [
                'id' => (string) $p->id,
                'name' => $p->name,
                'type_key' => $p->type?->key
            ])
            ->toArray();

        // Initial Filter
        $this->filterProjects();

        $this->inProgressTasks = $this->taskRepo->getInProgressTasksForCustomer($this->customer_id)->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->toArray();

        $serviceCats = Service::query()->where('customer_id', $this->customer_id)->where('is_active', true)->pluck('service_category')->unique()->filter()->toArray();
        $this->categories = ReferenceItem::query()->where('category_key', 'SERVICE_CATEGORY')->whereIn('key', $serviceCats)->orderBy('sort_order')->get(['id', 'key', 'display_label as name'])->toArray();
    }

    public function updatedReportType(): void
    {
        $this->project_id = null; // Reset selection on type change
        $this->filterProjects();
    }

    private function filterProjects(): void
    {
        if ($this->report_type === 'PROJECT') {
            // "Yapım Projesi" -> Filter only PROJECT_BUILD key
            $this->projects = array_values(array_filter($this->allProjects, fn($p) => $p['type_key'] === 'PROJECT_BUILD'));
        } else {
            // Default or SERVICE -> Show all projects
            // Or if you want to filter SERVICE to PROJECT_SUPPORT, use:
            // $this->projects = array_values(array_filter($this->allProjects, fn($p) => $p['type_key'] === 'PROJECT_SUPPORT'));
            // But per request, only PROJECT restriction was explicit.
            $this->projects = $this->allProjects;
        }
    }

    public function updatedProjectId(): void
    {
        $this->assignedHours = null;
        $this->selectedProject = null;
        if ($this->project_id && \Illuminate\Support\Str::isUuid($this->project_id)) {
            $this->selectedProject = Project::with(['phases.modules', 'type'])->find($this->project_id);
            if ($this->selectedProject) {
                $total = 0;
                foreach ($this->selectedProject->phases as $ph)
                    foreach ($ph->modules as $m)
                        $total += (int) $m->estimated_hours;
                $this->assignedHours = $total > 0 ? $total : null;
            }
        }
    }

    public function openReportModal(): void
    {
        $this->modalLines = [['hours' => 1, 'minutes' => 0, 'content' => '']];
        $this->showReportModal = true;
    }
    public function addModalLine(): void
    {
        $this->modalLines[] = ['hours' => 1, 'minutes' => 0, 'content' => ''];
    }
    public function removeModalLine($index): void
    {
        unset($this->modalLines[$index]);
        $this->modalLines = array_values($this->modalLines);
    }

    public function confirmModalLines(): void
    {
        $this->validate(['modalLines.*.hours' => 'required|integer|min:0|max:10', 'modalLines.*.minutes' => 'required|integer|in:0,15,30,45', 'modalLines.*.content' => 'required|min:5']);
        foreach ($this->modalLines as $line) {
            $line['user_name'] = auth()->user()->name;
            $this->reportLines[] = $line;
        }
        $this->showReportModal = false;
        $this->success('Rapor satırları eklendi.');
    }

    public function removeReportLine($index): void
    {
        unset($this->reportLines[$index]);
        $this->reportLines = array_values($this->reportLines);
    }

    public function save(): void
    {
        if ($this->creation_target === 'NONE') {
            $this->project_id = null;
            $this->task_id = null;
            $this->service_id = null;
            // Validasyon yaparken report_type'ı GENERAL yap.
            // Fakat DB'de report_type ne olmalı? 'GENERAL' yapalım.
            $this->report_type = 'GENERAL';
        } elseif ($this->creation_target === 'TASK') {
            $this->validate(['task_id' => 'required|uuid']);
            // Find project related to task
            $task = \App\Models\ProjectTask::with('project.type')->find($this->task_id);
            if (!$task || !$task->project) {
                $this->addError('task_id', 'Seçilen görevin bağlı olduğu bir proje bulunamadı.');
                return;
            }
            $this->project_id = $task->project_id;
            // Determine report type based on project type
            $typeKey = $task->project->type?->key;
            $this->report_type = ($typeKey === 'PROJECT_BUILD') ? 'PROJECT' : 'SERVICE';
        }

        if ($this->creation_target !== 'NONE') {
            if ($this->report_type === 'SERVICE' && $this->creation_target === 'PROJECT') {
                $this->validate(['service_category_key' => 'required|string']);
            }
            $this->validate(['customer_id' => 'required|uuid', 'report_type' => 'required|in:PROJECT,SERVICE', 'project_id' => 'required|uuid', 'reportLines' => 'required|array|min:1']);
        } else {
            $this->validate(['reportLines' => 'required|array|min:1']);
        }

        // Date Validation
        $this->validate([
            'report_date' => 'required|date|before_or_equal:today'
        ]);

        $data = ['customer_id' => $this->customer_id, 'report_type' => $this->report_type, 'project_id' => $this->project_id, 'service_id' => $this->service_id, 'task_id' => $this->task_id, 'created_at' => $this->report_date];

        if ($this->report?->exists) {
            $this->reportRepo->updateReport($this->report, $data, $this->reportLines[0]);
            $this->success('Rapor güncellendi.');
            $this->isViewMode = true;
        } else {
            $this->reportRepo->saveReports($data, $this->reportLines);
            $this->success('Raporlar oluşturuldu.');
            $this->redirect(route('projects.index', ['tab' => 'reports']), navigate: true);
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = !$this->isViewMode;
    }
    public function delete(): void
    {
        if ($this->report?->exists) {
            $this->report->delete();
            $this->warning('Rapor silindi.');
            $this->redirect(route('projects.index', ['tab' => 'reports']), navigate: true);
        }
    }

    public function getTotalTimeProperty()
    {
        $total = 0;
        foreach ($this->reportLines as $l)
            $total += ($l['hours'] * 60) + $l['minutes'];
        return ['hours' => floor($total / 60), 'minutes' => $total % 60];
    }

    public function toggleDateEdit()
    {
        $this->isDateEditing = !$this->isDateEditing;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        <a href="{{ route('projects.index', ['tab' => 'reports']) }}"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Rapor Listesi</span>
        </a>

        @include('livewire.projects.reports.parts._header')

        <div class="grid grid-cols-12 gap-6">
            @include('livewire.projects.reports.parts._form-left')
            @include('livewire.projects.reports.parts._sidebar')
        </div>
    </div>

    @include('livewire.projects.reports.parts._modal')
</div>