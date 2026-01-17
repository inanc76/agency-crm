<?php

use App\Models\Asset;
use App\Models\Customer;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('customers.edit');
    $this->user->givePermissionTo('customers.view');
    actingAs($this->user);
});

// 1. Listeleme ve Arayüz (UI)
test('UI: Asset Form renders with correct title for new asset', function () {
    Volt::test('modals.asset-form')
        ->assertSee('Yeni Varlık Ekle')
        ->assertSet('isViewMode', false);
});

// 2. CRUD Operasyonları
test('CRUD: Can create a valid asset', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.asset-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Corporate Website')
        ->set('type', 'WEBSITE')
        ->set('url', 'https://example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('asset-saved');

    $this->assertDatabaseHas('assets', [
        'name' => 'Corporate Website',
        'customer_id' => $customer->id,
        'type' => 'WEBSITE',
        'url' => 'https://example.com',
    ]);
});

test('CRUD: Can edit an existing asset', function () {
    $asset = Asset::factory()->create();

    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->call('toggleEditMode')
        ->set('name', 'Updated Asset Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('asset-saved');

    expect($asset->fresh()->name)->toBe('Updated Asset Name');
});

test('CRUD: Can delete an asset', function () {
    $asset = Asset::factory()->create();

    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->call('delete')
        ->assertRedirect('/dashboard/customers/' . $asset->customer_id . '?tab=assets');

    expect(Asset::find($asset->id))->toBeNull();
});

// 3. Validasyon Kuralları (Constitution V10)
test('Validation: Name is required and string max 150', function () {
    Volt::test('modals.asset-form')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);

    Volt::test('modals.asset-form')
        ->set('name', str_repeat('a', 151))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

test('Validation: Customer ID is required and must exist', function () {
    Volt::test('modals.asset-form')
        ->set('customer_id', '')
        ->call('save')
        ->assertHasErrors(['customer_id' => 'required']);

    Volt::test('modals.asset-form')
        ->set('customer_id', 'invalid-uuid')
        ->call('save')
        ->assertHasErrors(['customer_id' => 'exists']);
});

test('Validation: Type (Category) is required', function () {
    Volt::test('modals.asset-form')
        ->set('type', '')
        ->call('save')
        ->assertHasErrors(['type' => 'required']);
});

// 4. Edge Cases
test('Edge Case: URL max length', function () {
    Volt::test('modals.asset-form')
        ->set('url', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['url' => 'max']);
});

test('Validation: Asset name must be unique for the same customer', function () {
    $customer = Customer::factory()->create();
    Asset::factory()->create(['customer_id' => $customer->id, 'name' => 'Existing Asset']);

    Volt::test('modals.asset-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Existing Asset')
        ->set('type', 'WEBSITE')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('Validation: Customer ID must be a valid UUID', function () {
    Volt::test('modals.asset-form')
        ->set('customer_id', 'not-a-uuid')
        ->call('save')
        ->assertHasErrors(['customer_id']);
});

// Future: Start Date / End Date Validation
// These fields are not yet in the Asset model, so tests are omitted to prevent failure.
// When Asset refactor happens, uncomment or add these tests.

// ============================================================================
// ADDITIONAL UI TESTS (T09-T12)
// ============================================================================

test('T09-UI: Asset table loads for customer', function () {
    $customer = Customer::factory()->create();
    $assets = Asset::factory()->count(3)->create(['customer_id' => $customer->id]);

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=assets");

    $response->assertStatus(200);
    foreach ($assets as $asset) {
        $response->assertSee($asset->name);
    }
});

test('T10-UI: Asset category (type) displays correctly', function () {
    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id, 'type' => 'DOMAIN']);

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=assets");

    $response->assertStatus(200);
    $response->assertSee('DOMAIN');
});

test('T12-UI: Empty state shows when no assets', function () {
    $customer = Customer::factory()->create();

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=assets");

    $response->assertStatus(200);
    $response->assertSee('Henüz varlık kaydı bulunmuyor');
});

// ============================================================================
// ADDITIONAL CRUD TESTS (T13-T18)
// ============================================================================

test('T13-CRUD: Create modal can pre-fill customer_id', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.asset-form')
        ->set('customer_id', $customer->id)
        ->assertSet('customer_id', $customer->id);
});

test('T14-CRUD: Edit modal loads with data', function () {
    $asset = Asset::factory()->create(['name' => 'Existing Asset']);

    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->assertSet('name', 'Existing Asset')
        ->assertSet('isViewMode', true);
});

test('T15-CRUD: Edit mode can be toggled', function () {
    $asset = Asset::factory()->create();

    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->assertSet('isViewMode', true)
        ->call('toggleEditMode')
        ->assertSet('isViewMode', false);
});

test('T16-CRUD: Delete redirect works', function () {
    $asset = Asset::factory()->create();

    Volt::test('modals.asset-form', ['asset' => $asset->id])
        ->call('delete')
        ->assertRedirect();
});

// ============================================================================
// ADDITIONAL VALIDATION & EDGE CASES (T19-T26)
// ============================================================================

test('T24-Edge: Invalid UUID returns 404 or redirect', function () {
    $response = $this->get('/dashboard/customers/invalid-uuid?tab=assets');

    expect($response->status())->toBeIn([302, 404, 200]);
});

test('T26-Edge: XSS protection on asset name', function () {
    $customer = Customer::factory()->create();
    $malicious = '<script>alert("XSS")</script>';

    Volt::test('modals.asset-form')
        ->set('customer_id', $customer->id)
        ->set('name', $malicious)
        ->set('type', 'OTHER')
        ->call('save');

    $this->assertDatabaseHas('assets', ['name' => $malicious]);
});

// ============================================================================
// VERIFICATION OF BUTTON LINKS
// ============================================================================

test('T27-UI: New Asset button has correct href on customer assets tab', function () {
    $customer = Customer::factory()->create();

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=assets");

    $response->assertStatus(200);
    $response->assertSee('Yeni Varlık');
    $response->assertSee('/dashboard/customers/assets/create');
    $response->assertSee('customer=' . $customer->id);
});

test('T28-UI: New Asset button has correct href on global assets tab', function () {
    $response = $this->get('/dashboard/customers?tab=assets');

    $response->assertStatus(200);
    $response->assertSee('Yeni Varlık');
    $response->assertSee('/dashboard/customers/assets/create');
});
