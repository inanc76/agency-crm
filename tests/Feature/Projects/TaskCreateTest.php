<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create(['name' => 'Test User']);
    $this->actingAs($this->user);

    // Setup reference data
    $this->customer = Customer::factory()->create(['name' => 'Test Customer']);
    $this->project = Project::factory()->create([
        'customer_id' => $this->customer->id,
        'name' => 'Test Project',
        'project_id_code' => 'PRJ-001'
    ]);

    $this->highPriority = ReferenceItem::firstOrCreate(
        ['category_key' => 'TASK_PRIORITY', 'key' => 'HIGH'],
        ['display_label' => 'Yüksek', 'is_active' => true]
    );

    $this->openStatus = ReferenceItem::firstOrCreate(
        ['category_key' => 'TASK_STATUS', 'key' => 'open'],
        ['display_label' => 'Açık', 'is_active' => true]
    );
});

describe('Task Create Form', function () {

    it('can create a task with required fields', function () {
        $assignedTo = User::factory()->create();

        Volt::test('projects.tasks.create')
            ->set('customer_id', $this->customer->id)
            ->set('project_id', $this->project->id)
            ->set('assigned_to', [$assignedTo->id])
            ->set('priority_id', $this->highPriority->id)
            ->set('status_id', $this->openStatus->id)
            ->set('name', 'New Task Topic')
            ->set('description', 'Task detailed description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('projects.index', ['tab' => 'tasks']));

        $this->assertDatabaseHas('project_tasks', [
            'name' => 'New Task Topic',
            'project_id' => $this->project->id,
            'priority' => 'high'
        ]);
    });

    it('validates required fields for task', function () {
        Volt::test('projects.tasks.create')
            ->set('status_id', '') // Reset default
            ->call('save')
            ->assertHasErrors(['customer_id', 'project_id', 'assigned_to', 'priority_id', 'status_id', 'name']);
    });

    it('loads projects reactively when customer is selected', function () {
        $otherCustomer = Customer::factory()->create();
        $otherProject = Project::factory()->create(['customer_id' => $otherCustomer->id, 'name' => 'Other Project']);

        Volt::test('projects.tasks.create')
            ->set('customer_id', $otherCustomer->id)
            ->assertCount('projects', 1)
            ->assertSee($otherProject->name);
    });

    it('can edit an existing task', function () {
        $task = ProjectTask::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'Old Topic',
            'status_id' => $this->openStatus->id
        ]);
        $task->users()->attach($this->user->id);

        Volt::test('projects.tasks.create', ['task' => $task])
            ->set('priority_id', $this->highPriority->id)
            ->set('name', 'Updated Topic')
            ->call('save')
            ->assertHasNoErrors();

        expect($task->fresh()->name)->toBe('Updated Topic');
    });

});
