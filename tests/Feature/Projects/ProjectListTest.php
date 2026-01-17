<?php

use App\Models\User;
use App\Models\Project;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    seedReferenceData();

    // Ensure critical statuses exist for filtering tests
    $cat = ReferenceCategory::where('key', 'PROJECT_STATUS')->first();
    if (!$cat) {
        $cat = ReferenceCategory::create(['key' => 'PROJECT_STATUS', 'name' => 'Status', 'display_label' => 'Status', 'is_active' => true]);
    }

    ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_STATUS', 'key' => 'project_active'],
        ['category_id' => $cat->id, 'display_label' => 'Aktif', 'is_active' => true, 'sort_order' => 1]
    );

    ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_STATUS', 'key' => 'project_completed'],
        ['category_id' => $cat->id, 'display_label' => 'Tamamlandı', 'is_active' => true, 'sort_order' => 2]
    );

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('T01-T04: Navigasyon ve Sekmeler', function () {
    $this->get('/dashboard/projects?tab=projects')->assertStatus(200);
    $this->get('/dashboard/projects?tab=tasks')->assertStatus(200);
    $this->get('/dashboard/projects?tab=reports')->assertStatus(200);
});

test('T05: Proje listesi görüntüleniyor', function () {
    Project::factory()->create(['name' => 'Listelenen Proje', 'leader_id' => $this->user->id]);

    $this->get('/dashboard/projects?tab=projects')
        ->assertSee('Listelenen Proje');
});

test('T06: Proje arama fonksiyonu', function () {
    Project::factory()->create(['name' => 'Aranan Proje', 'leader_id' => $this->user->id]);
    Project::factory()->create(['name' => 'Gizli Proje', 'leader_id' => $this->user->id]);

    Volt::test('projects.tabs.projects-tab')
        ->set('search', 'Aranan')
        ->assertSee('Aranan Proje')
        ->assertDontSee('Gizli Proje');
});

test('T07: Durum Filtreleri', function () {
    $statusActive = ReferenceItem::where('key', 'project_active')->first();
    $statusCompleted = ReferenceItem::where('key', 'project_completed')->first();

    Project::factory()->create(['name' => 'Aktif Proje', 'status_id' => $statusActive->id]);
    Project::factory()->create(['name' => 'Tamamlanan Proje', 'status_id' => $statusCompleted->id]);

    Volt::test('projects.tabs.projects-tab')
        ->set('statusFilter', $statusActive->id) // Fix: Use ID
        ->assertSee('Aktif Proje')
        ->assertDontSee('Tamamlanan Proje');
});
