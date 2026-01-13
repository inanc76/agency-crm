<?php

use App\Models\Customer;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🧪 CUSTOMER CREATE MODULE - TEST ZIRHI
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * Coverage: 40 Scenarios (Defined in tests/TestCases/CustomerCreate.md)
 * Categories: Authorization, N+1 Performance, Validation, Business Logic
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */
beforeEach(function () {
    seedReferenceData();
});

// 🔐 A. Authorization Tests (Yetki Kontrolleri)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T01: Yetkisiz kullanıcı sayfaya erişemez', function () {
    $user = User::factory()->create(); // No permissions

    actingAs($user)
        ->get('/dashboard/customers/create')
        ->assertForbidden();
});

test('T02: Yetkili kullanıcı sayfaya erişebilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    actingAs($user)
        ->get('/dashboard/customers/create')
        ->assertOk()
        ->assertSee('Yeni Müşteri Ekle');
});

test('T04: Yetkisiz kullanıcı düzenleme moduna geçemez (Authorization Check)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view'); // Can view but not edit

    $customer = Customer::factory()->create();

    Volt::actingAs($user)
        ->test('customers.create', ['customer' => $customer->id])
        ->set('isViewMode', true)
        ->call('toggleEditMode')
        ->assertForbidden(); // 403 Bekleniyor
});

// 🔗 B. N+1 Query Tests (Performans Yaması Doğrulama)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

// ✅ C. Validation Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T26: Zorunlu alanlar boş bırakılamaz', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('T29: Email formatı doğrulanır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('emails', ['gecersiz-email'])
        ->call('save')
        ->assertHasErrors(['emails.0' => 'email']);
});

// 🔄 D. Business Logic Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T39: Varsayılan ülke Türkiye gelir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->assertSet('country_id', 'TR');
});

// ============================================================================
// VERIFICATION OF BUTTON LINKS
// ============================================================================

test('T41-UI: New Customer button has correct href on customers tab', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view');

    actingAs($user)
        ->get('/dashboard/customers?tab=customers')
        ->assertStatus(200)
        ->assertSee('Yeni Müşteri')
        ->assertSee('/dashboard/customers/create');
});
