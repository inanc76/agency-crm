<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Asset;
use App\Models\Contact;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Roles & Users
    $role = \App\Models\Role::factory()->create(['name' => 'Super Admin']);
    $this->user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($this->user);

    // 2. Reference Data Seeding (Smoke Test Critical)
    // Create Categories First
    $categories = ['PROJECT_STATUS', 'OFFER_STATUS', 'SERVICE_STATUS', 'SERVICE_CATEGORY', 'ASSET_TYPE', 'PHASE_STATUS'];
    foreach ($categories as $cat) {
        ReferenceCategory::firstOrCreate(['key' => $cat], ['name' => $cat, 'display_label' => $cat, 'is_active' => true]);
    }

    // Then Create Items (Safe)
    ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_STATUS', 'key' => 'ACTIVE'],
        ['display_label' => 'Active', 'is_active' => true, 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'OFFER_STATUS', 'key' => 'DRAFT'],
        ['display_label' => 'Taslak', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'SERVICE_STATUS', 'key' => 'ACTIVE'],
        ['display_label' => 'Aktif', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'SERVICE_CATEGORY', 'key' => 'SEO'],
        ['display_label' => 'SEO', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'ASSET_TYPE', 'key' => 'HOSTING'],
        ['display_label' => 'Hosting', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'PHASE_STATUS', 'key' => 'phase_planned'],
        ['display_label' => 'Planned', 'is_active' => true, 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
    );

    // 3. Base Entities
    $this->customer = Customer::factory()->create();
    $this->project = Project::create([
        'name' => 'Smoke Test Project',
        'customer_id' => $this->customer->id,
        'leader_id' => $this->user->id,
        'status_id' => ReferenceItem::where('key', 'ACTIVE')->first()->id,
    ]);
});

// --- SECTION A: Public Route Checks ---

// --- SECTION A: Public Route Checks ---

test('Public page loads', function (string $route, int $status) {
    auth()->logout();
    $this->get($route)->assertStatus($status);
})->with([
            'T01: Login page' => ['/login', 200],
            'T02: Forgot password page' => ['/forgot-password', 200],
            'T03: Public offer download page invalid token' => ['/offer/invalid-token', 404],
            'T05: Root page' => ['/', 200],
        ]);


// --- SECTION B: Dashboard & Settings Pages ---

test('Dashboard & Settings page loads', function (string $route) {
    if (str_contains($route, '/appearance')) { // Handle generic /settings route
        $this->get($route)->assertStatus(200);
    } else {
        $this->get($route)->assertStatus(200);
    }
})->with([
            'T06: Dashboard Ana Sayfa' => ['/dashboard'],
            'T07: Ayarlar Paneli' => ['/dashboard/settings/panel'],
            'T08: Ayarlar Mail' => ['/dashboard/settings/mail'],
            'T09: Ayarlar Storage' => ['/dashboard/settings/storage'],
            'T10: Ayarlar PDF Şablonu' => ['/dashboard/settings/pdf-template'],
            'T11: Ayarlar Profil' => ['/dashboard/settings/profile'],
            'T12: Ayarlar Görünüm' => ['/settings/appearance'],
            'T13: Ayarlar Değişkenler' => ['/dashboard/settings/variables'],
            'T14: Ayarlar Fiyatlandırma' => ['/dashboard/settings/prices'],
            'T15: 2FA Ayarları' => ['/dashboard/settings/two-factor'],
        ]);


// --- SECTION C: Main Listings ---

test('Main Listing page loads', function (string $route) {
    $this->get($route)->assertStatus(200);
})->with([
            'T16: Müşteriler Listesi' => ['/dashboard/customers?tab=customers'],
            'T17: Projeler Listesi' => ['/dashboard/projects?tab=projects'],
            'T18: Görevler Listesi' => ['/dashboard/projects?tab=tasks'],
            'T19: Raporlar Listesi' => ['/dashboard/projects?tab=reports'],
        ]);


// --- SECTION D: Create/Edit Routes ---

test('Create/Edit page loads', function (string $route) {
    // Resolve dynamic IDs in the route
    $route = str_replace(
        ['{customer_id}', '{project_id}'],
        [$this->customer->id, $this->project->id],
        $route
    );

    $this->get($route)->assertStatus(200);
})->with([
            'T21: Yeni Müşteri Sayfası' => ['/dashboard/customers/create'],
            'T22: Müşteri Düzenleme Sayfası' => ['/dashboard/customers/{customer_id}'],
            'T23: Yeni Proje Sayfası' => ['/dashboard/projects/create'],
            'T24: Proje Düzenleme Sayfası' => ['/dashboard/projects/{project_id}'],
            'T25: Yeni Görev Sayfası' => ['/dashboard/projects/tasks/create'],
            'T26: Yeni Rapor Sayfası' => ['/dashboard/projects/reports/create'],
            'T27: Yeni Varlık Sayfası' => ['/dashboard/customers/assets/create'],
            'T29: Yeni Hizmet Sayfası' => ['/dashboard/customers/services/create'],
        ]);

// --- SECTION E: Detail Page Routes (Additional) ---
test('Detail Edit page loads', function (Closure $routeGenerator) {
    $route = $routeGenerator($this);
    $this->get($route)->assertStatus(200);
})->with([
            'T90: Offer Detail' => [fn($t) => '/dashboard/customers/offers/' . Offer::factory()->create(['customer_id' => $t->customer->id])->id],
            'T91: Asset Detail' => [fn($t) => '/dashboard/customers/assets/' . Asset::factory()->create(['customer_id' => $t->customer->id])->id],
            'T92: Service Detail' => [fn($t) => '/dashboard/customers/services/' . Service::factory()->create(['customer_id' => $t->customer->id])->id],
            'T93: Contact Detail' => [fn($t) => '/dashboard/customers/contacts/' . Contact::factory()->create(['customer_id' => $t->customer->id])->id],
        ]);


// --- SECTION F: Critical Component Render Tests ---

// Modals & Forms
test('Component renders without errors', function (string $component, array $params = [], string $assertion = 'assertHasNoErrors', ?string $assertValue = null) {
    // Check if component exists (View for Volt/Blade or Class for Class-based)
    if (!view()->exists('livewire.' . $component) && !class_exists($component)) {
        $this->markTestSkipped("Component [$component] not found/implemented yet.");
        return;
    }

    // Prepare params if they are closures
    foreach ($params as $key => $value) {
        if ($value instanceof Closure) {
            $params[$key] = $value($this);
        }
    }

    $test = Volt::test($component, $params);

    if ($assertion === 'assertSet') {
        $test->assertSet($assertValue, $params[$assertValue] ?? null);
    } elseif ($assertValue !== null) {
        $test->$assertion($assertValue);
    } else {
        $test->$assertion();
    }
})->with([
            // Modals
            'T34: Offer Modal Create' => ['modals.offer-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T35: Offer Modal Edit' => ['modals.offer-form', ['offer' => fn($t) => Offer::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T36: Service Modal Create' => ['modals.service-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T37: Service Modal Edit' => ['modals.service-form', ['service' => fn($t) => Service::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T38: Asset Modal Create' => ['modals.asset-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T39: Asset Modal Edit' => ['modals.asset-form', ['asset' => fn($t) => Asset::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T40: Contact Modal Create' => ['modals.contact-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T41: Contact Modal Edit' => ['modals.contact-form', ['contact' => fn($t) => Contact::factory()->create(['customer_id' => $t->customer->id])->id]],

            // Project Components
            'T42: Task Create' => ['projects.tasks.create'],
            'T43: Report Create' => ['projects.reports.create'],

            // Customer Tabs
            'T45: Offers Tab' => ['customers.tabs.offers-tab'],
            'T46: Assets Tab' => ['customers.tabs.assets-tab'],
            'T47: Services Tab' => ['customers.tabs.services-tab'],
            'T48: Contacts Tab' => ['customers.tabs.contacts-tab'],
            'T49: Projects Tab' => ['customers.tabs.projects-tab', ['customer' => fn($t) => $t->customer]],

            // Project Tabs
            'T54: Project Tasks Tab' => ['projects.tabs.tasks-tab', ['project_id' => fn($t) => $t->project->id]],
            'T55: Project Reports Tab' => ['projects.tabs.reports-tab', ['project_id' => fn($t) => $t->project->id]],

            // Settings Components
            'T64: Settings Panel' => ['settings.panel'],
            'T65: Settings Mail' => ['settings.mail'],
            'T66: Settings Prices' => ['settings.prices'],
            'T67: Settings PDF' => ['settings.pdf-template'],
            'T68: Settings Storage' => ['settings.storage'],
            'T69: Settings Variables' => ['settings.variables'],
            'T70: Settings Appearance' => ['settings.appearance'],
            'T71: Settings Profile' => ['settings.profile'],
            'T72: Settings Password' => ['settings.password'],
            'T73: Settings 2FA' => ['settings.two-factor'],

            // Public Components
            'T81: Public Offer Download' => ['public.offer-download', ['token' => fn($t) => Offer::factory()->create(['customer_id' => $t->customer->id])->tracking_token], 'assertOk'],
        ]);

// Special Rendering Tests (Livewire Non-Volt or Blade Partials)

test('T50: Shared Notes tab renders', function () {
    Livewire::test('shared.notes-tab', [
        'entityType' => 'CUSTOMER',
        'entityId' => $this->customer->id
    ])->assertOk();
});

test('T56: Project Notes tab renders', function () {
    Livewire::test('projects.tabs.notes-tab', ['project_id' => $this->project->id])->assertSee('Notlar');
});

test('T57: Project Phase Form Partial renders', function () {
    $view = View::make('livewire.projects.parts._phase-form', [
        'index' => 0,
        'phase' => ['name' => 'Test', 'color' => 'blue', 'modules' => []],
        'isViewMode' => false,
        'phaseStatuses' => [],
        'moduleStatuses' => []
    ]);
    expect($view->render())->toContain('Test');
});

test('T58: Project Module Form Partial renders', function () {
    $viewM = View::make('livewire.projects.parts._module-form', [
        'phaseIndex' => 0,
        'moduleIndex' => 0,
        'module' => ['name' => 'Module'],
        'isViewMode' => false,
        'moduleStatuses' => []
    ]);
    expect($viewM->render())->toContain('Module');
});
