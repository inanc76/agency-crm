<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Assuming seedReferenceData is a helper available (it was in ContactsTabTest)
    // If not, I'll mock or skip if it fails.
    if (function_exists('seedReferenceData')) {
        seedReferenceData();
    }
});

test('Contact Form Component renders correctly', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    Volt::actingAs($user)
        ->test('modals.contact-form')
        ->assertOk()
        ->assertSee('Yeni Kişi Ekle');
});

test('Contact Form Component can create a contact', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    Volt::actingAs($user)
        ->test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Test Contact')
        ->set('status', 'WORKING')
        ->set('emails', ['test@example.com'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('contact-saved');

    $this->assertDatabaseHas('contacts', [
        'name' => 'Test Contact',
        'customer_id' => $customer->id,
        'status' => 'WORKING',
    ]);
});

test('Contact Form Component loads existing contact', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $contact = Contact::factory()->create([
        'customer_id' => $customer->id,
        'name' => 'Existing Contact',
        'status' => 'WORKING'
    ]);

    Volt::actingAs($user)
        ->test('modals.contact-form', ['contact' => $contact->id])
        ->assertSet('name', 'Existing Contact')
        ->assertSet('contactId', $contact->id)
        ->assertSee('Existing Contact'); // View mode check might rely on isViewMode logic
});
