<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\ReferenceItem;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create(['name' => 'Report Creator']);
    $this->actingAs($this->user);

    $this->customer = Customer::factory()->create(['name' => 'Test Customer']);

    // Project with type PROJECT_BUILD for 'PROJECT' report type compatibility
    $projectType = ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_TYPE', 'key' => 'PROJECT_BUILD'],
        ['display_label' => 'Yapım Projesi', 'is_active' => true]
    );

    $this->project = Project::factory()->create([
        'customer_id' => $this->customer->id,
        'name' => 'Construction Project',
        'type_id' => $projectType->id
    ]);
});

describe('Report Create Form', function () {

    it('can create a report with valid data', function () {
        Volt::test('projects.reports.create')
            ->set('customer_id', $this->customer->id)
            ->set('report_type', 'PROJECT')
            ->set('project_id', $this->project->id)
            ->set('reportLines', [
                ['hours' => 2, 'minutes' => 30, 'content' => 'Worked on foundation analysis', 'user_name' => 'Report Creator']
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('projects.index', ['tab' => 'reports']));

        $this->assertDatabaseHas('project_reports', [
            'content' => 'Worked on foundation analysis',
            'hours' => 2,
            'minutes' => 30,
            'report_type' => 'PROJECT'
        ]);
    });

    it('validates required fields for report', function () {
        Volt::test('projects.reports.create')
            ->set('creation_target', 'PROJECT')
            ->call('save')
            ->assertHasErrors(['customer_id', 'project_id', 'reportLines']);
    });

    it('validates at least one report line is required', function () {
        Volt::test('projects.reports.create')
            ->set('customer_id', $this->customer->id)
            ->set('report_type', 'PROJECT')
            ->set('project_id', $this->project->id)
            ->set('reportLines', [])
            ->call('save')
            ->assertHasErrors(['reportLines']);
    });

    it('calculates total time reactively', function () {
        Volt::test('projects.reports.create')
            ->set('reportLines', [
                ['hours' => 1, 'minutes' => 30, 'content' => 'Line 1'],
                ['hours' => 2, 'minutes' => 15, 'content' => 'Line 2'],
            ])
            ->assertSet('totalTime', ['hours' => 3, 'minutes' => 45]);
    });

    it('validates report date cannot be in the future', function () {
        Volt::test('projects.reports.create')
            ->set('report_date', now()->addDay()->format('Y-m-d'))
            ->set('customer_id', $this->customer->id)
            ->set('report_type', 'PROJECT')
            ->set('project_id', $this->project->id)
            ->set('reportLines', [['hours' => 1, 'minutes' => 0, 'content' => 'Valid content']])
            ->call('save')
            ->assertHasErrors(['report_date']);
    });

});
