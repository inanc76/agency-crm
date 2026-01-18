<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Models\Role;
use App\Models\MailTemplate;
use Livewire\Volt\Volt;
use App\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Setup Admin User with Permissions
    $adminRole = Role::factory()->create(['name' => 'Admin']);

    $this->user = User::factory()->create([
        'email' => 'admin@agencycrm.com',
        'role_id' => $adminRole->id
    ]);

    // Permissions needed
    $this->user->givePermissionTo('settings.view');
    $this->user->givePermissionTo('settings.edit');

    $this->actingAs($this->user);
});

test('mail templates index page is accessible', function () {
    $response = $this->get('/dashboard/settings/mail-templates');
    $response->assertStatus(200);
});

test('can list mail templates', function () {
    MailTemplate::create([
        'system_key' => 'custom_email',
        'name' => 'Özel Mail',
        'subject' => 'Özel Konu',
        'content' => 'İçerik',
        'is_system' => false
    ]);

    Volt::test('settings.mail-templates.index')
        ->assertSee('Özel Mail')
        ->assertSee('custom_email');
});

test('edit page is accessible', function () {
    $template = MailTemplate::create([
        'system_key' => 'test_template',
        'name' => 'Test Şablon',
        'subject' => 'Konu',
        'content' => 'İçerik',
        'is_system' => false,
    ]);

    $response = $this->get('/dashboard/settings/mail-templates/' . $template->id);
    $response->assertStatus(200);
});

test('can update a mail template', function () {
    $template = MailTemplate::create([
        'system_key' => 'update_test',
        'name' => 'Eski İsim',
        'subject' => 'Eski Konu',
        'content' => 'Eski İçerik',
        'is_system' => false,
    ]);

    Volt::test('settings.mail-templates.edit', ['template' => $template])
        ->set('subject', 'Yeni Konu')
        ->set('content', 'Yeni İçerik')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('mail_templates', [
        'id' => $template->id,
        'subject' => 'Yeni Konu',
        'content' => 'Yeni İçerik'
    ]);
});

test('subject and content are required', function () {
    $template = MailTemplate::create([
        'system_key' => 'validation_test',
        'name' => 'Validation Test',
        'subject' => 'Original',
        'content' => 'Original',
        'is_system' => false,
    ]);

    Volt::test('settings.mail-templates.edit', ['template' => $template])
        ->set('subject', '')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['subject', 'content']);
});

test('cannot update system key', function () {
    // Testing logic indirectly by trying to update and asserting system_key remains same

    $template = MailTemplate::create([
        'system_key' => 'protected_key',
        'name' => 'Protected',
        'subject' => 'S',
        'content' => 'C',
        'is_system' => false,
    ]);

    Volt::test('settings.mail-templates.edit', ['template' => $template])
        ->set('subject', 'Changed')
        ->call('save');

    $this->assertDatabaseHas('mail_templates', [
        'id' => $template->id,
        'system_key' => 'protected_key', // Should not change
        'subject' => 'Changed'
    ]);
});

test('variables are preserved', function () {
    // Check if system variables in sidebar are visible
    Volt::test('settings.mail-templates.edit')
        ->assertSee('{{customer.name}}')
        ->assertSee('{{offer.number}}');
});
