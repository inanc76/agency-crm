<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
use Livewire\Volt\Volt;
use function Pest\Laravel\{actingAs};

beforeEach(function () {
    seedReferenceData();
});

test('Assets Tab: Type Filter Works', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    Asset::factory()->create(['customer_id' => $customer->id, 'name' => 'Web Site', 'type' => 'DOMAIN']);
    Asset::factory()->create(['customer_id' => $customer->id, 'name' => 'Linux Server', 'type' => 'HOSTING']);

    Volt::actingAs($user)
        ->test('customers.tabs.assets-tab', ['customerId' => $customer->id])
        ->set('typeFilter', 'DOMAIN')
        ->assertSee('Web Site')
        ->assertDontSee('Linux Server');
});
