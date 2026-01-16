<?php

namespace App\Livewire\Projects\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasHierarchyManagement
 * 
 * Manages Phases and Modules logic (Hierarchical Form).
 */
trait HasHierarchyManagement
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
    public ?int $editingModuleIndex = null;

    public array $moduleForm = [
        'name' => '',
        'description' => '',
        'status_id' => '',
        'start_date' => null,
        'end_date' => null,
        'assigned_users' => [],
        'estimated_hours' => null,
        'is_unlimited' => true,
    ];

    /**
     * PHASES MANAGEMENT
     */
    public function savePhase(): void
    {
        $this->validate(['phaseForm.name' => 'required|string|max:255']);

        $colors = ['#3b82f6', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        if ($this->editingPhaseIndex !== null) {
            // Update existing phase
            $this->phases[$this->editingPhaseIndex]['name'] = $this->phaseForm['name'];
            $this->phases[$this->editingPhaseIndex]['description'] = $this->phaseForm['description'];
            $this->phases[$this->editingPhaseIndex]['status_id'] = $this->phaseForm['status_id'] ?: null;

            $this->success('Faz Güncellendi', 'Faz bilgileri güncellendi.');
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
            $this->success('Faz Eklendi', 'Yeni faz listeye eklendi.');
        }

        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }

        $this->phaseModalOpen = false;
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
                $this->error('Sınır Aşıldı', 'Bir projeye en fazla 20 faz eklenebilir.');
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

    public function removePhase(int $index): void
    {
        unset($this->phases[$index]);
        $this->phases = array_values($this->phases);

        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }
    }

    /**
     * MODULES MANAGEMENT
     */
    public function addModule(int $phaseIndex): void
    {
        // Simple add placeholder is replaced by openModuleModal in the refined UI logic
        // But kept here if referenced directly
        $this->phases[$phaseIndex]['modules'][] = [
            'name' => '',
        ];
    }

    public function removeModule(int $phaseIndex, int $moduleIndex): void
    {
        unset($this->phases[$phaseIndex]['modules'][$moduleIndex]);
        $this->phases[$phaseIndex]['modules'] = array_values($this->phases[$phaseIndex]['modules']);
        $this->calculatePhaseDates($phaseIndex);

        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }
    }

    public function openModuleModal(int $phaseIndex, ?int $moduleIndex = null): void
    {
        $this->editingModulePhaseIndex = $phaseIndex;
        $this->editingModuleIndex = $moduleIndex;

        if ($moduleIndex !== null) {
            // Edit Mode
            $module = $this->phases[$phaseIndex]['modules'][$moduleIndex];
            $defaultStatus = $this->moduleStatuses[0]['id'] ?? '';
            $currentStatus = $module['status_id'] ?? '';
            if (empty($currentStatus)) {
                $currentStatus = $defaultStatus;
            }

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

            $this->dispatch('update-date-range', key: 'module_modal', start: $module['start_date'] ?? null, end: $module['end_date'] ?? null);
        } else {
            // Create Mode
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
            $this->success('Modül Güncellendi');
        } else {
            $moduleData['id'] = (string) Str::uuid();
            $modules[] = $moduleData;
            $this->success('Modül Eklendi');
        }

        // Sorting
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

        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }

        $this->moduleModalOpen = false;
    }

    private function calculatePhaseDates(int $phaseIndex): void
    {
        $modules = $this->phases[$phaseIndex]['modules'] ?? [];
        if (empty($modules)) {
            return;
        }

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
}
