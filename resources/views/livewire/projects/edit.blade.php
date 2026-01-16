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
use App\Models\ProjectReport;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Proje Oluştur'])]
    class extends Component
    {
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

        // Tab State
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

            // Handle Query String for Tab, ensuring it is a valid tab
            $tab = request()->query('tab');
            if ($tab && in_array($tab, ['project_info', 'tasks', 'reports', 'notes'])) {
                $this->activeTab = $tab;
            }

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

        public function updatedActiveTab($value)
        {
            $this->dispatch('url-changed', url: route('projects.edit', ['project' => $this->projectId, 'tab' => $value]));
        }

        private function loadReferenceData(): void
        {
            $this->customers = Customer::orderBy('name')
                ->get(['id', 'name', 'logo_url'])
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'logo_url' => $c->logo_url])
                ->toArray();

            $this->leaders = User::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
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
            if (! $this->auto_calculate_start_date) {
                return;
            }

            $minDate = null;
            foreach ($this->phases as $phase) {
                if (! empty($phase['start_date'])) {
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
            if (! $this->auto_calculate_end_date) {
                return;
            }

            $maxDate = null;
            foreach ($this->phases as $phase) {
                if (! empty($phase['end_date'])) {
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
                if ($this->leader_id && ! in_array($this->leader_id, $projectParticipants)) {
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
                if (! empty($m['start_date'])) {
                    $startDates[] = $m['start_date'];
                }
                if (! empty($m['end_date'])) {
                    $endDates[] = $m['end_date'];
                }
            }

            if (! empty($startDates)) {
                $this->phases[$phaseIndex]['start_date'] = min($startDates);
            }

            if (! empty($endDates)) {
                $this->phases[$phaseIndex]['end_date'] = max($endDates);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // STATE MANAGEMENT
        // ─────────────────────────────────────────────────────────────────────────

        public function toggleEdit(): void
        {
            $this->isViewMode = ! $this->isViewMode;
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
                if (! $existingUser) {
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

                        if (! $externalUser) {
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
                        if (! $project->users()->where('users.id', $externalUser->id)->exists()) {
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
                    if (! empty($phasesToDelete)) {
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
                        if (! empty($modulesToDelete)) {
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

                    if (! $this->projectId) {
                        $this->success('Proje Oluşturuldu', 'Yeni proje başarıyla oluşturuldu.');
                        $this->redirect(route('projects.edit', $this->projectId), navigate: true);
                    }
                });

                $this->isViewMode = true;
                // Reload data to refresh IDs and state
                $this->loadProjectData();
            } catch (\Exception $e) {
                $this->error('Hata', 'Proje kaydedilirken bir hata oluştu: '.$e->getMessage());
            }
        }

        public function delete(): void
        {
            if (! $this->projectId) {
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
    }; ?>

<div 
    x-data="unsavedChangesWatcher"
    x-on:input="markDirty()"
    x-on:change="markDirty()"
    class="p-6 min-h-screen" 
    style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/projects"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Proje Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name ?: 'Proje Detayı' }}
                    @elseif($projectId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Proje Oluştur
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($projectId)
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Proje</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">Kod: {{ Project::find($projectId)?->project_id_code }}</span>
                    @else
                        <p class="text-sm opacity-60 text-skin-base">
                            Yeni proje bilgilerini girin
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($isViewMode)
                    {{-- View Mode Actions --}}
                    @if($projectId)
                        <button type="button" wire:click="delete" wire:confirm="Bu projeyi silmek istediğinize emin misiniz?"
                            wire:key="btn-delete-{{ $projectId }}"
                            class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                            Sil
                        </button>
                    @endif
                    <button type="button" wire:click="toggleEdit"
                        wire:key="btn-edit-{{ $projectId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    {{-- Edit Mode Actions --}}
                    <button type="button" wire:click="{{ $projectId ? 'toggleEdit' : '' }}" 
                            wire:key="btn-cancel-{{ $projectId ?: 'new' }}"
                            @if(!$projectId) onclick="window.location.href='/dashboard/projects'" @endif
                        class="theme-btn-cancel px-4 py-2 text-sm">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $projectId ?: 'new' }}"
                        @click="markClean()"
                        class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($projectId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- TABS NAVIGATION --}}
        @if($projectId)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                @php
                    $tabs = [
                        'project_info' => 'Proje Bilgileri',
                        'tasks' => 'Görevler',
                        'reports' => 'Raporlar',
                        'notes' => 'Notlar',
                    ];
                @endphp
                
                @foreach($tabs as $key => $label)
                    <button 
                        wire:click="$set('activeTab', '{{ $key }}')"
                        class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === $key ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}"
                        onclick="history.pushState(null, '', '{{ route('projects.edit', ['project' => $projectId, 'tab' => $key]) }}')">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- TAB CONTENT --}}
        <div>
            {{-- Tab 1: Proje Bilgileri --}}
            <div x-show="$wire.activeTab === 'project_info'">
                {{-- Main Layout: 8/12 Left, 4/12 Right --}}
                <div class="grid grid-cols-12 gap-6">
                    {{-- Left Column (8/12) --}}
                    <div class="col-span-8 flex flex-col gap-6">
                        {{-- Basic Info Card --}}
                        <div class="theme-card p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                                <x-mary-icon name="o-folder" class="w-5 h-5" />
                        Proje Bilgileri
                    </h3>

                    <div class="grid grid-cols-2 gap-8">
                        {{-- Name --}}
                        <div class="col-span-2">
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Adı *</label>
                            @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $name ?: '-' }}</div>
                            @else
                                <input type="text" wire:model.blur="name" placeholder="Proje adını girin..." class="input w-full">
                                @error('name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- Customer --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri *</label>
                            @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $selectedCustomer['name'] ?? '-' }}</div>
                            @else
                                <select wire:model.live="customer_id" class="select w-full">
                                    <option value="">Müşteri Seçin</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- Leader Removed (Moved to Participants Card) --}}

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Durum *</label>
                            @if($isViewMode)
                                @php $statusLabel = collect($statuses)->firstWhere('id', $status_id)['display_label'] ?? '-'; @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $statusLabel }}</div>
                            @else
                                <select wire:model="status_id" class="select w-full">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Timezone --}}
                        <div>
                             <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Zaman Dilimi *</label>
                             @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $timezone }}</div>
                             @else
                                <select wire:model="timezone" class="select w-full">
                                    <option value="Europe/Istanbul">İstanbul (UTC+3)</option>
                                    <option value="UTC">UTC</option>
                                </select>
                                @error('timezone') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                             @endif
                        </div>

                        {{-- Project Type --}}
                        <div>
                             <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Tipi *</label>
                             @if($isViewMode)
                                @php $typeLabel = collect($projectTypes)->firstWhere('id', $type_id)['display_label'] ?? '-'; @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $typeLabel }}</div>
                             @else
                                <select wire:model="type_id" class="select w-full">
                                    <option value="">Proje Tipi Seçin</option>
                                    @foreach($projectTypes as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['display_label'] }}</option>
                                    @endforeach
                                </select>
                                @error('type_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                             @endif
                        </div>

                        {{-- Dates --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-medium opacity-60 text-skin-base">Başlangıç Tarihi *</label>
                                @if(!$isViewMode)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-400">Otomatik</span>
                                        <input type="checkbox" wire:model.live="auto_calculate_start_date" class="toggle toggle-xs toggle-success" />
                                    </div>
                                @endif
                            </div>
                            
                            @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d.m.Y') : '-' }}</div>
                            @else
                                <div class="relative">
                                    <input type="date" wire:model="start_date" class="input w-full" @if($auto_calculate_start_date) readonly @endif>
                                    @if($auto_calculate_start_date)
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400" />
                                        </div>
                                    @endif
                                </div>
                                @if($auto_calculate_start_date)
                                    <p class="text-[10px] text-slate-400 mt-1">Fazlardan otomatik hesaplanıyor</p>
                                @endif
                                @error('start_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-medium opacity-60 text-skin-base">Hedef Bitiş Tarihi *</label>
                                @if(!$isViewMode)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-400">Otomatik</span>
                                        <input type="checkbox" wire:model.live="auto_calculate_end_date" class="toggle toggle-xs toggle-success" />
                                    </div>
                                @endif
                            </div>
                            
                            @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $target_end_date ? \Carbon\Carbon::parse($target_end_date)->format('d.m.Y') : '-' }}</div>
                            @else
                                <div class="relative">
                                    <input type="date" wire:model="target_end_date" class="input w-full" @if($auto_calculate_end_date) readonly @endif>
                                    @if($auto_calculate_end_date)
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400" />
                                        </div>
                                    @endif
                                </div>
                                @if($auto_calculate_end_date)
                                    <p class="text-[10px] text-slate-400 mt-1">Fazlardan otomatik hesaplanıyor</p>
                                @endif
                                @error('target_end_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>



                        {{-- Description --}}
                        <div class="col-span-2">
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Açıklama</label>
                            @if($isViewMode)
                                <div class="text-sm text-skin-base whitespace-pre-wrap">{{ $description ?: '-' }}</div>
                            @else
                                <textarea wire:model="description" rows="3" class="textarea w-full"
                                    placeholder="Proje açıklaması..."></textarea>
                            @endif
                        </div>
                    </div>
                </div>





                {{-- Participants Card --}}
                <div class="theme-card p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-users" class="w-5 h-5" />
                        Katılımcılar
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        {{-- Leader --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Lideri <span class="text-red-500">*</span></label>
                            @if($isViewMode)
                                @php $leaderName = collect($leaders)->firstWhere('id', $leader_id)['name'] ?? '-'; @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $leaderName }}</div>
                            @else
                                <select wire:model="leader_id" class="select w-full">
                                    <option value="">Proje Lideri Seçin</option>
                                    @foreach($leaders as $leader)
                                        <option value="{{ $leader['id'] }}">{{ $leader['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('leader_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                         {{-- Team Members --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Üyeleri</label>
                            @if($isViewMode)
                                @php 
                                    $memberNames = collect($leaders)
                                        ->whereIn('id', $team_members)
                                        ->pluck('name')
                                        ->join(', ');
                                @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $memberNames ?: '-' }}</div>
                            @else
                                <x-mary-choices 
                                    wire:model="team_members" 
                                    :options="$leaders" 
                                    option-label="name" 
                                    option-value="id"
                                    searchable
                                    class="w-full"
                                    no-result-text="Sonuç bulunamadı"
                                />
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Hierarchical Form Card --}}
                <div class="theme-card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] flex items-center gap-2">
                            <x-mary-icon name="o-squares-2x2" class="w-5 h-5" />
                            Proje Hiyerarşisi
                        </h3>
                        @if(!$isViewMode)
                        <button wire:click="openPhaseModal" 
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                            <x-mary-icon name="o-plus" class="w-4 h-4" />
                            Faz Ekle
                        </button>
                        @endif
                    </div>

                    {{-- Phases List --}}
                    <div class="space-y-4">
                        @forelse($phases as $index => $phase)
                            @include('livewire.projects.parts._phase-form', [
                                'index' => $index,
                                'phase' => $phase,
                                'isViewMode' => $isViewMode
                            ])
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                                <x-mary-icon name="o-cube-transparent" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                                <p class="text-slate-500 mb-2">Henüz faz eklenmedi</p>
                                <p class="text-xs text-slate-400">"Faz Ekle" butonuna tıklayarak proje aşamalarını tanımlayın</p>
                            </div>
                        @endforelse
                    </div>
                </div>



            </div>

            {{-- Right Column (4/12) --}}
            <div class="col-span-4 flex flex-col gap-6" wire:key="customer-preview-{{ $customer_id }}">
                {{-- Customer Logo Card --}}
                <div class="theme-card p-6 shadow-sm sticky top-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Proje Özeti</h3>

                    @if($selectedCustomer)
                        <div class="flex items-center gap-4 mb-4">
                            @if($selectedCustomer["logo_url"])
                                <img src="{{ str_contains($selectedCustomer["logo_url"], "/storage/") ? $selectedCustomer["logo_url"] : asset("storage" . $selectedCustomer["logo_url"]) }}" alt="{{ $selectedCustomer["name"] }}" class="w-12 h-12 rounded-lg object-cover shadow-sm bg-white" />
                            @else
                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <x-mary-icon name="o-building-office" class="w-6 h-6 text-slate-400" />
                                </div>
                            @endif
                            <div class="font-bold text-[var(--color-text-heading)]">{{ $selectedCustomer["name"] }}
                            </div>
                        </div>

                        {{-- Deadline Logic --}}
                        {{-- Phases Deadline Logic --}}
                        @if(!empty($phases))
                            @foreach($phases as $phase)
                                @if(!empty($phase['end_date']))
                                    @php
                                        $pDeadline = \Carbon\Carbon::parse($phase['end_date'])->startOfDay();
                                        $today = \Carbon\Carbon::now()->startOfDay();
                                        $pDiff = $today->diffInDays($pDeadline, false);
                                        
                                        $pBusinessDays = $today->diffInDaysFiltered(function(\Carbon\Carbon $date) {
                                            return !$date->isWeekend();
                                        }, $pDeadline);

                                        $pColorClass = 'text-green-600';
                                        $pText = abs($pDiff) . ' Gün var (' . $pBusinessDays . ' İş Günü)';
                                        
                                        if ($pDiff < 0) {
                                            $pColorClass = 'text-red-500';
                                            $pText = abs($pDiff) . ' Gün geçti (' . $pBusinessDays . ' İş Günü)';
                                        } elseif ($pDiff <= 7) {
                                            $pColorClass = 'text-orange-500';
                                            $pText = abs($pDiff) . ' Gün var (' . $pBusinessDays . ' İş Günü)';
                                        }
                                    @endphp
                                    <div class="flex items-center justify-between text-xs py-1.5 border-t border-slate-50">
                                        <div class="flex items-center gap-2 overflow-hidden mr-2">
                                            @php
                                                $words = explode(' ', $phase['name']);
                                                $initials = mb_substr($words[0] ?? '', 0, 1);
                                                if (count($words) > 1) {
                                                    $initials .= mb_substr($words[1] ?? '', 0, 1);
                                                }
                                                $initials = mb_strtoupper($initials);
                                            @endphp
                                            <div class="w-5 h-5 rounded flex items-center justify-center font-bold text-white text-[9px] flex-shrink-0"
                                                 style="background-color: {{ $phase['color'] ?? 'var(--primary-color)' }};">
                                                {{ $initials }}
                                            </div>
                                            <span class="text-slate-500 font-medium truncate">{{ $phase['name'] }}:</span>
                                        </div>
                                        <span class="{{ $pColorClass }} whitespace-nowrap">{{ $pText }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- Project Deadline Logic --}}
                        {{-- Project Deadline Logic --}}
                        @if($target_end_date)
                            @php
                                $deadline = \Carbon\Carbon::parse($target_end_date)->startOfDay();
                                $isFrozen = !empty($completed_at);
                                $referenceDate = $isFrozen ? \Carbon\Carbon::parse($completed_at)->startOfDay() : \Carbon\Carbon::now()->startOfDay();
                                
                                $diff = $referenceDate->diffInDays($deadline, false);
                                
                                $businessDays = $referenceDate->diffInDaysFiltered(function(\Carbon\Carbon $date) {
                                    return !$date->isWeekend();
                                }, $deadline);

                                // Default: Active & Future
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
                            <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                                <span class="text-slate-700">Proje Bitiş:</span>
                                <span class="{{ $colorClass }}">{{ $text }}</span>
                            </div>
                            
                            @php
                                $totalAssignedHours = 0;
                                foreach($phases as $p) {
                                    foreach($p['modules'] ?? [] as $m) {
                                        $totalAssignedHours += (int)($m['estimated_hours'] ?? 0);
                                    }
                                }
                                
                                $spentTime = $this->spentTime;
                                $spentMinutes = $spentTime['total_minutes'];
                                $assignedMinutes = $totalAssignedHours * 60;
                                $remainingMinutes = $assignedMinutes - $spentMinutes;
                                
                                $remainingHours = floor(abs($remainingMinutes) / 60);
                                $remainingMins = abs($remainingMinutes) % 60;
                                $isNegative = $remainingMinutes < 0;
                            @endphp
                            
                            <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                                <span class="text-slate-700">Atanan Saatler:</span>
                                <span class="text-slate-900">{{ $totalAssignedHours > 0 ? $totalAssignedHours . ' Saat' : '-' }}</span>
                            </div>

                            <div class="flex items-center justify-between text-base font-bold py-2 border-t border-slate-100">
                                <span class="text-slate-700">Harcanan Saatler:</span>
                                <span class="text-red-500">{{ $spentTime['hours'] }}:{{ sprintf('%02d', $spentTime['minutes']) }} Saat</span>
                            </div>

                            <div class="flex items-center justify-between text-base font-bold py-2 border-t border-slate-100">
                                <span class="text-slate-700">Kalan Saatler:</span>
                                <span class="{{ $isNegative ? 'text-red-500' : 'text-green-600' }}">
                                    {{ $isNegative ? '-' : '' }}{{ $remainingHours }}:{{ sprintf('%02d', $remainingMins) }} Saat
                                </span>
                            </div>
                        @else
                                <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                                <span class="text-slate-700">Proje Bitiş:</span>
                                <span class="text-slate-400">-</span>
                            </div>
                        @endif                    @else
                        <div class="text-center py-8">
                            <x-mary-icon name="o-building-office-2" class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                            <p class="text-sm text-slate-500">Müşteri seçilmedi</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>


            {{-- Tab 2: Görevler --}}
            <div x-show="$wire.activeTab === 'tasks'" style="display: none;">
                @if($projectId)
                    <livewire:projects.tabs.tasks-tab :project_id="$projectId" wire:key="project-tasks-{{ $projectId }}" />
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>Lütfen önce projeyi kaydedin.</p>
                    </div>
                @endif
            </div>

            {{-- Tab 3: Raporlar --}}
            <div x-show="$wire.activeTab === 'reports'" style="display: none;">
                @if($projectId)
                    <livewire:projects.tabs.reports-tab :project_id="$projectId" wire:key="project-reports-{{ $projectId }}" />
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>Lütfen önce projeyi kaydedin.</p>
                    </div>
                @endif
            </div>

            {{-- Tab 4: Notlar --}}
            <div x-show="$wire.activeTab === 'notes'" style="display: none;">
                @livewire('projects.tabs.notes-tab', [
                    'project_id' => $projectId
                ], key('notes-tab-project-' . $projectId))
            </div>
        </div>
    </div>
    
    {{-- MODALS --}}

    {{-- Phase Modal --}}
    <x-mary-modal wire:model="phaseModalOpen" title="{{ $editingPhaseIndex !== null ? 'Faz Düzenle' : 'Yeni Faz Ekle' }}" class="backdrop-blur-sm">
        <div class="grid gap-4">
            {{-- Status Selection Removed (Auto-Calculated) --}}

            <div>
                <label class="block text-sm font-medium mb-1">Faz Adı <span class="text-red-500">*</span></label>
                <input type="text" wire:model="phaseForm.name" placeholder="Örn: Planlama Aşaması" 
                       class="input w-full bg-white border-slate-300" />
                @error('phaseForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Açıklama</label>
                <textarea wire:model="phaseForm.description" placeholder="Faz hakkında kısa açıklama..." 
                          class="textarea w-full bg-white border-slate-300" rows="3"></textarea>
            </div>
            
            <div class="bg-blue-50 p-3 rounded-lg flex items-start gap-3 text-sm text-blue-700">
                <x-mary-icon name="o-information-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold">Otomatik Hesaplama:</span>
                    <p class="mt-1 opacity-90">Fazın başlangıç/bitiş tarihleri ve durumu, altına ekleyeceğiniz modüllerden otomatik olarak hesaplanacaktır.</p>
                </div>
            </div>
        </div>
        
        <x-slot:actions>
            <button class="btn btn-ghost" wire:click="$set('phaseModalOpen', false)">İptal</button>
            <button class="theme-btn-save" wire:click="savePhase">{{ $editingPhaseIndex !== null ? 'Güncelle' : 'Ekle' }}</button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- Module Modal --}}
    <x-mary-modal wire:model="moduleModalOpen" title="{{ $editingModuleIndex !== null ? 'Modül Düzenle' : 'Yeni Modül Ekle' }}" class="backdrop-blur-sm">
        <div class="grid gap-4">
            {{-- Status --}}
            <div>
                 <label class="block text-sm font-medium mb-1">Durum <span class="text-red-500">*</span></label>
                 <select wire:model="moduleForm.status_id" class="select w-full bg-white border-slate-300">
                     <option value="">Lütfen Seçiniz</option>
                     @foreach($moduleStatuses as $status)
                         <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                     @endforeach
                 </select>
                 @error('moduleForm.status_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Info --}}
            <div>
                <label class="block text-sm font-medium mb-1">Modül Adı <span class="text-red-500">*</span></label>
                <input type="text" wire:model="moduleForm.name" placeholder="Örn: Login Ekranı Tasarımı" 
                       class="input w-full bg-white border-slate-300" />
                @error('moduleForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Açıklama</label>
                <textarea wire:model="moduleForm.description" placeholder="Modül detayları..." 
                          class="textarea w-full bg-white border-slate-300" rows="2"></textarea>
            </div>

            {{-- Date Picker --}}
            <div wire:ignore>
                <label class="block text-sm font-medium mb-1">Zaman Planı</label>
                <x-date-range-picker 
                    :startDate="$moduleForm['start_date'] ?? null" 
                    :endDate="$moduleForm['end_date'] ?? null" 
                    startWireModel="moduleForm.start_date"
                    endWireModel="moduleForm.end_date"
                    eventKey="module_modal"
                />
            </div>

            {{-- Estimated Hours --}}
            <div class="border-t border-slate-100 pt-3 mt-1">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Atanan Çalışma Saati</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500">Sınırsız</span>
                        <input type="checkbox" wire:model.live="moduleForm.is_unlimited" class="toggle toggle-sm toggle-primary" />
                    </div>
                </div>

                @if(!$moduleForm['is_unlimited'])
                    <div class="flex items-center gap-2 animate-in slide-in-from-left duration-200">
                        <div class="flex-1">
                            <input type="number" wire:model="moduleForm.estimated_hours" 
                                   placeholder="Örn: 20" 
                                   min="0" max="200"
                                   class="input w-full bg-white border-slate-300 select-sm h-10" />
                        </div>
                        <span class="text-sm font-medium text-slate-500">Saat</span>
                    </div>
                    @error('moduleForm.estimated_hours') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @endif
            </div>

            {{-- ACL: Participants --}}
            <div class="border-t border-slate-100 pt-3 mt-1">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Yetkili Kişiler</label>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                    @php
                        // Combine Leader and Team Members for the list
                        $allParticipants = collect($leaders)->whereIn('id', array_merge($team_members, [$leader_id]))->values();
                    @endphp

                    @forelse($allParticipants as $participant)
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 p-1.5 rounded-lg transition-colors">
                            <input type="checkbox" value="{{ $participant['id'] }}" wire:model="moduleForm.assigned_users" 
                                   class="checkbox checkbox-xs checkbox-primary rounded" />
                            <span class="text-sm text-slate-700">{{ $participant['name'] }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 italic">Projeye atanmış katılımcı yok.</p>
                    @endforelse
                </div>
                <p class="text-[10px] text-slate-400 mt-1">* İşaretli olmayan kullanıcılar bu modülü göremez.</p>
            </div>
        </div>
        
        <x-slot:actions>
            <button class="btn btn-ghost" wire:click="$set('moduleModalOpen', false)">İptal</button>
            <button class="theme-btn-save" wire:click="saveModule">{{ $editingModuleIndex !== null ? 'Güncelle' : 'Ekle' }}</button>
        </x-slot:actions>
    </x-mary-modal>
</div>

@script
<script>
    Alpine.data('unsavedChangesWatcher', () => ({
        isDirty: false,
        
        init() {
            // Warn on browser close / refresh
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = 'Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinize emin misiniz?';
                }
            });

            // Warn on internal Livewire navigation (if using wire:navigate)
            document.addEventListener('livewire:navigate', (event) => {
                if (this.isDirty && !confirm('Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinize emin misiniz?')) {
                    event.preventDefault();
                }
            });
        },

        markDirty() {
            this.isDirty = true;
        },

        markClean() {
            this.isDirty = false;
        }
    }));
</script>
@endscript