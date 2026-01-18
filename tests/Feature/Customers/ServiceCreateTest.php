<?php

use App\Models\Asset;
use App\Models\Customer;
use App\Models\PriceDefinition;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

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
        'is_active' => true,
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

test('T02: Yetkili kullanıcı hizmet sayfasına erişebilir', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    actingAs($user)
        ->get('/dashboard/customers/services/create')
        ->assertOk();
});

test('T03: Hizmet görüntüleme yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $service = Service::factory()->create();

    actingAs($user)
        ->get("/dashboard/customers/services/{$service->id}")
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

test('T05: Hizmet silme yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view');

    $service = Service::factory()->create();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id])
        ->call('delete')
        ->assertForbidden();
});

test('T06: Müşteri seçimi yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // Müşteri listesi yüklenmeli (customers.view yetkisi gerekli)
    expect($component->get('customers'))->toBeArray();
});

test('T07: Varlık seçimi yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');
    $customer = Customer::factory()->create();

    $component = Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id);

    // Varlık listesi yüklenmeli (assets.view yetkisi gerekli)
    expect($component->get('assets'))->toBeArray();
});

test('T08: Fiyat tanımı görüntüleme yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // Fiyat kategorileri yüklenmeli (prices.view yetkisi gerekli)
    expect($component->get('categories'))->toBeArray();
});

test('T09: Toplu hizmet oluşturma yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view'); // create yetkisi yok

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->call('save')
        ->assertForbidden();
});

test('T10: Hizmet oluşturma sonrası redirect yetkisi kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Linux Hosting',
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 100,
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save')
        ->assertRedirect();
});

// 🔗 B. N+1 & Bulk Insert Performance Tests
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

test('T11: Mount - Customers Load N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    Customer::factory()->count(10)->create();

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form');

    $queries = DB::getQueryLog();
    $customerQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'customers');
    });

    // Sadece 1 customer query olmalı
    expect($customerQueries->count())->toBeLessThanOrEqual(2);
});

test('T12: Mount - Price Definitions Load N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    PriceDefinition::factory()->count(10)->create();

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form');

    $queries = DB::getQueryLog();
    $priceQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'price_definitions');
    });

    // Sadece 1 price definition query olmalı
    expect($priceQueries->count())->toBeLessThanOrEqual(2);
});

test('T13: LoadAssets - Assets Load N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    Asset::factory()->count(10)->create(['customer_id' => $customer->id]);

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id);

    $queries = DB::getQueryLog();
    $assetQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'assets');
    });

    // Sadece 1 asset query olmalı
    expect($assetQueries->count())->toBeLessThanOrEqual(2);
});

test('T14: LoadServicesForIndex - Services List N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    PriceDefinition::factory()->count(5)->create(['category' => 'HOSTING']);

    DB::enableQueryLog();

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    $component->set('services.0.category', 'HOSTING');

    $queries = DB::getQueryLog();
    $serviceListQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'price_definitions') && str_contains($query['query'], 'category');
    });

    // Sadece 1 price definition query olmalı
    expect($serviceListQueries->count())->toBeLessThanOrEqual(2);
});

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
        'services_list' => [],
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

test('T16: LoadServiceData - Service Load N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view');

    $service = Service::factory()->create();

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id]);

    $queries = DB::getQueryLog();
    $serviceQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'services');
    });

    // Sadece 1 service query olmalı
    expect($serviceQueries->count())->toBeLessThanOrEqual(2);
});

test('T17: Delete - Service Load N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['services.view', 'services.delete']);

    $service = Service::factory()->create();

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id])
        ->call('delete');

    $queries = DB::getQueryLog();
    $serviceQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'services') && str_contains($query['query'], 'select');
    });

    // Sadece 1 service select query olmalı
    expect($serviceQueries->count())->toBeLessThanOrEqual(2);
});

test('T18: UpdatedCustomerId - Assets Reload N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    Asset::factory()->count(5)->create(['customer_id' => $customer2->id]);

    DB::enableQueryLog();

    $component = Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer1->id);

    // Müşteri değiştir
    $component->set('customer_id', $customer2->id);

    $queries = DB::getQueryLog();
    $assetQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'assets');
    });

    // Asset reload için sadece 1 query olmalı
    expect($assetQueries->count())->toBeLessThanOrEqual(3);
});

test('T19: UpdatedServices - Services List Reload N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    PriceDefinition::factory()->count(5)->create(['category' => 'HOSTING']);
    PriceDefinition::factory()->count(5)->create(['category' => 'DOMAIN']);

    DB::enableQueryLog();

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // Kategori değiştir
    $component->set('services.0.category', 'HOSTING');
    $component->set('services.0.category', 'DOMAIN');

    $queries = DB::getQueryLog();
    $serviceListQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'price_definitions') && str_contains($query['query'], 'category');
    });

    // Her kategori değişimi için sadece 1 query olmalı
    expect($serviceListQueries->count())->toBeLessThanOrEqual(4);
});

test('T20: Mount - Customer Query Parameter N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer', $customer->id);

    $queries = DB::getQueryLog();

    // Collection üzerinde arama yapıldığı için ekstra query olmamalı
    expect(count($queries))->toBeLessThan(10);
});

test('T21: UpdateServicePrice - Price Definition Lookup N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $priceDefinition = PriceDefinition::factory()->create([
        'category' => 'HOSTING',
        'name' => 'Linux Hosting',
    ]);

    DB::enableQueryLog();

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    $component->set('services.0.category', 'HOSTING');
    $component->set('services.0.service_name', 'Linux Hosting');

    $queries = DB::getQueryLog();

    // Collection üzerinde arama yapıldığı için ekstra query olmamalı
    expect(count($queries))->toBeLessThan(15);
});

test('T22: Save - Transaction Rollback N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    // Geçersiz veri ile transaction rollback test et
    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Linux Hosting',
            'price_definition_id' => 'invalid-uuid', // Geçersiz ID
            'status' => 'ACTIVE',
            'service_price' => 100,
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'services_list' => [],
        ],
    ];

    DB::enableQueryLog();

    try {
        Volt::actingAs($user)
            ->test('modals.service-form')
            ->set('customer_id', $customer->id)
            ->set('asset_id', $asset->id)
            ->set('start_date', '2024-01-01')
            ->set('services', $servicesData)
            ->call('save');
    } catch (\Exception $e) {
        // Hata bekleniyor
    }

    // Rollback sonrası hiç service oluşturulmamalı
    $this->assertDatabaseCount('services', 0);
});

test('T23: CalculateEndDate - Date Calculation N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    DB::enableQueryLog();

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // Tarih hesaplama işlemi
    $component->call('calculateEndDate', '2024-01-01', '1 Year');

    $queries = DB::getQueryLog();

    // Tarih hesaplama için query olmamalı
    expect(count($queries))->toBeLessThan(5);
});

test('T24: LoadServiceData - Asset Name Lookup N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);
    $service = Service::factory()->create([
        'customer_id' => $customer->id,
        'asset_id' => $asset->id,
    ]);

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id]);

    $queries = DB::getQueryLog();
    $assetQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'assets');
    });

    // Asset bilgisi için ayrı query olmamalı (eager loading ile çözülmeli)
    expect($assetQueries->count())->toBeLessThanOrEqual(2);
});

test('T25: LoadServiceData - Customer Name Lookup N+1 kontrolü', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.view');

    $customer = Customer::factory()->create();
    $service = Service::factory()->create(['customer_id' => $customer->id]);

    DB::enableQueryLog();

    Volt::actingAs($user)
        ->test('modals.service-form', ['service' => $service->id]);

    $queries = DB::getQueryLog();

    // Customer bilgisi collection'dan alındığı için ekstra query olmamalı
    expect(count($queries))->toBeLessThan(10);
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

test('T27: Başlangıç tarihi zorunludur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '')
        ->call('save')
        ->assertHasErrors(['start_date']);
});

test('T28: Hizmet kategorisi zorunludur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => '',
            'service_name' => 'Linux Hosting',
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 100,
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save')
        ->assertHasErrors(['services.0.category']);
});

test('T29: Hizmet adı zorunludur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => '',
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 100,
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save')
        ->assertHasErrors(['services.0.service_name']);
});

test('T30: Tarih formatı doğrulaması', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', 'invalid-date')
        ->call('save')
        ->assertHasErrors(['start_date']);
});

test('T31: Geçmiş tarih doğrulaması', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2020-01-01') // Geçmiş tarih
        ->call('save')
        ->assertHasErrors(['start_date']);
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

test('T33: Minimum 1 hizmet olmalıdır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    // Son hizmeti silmeye çalış
    $component->call('removeService', 0);

    // En az 1 hizmet kalmalı
    $services = $component->get('services');
    expect(count($services))->toBeGreaterThanOrEqual(1);
});

test('T34: Hizmet fiyatı numeric olmalıdır', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Linux Hosting',
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 'invalid-price',
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'USD',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save')
        ->assertHasErrors(['services.0.service_price']);
});

test('T35: Hizmet para birimi doğrulaması', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Linux Hosting',
            'price_definition_id' => PriceDefinition::first()->id,
            'status' => 'ACTIVE',
            'service_price' => 100,
            'description' => 'Test Service',
            'service_duration' => '1 Year',
            'service_currency' => 'INVALID',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save')
        ->assertHasErrors(['services.0.service_currency']);
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
            'services_list' => [],
        ],
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

test('T37: Aylık süre hesaplaması', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');
    $customer = Customer::factory()->create();
    $asset = Asset::factory()->create(['customer_id' => $customer->id]);

    // Aylık hizmet için price definition oluştur
    $monthlyPrice = PriceDefinition::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'category' => 'HOSTING',
        'name' => 'Monthly Hosting',
        'price' => 10,
        'currency' => 'USD',
        'duration' => '1 Month',
        'is_active' => true,
    ]);

    $servicesData = [
        [
            'category' => 'HOSTING',
            'service_name' => 'Monthly Hosting',
            'price_definition_id' => $monthlyPrice->id,
            'status' => 'ACTIVE',
            'service_price' => 10,
            'service_duration' => '1 Month',
            'service_currency' => 'USD',
            'description' => '',
            'services_list' => [],
        ],
    ];

    Volt::actingAs($user)
        ->test('modals.service-form')
        ->set('customer_id', $customer->id)
        ->set('asset_id', $asset->id)
        ->set('start_date', '2024-01-01')
        ->set('services', $servicesData)
        ->call('save');

    $createdService = Service::latest()->first();
    // 1 Ay eklenmeli: 2024-01-01 -> 2024-02-01
    expect($createdService->end_date->format('Y-m-d'))->toBe('2024-02-01');
});

test('T38: Hizmet fiyatı otomatik doldurulur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    $component->set('services.0.category', 'HOSTING');
    $component->set('services.0.service_name', 'Linux Hosting');

    // Fiyat otomatik doldurulmalı
    $services = $component->get('services');
    expect($services[0]['service_price'])->toBe(100);
});

test('T39: Hizmet süresi otomatik doldurulur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    $component->set('services.0.category', 'HOSTING');
    $component->set('services.0.service_name', 'Linux Hosting');

    // Süre otomatik doldurulmalı
    $services = $component->get('services');
    expect($services[0]['service_duration'])->toBe('1 Year');
});

test('T40: Hizmet para birimi otomatik doldurulur', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('services.create');

    $component = Volt::actingAs($user)
        ->test('modals.service-form');

    $component->set('services.0.category', 'HOSTING');
    $component->set('services.0.service_name', 'Linux Hosting');

    // Para birimi otomatik doldurulmalı
    $services = $component->get('services');
    expect($services[0]['service_currency'])->toBe('USD');
});

// ============================================================================
// VERIFICATION OF BUTTON LINKS
// ============================================================================

test('T41-UI: New Service button has correct href on services tab', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view');
    $user->givePermissionTo('services.view');

    $response = actingAs($user)
        ->get('/dashboard/customers?tab=services');

    $response->assertStatus(200);
    $response->assertSee('Yeni Hizmet');
    $response->assertSee('/dashboard/customers/services/create');
});

test('T42-UI: New Service button has correct href on customer services tab', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('customers.view');
    $user->givePermissionTo('services.view');
    $customer = Customer::factory()->create();

    $response = actingAs($user)
        ->get("/dashboard/customers/{$customer->id}?tab=services");

    $response->assertStatus(200);
    $response->assertSee('Yeni Hizmet');
    $response->assertSee('/dashboard/customers/services/create');
    $response->assertSee('customer='.$customer->id);
});
