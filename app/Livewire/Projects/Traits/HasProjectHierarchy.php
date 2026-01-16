<?php

namespace App\Livewire\Projects\Traits;

use App\Models\ReferenceItem;
use Illuminate\Support\Str;

trait HasProjectHierarchy
{
    // Hierarchical Form - Phases & Modules
    public array $phases = [];
    public array $phaseStatuses = [];
    public array $moduleStatuses = [];

    // Phase Modal State
    public bool $phaseModalOpen = false;
    public array $phaseForm = [
        'name' => '',
        'description' => '',
        'status_id' => '',
    ];
    public ?int $editingPhaseIndex = null;

    // Module Modal State
    public bool $moduleModalOpen = false;
    public ?int $editingModulePhaseIndex = null;
    public ?int $editingModuleIndex = null; // null = create
    public array $moduleForm = [
        'name' => '',
        'description' => '',
        'status_id' => '',
        'start_date' => null,
        'end_date' => null,
        'assigned_users' => [], // ID list
        'estimated_hours' => null,
        'is_unlimited' => true,
    ];

    public function loadHierarchyReferenceData(): void
    {
        $this->phaseStatuses = ReferenceItem::where('category_key', 'PHASE_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->toArray();

        $this->moduleStatuses = ReferenceItem::where('category_key', 'MODULE_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->toArray();
    }

    public function loadProjectPhases($project): void
    {
        $this->phases = $project->phases->map(function ($phase) {
            return [
                'id' => $phase->id,
                'name' => $phase->name,
                'start_date' => $phase->start_date?->format('Y-m-d'),
                'end_date' => $phase->end_date?->format('Y-m-d'),
                'color' => $phase->custom_fields['color'] ?? '#3b82f6',
                'modules' => $phase->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'description' => $module->description,
                        'status_id' => $module->status_id,
                        'start_date' => $module->start_date?->format('Y-m-d'),
                        'end_date' => $module->end_date?->format('Y-m-d'),
                        'estimated_hours' => $module->estimated_hours,
                        'assigned_users' => $module->users->pluck('id')->toArray(),
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Recalculate phase dates
        foreach ($this->phases as $idx => $phase) {
            $this->calculatePhaseDates($idx);
        }
    }

    public function openPhaseModal(?int $index = null): void
    {
        $this->editingPhaseIndex = $index;

        if ($index !== null) {
            // Edit Mode
            $phase = $this->phases[$index];
            $this->phaseForm = [
                'name' => $phase['name'],
                'description' => $phase['description'] ?? '',
                'status_id' => $phase['status_id'] ?? '',
            ];
        } else {
            // Create Mode
            if (count($this->phases) >= 20) {
                $this->addError('phase_limit', 'Bir projeye en fazla 20 faz eklenebilir.');
                return;
            }

            $this->phaseForm = [
                'name' => '',
                'description' => '',
                'status_id' => $this->phaseStatuses[0]['id'] ?? '',
            ];
        }

        $this->phaseModalOpen = true;
    }

    public function savePhase(): void
    {
        $this->validate(['phaseForm.name' => 'required|string|max:255']);

        $colors = ['#3b82f6', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        if ($this->editingPhaseIndex !== null) {
            // Update existing phase
            $this->phases[$this->editingPhaseIndex]['name'] = $this->phaseForm['name'];
            $this->phases[$this->editingPhaseIndex]['description'] = $this->phaseForm['description'];
            $this->phases[$this->editingPhaseIndex]['status_id'] = $this->phaseForm['status_id'] ?: null;
        } else {
            // Create new phase
            $colorIndex = count($this->phases) % count($colors);

            $this->phases[] = [
                'id' => (string) Str::uuid(),
                'name' => $this->phaseForm['name'],
                'description' => $this->phaseForm['description'],
                'status_id' => $this->phaseForm['status_id'] ?: null,
                'start_date' => null,
                'end_date' => null,
                'color' => $colors[$colorIndex],
                'modules' => [],
            ];
        }

        $this->fullDateRecalculation();
        $this->phaseModalOpen = false;
    }

    public function removePhase(int $index): void
    {
        unset($this->phases[$index]);
        $this->phases = array_values($this->phases);
        $this->fullDateRecalculation();
    }

    public function openModuleModal(int $phaseIndex, ?int $moduleIndex = null): void
    {
        $this->editingModulePhaseIndex = $phaseIndex;
        $this->editingModuleIndex = $moduleIndex;

        if ($moduleIndex !== null) {
            // Edit Mode
            $module = $this->phases[$phaseIndex]['modules'][$moduleIndex];

            $defaultStatus = $this->moduleStatuses[0]['id'] ?? '';
            $currentStatus = $module['status_id'] ?? $defaultStatus;

            $this->moduleForm = [
                'name' => $module['name'],
                'description' => $module['description'] ?? '',
                'status_id' => $currentStatus,
                'start_date' => $module['start_date'] ?? null,
                'end_date' => $module['end_date'] ?? null,
                'assigned_users' => $module['assigned_users'] ?? [],
                'estimated_hours' => $module['estimated_hours'] ?? null,
                'is_unlimited' => ($module['estimated_hours'] ?? null) === null,
            ];

            // Dispatch event to update date picker UI
            $this->dispatch('update-date-range', key: 'module_modal', start: $module['start_date'] ?? null, end: $module['end_date'] ?? null);
        } else {
            // Create Mode
            // Default ACL: All current project participants
            // Accessing properties from HasProjectData. NOTE: Need to verify scope or use method.
            // Traits share properties. $this->team_members works.
            $projectParticipants = $this->team_members;
            if ($this->leader_id && !in_array($this->leader_id, $projectParticipants)) {
                $projectParticipants[] = $this->leader_id;
            }

            $this->moduleForm = [
                'name' => '',
                'description' => '',
                'status_id' => $this->moduleStatuses[0]['id'] ?? '',
                'start_date' => null,
                'end_date' => null,
                'assigned_users' => $projectParticipants,
                'estimated_hours' => null,
                'is_unlimited' => true,
            ];

            // Clear date picker UI
            $this->dispatch('update-date-range', key: 'module_modal', start: null, end: null);
        }

        $this->moduleModalOpen = true;
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleForm.name' => 'required|string|max:255',
            'moduleForm.status_id' => 'required',
            'moduleForm.estimated_hours' => 'nullable|numeric|min:0|max:200',
        ]);

        $phaseIdx = $this->editingModulePhaseIndex;
        $phase = $this->phases[$phaseIdx];
        $modules = $phase['modules'] ?? [];

        $moduleData = [
            'name' => $this->moduleForm['name'],
            'description' => $this->moduleForm['description'],
            'status_id' => $this->moduleForm['status_id'],
            'start_date' => $this->moduleForm['start_date'],
            'end_date' => $this->moduleForm['end_date'],
            'assigned_users' => $this->moduleForm['assigned_users'],
            'estimated_hours' => $this->moduleForm['is_unlimited'] ? null : $this->moduleForm['estimated_hours'],
        ];

        if ($this->editingModuleIndex !== null) {
            $moduleData['id'] = $modules[$this->editingModuleIndex]['id'] ?? (string) Str::uuid();
            $modules[$this->editingModuleIndex] = $moduleData;
        } else {
            $moduleData['id'] = (string) Str::uuid();
            $modules[] = $moduleData;
        }

        // Sort modules by date
        usort($modules, function ($a, $b) {
            $dateA = $a['start_date'] ?? null;
            $dateB = $b['start_date'] ?? null;

            if ($dateA === $dateB)
                return 0;
            if ($dateA === null)
                return 1;
            if ($dateB === null)
                return -1;
            return $dateA <=> $dateB;
        });

        $phase['modules'] = array_values($modules);
        $this->phases[$phaseIdx] = $phase;

        $this->calculatePhaseDates($phaseIdx);
        $this->fullDateRecalculation();

        $this->moduleModalOpen = false;
    }

    public function removeModule(int $phaseIndex, int $moduleIndex): void
    {
        unset($this->phases[$phaseIndex]['modules'][$moduleIndex]);
        $this->phases[$phaseIndex]['modules'] = array_values($this->phases[$phaseIndex]['modules']);
        $this->calculatePhaseDates($phaseIndex);
        $this->fullDateRecalculation();
    }

    private function calculatePhaseDates(int $phaseIndex): void
    {
        $modules = $this->phases[$phaseIndex]['modules'] ?? [];
        if (empty($modules))
            return;

        $startDates = [];
        $endDates = [];

        foreach ($modules as $m) {
            if (!empty($m['start_date']))
                $startDates[] = $m['start_date'];
            if (!empty($m['end_date']))
                $endDates[] = $m['end_date'];
        }

        if (!empty($startDates)) {
            $this->phases[$phaseIndex]['start_date'] = min($startDates);
        }

        if (!empty($endDates)) {
            $this->phases[$phaseIndex]['end_date'] = max($endDates);
        }
    }

    private function fullDateRecalculation(): void
    {
        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }
    }

    // Abstract methods expected by HasProjectData but implemented here since they manipulate phases
    private function calculateAutoStartDate(): void
    {
        if (!$this->auto_calculate_start_date)
            return;

        $minDate = null;
        foreach ($this->phases as $phase) {
            if (!empty($phase['start_date'])) {
                if ($minDate === null || $phase['start_date'] < $minDate) {
                    $minDate = $phase['start_date'];
                }
            }
        }
        $this->start_date = $minDate;
    }

    private function calculateAutoEndDate(): void
    {
        if (!$this->auto_calculate_end_date)
            return;

        $maxDate = null;
        foreach ($this->phases as $phase) {
            if (!empty($phase['end_date'])) {
                if ($maxDate === null || $phase['end_date'] > $maxDate) {
                    $maxDate = $phase['end_date'];
                }
            }
        }
        $this->target_end_date = $maxDate;
    }
}
