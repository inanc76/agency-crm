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
    ->in('Feature', 'Unit')
    ->beforeEach(function () {
        Illuminate\Support\Facades\Gate::define('customers.create', fn($user) => $user->hasPermissionTo('customers.create'));
        Illuminate\Support\Facades\Gate::define('customers.edit', fn($user) => $user->hasPermissionTo('customers.edit'));
        Illuminate\Support\Facades\Gate::define('customers.view', fn($user) => $user->hasPermissionTo('customers.view'));
        Illuminate\Support\Facades\Gate::define('services.create', fn($user) => $user->hasPermissionTo('services.create'));
        Illuminate\Support\Facades\Gate::define('services.edit', fn($user) => $user->hasPermissionTo('services.edit'));
        Illuminate\Support\Facades\Gate::define('services.delete', fn($user) => $user->hasPermissionTo('services.delete'));
        Illuminate\Support\Facades\Gate::define('services.view', fn($user) => $user->hasPermissionTo('services.view'));
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
    if (!\Illuminate\Support\Facades\DB::table('reference_categories')->where('key', 'CUSTOMER_TYPE')->exists()) {
        \Illuminate\Support\Facades\DB::table('reference_categories')->insert([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'key' => 'CUSTOMER_TYPE',
            'name' => 'Müşteri Tipleri',
            'is_active' => true,
        ]);
    }

    // Reference Items
    if (!\App\Models\ReferenceItem::where('key', 'CUSTOMER')->exists()) {
        \App\Models\ReferenceItem::create([
            'category_key' => 'CUSTOMER_TYPE',
            'key' => 'CUSTOMER',
            'display_label' => 'Müşteri',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1
        ]);
    }
}
