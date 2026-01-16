<?php

namespace App\Livewire\Projects\Traits;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait HasProjectPersistence
{
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

        // External user validation
        if ($this->inviteExternalUser) {
            $existingUser = User::where('email', $this->externalUserEmail)->first();
            if (!$existingUser) {
                $rules['externalUserEmail'] = 'required|email|unique:users,email';
                $rules['externalUserName'] = 'required|string|max:255';
            }
        }

        $validated = $this->validate($rules);
        $validated['type_id'] = $this->type_id ?: null;

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
                            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                            'role_id' => $guestRole->id,
                            'custom_fields' => ['is_external' => true],
                        ]);
                    }

                    if (!$project->users()->where('users.id', $externalUser->id)->exists()) {
                        $project->users()->attach($externalUser->id, ['role' => 'external']);
                    }
                }

                // Sync Team Members (Overrides External logic if conflict, but needed for Participants Card)
                $project->users()->sync($this->team_members);

                // 2. Hierarchical Form Sync (Phases & Modules)
                // Access phases from Hierarchy Trait
                $existingPhaseIds = $project->phases()->pluck('id')->toArray();
                $submittedPhaseIds = collect($this->phases)->pluck('id')->filter()->toArray();

                // Delete removed phases
                $phasesToDelete = array_diff($existingPhaseIds, $submittedPhaseIds);
                if (!empty($phasesToDelete)) {
                    $project->phases()->whereIn('id', $phasesToDelete)->delete();
                }

                foreach ($this->phases as $phaseIndex => $phaseData) {
                    if (empty($phaseData['name']))
                        continue;

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

                    $modulesToDelete = array_diff($existingModuleIds, $submittedModuleIds);
                    if (!empty($modulesToDelete)) {
                        $phase->modules()->whereIn('id', $modulesToDelete)->delete();
                    }

                    foreach ($phaseData['modules'] ?? [] as $moduleIndex => $moduleData) {
                        if (empty($moduleData['name']))
                            continue;

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
            $this->loadProjectData();
        } catch (\Exception $e) {
            $this->error('Hata', 'Proje kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        if (!$this->projectId)
            return;

        try {
            $project = Project::findOrFail($this->projectId);
            $project->delete();

            $this->success('Proje Silindi', 'Proje başarıyla silindi.');
            $this->redirect('/dashboard/projects', navigate: true);
        } catch (\Exception $e) {
            $this->error('Hata', 'Proje silinirken bir hata oluştu.');
        }
    }
}
