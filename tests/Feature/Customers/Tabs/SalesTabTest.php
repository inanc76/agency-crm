<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Offer;
use Livewire\Volt\Volt;
use function Pest\Laravel\{actingAs};

beforeEach(function () {
    seedReferenceData();
});

test('Sales Tab: Listing & Formatting', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $offer = Offer::factory()->create(['customer_id' => $customer->id, 'number' => 'OFF-001']);

    Sale::factory()->create([
        'customer_id' => $customer->id,
        'offer_id' => $offer->id,
        'amount' => 1500.00,
        'currency' => 'USD'
    ]);

    Volt::actingAs($user)
        ->test('customers.tabs.sales-tab', ['customerId' => $customer->id])
        ->assertSee('OFF-001')
        ->assertSee('1,500.00 USD');
});
