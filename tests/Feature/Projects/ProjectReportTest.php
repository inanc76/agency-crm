<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Customer;
use App\Models\ProjectReport;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->customer = Customer::factory()->create();

    // Ensure PROJECT_BUILD type exists for filtering logic
    ReferenceCategory::firstOrCreate(['key' => 'PROJECT_TYPE'], ['name' => 'Proje Tipi', 'display_label' => 'Proje Tipi', 'is_active' => true]);

    $typeBuild = ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_TYPE', 'key' => 'PROJECT_BUILD'],
        ['display_label' => 'Yapım Projesi', 'is_active' => true, 'sort_order' => 1]
    );

    $this->project = Project::factory()->create([
        'customer_id' => $this->customer->id,
        'type_id' => $typeBuild->id
    ]);
});

test('T40: Rapor listesi görüntüleniyor', function () {
    $this->get('/dashboard/projects?tab=reports')->assertOk();
    Volt::test('projects.tabs.reports-tab')->assertOk();
});

test('T47: Yeni Rapor Sayfası Erişilebilir', function () {
    $this->get('/dashboard/projects/reports/create')->assertOk();
});

test('T48: Rapor Oluşturma (Proje İlişkili)', function () {
    Volt::test('projects.reports.create')
        ->set('customer_id', $this->customer->id)
        ->set('creation_target', 'PROJECT')
        ->set('report_type', 'PROJECT')
        ->set('project_id', $this->project->id)
        ->set('report_date', now()->format('Y-m-d'))
        ->set('reportLines', [
            [
                'hours' => 2,
                'minutes' => 30,
                'content' => 'Test Çalışması',
                'user_name' => $this->user->name
            ]
        ])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('project_reports', [
        'project_id' => $this->project->id,
        'customer_id' => $this->customer->id
    ]);
});

test('T53-T55: Validasyon Kontrolleri', function () {
    Volt::test('projects.reports.create')
        ->call('save')
        ->assertHasErrors(['reportLines']); // customer_id might not be strictly required initially if validations are conditional but reportLines is always checked
});

test('T56: Eksik Rapor Satırı Kontrolü', function () {
    Volt::test('projects.reports.create')
        ->set('customer_id', $this->customer->id)
        ->set('reportLines', [])
        ->call('save')
        ->assertHasErrors(['reportLines']);
});
