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
    use \App\Livewire\Projects\Traits\HasProjectFormState;
    use \App\Livewire\Projects\Traits\HasHierarchyManagement;









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
    // STATE MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    public function toggleEdit(): void
    {
        $this->isViewMode = !$this->isViewMode;
    }

    public function save(\App\Services\ProjectCreationService $service): void
    {
        $validated = $this->validate();

        // External user validation
        if ($this->inviteExternalUser) {
            // Check if user already exists
            $existingUser = User::where('email', $this->externalUserEmail)->first();
            if (!$existingUser) {
                $this->validate([
                    'externalUserEmail' => 'required|email|unique:users,email',
                    'externalUserName' => 'required|string|max:255'
                ]);
            }
        }

        // Prepare data
        $validated['type_id'] = $this->type_id ?: null;
        $validated['custom_fields'] = [
            'auto_calculate_end_date' => $this->auto_calculate_end_date,
            'auto_calculate_start_date' => $this->auto_calculate_start_date,
        ];

        try {
            $project = $service->store(
                $validated,
                $this->phases,
                $this->team_members,
                $this->inviteExternalUser,
                $this->externalUserEmail,
                $this->externalUserName,
                $this->projectId
            );

            if (!$this->projectId) {
                $this->projectId = $project->id;
                $this->success('Proje Oluşturuldu', 'Yeni proje başarıyla oluşturuldu.');
                $this->redirect(route('projects.edit', $this->projectId), navigate: true);
            } else {
                $this->success('Proje Güncellendi', 'Proje başarıyla güncellendi.');
                $this->isViewMode = true;
                $this->loadProjectData();
            }

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
        {{-- Back Button --}}
        <a href="{{ route('projects.index') }}"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Proje Listesi</span>
        </a>

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