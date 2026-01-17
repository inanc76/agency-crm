<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Setup project context
    $this->customer = Customer::factory()->create();
    $this->project = Project::factory()->create(['customer_id' => $this->customer->id]);
    $this->phase = ProjectPhase::create([
        'project_id' => $this->project->id,
        'name' => 'Test Phase',
        'sort_order' => 1
    ]);
    $this->module = ProjectModule::create([
        'phase_id' => $this->phase->id,
        'project_id' => $this->project->id,
        'name' => 'Test Module',
        'sort_order' => 1
    ]);
});

describe('Tasks Tab', function () {

    it('can list tasks', function () {
        ProjectTask::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'module_id' => $this->module->id,
        ]);

        Volt::test('projects.tabs.tasks-tab')
            ->assertCount('tasks', 3);
    });

    it('can search tasks by name', function () {
        ProjectTask::factory()->create(['name' => 'Task Alpha', 'project_id' => $this->project->id, 'module_id' => $this->module->id]);
        ProjectTask::factory()->create(['name' => 'Task Beta', 'project_id' => $this->project->id, 'module_id' => $this->module->id]);

        Volt::test('projects.tabs.tasks-tab')
            ->set('search', 'Alpha')
            ->assertCount('tasks', 1)
            ->assertSee('Task Alpha')
            ->assertDontSee('Task Beta');
    });

    it('can filter tasks by priority', function () {
        // Use seeded priorities
        $highPriority = ReferenceItem::where('category_key', 'TASK_PRIORITY')->where('key', 'high')->first();
        $lowPriority = ReferenceItem::firstOrCreate(
            ['category_key' => 'TASK_PRIORITY', 'key' => 'low'],
            ['display_label' => 'Düşük', 'is_active' => true]
        );

        ProjectTask::factory()->create(['name' => 'High Task', 'priority' => 'high', 'project_id' => $this->project->id, 'module_id' => $this->module->id]);
        ProjectTask::factory()->create(['name' => 'Low Task', 'priority' => 'low', 'project_id' => $this->project->id, 'module_id' => $this->module->id]);

        Volt::test('projects.tabs.tasks-tab')
            ->set('priorityFilter', 'high')
            ->assertCount('tasks', 1)
            ->assertSee('High Task')
            ->assertDontSee('Low Task');
    });

    it('can filter tasks by status', function () {
        $statusActive = ReferenceItem::firstOrCreate(
            ['category_key' => 'TASK_STATUS', 'key' => 'active'],
            ['display_label' => 'Aktif', 'is_active' => true]
        );
        $statusDone = ReferenceItem::where('category_key', 'TASK_STATUS')->where('key', 'done')->first();

        ProjectTask::factory()->create(['name' => 'Active Task', 'status_id' => $statusActive->id, 'project_id' => $this->project->id, 'module_id' => $this->module->id]);
        ProjectTask::factory()->create(['name' => 'Done Task', 'status_id' => $statusDone->id, 'project_id' => $this->project->id, 'module_id' => $this->module->id]);

        Volt::test('projects.tabs.tasks-tab')
            ->set('statusFilter', $statusActive->id)
            ->assertCount('tasks', 1)
            ->assertSee('Active Task')
            ->assertDontSee('Done Task');
    });

    it('can bulk delete tasks', function () {
        $tasks = ProjectTask::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'module_id' => $this->module->id,
        ]);

        Volt::test('projects.tabs.tasks-tab')
            ->set('selected', [$tasks[0]->id, $tasks[1]->id])
            ->call('deleteSelected')
            ->assertHasNoErrors();

        expect(ProjectTask::count())->toBe(1);
    });
});
