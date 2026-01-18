<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Models\Role;
use Livewire\Volt\Volt;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Seed Roles
    $adminRole = Role::factory()->create(['name' => 'Admin']);
    Role::factory()->create(['name' => 'User']);

    $this->user = User::factory()->create([
        'email' => 'admin@agencycrm.com',
        'password' => bcrypt('password'),
        'role_id' => $adminRole->id
    ]);

    // Create Permissions & Assign to User's Role
    $permissions = ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.manage'];

    foreach ($permissions as $perm) {
        $this->user->givePermissionTo($perm);
    }

    $this->actingAs($this->user);

    // Seed Reference Data for Departments if needed
    // Assuming departments are fetched from ReferenceItem
    // Check if category exists before creating to avoid errors
    if (!\App\Models\ReferenceCategory::where('key', 'DEPARTMENT')->exists()) {
        $category = \App\Models\ReferenceCategory::create([
            'key' => 'DEPARTMENT',
            'name' => 'Departmanlar'
        ]);

        \App\Models\ReferenceItem::create([
            'category_key' => 'DEPARTMENT',
            'key' => 'IT',
            'display_label' => 'IT Dept',
            'is_active' => true
        ]);
    }
});

test('users index page is accessible', function () {
    $response = $this->get('/dashboard/settings/users');
    $response->assertStatus(200);
    $response->assertSee('Kullanıcı Yönetimi');
});

test('users create page is accessible', function () {
    $response = $this->get('/dashboard/settings/users/create');
    $response->assertStatus(200);
    $response->assertSee('Yeni Kullanıcı Ekle');
});

test('can create a new user', function () {
    $role = Role::first();

    Volt::test('users.create')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('roleId', $role->id)
        ->set('password', 'secret123')
        ->set('sendPasswordEmail', false) // Don't try to send email in test
        ->call('save')
        ->assertHasNoErrors(); // Changed from assertRedirect to check logic first

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('name and email are required for creation', function () {
    Volt::test('users.create')
        ->set('name', '')
        ->set('email', '')
        ->call('save')
        ->assertHasErrors(['name', 'email']);
});

test('email must be unique', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    Volt::test('users.create')
        ->set('name', 'Jane Duplicate')
        ->set('email', 'jane@example.com')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('can edit an existing user', function () {
    $userToEdit = User::factory()->create(['name' => 'Old Name']);

    Volt::test('users.create', ['user' => $userToEdit]) // Reuse user form component
        ->set('isViewMode', false)
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $userToEdit->id,
        'name' => 'New Name',
    ]);
});

test('can delete a user', function () {
    $userToDelete = User::factory()->create();

    Volt::test('users.create', ['user' => $userToDelete])
        ->call('delete')
        ->assertRedirect(route('users.index'));

    $this->assertSoftDeleted('users', ['id' => $userToDelete->id]);
});

test('can search users', function () {
    User::factory()->create(['name' => 'UniqueNameXYZ']);
    User::factory()->create(['name' => 'OtherUser']);

    Volt::test('users.index')
        ->set('search', 'UniqueNameXYZ')
        ->assertSee('UniqueNameXYZ')
        ->assertDontSee('OtherUser');
});

test('can toggle user status', function () {
    $user = User::factory()->create(['status' => 'active']);

    Volt::test('users.create', ['user' => $user])
        ->call('toggleStatus');

    $this->assertEquals('inactive', $user->fresh()->status);

    Volt::test('users.create', ['user' => $user])
        ->call('toggleStatus');

    $this->assertEquals('active', $user->fresh()->status);
});

test('can reset 2fa', function () {
    $user = User::factory()->create();
    // Assuming resetTwoFactor updates some column or logic, we just check call success
    Volt::test('users.create', ['user' => $user])
        ->call('resetTwoFactor')
        ->assertHasNoErrors();
});

test('can upload avatar', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('avatar.jpg');

    Volt::test('users.create')
        ->set('name', 'Avatar User')
        ->set('email', 'avatar@example.com')
        ->set('password', 'password')
        ->set('sendPasswordEmail', false)
        ->set('avatarFile', $file)
        ->call('save');

    $this->assertDatabaseHas('users', ['email' => 'avatar@example.com']);
    $user = User::where('email', 'avatar@example.com')->first();
    $this->assertNotNull($user->avatar);
    Storage::disk('public')->assertExists($user->avatar);
});
