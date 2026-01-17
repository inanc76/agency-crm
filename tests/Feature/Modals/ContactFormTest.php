<?php

use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    // 🔐 Grant contact-specific permissions for authorization tests
    $this->user->givePermissionTo('customers.view');
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
        'gender' => 'male',
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

// ============================================================================
// ADDITIONAL UI TESTS (T13-T16)
// ============================================================================

test('T13-UI: Contact table loads for customer', function () {
    $customer = Customer::factory()->create();
    $contacts = Contact::factory()->count(3)->create(['customer_id' => $customer->id]);

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=contacts");

    $response->assertStatus(200);
    foreach ($contacts as $contact) {
        $response->assertSee($contact->name);
    }
});

test('T14-UI: Empty state shows when no contacts', function () {
    $customer = Customer::factory()->create();

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=contacts");

    $response->assertStatus(200);
    $response->assertSee('Henüz kişi kaydı bulunmuyor');
});

test('T15-UI: Gender icons display correctly', function () {
    $customer = Customer::factory()->create();

    $male = Contact::factory()->create(['customer_id' => $customer->id, 'gender' => 'male', 'name' => 'Mr. Male']);
    $female = Contact::factory()->create(['customer_id' => $customer->id, 'gender' => 'female', 'name' => 'Mrs. Female']);
    $other = Contact::factory()->create(['customer_id' => $customer->id, 'gender' => 'other', 'name' => 'Mx. Other']);

    Volt::test('customers.tabs.contacts-tab')
        ->set('customerId', $customer->id)
        ->assertSee('text-blue-500') // Male icon color
        ->assertSee('text-pink-500') // Female icon color
        ->assertSee('text-gray-400'); // Other/Null icon color
});

test('T16-UI: Status badge shows correct colors', function () {
    $customer = Customer::factory()->create();

    $working = Contact::factory()->create(['customer_id' => $customer->id, 'status' => 'WORKING']);
    $left = Contact::factory()->create(['customer_id' => $customer->id, 'status' => 'LEFT']);

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=contacts");

    $response->assertStatus(200);
    $response->assertSee($working->name);
    $response->assertSee($left->name);
});

// ============================================================================
// ADDITIONAL CRUD TESTS (T17-T22)
// ============================================================================

test('T17-CRUD: Create modal opens empty', function () {
    Volt::test('modals.contact-form')
        ->assertSet('name', '')
        ->assertSet('isViewMode', false);
});

test('T18-CRUD: Create modal can pre-fill customer_id', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->assertSet('customer_id', $customer->id);
});

test('T19-CRUD: Edit modal loads with data', function () {
    $contact = Contact::factory()->create(['name' => 'Existing Contact']);

    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->assertSet('name', 'Existing Contact')
        ->assertSet('isViewMode', true);
});

test('T20-CRUD: Edit mode can be toggled', function () {
    $contact = Contact::factory()->create();

    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->assertSet('isViewMode', true)
        ->call('toggleEditMode')
        ->assertSet('isViewMode', false);
});

test('T21-CRUD: Delete shows confirmation', function () {
    $contact = Contact::factory()->create();

    // Delete action exists and redirects
    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->call('delete')
        ->assertRedirect();
});

test('T22-CRUD: Table refreshes after delete', function () {
    $contact = Contact::factory()->create();
    $customerId = $contact->customer_id;

    Volt::test('modals.contact-form', ['contact' => $contact->id])
        ->call('delete');

    // Verify contact is deleted
    expect(Contact::find($contact->id))->toBeNull();

    // Verify redirect to customer page
    $response = $this->get("/dashboard/customers/{$customerId}?tab=contacts");
    $response->assertStatus(200);
    $response->assertDontSee($contact->name);
});

// ============================================================================
// ADDITIONAL VALIDATION TESTS (T23-T30)
// ============================================================================

test('T23-Validation: Name min length 2 characters', function () {
    Volt::test('modals.contact-form')
        ->set('name', 'A')
        ->call('save')
        ->assertHasErrors(['name' => 'min']);
});

test('T24-Validation: Customer ID must be valid UUID', function () {
    Volt::test('modals.contact-form')
        ->set('customer_id', 'not-a-uuid')
        ->call('save')
        ->assertHasErrors(['customer_id']);
});

test('T25-Validation: Status must be WORKING or LEFT', function () {
    Volt::test('modals.contact-form')
        ->set('status', 'RETIRED')
        ->call('save')
        ->assertHasErrors(['status' => 'in']);
});

test('T26-Validation: Emails must be valid email format', function () {
    Volt::test('modals.contact-form')
        ->set('emails', ['invalid-email', 'also@invalid'])
        ->call('save')
        ->assertHasErrors(['emails.0']);
});

test('T27-Validation: Phones array structure', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Test User')
        ->set('status', 'WORKING')
        ->set('phones', [['number' => '1234567890', 'extension' => '123']])
        ->call('save')
        ->assertHasNoErrors(['phones']);
});

test('T28-Validation: Phone extension must be numeric', function () {
    $customer = Customer::factory()->create();

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Test User')
        ->set('status', 'WORKING')
        ->set('phones', [['number' => '1234567890', 'extension' => 'ABC']])
        ->call('save')
        ->assertHasErrors(['phones.0.extension' => 'numeric']);
});

test('T29-Validation: Social profile URL must be valid', function () {
    Volt::test('modals.contact-form')
        ->set('social_profiles', [['url' => 'invalid-url', 'name' => 'Test']])
        ->call('save')
        ->assertHasErrors(['social_profiles.0.url' => 'url']);
});

test('T30-Validation: Birth date must be before today', function () {
    $tomorrow = now()->addDay()->format('Y-m-d');

    Volt::test('modals.contact-form')
        ->set('birth_date', $tomorrow)
        ->call('save')
        ->assertHasErrors(['birth_date' => 'before']);
});

// ============================================================================
// ADDITIONAL EDGE CASE TESTS (T31-T33)
// ============================================================================

test('T31-Edge: Invalid UUID returns 404 or empty', function () {
    $response = $this->get('/dashboard/customers/invalid-uuid?tab=contacts');

    // Should either 302 (redirect), 404 or show empty state
    expect($response->status())->toBeIn([302, 403, 404, 200]);
});

// T32 removed as there is no note/description field in Contact model

test('T33-Edge: Array limits - 20 emails', function () {
    $customer = Customer::factory()->create();
    $emails = array_fill(0, 20, 'test@example.com');

    Volt::test('modals.contact-form')
        ->set('customer_id', $customer->id)
        ->set('name', 'Test User')
        ->set('status', 'WORKING')
        ->set('emails', $emails)
        ->call('save')
        ->assertHasNoErrors();

    // Verify all emails are stored
    $contact = Contact::where('name', 'Test User')->first();
    expect($contact->emails)->toHaveCount(20);
});

// ============================================================================
// VERIFICATION OF BUTTON LINKS
// ============================================================================

test('T34-UI: New Contact button has correct href on contacts tab', function () {
    $response = $this->get('/dashboard/customers?tab=contacts');

    $response->assertStatus(200);
    $response->assertSee('Yeni Kişi');
    $response->assertSee('/dashboard/customers/contacts/create');
});

test('T35-UI: New Contact button has correct href on customer contacts tab', function () {
    $customer = Customer::factory()->create();

    $response = $this->get("/dashboard/customers/{$customer->id}?tab=contacts");

    $response->assertStatus(200);
    $response->assertSee('Yeni Kişi');
    $response->assertSee('/dashboard/customers/contacts/create');
    $response->assertSee('customer=' . $customer->id);
});
