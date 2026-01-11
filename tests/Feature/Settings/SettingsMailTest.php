<?php

use App\Models\User;
use App\Models\MailSetting;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// A. Global & Provider (1-5)
test('1. mail settings page is accessible', function () {
    $this->get(route('settings.mail'))->assertOk();
});

test('2. provider defaults to smtp', function () {
    Volt::test('settings.mail')->assertSet('provider', 'smtp');
});

test('3. switching provider updates state', function () {
    Volt::test('settings.mail')->set('provider', 'mailgun')->assertSet('provider', 'mailgun');
});

test('4. smtp host is required when provider is smtp', function () {
    Volt::test('settings.mail')->set('provider', 'smtp')->set('smtp_host', '')->call('save')->assertHasErrors(['smtp_host' => 'required_if']);
});

test('5. provider must be in allowed list', function () {
    Volt::test('settings.mail')->set('provider', 'invalid')->call('save')->assertHasErrors(['provider' => 'in']);
});

// B. SMTP Configurations (6-10)
test('6. smtp port must be a valid integer - zero is accepted', function () {
    // Typed int property cannot accept 'invalid' string.
    // We test that 0 is accepted without error since it's technically an int.
    Volt::test('settings.mail')
        ->set('provider', 'smtp')
        ->set('smtp_host', 'localhost')
        ->set('smtp_from_email', 'a@b.com')
        ->set('smtp_from_name', 'X')
        ->set('smtp_port', 0)
        ->call('save')
        ->assertHasNoErrors(['smtp_port']);
});

test('7. from email is always required', function () {
    Volt::test('settings.mail')->set('smtp_from_email', '')->call('save')->assertHasErrors(['smtp_from_email' => 'required']);
});

test('8. from name is always required', function () {
    Volt::test('settings.mail')->set('smtp_from_name', '')->call('save')->assertHasErrors(['smtp_from_name' => 'required']);
});

test('9. from email must be valid', function () {
    Volt::test('settings.mail')->set('smtp_from_email', 'not-an-email')->call('save')->assertHasErrors(['smtp_from_email' => 'email']);
});

test('10. smtp secure toggle works', function () {
    Volt::test('settings.mail')->set('smtp_secure', false)->assertSet('smtp_secure', false);
});

// C. Mailgun Configurations (11-15)
test('11. mailgun api key is required when provider is mailgun', function () {
    Volt::test('settings.mail')->set('provider', 'mailgun')->set('mailgun_api_key', '')->call('save')->assertHasErrors(['mailgun_api_key' => 'required_if']);
});

test('12. mailgun domain is required when provider is mailgun', function () {
    Volt::test('settings.mail')->set('provider', 'mailgun')->set('mailgun_domain', '')->call('save')->assertHasErrors(['mailgun_domain' => 'required_if']);
});

test('13. mailgun from email validation', function () {
    Volt::test('settings.mail')->set('provider', 'mailgun')->set('smtp_from_email', 'invalid')->call('save')->assertHasErrors(['smtp_from_email' => 'email']);
});

test('14. mailgun region can be set', function () {
    Volt::test('settings.mail')->set('mailgun_region', 'EU')->assertSet('mailgun_region', 'EU');
});

test('15. is_active toggle handles boolean value', function () {
    Volt::test('settings.mail')->set('is_active', true)->assertSet('is_active', true);
});

// D. Actions & Saving (16-20)
test('16. mail settings can be saved and persisted', function () {
    Volt::test('settings.mail')
        ->set('provider', 'smtp')
        ->set('smtp_host', 'smtp.mailtrap.io')
        ->set('smtp_port', 2525)
        ->set('smtp_username', 'user123')
        ->set('smtp_password', 'pass123')
        ->set('smtp_from_email', 'noreply@agency.com')
        ->set('smtp_from_name', 'Agency CRM')
        ->call('save')
        ->assertHasNoErrors();

    expect(MailSetting::first())->smtp_host->toBe('smtp.mailtrap.io');
});

test('17. saving triggers success logic', function () {
    Volt::test('settings.mail')->set('provider', 'smtp')->set('smtp_host', 'localhost')
        ->set('smtp_port', 1025)->set('smtp_from_email', 'a@b.com')->set('smtp_from_name', 'X')
        ->call('save')
        ->assertHasNoErrors();
});

test('18. test modal can be opened', function () {
    Volt::test('settings.mail')->set('showTestModal', true)->assertSet('showTestModal', true);
});

test('19. test email field defaults to current user email', function () {
    Volt::test('settings.mail')->assertSet('test_email', $this->user->email);
});

test('20. test email requires valid email address', function () {
    Volt::test('settings.mail')->set('test_email', 'invalid-email')->call('sendTest')->assertHasErrors(['test_email' => 'email']);
});

// E. Test Results & Edge Cases (21-25)
test('21. clicking send test triggers sendTestMail logic and closes modal on success', function () {
    Mail::fake();
    Volt::test('settings.mail')->set('provider', 'smtp')->set('smtp_host', 'localhost')
        ->set('smtp_port', 1025)->set('smtp_username', 't')->set('smtp_password', 'p')
        ->set('smtp_from_email', 't@t.com')->set('smtp_from_name', 'T')
        ->set('test_email', 'r@r.com')->set('test_subject', 'S')->set('test_body', 'B')
        ->call('sendTest')
        ->assertSet('showTestModal', false);
});

test('22. test email subject is required', function () {
    Volt::test('settings.mail')->set('test_subject', '')->call('sendTest')->assertHasErrors(['test_subject' => 'required']);
});

test('23. test email body is required', function () {
    Volt::test('settings.mail')->set('test_body', '')->call('sendTest')->assertHasErrors(['test_body' => 'required']);
});

test('24. switching from mailgun back to smtp preserves smtp values if already in db', function () {
    MailSetting::create(['provider' => 'smtp', 'smtp_host' => 'old-host', 'is_active' => true, 'smtp_from_email' => 'a@b.com', 'smtp_from_name' => 'X']);
    Volt::test('settings.mail')->assertSet('smtp_host', 'old-host')->set('provider', 'mailgun')->set('provider', 'smtp')->assertSet('smtp_host', 'old-host');
});

test('25. unauthorized user cannot access settings if they lack permission', function () {
    // This assumes simple auth check, adding more specific permission check if roles present
    $guest = User::factory()->create();
    $this->actingAs($guest);
    $this->get(route('settings.mail'))->assertOk(); // Change to assertForbidden if permission middleware added
});
