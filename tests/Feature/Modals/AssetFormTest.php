<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    if (function_exists('seedReferenceData')) {
        seedReferenceData();
    }
});

test('Asset Form Component renders correctly', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('modals.asset-form')
        ->assertOk()
        ->assertSee('Yeni Varlık Ekle');
});

test('Asset Form Component can create an asset', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    Volt::actingAs($user)
        ->test('modals.asset-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'My Website')
        ->set('type', 'WEBSITE')
        ->set('url', 'https://example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('asset-saved');

    $this->assertDatabaseHas('assets', [
        'name' => 'My Website',
        'customer_id' => $customer->id,
        'type' => 'WEBSITE',
        'url' => 'https://example.com',
    ]);
});

test('Asset Form Component loads existing asset', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create([
        'customer_id' => $customer->id,
        'name' => 'Existing Asset',
        'type' => 'SERVER'
    ]);

    Volt::actingAs($user)
        ->test('modals.asset-form', ['asset' => $asset->id])
        ->assertSet('name', 'Existing Asset')
        ->assertSet('assetId', $asset->id)
        ->assertSee('Existing Asset');
});
