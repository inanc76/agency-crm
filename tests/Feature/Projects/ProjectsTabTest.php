<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceItem;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Projects Tab', function () {

    it('can list projects', function () {
        $customer = Customer::factory()->create();
        Project::factory()->count(3)->create([
            'customer_id' => $customer->id,
            'status_id' => ReferenceItem::where('key', 'project_active')->first()->id
        ]);

        Volt::test('projects.tabs.projects-tab')
            ->assertCount('projects', 3);
    });

    it('can search projects by name', function () {
        $customer = Customer::factory()->create();
        Project::factory()->create(['name' => 'Project Alpha', 'customer_id' => $customer->id]);
        Project::factory()->create(['name' => 'Project Beta', 'customer_id' => $customer->id]);

        Volt::test('projects.tabs.projects-tab')
            ->set('search', 'Alpha')
            ->assertCount('projects', 1)
            ->assertSee('Project Alpha')
            ->assertDontSee('Project Beta');
    });

    it('can filter projects by status', function () {
        $customer = Customer::factory()->create();

        $activeStatus = ReferenceItem::where('category_key', 'PROJECT_STATUS')->where('key', 'project_active')->first();
        $draftStatus = ReferenceItem::firstOrCreate(
            ['category_key' => 'PROJECT_STATUS', 'key' => 'project_draft'],
            ['display_label' => 'Taslak', 'is_active' => true]
        );

        Project::factory()->create(['name' => 'Active Project', 'status_id' => $activeStatus->id, 'customer_id' => $customer->id]);
        Project::factory()->create(['name' => 'Draft Project', 'status_id' => $draftStatus->id, 'customer_id' => $customer->id]);

        Volt::test('projects.tabs.projects-tab')
            ->set('statusFilter', $activeStatus->id)
            ->assertCount('projects', 1)
            ->assertSee('Active Project')
            ->assertDontSee('Draft Project');
    });

    it('can filter projects by type', function () {
        $customer = Customer::factory()->create();

        $webType = ReferenceItem::firstOrCreate(
            ['category_key' => 'PROJECT_TYPE', 'key' => 'web_dev'],
            ['display_label' => 'Web Dev', 'is_active' => true]
        );
        $mobileType = ReferenceItem::firstOrCreate(
            ['category_key' => 'PROJECT_TYPE', 'key' => 'mobile_dev'],
            ['display_label' => 'Mobile Dev', 'is_active' => true]
        );

        Project::factory()->create(['name' => 'Web Project', 'type_id' => $webType->id, 'customer_id' => $customer->id]);
        Project::factory()->create(['name' => 'Mobile Project', 'type_id' => $mobileType->id, 'customer_id' => $customer->id]);

        Volt::test('projects.tabs.projects-tab')
            ->set('typeFilter', $webType->id)
            ->assertCount('projects', 1)
            ->assertSee('Web Project')
            ->assertDontSee('Mobile Project');
    });

    it('shows empty state when no projects found', function () {
        Volt::test('projects.tabs.projects-tab')
            ->set('search', 'NonExistentProject')
            ->assertSee('Henüz Proje Yok')
            ->assertSee('Yeni Proje Oluştur');
    });
});
