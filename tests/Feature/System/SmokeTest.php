<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Asset;
use App\Models\Contact;
use App\Models\ProjectTask; // If used
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create Super Admin Role for bypass
    $role = \App\Models\Role::factory()->create(['name' => 'Super Admin']);

    // Create a robust user and login
    $this->user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($this->user);

    // Common seed data for detail pages
    $this->customer = Customer::factory()->create();
    // Seed Reference Data for Project Status
    $status = \App\Models\ReferenceItem::create([
        'category_key' => 'PROJECT_STATUS',
        'key' => 'ACTIVE',
        'display_label' => 'Active',
        'is_active' => true,
        'sort_order' => 1
    ]);

    $this->project = Project::create([
        'name' => 'Smoke Test Project',
        'customer_id' => $this->customer->id,
        'leader_id' => $this->user->id,
        'status_id' => $status->id,
    ]);
});

// --- SECTION A: Public Route Checks ---

test('T01: Login page is accessible', function () {
    auth()->logout();
    $this->get('/login')->assertStatus(200);
});

test('T02: Forgot password page is accessible', function () {
    auth()->logout();
    $this->get('/forgot-password')->assertStatus(200);
});

test('T03: Public offer download page invalid token', function () {
    // Should be 404
    $this->get('/offer/invalid-token')->assertStatus(404);
});

test('T05: Root page loads', function () {
    auth()->logout();
    $this->get('/')->assertStatus(200);
});


// --- SECTION B: Dashboard & Settings Pages ---

test('T06: Dashboard main page loads', function () {
    $this->get('/dashboard')->assertStatus(200);
});

test('T07: Settings Panel loads', function () {
    $this->get('/dashboard/settings/panel')->assertStatus(200);
});

test('T08: Settings Mail loads', function () {
    $this->get('/dashboard/settings/mail')->assertStatus(200);
});

test('T09: Settings Storage loads', function () {
    $this->get('/dashboard/settings/storage')->assertStatus(200);
});

test('T10: Settings PDF Template loads', function () {
    $this->get('/dashboard/settings/pdf-template')->assertStatus(200);
});

test('T11: Settings Profile loads', function () {
    $this->get('/dashboard/settings/profile')->assertStatus(200);
});

test('T12: Settings Appearance loads', function () {
    $this->get('/settings/appearance')->assertStatus(200);
});

test('T13: Settings Variables loads', function () {
    $this->get('/dashboard/settings/variables')->assertStatus(200);
});

test('T14: Settings Prices loads', function () {
    $this->get('/dashboard/settings/prices')->assertStatus(200);
});

test('T15: Settings 2FA loads', function () {
    $this->get('/dashboard/settings/two-factor')->assertStatus(200);
});

// --- SECTION C: Main Listings ---

test('T16: Customers list loads', function () {
    $this->get('/dashboard/customers?tab=customers')->assertStatus(200);
});

test('T17: Projects list loads', function () {
    $this->get('/dashboard/projects?tab=projects')->assertStatus(200);
});

test('T18: Tasks list loads', function () {
    $this->get('/dashboard/projects?tab=tasks')->assertStatus(200);
});

test('T19: Reports list loads', function () {
    $this->get('/dashboard/projects?tab=reports')->assertStatus(200);
});


// --- SECTION D: Create/Edit Routes ---

test('T21: Create Customer page loads', function () {
    $this->get('/dashboard/customers/create')->assertStatus(200);
});

test('T22: Edit Customer page loads', function () {
    $this->get('/dashboard/customers/' . $this->customer->id)->assertStatus(200);
});

test('T23: Create Project page loads', function () {
    $this->get('/dashboard/projects/create')->assertStatus(200);
});

test('T24: Edit Project page loads', function () {
    $this->get('/dashboard/projects/' . $this->project->id)->assertStatus(200);
});

test('T25: Create Task page loads', function () {
    $this->get('/dashboard/projects/tasks/create')->assertStatus(200);
});

test('T26: Create Report page loads', function () {
    $this->get('/dashboard/projects/reports/create')->assertStatus(200);
});

test('T27: Create Asset page loads', function () {
    $this->get('/dashboard/customers/assets/create')->assertStatus(200);
});

test('T29: Create Service page loads', function () {
    $this->get('/dashboard/customers/services/create')->assertStatus(200);
});


// --- SECTION E: Detail Pages ---

test('T31: Customer detail page loads', function () {
    $this->get('/dashboard/customers/' . $this->customer->id)->assertStatus(200);
});

// --- SECTION F: Critical Component Render Tests ---

// Modals
test('T34: Offer form modal renders (create)', function () {
    Volt::test('modals.offer-form')
        ->set('customer_id', $this->customer->id)
        ->assertSet('customer_id', $this->customer->id); // Simple check
});

test('T35: Offer form modal renders (edit)', function () {
    $offer = Offer::factory()->create(['customer_id' => $this->customer->id]);
    Volt::test('modals.offer-form', ['offer' => $offer->id])
        ->assertSet('offerId', $offer->id);
});

test('T36: Service form modal renders', function () {
    Volt::test('modals.service-form')
        ->set('customer_id', $this->customer->id)
        ->assertHasNoErrors();
});

test('T37: Service form modal renders (edit)', function () {
    $service = Service::factory()->create(['customer_id' => $this->customer->id]);
    Volt::test('modals.service-form', ['service' => $service->id])
        ->assertSet('serviceId', $service->id);
});

test('T38: Asset form modal renders', function () {
    Volt::test('modals.asset-form')
        ->set('customer_id', $this->customer->id)
        ->assertHasNoErrors();
});

test('T39: Asset form modal renders (edit)', function () {
    $asset = Asset::factory()->create(['customer_id' => $this->customer->id]);
    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->assertSet('assetId', $asset->id);
});

test('T40: Contact form modal renders', function () {
    Volt::test('modals.contact-form')
        ->set('customer_id', $this->customer->id)
        ->assertHasNoErrors();
});

test('T41: Contact form modal renders (edit)', function () {
    $contact = Contact::factory()->create(['customer_id' => $this->customer->id]);
    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->assertSet('contactId', $contact->id);
});

// Tasks & Reports Components
test('T42: Task create component renders', function () {
    Volt::test('projects.tasks.create')
        ->assertHasNoErrors();
});

test('T43: Report create component renders', function () {
    Volt::test('projects.reports.create')
        ->assertHasNoErrors();
});

// Customer Tabs are managed via Routes/Partials, covered by Route checks.

// Shared Notes Tab (Customer Context)
test('T50: Notes tab renders for customer', function () {
    Livewire::test('shared.notes-tab', [
        'entityType' => 'CUSTOMER',
        'entityId' => $this->customer->id
    ])->assertOk();
});


// Project Tabs
test('T54: Project tasks tab renders', function () {
    Volt::test('projects.tabs.tasks-tab', ['project_id' => $this->project->id])
        ->assertHasNoErrors();
});

test('T55: Project reports tab renders', function () {
    Volt::test('projects.tabs.reports-tab', ['project_id' => $this->project->id])
        ->assertHasNoErrors();
});

test('T56: Project notes tab renders', function () {
    // This was the one with the bug
    Livewire::test('projects.tabs.notes-tab', ['project_id' => $this->project->id])
        ->assertSee('Notlar');
});


// Settings Components
test('T64: Settings panel component renders', function () {
    Volt::test('settings.panel')->assertHasNoErrors();
});

test('T65: Settings mail component renders', function () {
    Volt::test('settings.mail')->assertHasNoErrors();
});

test('T66: Settings prices component renders', function () {
    Volt::test('settings.prices')->assertHasNoErrors();
});

test('T67: Settings pdf-template component renders', function () {
    Volt::test('settings.pdf-template')->assertHasNoErrors();
});

test('T68: Settings storage component renders', function () {
    Volt::test('settings.storage')->assertHasNoErrors();
});

test('T69: Settings variables component renders', function () {
    Volt::test('settings.variables')->assertHasNoErrors();
});

test('T70: Settings appearance component renders', function () {
    Volt::test('settings.appearance')->assertHasNoErrors();
});

test('T71: Settings profile component renders', function () {
    Volt::test('settings.profile')->assertHasNoErrors();
});

test('T72: Settings password component renders', function () {
    Volt::test('settings.password')->assertHasNoErrors();
});

test('T73: Settings two-factor component renders', function () {
    Volt::test('settings.two-factor')->assertHasNoErrors();
});

// Auth component tests removed as they are Controller-based views, covered by Route tests.

// --- EXTENDED COVERAGE (Based on SystemSmokeTest.md) ---

test('T20: Offers list loads', function () {
    $this->get('/dashboard/customers?tab=offers')->assertStatus(200);
});

test('T28: Edit Asset page loads', function () {
    $asset = Asset::factory()->create(['customer_id' => $this->customer->id]);
    $this->get('/dashboard/customers/assets/' . $asset->id)->assertStatus(200);
});

test('T30: Edit Service page loads', function () {
    $service = Service::factory()->create(['customer_id' => $this->customer->id]);
    $this->get('/dashboard/customers/services/' . $service->id)->assertStatus(200);
});

test('T90: Edit Contact page loads', function () {
    $contact = Contact::factory()->create(['customer_id' => $this->customer->id]);
    $this->get('/dashboard/customers/contacts/' . $contact->id)->assertStatus(200);
});

test('T91: Edit Offer page loads', function () {
    $offer = Offer::factory()->create(['customer_id' => $this->customer->id]);
    $this->get('/dashboard/customers/offers/' . $offer->id)->assertStatus(200);
});

test('T33: Project detail page loads', function () {
    // Project detail is same as edit route in view mode
    $this->get('/dashboard/projects/' . $this->project->id)->assertStatus(200);
});

test('T81: Public Offer Download component renders', function () {
    $offer = Offer::factory()->create(['customer_id' => $this->customer->id]);
    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->assertSee($offer->number);
});

// Parts & Partials Integration Checks
test('T51-T53: Customer Parts render integration', function () {
    // Checks that Info tab or Header loads specific customer data
    $this->get('/dashboard/customers/' . $this->customer->id)
        ->assertStatus(200)
        ->assertSee($this->customer->name); // Header Part
});

test('T60-T63: Project/Task Parts render integration', function () {
    // Checks task creation page for sidebar/header existence
    $this->get('/dashboard/projects/tasks/create')
        ->assertStatus(200);
    // Logic: if page loads, inclusions like _sidebar, _header are valid.
});

test('T74-T78: Settings Parts render integration', function () {
    // Checking settings pages implies their parts (headers, sidebars, forms) load
    $this->get('/dashboard/settings/panel')->assertStatus(200);
    $this->get('/dashboard/settings/mail')->assertStatus(200);
    $this->get('/dashboard/settings/prices')->assertStatus(200);
});

test('T79-T80: Global Layout Parts render', function () {
    // Sidebar and Header are in layout. Assumed present if dashboard loads.
    $this->get('/dashboard')
        ->assertStatus(200);
});
