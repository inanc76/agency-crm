<?php

namespace Tests\Feature\Customers;

use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Message;
use Livewire\Volt\Volt;
use App\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $adminRole = Role::factory()->create(['name' => 'Admin']);

    $this->user = User::factory()->create([
        'email' => 'admin@agencycrm.com',
        'role_id' => $adminRole->id
    ]);

    // Permissions
    $perms = ['customers.view', 'customers.edit', 'messages.create', 'messages.view'];
    foreach ($perms as $p) {
        $this->user->givePermissionTo($p);
    }

    $this->actingAs($this->user);
});

test('messages list tab is accessible', function () {
    // Requires reference data for customer factory usually
    seedReferenceData();

    $customer = Customer::factory()->create();

    // This is actually the customer detail page with tab=messages
    $response = $this->get("/dashboard/customers/{$customer->id}?tab=messages");
    $response->assertStatus(200);
});

test('create message page is accessible', function () {
    $response = $this->get('/dashboard/customers/messages/create');
    $response->assertStatus(200);
});

test('can create draft messages from template', function () {
    seedReferenceData();

    // Ensure MAIL_TYPE exists
    if (!\App\Models\ReferenceCategory::where('key', 'MAIL_TYPE')->exists()) {
        \App\Models\ReferenceCategory::create(['key' => 'MAIL_TYPE', 'name' => 'Mail Tipleri']);
        \App\Models\ReferenceItem::create(['category_key' => 'MAIL_TYPE', 'key' => 'EMAIL', 'display_label' => 'E-Posta', 'is_active' => true]);
    }

    $customer = Customer::factory()->create();
    $contact = \App\Models\Contact::factory()->create(['customer_id' => $customer->id]);

    // Use Message model or MailTemplate model? MailTemplate factory doesn't exist.
    $template = \App\Models\MailTemplate::create([
        'name' => 'Test Temp',
        'subject' => 'Subject Template',
        'content' => 'Content Template',
        'is_system' => false
    ]);

    Volt::test('customers.messages.create')
        ->set('customer_id', $customer->id)
        ->set('template_id', $template->id)
        ->set('selected_contacts', [$contact->id]) // Must be array
        ->call('createDraft')
        ->assertRedirect('/dashboard/customers?tab=messages');

    $this->assertDatabaseHas('messages', [
        'customer_id' => $customer->id,
        'contact_id' => $contact->id,
        'mail_template_id' => $template->id,
        'subject' => 'Subject Template', // Should be rendered from template
        'status' => 'DRAFT'
    ]);
});

test('can view message detail', function () {
    seedReferenceData();
    $customer = Customer::factory()->create();
    $message = Message::factory()->create([
        'customer_id' => $customer->id,
        'subject' => 'Detail Test',
        'body' => 'Detail Content'
    ]);

    $response = $this->get('/dashboard/customers/messages/' . $message->id);
    $response->assertStatus(200);
    $response->assertSee('Detail Test');
});

test('validation works for create message', function () {
    Volt::test('customers.messages.create')
        ->call('createDraft')
        ->assertHasErrors(['customer_id', 'template_id', 'selected_contacts']);
});
