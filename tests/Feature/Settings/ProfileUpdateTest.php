<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))->assertStatus(302);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('users.create', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('save');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Volt::test('users.create', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('save');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    // SoftDeletes kullanıldığından, deleted_at dolmuş olmalı
    $user->refresh();
    expect($user->deleted_at)->not->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});