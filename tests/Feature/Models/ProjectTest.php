<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🏗️ Project Model Tests - ReferenceData Entegrasyonu
 * ═══════════════════════════════════════════════════════════════════════════
 */
describe('Project Model', function () {

    it('can create a project with UUID primary key', function () {
        $project = Project::factory()->create();

        expect($project->id)->toBeString();
        expect(strlen($project->id))->toBe(36); // UUID format
    });

    it('auto-generates project_id_code on creation', function () {
        $project = Project::factory()->create();

        expect($project->project_id_code)->toStartWith('PRJ-');
        expect($project->project_id_code)->toMatch('/^PRJ-\d{4}-\d{3}$/');
    });

    it('generates sequential project codes', function () {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $code1 = (int) substr($project1->project_id_code, -3);
        $code2 = (int) substr($project2->project_id_code, -3);

        expect($code2)->toBe($code1 + 1);
    });

    it('belongs to a customer', function () {
        $customer = Customer::factory()->create();
        $project = Project::factory()->create(['customer_id' => $customer->id]);

        expect($project->customer)->toBeInstanceOf(Customer::class);
        expect($project->customer->id)->toBe($customer->id);
    });

    it('belongs to a leader (user)', function () {
        $leader = User::factory()->create();
        $project = Project::factory()->create(['leader_id' => $leader->id]);

        expect($project->leader)->toBeInstanceOf(User::class);
        expect($project->leader->id)->toBe($leader->id);
    });

    it('has status relationship to ReferenceItem', function () {
        $project = Project::factory()->create();

        expect($project->status)->toBeInstanceOf(ReferenceItem::class);
        expect($project->status->category_key)->toBe('PROJECT_STATUS');
    });

    it('has many phases', function () {
        $project = Project::factory()->create();
        ProjectPhase::factory()->count(3)->create(['project_id' => $project->id]);

        expect($project->phases)->toHaveCount(3);
    });

    it('can attach users with roles', function () {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $project->users()->attach($user->id, ['role' => 'member']);

        expect($project->users)->toHaveCount(1);
        expect($project->users->first()->pivot->role)->toBe('member');
    });

    it('casts custom_fields as ArrayObject', function () {
        $project = Project::factory()->create([
            'custom_fields' => ['budget' => 50000, 'priority' => 'high'],
        ]);

        expect($project->custom_fields)->toBeInstanceOf(\ArrayObject::class);
        expect($project->custom_fields['budget'])->toBe(50000);
    });
});

describe('ProjectPhase Model', function () {

    it('belongs to a project', function () {
        $phase = ProjectPhase::factory()->create();

        expect($phase->project)->toBeInstanceOf(Project::class);
    });

    it('has status relationship to ReferenceItem', function () {
        $phase = ProjectPhase::factory()->create();

        expect($phase->status)->toBeInstanceOf(ReferenceItem::class);
        expect($phase->status->category_key)->toBe('PHASE_STATUS');
    });

    it('has status_key accessor', function () {
        $phase = ProjectPhase::factory()->create();

        expect($phase->status_key)->toStartWith('phase_');
    });

    it('has many modules', function () {
        $phase = ProjectPhase::factory()->create();
        ProjectModule::factory()->count(2)->create(['phase_id' => $phase->id]);

        expect($phase->modules)->toHaveCount(2);
    });
});

describe('ProjectModule Model', function () {

    it('belongs to a phase', function () {
        $module = ProjectModule::factory()->create();

        expect($module->phase)->toBeInstanceOf(ProjectPhase::class);
    });

    it('has status relationship to ReferenceItem', function () {
        $module = ProjectModule::factory()->create();

        expect($module->status)->toBeInstanceOf(ReferenceItem::class);
        expect($module->status->category_key)->toBe('MODULE_STATUS');
    });

    it('has isTerminal method', function () {
        $module = ProjectModule::factory()->completed()->create();

        expect($module->isTerminal())->toBeTrue();
    });

    it('has many tasks', function () {
        $module = ProjectModule::factory()->create();
        ProjectTask::factory()->count(5)->create(['module_id' => $module->id]);

        expect($module->tasks)->toHaveCount(5);
    });

    it('can attach users for module access', function () {
        $module = ProjectModule::factory()->create();
        $user = User::factory()->create();

        $module->users()->attach($user->id);

        expect($module->users)->toHaveCount(1);
    });
});

describe('ProjectTask Model', function () {

    it('belongs to a module', function () {
        $task = ProjectTask::factory()->create();

        expect($task->module)->toBeInstanceOf(ProjectModule::class);
    });

    it('can assign users with timestamp', function () {
        $task = ProjectTask::factory()->create();
        $user = User::factory()->create();

        $task->users()->attach($user->id);

        expect($task->users)->toHaveCount(1);
        expect($task->users->first()->pivot->assigned_at)->not->toBeNull();
    });

    it('tracks time with estimated and actual hours', function () {
        $task = ProjectTask::factory()->create([
            'estimated_hours' => 8.5,
            'actual_hours' => 10.25,
        ]);

        expect($task->estimated_hours)->toBe('8.50');
        expect($task->actual_hours)->toBe('10.25');
    });
});

describe('Full Hierarchy', function () {

    it('creates complete project hierarchy', function () {
        $project = Project::factory()->create();
        $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
        $module = ProjectModule::factory()->create(['phase_id' => $phase->id]);
        $task = ProjectTask::factory()->create(['module_id' => $module->id]);

        // Navigate down the hierarchy
        expect($project->phases->first()->modules->first()->tasks->first()->id)
            ->toBe($task->id);
    });

    it('cascades delete through hierarchy', function () {
        $project = Project::factory()->create();
        $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
        $module = ProjectModule::factory()->create(['phase_id' => $phase->id]);
        $task = ProjectTask::factory()->create(['module_id' => $module->id]);

        $project->forceDelete();

        expect(ProjectPhase::find($phase->id))->toBeNull();
        expect(ProjectModule::find($module->id))->toBeNull();
        expect(ProjectTask::find($task->id))->toBeNull();
    });
});
