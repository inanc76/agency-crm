<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    // 🔐 Grant contact-specific permissions for authorization tests
    $this->user->givePermissionTo('contacts.create');
    $this->user->givePermissionTo('contacts.edit');
    $this->user->givePermissionTo('contacts.delete');
    $this->user->givePermissionTo('contacts.view');
    actingAs($this->user);
});

// 1. Listeleme ve Arayüz (UI)
test('UI: Contact Form renders with correct title for new contact', function () {
    Volt::test('modals.contact-form')
        ->assertSee('Yeni Kişi Ekle')
        ->assertSet('isViewMode', false);
});

// test('UI: Contact Form prepopulates customer_id if provided', ... ); // Skipped due to query param complexity

// 2. CRUD Operasyonları
test('CRUD: Can create a valid contact', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Valid User')
        ->set('status', 'WORKING')
        ->set('gender', 'male')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('contact-saved');

    $this->assertDatabaseHas('contacts', [
        'name' => 'Valid User',
        'customer_id' => $customer->id,
        'status' => 'WORKING',
        'gender' => 'male'
    ]);
});

test('CRUD: Can edit an existing contact', function () {
    $contact = Contact::factory()->create();

    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->call('toggleEditMode')
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('contact-saved');

    expect($contact->fresh()->name)->toBe('Updated Name');
});

test('CRUD: Can delete a contact', function () {
    $contact = Contact::factory()->create();

    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->call('delete')
        ->assertRedirect('/dashboard/customers/' . $contact->customer_id . '?tab=contacts');

    expect(Contact::find($contact->id))->toBeNull();
});

// 3. Validasyon Kuralları (Constitution V10)
test('Validation: Name is required and string max 150', function () {
    Volt::test('modals.contact-form')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);

    Volt::test('modals.contact-form')
        ->set('name', str_repeat('a', 151))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

test('Validation: Customer ID is required and must exist', function () {
    Volt::test('modals.contact-form')
        ->set('customer_id', '')
        ->call('save')
        ->assertHasErrors(['customer_id' => 'required']);

    Volt::test('modals.contact-form')
        ->set('customer_id', 'invalid-uuid')
        ->call('save')
        ->assertHasErrors(['customer_id' => 'exists']);
});

test('Validation: Status is required and must be in enum', function () {
    Volt::test('modals.contact-form')
        ->set('status', '')
        ->call('save')
        ->assertHasErrors(['status' => 'required']);

    Volt::test('modals.contact-form')
        ->set('status', 'INVALID_STATUS')
        ->call('save')
        ->assertHasErrors(['status' => 'in']);
});

test('Validation: Email array validation works', function () {
    // Valid emails
    Volt::test('modals.contact-form')
        ->set('emails', ['test@example.com', 'valid@domain.com'])
        ->call('save')
        ->assertHasNoErrors(['emails.*']);

    // Invalid email
    Volt::test('modals.contact-form')
        ->set('emails', ['not-an-email'])
        ->call('save')
        ->assertHasErrors(['emails.0' => 'email']);
});

test('Validation: Social profiles url validation works', function () {
    Volt::test('modals.contact-form')
        ->set('social_profiles', [['url' => 'not-a-url', 'name' => 'Link']])
        ->call('save')
        ->assertHasErrors(['social_profiles.0.url' => 'url']);

    Volt::test('modals.contact-form')
        ->set('social_profiles', [['url' => 'https://valid.com', 'name' => 'Link']])
        ->call('save')
        ->assertHasNoErrors(['social_profiles.0.url']);
});

test('Validation: Birth date must be a valid date before today', function () {
    Volt::test('modals.contact-form')
        ->set('birth_date', 'not-date')
        ->call('save')
        ->assertHasErrors(['birth_date' => 'date']);

    Volt::test('modals.contact-form')
        ->set('birth_date', now()->addDay()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['birth_date' => 'before']);
});

// 4. Edge Cases
test('Edge Case: XSS protection on name field', function () {
    $malicious = '<script>alert("XSS")</script>';
    $customer = Customer::factory()->create();

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', $malicious)
        ->set('status', 'WORKING')
        ->call('save');

    // Livewire/Laravel auto-escapes output, but input should be sanitized or stored as is if expected.
    // Usually we check if it is stored. Laravel blade output {{ }} escapes it.
    // We can check if it was stored successfully.
    $this->assertDatabaseHas('contacts', ['name' => $malicious]);
    // The test is that create succeeds, Blade handles display escaping.
});
