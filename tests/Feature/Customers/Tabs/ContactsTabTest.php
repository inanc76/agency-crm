<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\{actingAs};

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🧪 CONTACTS TAB TEST (Micro-Module)
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * Coverage: 15 Scenarios (Defined in tests/TestCases/CustomerTabs.md)
 * Focus: Isolation, Authorization, Data Filtering
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */

beforeEach(function () {
    seedReferenceData();
});

test('T01: Isolation Check - Component bağımsız çalışabilmeli', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    // Component lazy load ile mount edilebilmeli
    Volt::actingAs($user)
        ->test('customers.tabs.contacts-tab', ['customerId' => $customer->id])
        ->assertSet('customerId', (string) $customer->id)
        ->assertOk();
});

test('T02: Data Filtering - Sadece ilgili müşterinin kişileri gelmeli', function () {
    $user = User::factory()->create();
    $customer1 = Customer::factory()->hasContacts(3)->create();
    $customer2 = Customer::factory()->hasContacts(2)->create(); // Noise data

    Volt::actingAs($user)
        ->test('customers.tabs.contacts-tab', ['customerId' => $customer1->id])
        ->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 3;
        }) // Sadece Customer1'in 3 kişisi
        ->assertSee($customer1->contacts->first()->name)
        ->assertDontSee($customer2->contacts->first()->name);
});

test('T04: State Retention - Arama filtresi çalışmalı', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    Contact::factory()->create(['customer_id' => $customer->id, 'name' => 'Volkan Inanc']);
    Contact::factory()->create(['customer_id' => $customer->id, 'name' => 'John Doe']);

    Volt::actingAs($user)
        ->test('customers.tabs.contacts-tab', ['customerId' => $customer->id])
        ->set('search', 'Volkan')
        ->assertSee('Volkan Inanc')
        ->assertDontSee('John Doe');
});

test('T05: Parent-Child Communication - Silme işlemi parent state update istiyor', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $contact = Contact::factory()->create(['customer_id' => $customer->id]);

    Volt::actingAs($user)
        ->test('customers.tabs.contacts-tab', ['customerId' => $customer->id])
        ->set('selected', [$contact->id])
        ->call('deleteSelected')
        ->assertDispatched('contacts-updated'); // Event fırlatılmalı

    expect(Contact::find($contact->id))->toBeNull();
});


