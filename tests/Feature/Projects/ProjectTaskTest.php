<?php

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Customer;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedReferenceData();

    // Ensure Task Statuses and Priorities exist
    $catPriority = ReferenceCategory::firstOrCreate(['key' => 'TASK_PRIORITY'], ['name' => 'Task Priority', 'display_label' => 'Öncelik', 'is_active' => true]);
    $catStatus = ReferenceCategory::firstOrCreate(['key' => 'TASK_STATUS'], ['name' => 'Task Status', 'display_label' => 'Durum', 'is_active' => true]);

    ReferenceItem::firstOrCreate(
        ['category_key' => 'TASK_PRIORITY', 'key' => 'task_priority_normal'],
        ['category_id' => $catPriority->id, 'display_label' => 'Normal', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'TASK_STATUS', 'key' => 'task_todo'],
        ['category_id' => $catStatus->id, 'display_label' => 'Yapılacak', 'is_active' => true, 'sort_order' => 1]
    );

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->customer = Customer::factory()->create();
    $this->project = Project::factory()->create(['customer_id' => $this->customer->id]);
});

test('T22: Görev listesi görüntüleniyor', function () {
    $this->get('/dashboard/projects?tab=tasks')->assertOk();
    Volt::test('projects.tabs.tasks-tab')->assertOk();
});

test('T30: Yeni Görev Sayfası Erişilebilir', function () {
    $this->get('/dashboard/projects/tasks/create')->assertOk();
});

test('T31: Zorunlu alanlarla görev oluşturma', function () {
    // Get fresh items with IDs
    $priority = ReferenceItem::where('key', 'task_priority_normal')->first();
    $status = ReferenceItem::where('key', 'task_todo')->first();

    Volt::test('projects.tasks.create')
        ->set('name', 'Test Görevi')
        ->set('customer_id', $this->customer->id)
        ->set('project_id', $this->project->id)
        ->set('assigned_to', [$this->user->id])
        ->set('priority_id', $priority->id)
        ->set('status_id', $status->id)
        ->set('description', 'Açıklama')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('project_tasks', ['name' => 'Test Görevi']);
});

test('T35-T37: Validasyon Kontrolleri', function () {
    Volt::test('projects.tasks.create')
        ->call('save')
        ->assertHasErrors(['name', 'customer_id']);
});

test('T32: Müşteri-Proje İlişkisi', function () {
    $component = Volt::test('projects.tasks.create')
        ->set('customer_id', $this->customer->id);
    $component->assertSet('customer_id', $this->customer->id);
});
