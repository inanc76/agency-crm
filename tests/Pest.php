<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit', 'TestCases', 'e2e')
    ->beforeEach(function () {
        Illuminate\Support\Facades\Gate::define('customers.create', fn($user) => $user->hasPermissionTo('customers.create'));
        Illuminate\Support\Facades\Gate::define('customers.edit', fn($user) => $user->hasPermissionTo('customers.edit'));
        Illuminate\Support\Facades\Gate::define('customers.view', fn($user) => $user->hasPermissionTo('customers.view'));
        Illuminate\Support\Facades\Gate::define('services.create', fn($user) => $user->hasPermissionTo('services.create'));
        Illuminate\Support\Facades\Gate::define('services.edit', fn($user) => $user->hasPermissionTo('services.edit'));
        Illuminate\Support\Facades\Gate::define('services.delete', fn($user) => $user->hasPermissionTo('services.delete'));
        Illuminate\Support\Facades\Gate::define('services.view', fn($user) => $user->hasPermissionTo('services.view'));
        Illuminate\Support\Facades\Gate::define('settings.view', fn($user) => $user->hasPermissionTo('settings.view'));
        Illuminate\Support\Facades\Gate::define('settings.edit', fn($user) => $user->hasPermissionTo('settings.edit'));
    });

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Seeds essential reference data for tests (Countries, Cities, Customer Types).
 * Prevents foreign key constraint violations during test execution.
 */
function seedReferenceData()
{
    // Countries
    if (!\Illuminate\Support\Facades\DB::table('countries')->where('id', 'TR')->exists()) {
        \Illuminate\Support\Facades\DB::table('countries')->insert([
            'id' => 'TR',
            'code' => 'TR',
            'name' => 'Türkiye',
            'is_active' => true,
            'sort_order' => 1
        ]);
    }

    // Cities
    if (!\Illuminate\Support\Facades\DB::table('cities')->where('id', '34')->exists()) {
        \Illuminate\Support\Facades\DB::table('cities')->insert([
            'id' => '34',
            'country_id' => 'TR',
            'name' => 'İstanbul',
            'is_active' => true,
            'sort_order' => 1
        ]);
    }

    // Reference Categories
    $categories = [
        'CUSTOMER_TYPE' => 'Müşteri Tipleri',
        'CONTACT_STATUS' => 'Kişi Durumları',
        'SERVICE_STATUS' => 'Hizmet Durumları',
        'CURRENCY' => 'Para Birimleri',
        'SERVICE_CATEGORY' => 'Hizmet Kategorileri',
        'OFFER_STATUS' => 'Teklif Durumları',
        'VAT_RATES' => 'KDV Oranları',
        'ASSET_TYPE' => 'Varlık Tipleri',
        'PROJECT_STATUS' => 'Proje Durumları',
        'PROJECT_TYPE' => 'Proje Tipleri',
        'TASK_STATUS' => 'Görev Durumları',
        'TASK_PRIORITY' => 'Görev Öncelikleri',
        'PHASE_STATUS' => 'Faz Durumları',
        'MODULE_STATUS' => 'Modül Durumları'
    ];

    foreach ($categories as $key => $name) {
        if (!\Illuminate\Support\Facades\DB::table('reference_categories')->where('key', $key)->exists()) {
            \Illuminate\Support\Facades\DB::table('reference_categories')->insert([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'key' => $key,
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }

    // Reference Items
    $items = [
        ['category' => 'CUSTOMER_TYPE', 'key' => 'CUSTOMER', 'label' => 'Müşteri'],
        ['category' => 'CONTACT_STATUS', 'key' => 'WORKING', 'label' => 'Çalışıyor'],
        ['category' => 'CONTACT_STATUS', 'key' => 'LEFT', 'label' => 'Ayrıldı'],
        ['category' => 'SERVICE_STATUS', 'key' => 'ACTIVE', 'label' => 'Aktif'],
        ['category' => 'SERVICE_STATUS', 'key' => 'PASSIVE', 'label' => 'Pasif'],
        ['category' => 'CURRENCY', 'key' => 'TRY', 'label' => 'Türk Lirası'],
        ['category' => 'CURRENCY', 'key' => 'USD', 'label' => 'Amerikan Doları'],
        ['category' => 'OFFER_STATUS', 'key' => 'DRAFT', 'label' => 'Taslak'],
        ['category' => 'OFFER_STATUS', 'key' => 'SENT', 'label' => 'Gönderildi'],
        ['category' => 'OFFER_STATUS', 'key' => 'ACCEPTED', 'label' => 'Onaylandı'],
        ['category' => 'OFFER_STATUS', 'key' => 'REJECTED', 'label' => 'Reddedildi'],
        ['category' => 'VAT_RATES', 'key' => 'TR_20', 'label' => 'Türkiye (%20)'],
        ['category' => 'ASSET_TYPE', 'key' => 'WEBSITE', 'label' => 'WEBSITE'],
        ['category' => 'ASSET_TYPE', 'key' => 'DOMAIN', 'label' => 'DOMAIN'],
        ['category' => 'ASSET_TYPE', 'key' => 'OTHER', 'label' => 'OTHER'],
        ['category' => 'PROJECT_STATUS', 'key' => 'project_active', 'label' => 'Aktif'],
        ['category' => 'PROJECT_STATUS', 'key' => 'project_completed', 'label' => 'Tamamlandı'],
        ['category' => 'TASK_PRIORITY', 'key' => 'high', 'label' => 'Yüksek'],
        ['category' => 'TASK_PRIORITY', 'key' => 'normal', 'label' => 'Normal'],
        ['category' => 'TASK_STATUS', 'key' => 'open', 'label' => 'Açık'],
        ['category' => 'TASK_STATUS', 'key' => 'done', 'label' => 'Bitti'],
    ];

    foreach ($items as $index => $item) {
        if (!\App\Models\ReferenceItem::where('category_key', $item['category'])->where('key', $item['key'])->exists()) {
            \App\Models\ReferenceItem::create([
                'category_key' => $item['category'],
                'key' => $item['key'],
                'display_label' => $item['label'],
                'is_default' => $index === 0,
                'is_active' => true,
                'sort_order' => $index + 1
            ]);
        }
    }
}
