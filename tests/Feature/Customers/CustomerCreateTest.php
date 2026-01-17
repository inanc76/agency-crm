<?php

use App\Models\Contact;
use App\Models\Asset;
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

test('T03: Müşteri görüntüleme yetkisi (View Mode)', function () {
    $user = User::factory()->create(); // No view permission
    $customer = Customer::factory()->create();

    try {
        Volt::actingAs($user)
            ->test('customers.create', ['customer' => $customer->id]);
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        expect(true)->toBeTrue();
        return;
    }

    actingAs($user)
        ->get("/dashboard/customers/{$customer->id}")
        ->assertForbidden();
});

test('T05: Müşteri silme yetkisi', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view');

    $customer = Customer::factory()->create();

    Volt::actingAs($user)
        ->test('customers.create', ['customer' => $customer->id])
        ->call('delete')
        ->assertForbidden();
});

test('T09: Toplu veri görüntüleme yetkisi', function () {
    $user = User::factory()->create(); // No permissions

    actingAs($user)
        ->get('/dashboard/customers?tab=customers')
        ->assertForbidden();
});

// 🔗 B. N+1 Query Tests (Performans Yaması Doğrulama)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T11-T19: Customer Load performansı zırhlıdır (N+1 Query Check)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view');

    $customer = Customer::factory()->create();
    Contact::factory()->count(3)->create(['customer_id' => $customer->id]);

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('customers.create', ['customer' => $customer->id])
        ->assertSet('customerId', $customer->id);

    expect(count(DB::getQueryLog()))->toBeLessThan(35);
});

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

test('T27 & T28: Ülke ve Şehir zorunludur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('country_id', '')
        ->set('city_id', '')
        ->call('save')
        ->assertHasErrors(['country_id' => 'required', 'city_id' => 'required']);
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

test('T30: Website URL formatı doğrulanır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('websites', ['ht tp://invalid-url'])
        ->call('save')
        ->assertHasErrors(['websites.0' => 'url']);
});

test('T31 & T32: Logo validasyonu (Size & Type)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    // T31: Check Size
    $largeFile = \Illuminate\Http\UploadedFile::fake()->image('large.jpg')->size(6000);
    Volt::actingAs($user)->test('customers.create')->set('logo', $largeFile)->call('save')->assertHasErrors(['logo' => 'max']);

    // T32: Check Type
    $pdfFile = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1000);
    Volt::actingAs($user)->test('customers.create')->set('logo', $pdfFile)->call('save')->assertHasErrors(['logo' => 'image']);
});

test('T33: En fazla 3 email eklenebilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('emails', ['', '', ''])
        ->call('addEmail')
        ->assertCount('emails', 3);
});

test('T34: En fazla 10 ilişkili firma eklenebilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    $otherCustomers = Customer::factory()->count(11)->create();

    $component = Volt::actingAs($user)->test('customers.create');

    foreach ($otherCustomers->take(10) as $c) {
        $component->call('addRelatedCustomer', $c->id);
    }

    $component->call('addRelatedCustomer', $otherCustomers->last()->id);

    $component->assertCount('related_customers', 10);
});

// 🔄 D. Business Logic Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T35: Telefon numarası normalize edilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('name', 'Normalize Customer')
        ->set('country_id', 'TR')
        ->set('city_id', '34')
        ->set('customer_type', 'CUSTOMER')
        ->set('phones', ['(555) 123-45 67'])
        ->call('save')
        ->assertHasNoErrors();

    // DB Check: (555) 123-45 67 -> 555 12345 67
    $customer = Customer::where('name', 'Normalize Customer')->first();
    expect($customer->phones[0])->toBe('555 12345 67');
});

test('T36: Web sitesi otomatik normalize edilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('websites.0', 'google.com')
        ->set('name', 'Web Customer')
        ->set('country_id', 'TR')
        ->set('city_id', '34')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('customers', [
        'name' => 'Web Customer',
        'website' => 'https://google.com'
    ]);
});

test('T37: İsimler otomatik Title Case yapılır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->set('name', 'ali veli')
        ->assertSet('name', 'Ali Veli');
});

test('T38-T40: Varsayılan tanımlamalar (Type, Country)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.create');

    Volt::actingAs($user)
        ->test('customers.create')
        ->assertSet('customer_type', 'CUSTOMER')
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
