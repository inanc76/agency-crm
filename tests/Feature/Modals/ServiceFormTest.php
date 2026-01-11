<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\Service;
use App\Models\PriceDefinition;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    if (function_exists('seedReferenceData')) {
        seedReferenceData();
    }
});

test('Service Form Component renders correctly', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->assertOk()
        ->assertSee('Yeni Hizmet Ekle');
});

test('Service Form Component can create a service', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);
    $priceDefinition = PriceDefinition::factory()->create(['category' => 'Web', 'name' => 'Hosting', 'price' => 100, 'duration' => '1 Year']);

    $user->givePermissionTo('services.create');

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services.0.category', 'Web')
        ->set('services.0.service_name', 'Hosting')
        ->set('services.0.status', 'ACTIVE')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('service-saved');

    $this->assertDatabaseHas('services', [
        'customer_id' => $customer->id,
        'asset_id' => $asset->id,
        'service_name' => 'Hosting',
        'status' => 'ACTIVE',
    ]);
});
