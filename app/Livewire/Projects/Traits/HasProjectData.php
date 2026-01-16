<?php

namespace App\Livewire\Projects\Traits;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

trait HasProjectData
{
    // Temel Bilgiler
    public string $name = '';
    public string $description = '';
    public string $customer_id = '';
    public string $leader_id = '';
    public ?string $status_id = null;
    public ?string $type_id = null;
    public array $team_members = [];
    public string $timezone = 'Europe/Istanbul';

    // Tarihler
    public ?string $start_date = null;
    public ?string $target_end_date = null;
    public ?string $completed_at = null;

    // Logic
    public bool $auto_calculate_end_date = true;
    public bool $auto_calculate_start_date = true;

    // State
    public bool $isViewMode = false;
    public ?string $projectId = null;
    public string $activeTab = 'project_info';

    // Reference Data
    public $customers = [];
    public $leaders = [];
    public $statuses = [];
    public $projectTypes = [];
    public ?array $selectedCustomer = null;

    // External User
    public bool $inviteExternalUser = false;
    public string $externalUserEmail = '';
    public string $externalUserName = '';

    public function updatedActiveTab($value)
    {
        $this->dispatch('url-changed', url: route('projects.edit', ['project' => $this->projectId, 'tab' => $value]));
    }

    public function updatedCustomerId($value): void
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->selectedCustomer = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'logo_url' => $customer->logo_url,
                ];
            }
        } else {
            $this->selectedCustomer = null;
        }
    }

    public function updatedAutoCalculateStartDate(): void
    {
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }
    }

    public function updatedAutoCalculateEndDate(): void
    {
        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
    }

    private function loadReferenceData(): void
    {
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name', 'logo_url'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'logo_url' => $c->logo_url])
            ->toArray();

        $this->leaders = User::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->name])
            ->toArray();

        $this->statuses = ReferenceItem::where('category_key', 'PROJECT_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key'])
            ->toArray();

        $this->projectTypes = ReferenceItem::where('category_key', 'PROJECT_TYPE')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label'])
            ->toArray();
    }

    private function loadProjectData(): void
    {
        try {
            $project = Project::with(['customer', 'status', 'leader', 'phases.modules'])->findOrFail($this->projectId);

            $this->name = $project->name;
            $this->description = $project->description ?? '';
            $this->customer_id = $project->customer_id ?? '';
            $this->leader_id = $project->leader_id ?? '';
            $this->status_id = $project->status_id ?? '';
            $this->type_id = $project->type_id ?? '';
            $this->timezone = $project->timezone ?? 'Europe/Istanbul';
            $this->start_date = $project->start_date?->format('Y-m-d');
            $this->target_end_date = $project->target_end_date?->format('Y-m-d');
            $this->completed_at = $project->completed_at?->toIso8601String();
            $this->auto_calculate_end_date = $project->custom_fields['auto_calculate_end_date'] ?? true;
            $this->auto_calculate_start_date = $project->custom_fields['auto_calculate_start_date'] ?? true;

            if ($project->customer) {
                $this->selectedCustomer = [
                    'id' => $project->customer->id,
                    'name' => $project->customer->name,
                    'logo_url' => $project->customer->logo_url,
                ];
            }

            // Load Team Members
            $this->team_members = $project->users->pluck('id')->toArray();

            // Load phases (Defer to Hierarchy Trait if possible, but loadProjectData orchestrates it)
            // Assuming HasProjectHierarchy provides loadPhases logic, or we manipulate $this->phases directly here.
            // Since $this->phases is in Hierarchy Trait, we need to access it.
            // Traits share state.
            if (method_exists($this, 'loadProjectPhases')) {
                $this->loadProjectPhases($project);
            }

            $this->isViewMode = true;

            // Sync auto calculations if enabled
            if ($this->auto_calculate_start_date) {
                $this->calculateAutoStartDate();
            }
            if ($this->auto_calculate_end_date) {
                $this->calculateAutoEndDate();
            }
        } catch (\Exception $e) {
            $this->error('Proje Bulunamadı', 'İstenilen proje kaydı bulunamadı.');
            $this->redirect('/dashboard/projects', navigate: true);
        }
    }

    #[Computed]
    public function spentTime(): array
    {
        if (!$this->projectId) {
            return ['hours' => 0, 'minutes' => 0, 'total_minutes' => 0];
        }

        $totalMinutes = ProjectReport::where('project_id', $this->projectId)
            ->select(DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->value('total_minutes') ?? 0;

        return [
            'hours' => floor($totalMinutes / 60),
            'minutes' => (int) ($totalMinutes % 60),
            'total_minutes' => (int) $totalMinutes,
        ];
    }
}
