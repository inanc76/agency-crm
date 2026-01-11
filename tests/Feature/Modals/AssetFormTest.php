<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
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
        'url' => 'https://example.com'
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

// Future: Start Date / End Date Validation
// These fields are not yet in the Asset model, so tests are omitted to prevent failure.
// When Asset refactor happens, uncomment or add these tests.
