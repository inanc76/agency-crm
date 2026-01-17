<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->customer = Customer::factory()->create();
    $this->project = Project::factory()->create(['customer_id' => $this->customer->id]);
});

describe('Reports Tab', function () {

    it('can list reports', function () {
        ProjectReport::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'project_id' => $this->project->id,
            'created_by' => $this->user->id,
        ]);

        Volt::test('projects.tabs.reports-tab')
            ->assertCount('reports', 3);
    });

    it('can search reports by content', function () {
        ProjectReport::factory()->create(['content' => 'Daily analysis report', 'customer_id' => $this->customer->id, 'project_id' => $this->project->id, 'created_by' => $this->user->id]);
        ProjectReport::factory()->create(['content' => 'Weekly sync report', 'customer_id' => $this->customer->id, 'project_id' => $this->project->id, 'created_by' => $this->user->id]);

        Volt::test('projects.tabs.reports-tab')
            ->set('search', 'analysis')
            ->assertCount('reports', 1)
            ->assertSee('Daily analysis report')
            ->assertDontSee('Weekly sync report');
    });

    it('can bulk delete reports', function () {
        $reports = ProjectReport::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'project_id' => $this->project->id,
            'created_by' => $this->user->id,
        ]);

        Volt::test('projects.tabs.reports-tab')
            ->set('selected', [$reports[0]->id, $reports[1]->id])
            ->call('deleteSelected')
            ->assertHasNoErrors();

        expect(ProjectReport::count())->toBe(1);
    });
});
