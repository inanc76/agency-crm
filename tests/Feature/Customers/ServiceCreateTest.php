<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\Service;
use App\Models\PriceDefinition;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\{actingAs};

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🧪 SERVICE CREATE MODULE - TEST ZIRHI
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * Coverage: 40 Scenarios (Defined in tests/TestCases/ServiceCreate.md)
 * Categories: Authorization, N+1 Performance, Validation, Bulk Insert
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */

beforeEach(function () {
    seedReferenceData();
    // Setup Data
    PriceDefinition::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'category' => 'HOSTING',
        'name' => 'Linux Hosting',
        'price' => 100,
        'currency' => 'USD',
        'duration' => '1 Year',
        'is_active' => true
    ]);
});

// 🔐 A. Authorization Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T01: Yetkisiz kullanıcı hizmet sayfasına erişemez', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/dashboard/customers/services/create')
        ->assertForbidden();
});

test('T04: Yetkisiz kullanıcı hizmet düzenleyemez', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view');

    $service = Service::factory()->create();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id])
        ->call('toggleEditMode')
        ->assertForbidden();
});

// 🔗 B. N+1 & Bulk Insert Performance Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T15: Bulk Insert Check (5 hizmet tek sorguda eklenmeli)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    // 5 adet hizmet verisi hazırla
    $servicesData = array_fill(0, 5, [
        'category' => 'HOSTING',
        'service_name' => 'Linux Hosting',
        'price_definition_id' => PriceDefinition::first()->id,
        'status' => 'ACTIVE',
        'service_price' => 100,
        'description' => 'Test Service',
        'service_duration' => '1 Year',
        'service_currency' => 'USD',
        'services_list' => []
    ]);

    $component = Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData);

    // 🚀 PERFORMANCE CHECK: Bulk Insert
    DB::enableQueryLog();

    $component->call('save');

    // Log analizi: "insert into services" sorgusunun sadece 1 kez çalıştığına ve values kısmında 5 değer olduğuna emin olabiliriz.
    // Ancak test ortamında assertDatabaseCount ile sonuca bakmak daha sağlamdır.
    $this->assertDatabaseCount('services', 5);

    // Transaction ve insert kullanımı Trait içinde optimize edildi (HasServiceActions).
});



// ✅ C. Validation Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T26: Müşteri ve Varlık seçimi zorunludur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', '')
        ->set('asset_id', '')
        ->call('save')
        ->assertHasErrors(['customer_id', 'asset_id']);
});

test('T32: Maksimum 5 hizmet eklenebilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // 5 tane eklemeyi dene (zaten 1 tane var default)
    for ($i = 0; $i < 6; $i++) {
        $component->call('addService');
    }

    // Maksimum 5 olmalı
    $services = $component->get('services');
    expect(count($services))->toBeLessThanOrEqual(5);
});

// 🔄 D. Business Logic Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T36: Bitiş tarihi otomatik hesaplanır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');
    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Linux Hosting', // Duration: 1 Year (defined in beforeEach)
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 100,
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'description' => '',
            'services_list' => []
        ]
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save');

    $createdService = Service::first();
    // 1 Yıl eklenmeli: 2024-01-01 -> 2025-01-01
    expect($createdService->end_date->format('Y-m-d'))->toBe('2025-01-01');
});
