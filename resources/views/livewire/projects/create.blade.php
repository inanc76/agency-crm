<?php
/**
 * 🏗️ PROJECT CREATE/EDIT COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Single File Component)
 *
 * HİYERARŞİ:
 *  - Ana Dosya: Layout, State ve Form yönetimi
 *  - Partials: Form kartları
 * ---------------------------------------------------------
 */

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Proje Oluştur'])]
    class extends Component {
    use Toast;

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

    // State
    public bool $isViewMode = false;

    public ?string $projectId = null;

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

    // Hierarchical Form - Phases & Modules
    public array $phases = [];

    public array $phaseStatuses = [];

    public array $moduleStatuses = []; // [NEW] Module Statuses

    // Phase Modal State
    public bool $phaseModalOpen = false;

    public array $phaseForm = [
        'name' => '',
        'description' => '',
        'status_id' => '',
    ];

    public ?int $editingPhaseIndex = null;

    // [NEW] Module Modal State
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

    // Logic
    public bool $auto_calculate_end_date = true;

    public bool $auto_calculate_start_date = true;

    public function mount(?string $project = null): void
    {
        $this->loadReferenceData();

        if ($project) {
            $this->projectId = $project;
            $this->loadProjectData();
        } else {
            // Varsayılan durum ata
            $defaultStatus = ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('is_default', true)
                ->first();
            if ($defaultStatus) {
                $this->status_id = $defaultStatus->id;
            }
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

            // Load phases and modules
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

            // Recalculate phase and project dates to ensure sync
            foreach ($this->phases as $idx => $phase) {
                $this->calculatePhaseDates($idx);
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

    private function calculateAutoStartDate(): void
    {
        if (!$this->auto_calculate_start_date) {
            return;
        }

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

    public function updatedAutoCalculateEndDate(): void
    {
        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
    }

    private function calculateAutoEndDate(): void
    {
        if (!$this->auto_calculate_end_date) {
            return;
        }

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

    // ─────────────────────────────────────────────────────────────────────────
    // HIERARCHICAL FORM METHODS
    // ─────────────────────────────────────────────────────────────────────────

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

        // If manual update might change dates (though phases usually get dates from modules)
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

    public function addModule(int $phaseIndex): void
    {
        $this->phases[$phaseIndex]['modules'][] = [
            'name' => '',
        ];
    }

    public function removeModule(int $phaseIndex, int $moduleIndex): void
    {
        unset($this->phases[$phaseIndex]['modules'][$moduleIndex]);
        $this->phases[$phaseIndex]['modules'] = array_values($this->phases[$phaseIndex]['modules']);
        $this->calculatePhaseDates($phaseIndex); // Recalculate on remove

        if ($this->auto_calculate_end_date) {
            $this->calculateAutoEndDate();
        }
        if ($this->auto_calculate_start_date) {
            $this->calculateAutoStartDate();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MODULE MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    public function openModuleModal(int $phaseIndex, ?int $moduleIndex = null): void
    {
        $this->editingModulePhaseIndex = $phaseIndex;
        $this->editingModuleIndex = $moduleIndex;

        if ($moduleIndex !== null) {
            // Edit Mode
            $module = $this->phases[$phaseIndex]['modules'][$moduleIndex];

            // Fallback for legacy data without status
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

            // Dispatch event to update date picker UI
            $this->dispatch('update-date-range', key: 'module_modal', start: $module['start_date'] ?? null, end: $module['end_date'] ?? null);
        } else {
            // Create Mode
            // Default ACL: All current project participants are CHECKED
            // We use the full list of participants stored in Team Members + Leader
            $projectParticipants = $this->team_members;
            if ($this->leader_id && !in_array($this->leader_id, $projectParticipants)) {
                $projectParticipants[] = $this->leader_id;
            }

            $this->moduleForm = [
                'name' => '',
                'description' => '',
                'status_id' => $this->moduleStatuses[0]['id'] ?? '', // Default to first status
                'start_date' => null,
                'end_date' => null,
                'assigned_users' => $projectParticipants, // [CRITICAL] Default checked
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

        // Deep Clone / Explicit Array Retrieval to ensure Livewire Reactivity
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

        // Preserve ID if editing
        if ($this->editingModuleIndex !== null) {
            $moduleData['id'] = $modules[$this->editingModuleIndex]['id'] ?? (string) Str::uuid();
            $modules[$this->editingModuleIndex] = $moduleData;
            $this->success('Modül Güncellendi');
        } else {
            $moduleData['id'] = (string) Str::uuid();
            $modules[] = $moduleData;
            $this->success('Modül Eklendi');
        }

        // Re-assign explicitly to trigger update
        // [SORTING] Sort modules by Start Date (Ascending), Null dates last
        usort($modules, function ($a, $b) {
            $dateA = $a['start_date'] ?? null;
            $dateB = $b['start_date'] ?? null;

            if ($dateA === $dateB) {
                return 0;
            }
            if ($dateA === null) {
                return 1;
            } // Null goes last
            if ($dateB === null) {
                return -1;
            } // Null goes last

            return $dateA <=> $dateB;
        });

        $phase['modules'] = array_values($modules); // Re-index
        $this->phases[$phaseIdx] = $phase;

        // [OBSERVER PATTERN] Propagate Dates
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
            if (!empty($m['start_date'])) {
                $startDates[] = $m['start_date'];
            }
            if (!empty($m['end_date'])) {
                $endDates[] = $m['end_date'];
            }
        }

        if (!empty($startDates)) {
            $this->phases[$phaseIndex]['start_date'] = min($startDates);
        }

        if (!empty($endDates)) {
            $this->phases[$phaseIndex]['end_date'] = max($endDates);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STATE MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    public function toggleEdit(): void
    {
        $this->isViewMode = !$this->isViewMode;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'leader_id' => 'nullable|exists:users,id',
            'status_id' => 'required|exists:reference_items,id',
            'type_id' => 'required|exists:reference_items,id',
            'timezone' => 'required|string',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date|after_or_equal:start_date',
            'auto_calculate_end_date' => 'boolean',
            'auto_calculate_start_date' => 'boolean',
        ];

        // External user validation (only for new projects or if switch is toggled and not yet attached)
        if ($this->inviteExternalUser) {
            // Check if user already exists
            $existingUser = User::where('email', $this->externalUserEmail)->first();
            if (!$existingUser) {
                $rules['externalUserEmail'] = 'required|email|unique:users,email';
                $rules['externalUserName'] = 'required|string|max:255';
            }
        }

        $validated = $this->validate($rules);
        $validated['type_id'] = $this->type_id ?: null;

        // Add custom field persistence
        $validated['custom_fields'] = [
            'auto_calculate_end_date' => $this->auto_calculate_end_date,
            'auto_calculate_start_date' => $this->auto_calculate_start_date,
        ];

        try {
            DB::transaction(function () use ($validated) {
                if ($this->projectId) {
                    $project = Project::findOrFail($this->projectId);
                    $project->update($validated);
                    $this->success('Proje Güncellendi', 'Proje başarıyla güncellendi.');
                } else {
                    $project = Project::create($validated);
                    $this->projectId = $project->id;
                }

                // 1. External User Logic
                if ($this->inviteExternalUser && $this->externalUserEmail) {
                    $externalUser = User::where('email', $this->externalUserEmail)->first();

                    if (!$externalUser) {
                        $guestRole = \App\Models\Role::firstOrCreate(
                            ['name' => 'guest'],
                            ['description' => 'External Guest User']
                        );

                        $externalUser = User::create([
                            'name' => $this->externalUserName,
                            'email' => $this->externalUserEmail,
                            'password' => bcrypt(\Str::random(16)),
                            'role_id' => $guestRole->id,
                            'custom_fields' => ['is_external' => true],
                        ]);
                    }

                    // Attach if not already attached
                    if (!$project->users()->where('users.id', $externalUser->id)->exists()) {
                        $project->users()->attach($externalUser->id, ['role' => 'external']);
                    }
                }

                // Sync Team Members (Overrides External logic if conflict, but needed for Participants Card)
                $project->users()->sync($this->team_members);

                // 2. Hierarchical Form Sync (Phases & Modules)
                $existingPhaseIds = $project->phases()->pluck('id')->toArray();
                $submittedPhaseIds = collect($this->phases)->pluck('id')->filter()->toArray();

                // Delete removed phases
                $phasesToDelete = array_diff($existingPhaseIds, $submittedPhaseIds);
                if (!empty($phasesToDelete)) {
                    $project->phases()->whereIn('id', $phasesToDelete)->delete();
                }

                foreach ($this->phases as $phaseIndex => $phaseData) {
                    if (empty($phaseData['name'])) {
                        continue;
                    }

                    $phase = $project->phases()->updateOrCreate(
                        ['id' => $phaseData['id'] ?? null],
                        [
                            'name' => $phaseData['name'],
                            'start_date' => $phaseData['start_date'] ?? null,
                            'end_date' => $phaseData['end_date'] ?? null,
                            'custom_fields' => ['color' => $phaseData['color'] ?? null],
                            'order' => $phaseIndex + 1,
                        ]
                    );

                    // Sync Modules for this phase
                    $existingModuleIds = $phase->modules()->pluck('id')->toArray();
                    $submittedModuleIds = collect($phaseData['modules'] ?? [])->pluck('id')->filter()->toArray();

                    // Delete removed modules
                    $modulesToDelete = array_diff($existingModuleIds, $submittedModuleIds);
                    if (!empty($modulesToDelete)) {
                        $phase->modules()->whereIn('id', $modulesToDelete)->delete();
                    }

                    foreach ($phaseData['modules'] ?? [] as $moduleIndex => $moduleData) {
                        if (empty($moduleData['name'])) {
                            continue;
                        }

                        $module = $phase->modules()->updateOrCreate(
                            ['id' => $moduleData['id'] ?? null],
                            [
                                'name' => $moduleData['name'],
                                'description' => $moduleData['description'] ?? null,
                                'status_id' => $moduleData['status_id'] ?? null,
                                'start_date' => $moduleData['start_date'] ?? null,
                                'end_date' => $moduleData['end_date'] ?? null,
                                'estimated_hours' => $moduleData['estimated_hours'] ?? null,
                                'order' => $moduleIndex + 1,
                            ]
                        );

                        // Sync Assigned Users (ACL)
                        if (isset($moduleData['assigned_users']) && is_array($moduleData['assigned_users'])) {
                            $module->users()->sync($moduleData['assigned_users']);
                        }
                    }
                }

                if (!$this->projectId) {
                    $this->success('Proje Oluşturuldu', 'Yeni proje başarıyla oluşturuldu.');
                    $this->redirect(route('projects.edit', $this->projectId), navigate: true);
                }
            });

            $this->isViewMode = true;
            // Reload data to refresh IDs and state
            $this->loadProjectData();
        } catch (\Exception $e) {
            $this->error('Hata', 'Proje kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        if (!$this->projectId) {
            return;
        }

        try {
            $project = Project::findOrFail($this->projectId);
            $project->delete();

            $this->success('Proje Silindi', 'Proje başarıyla silindi.');
            $this->redirect('/dashboard/projects', navigate: true);
        } catch (\Exception $e) {
            $this->error('Hata', 'Proje silinirken bir hata oluştu.');
        }
    }
}; ?>

<div x-data="unsavedChangesWatcher" x-on:input="markDirty()" x-on:change="markDirty()" class="p-6 min-h-screen"
    style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        @include('livewire.projects.partials.create._header')

        {{-- Main Layout: 8/12 Left, 4/12 Right --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8 flex flex-col gap-6">
                @include('livewire.projects.partials.create._basic-info')





                @include('livewire.projects.partials.create._participants')

                @include('livewire.projects.partials.create._hierarchy')



            </div>

            {{-- Right Column (4/12) --}}
            <div class="col-span-4 flex flex-col gap-6" wire:key="customer-preview-{{ $customer_id }}">
                @include('livewire.projects.partials.create._customer-preview')

            </div>
        </div>
    </div>

    @include('livewire.projects.partials.create._modals')
</div>

@include('livewire.projects.partials.create._scripts')