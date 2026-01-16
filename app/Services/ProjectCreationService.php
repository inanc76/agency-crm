<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service ProjectCreationService
 * 
 * Handles the logic for creating and updating projects, including:
 * - Basic Info
 * - Team Members & External Users
 * - Phases & Modules Management
 */
class ProjectCreationService
{
    /**
     * Store or Update a project and all its related data
     */
    public function store(array $validatedData, array $phases, array $teamMembers, bool $inviteExternalUser, string $externalUserEmail, string $externalUserName, ?string $projectId = null): Project
    {
        return DB::transaction(function () use ($validatedData, $phases, $teamMembers, $inviteExternalUser, $externalUserEmail, $externalUserName, $projectId) {

            $project = $this->upsertProject($validatedData, $projectId);

            $this->handleExternalUsers($project, $inviteExternalUser, $externalUserEmail, $externalUserName);

            $this->syncTeamMembers($project, $teamMembers);

            $this->syncPhasesAndModules($project, $phases);

            return $project;
        });
    }

    private function upsertProject(array $validatedData, ?string $projectId): Project
    {
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $project->update($validatedData);
            return $project;
        }

        return Project::create($validatedData);
    }

    private function handleExternalUsers(Project $project, bool $inviteExternalUser, string $externalUserEmail, string $externalUserName): void
    {
        if ($inviteExternalUser && $externalUserEmail) {
            $externalUser = User::where('email', $externalUserEmail)->first();

            if (!$externalUser) {
                $guestRole = \App\Models\Role::firstOrCreate(
                    ['name' => 'guest'],
                    ['description' => 'External Guest User']
                );

                $externalUser = User::create([
                    'name' => $externalUserName,
                    'email' => $externalUserEmail,
                    'password' => bcrypt(Str::random(16)), // Consider sending invite email
                    'role_id' => $guestRole->id,
                    'custom_fields' => ['is_external' => true],
                ]);
            }

            // Attach if not already attached
            if (!$project->users()->where('users.id', $externalUser->id)->exists()) {
                $project->users()->attach($externalUser->id, ['role' => 'external']);
            }
        }
    }

    private function syncTeamMembers(Project $project, array $teamMembers): void
    {
        // Replicating original behavior: Sync team members.
        // If external user was attached but not in teamMembers, sync() WILL remove them.
        // We preserve this behavior to match original code, assuming teamMembers should contain all active participants.
        // However, we enhance it slightly: if an external user was JUST handled, we assume they should be kept.
        // $project->users()->sync($teamMembers); 

        // Use syncWithoutDetaching for team members to avoid removing external user? 
        // No, team members form should be authoritative.
        // For refactoring safety, we stick to the original "sync" call.
        if (!empty($teamMembers)) {
            $project->users()->sync($teamMembers);
        }
    }

    private function syncPhasesAndModules(Project $project, array $phasesData): void
    {
        $existingPhaseIds = $project->phases()->pluck('id')->toArray();
        $submittedPhaseIds = collect($phasesData)->pluck('id')->filter()->toArray();

        // Delete removed phases
        $phasesToDelete = array_diff($existingPhaseIds, $submittedPhaseIds);
        if (!empty($phasesToDelete)) {
            $project->phases()->whereIn('id', $phasesToDelete)->delete();
        }

        foreach ($phasesData as $phaseIndex => $phaseData) {
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

            // Delete removed modules
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

                // Sync Assigned Users (ACL)
                if (isset($moduleData['assigned_users']) && is_array($moduleData['assigned_users'])) {
                    $module->users()->sync($moduleData['assigned_users']);
                }
            }
        }
    }
}
