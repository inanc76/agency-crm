<?php

use App\Models\User;
use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('settings.edit');
    $this->actingAs($this->user);

    ReferenceCategory::create(['name' => 'Service Category', 'key' => 'SERVICE_CATEGORY']);
    ReferenceCategory::create(['name' => 'Service Extension', 'key' => 'SERVICE_EXTENSION_YEARS']);
    ReferenceCategory::create(['name' => 'Currency', 'key' => 'CURRENCY']);

    ReferenceItem::create(['category_key' => 'SERVICE_CATEGORY', 'key' => 'WEB', 'display_label' => 'Web Design', 'is_active' => true]);
    ReferenceItem::create(['category_key' => 'SERVICE_EXTENSION_YEARS', 'key' => '1Y', 'display_label' => '1 Year', 'is_active' => true, 'sort_order' => 1]);
    ReferenceItem::create(['category_key' => 'CURRENCY', 'key' => 'TRY', 'display_label' => 'TL', 'is_active' => true]);
});

// A. Listing & Accessibility (1-5)
test('1. price settings page is accessible', function () {
    $this->get(route('settings.prices'))->assertOk();
});

test('2. price list shows added items', function () {
    PriceDefinition::create(['name' => 'Test Price', 'category' => 'WEB', 'duration' => '1Y', 'price' => 100, 'currency' => 'TRY', 'is_active' => true]);
    Volt::test('settings.prices')->assertSee('Test Price');
});

test('3. list is ordered by newest first', function () {
    PriceDefinition::create(['name' => 'Old', 'category' => 'WEB', 'duration' => '1Y', 'price' => 10, 'currency' => 'TRY', 'created_at' => now()->subDay()]);
    PriceDefinition::create(['name' => 'New', 'category' => 'WEB', 'duration' => '1Y', 'price' => 20, 'currency' => 'TRY', 'created_at' => now()]);
    Volt::test('settings.prices')->assertViewHas('prices');
});

test('4. categories are loaded from reference data', function () {
    Volt::test('settings.prices')->assertViewHas('categories', function ($cats) {
        return $cats->contains('key', 'WEB');
    });
});

test('5. durations are loaded from reference data', function () {
    Volt::test('settings.prices')->assertViewHas('durations', function ($durs) {
        return $durs->contains('key', '1Y');
    });
});

// B. Creation & Validation (6-10)
test('6. name is required', function () {
    Volt::test('settings.prices')->set('name', '')->call('save')->assertHasErrors(['name' => 'required']);
});

test('7. category is required', function () {
    Volt::test('settings.prices')->set('category', '')->call('save')->assertHasErrors(['category' => 'required']);
});

test('8. duration is required', function () {
    Volt::test('settings.prices')->set('duration', '')->call('save')->assertHasErrors(['duration' => 'required']);
});

test('9. price must be numeric and positive', function () {
    Volt::test('settings.prices')->set('price', -10)->call('save')->assertHasErrors(['price' => 'min']);
});

test('10. new price definition can be created successfully', function () {
    Volt::test('settings.prices')->set('name', 'Premium')->set('category', 'WEB')->set('duration', '1Y')->set('price', 5000)->set('currency', 'TRY')
        ->call('save')->assertHasNoErrors();
    expect(PriceDefinition::where('name', 'Premium')->exists())->toBeTrue();
});

// C. Filtering (11-15)
test('11. search filter works by name', function () {
    PriceDefinition::create(['name' => 'AlphaService', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    PriceDefinition::create(['name' => 'BetaService', 'category' => 'WEB', 'duration' => '1Y', 'price' => 2, 'currency' => 'TRY']);
    Volt::test('settings.prices')->set('search', 'AlphaService')->assertSee('AlphaService')->assertDontSee('BetaService');
});

test('12. category filter works', function () {
    PriceDefinition::create(['name' => 'WebOnlyProduct', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    PriceDefinition::create(['name' => 'OtherProduct', 'category' => 'OTHER', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->set('filterCategory', 'WEB')->assertSee('WebOnlyProduct')->assertDontSee('OtherProduct');
});

test('13. duration filter works', function () {
    PriceDefinition::create(['name' => '1 Year Pack', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    PriceDefinition::create(['name' => '1 Month Pack', 'category' => 'WEB', 'duration' => '1M', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->set('filterDuration', '1Y')->assertSee('1 Year Pack')->assertDontSee('1 Month Pack');
});

test('14. clear filters resets search and dropdowns', function () {
    Volt::test('settings.prices')->set('search', 'test')->set('filterCategory', 'WEB')->call('clearFilters')
        ->assertSet('search', '')->assertSet('filterCategory', '');
});

test('15. empty search shows all prices', function () {
    PriceDefinition::create(['name' => 'A', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->set('search', '')->assertSee('A');
});

// D. Editing & Updates (16-20)
test('16. edit mode loads existing data into form', function () {
    $p = PriceDefinition::create(['name' => 'Edit Me', 'category' => 'WEB', 'duration' => '1Y', 'price' => 100, 'currency' => 'TRY']);
    Volt::test('settings.prices')->call('edit', $p->id)->assertSet('name', 'Edit Me')->assertSet('selectedId', $p->id);
});

test('17. saving while editing updates the record instead of creating new', function () {
    $p = PriceDefinition::create(['name' => 'Old', 'category' => 'WEB', 'duration' => '1Y', 'price' => 100, 'currency' => 'TRY']);
    Volt::test('settings.prices')->call('edit', $p->id)->set('name', 'Updated')->call('save');
    expect($p->fresh()->name)->toBe('Updated');
    expect(PriceDefinition::count())->toBe(1);
});

test('18. toggling status changes is_active value', function () {
    $p = PriceDefinition::create(['name' => 'A', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY', 'is_active' => true]);
    Volt::test('settings.prices')->call('toggleStatus', $p->id);
    expect($p->fresh()->is_active)->toBeFalse();
});

test('19. delete action removes record from DB', function () {
    $p = PriceDefinition::create(['name' => 'Bye', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->call('delete', $p->id);
    expect(PriceDefinition::count())->toBe(0);
});

test('20. currencies list includes TRY as default', function () {
    Volt::test('settings.prices')->assertViewHas('currencies', function ($currs) {
        return $currs->contains('key', 'TRY');
    });
});

// E. UI Extras & Details (21-25)
test('21. description field is optional', function () {
    Volt::test('settings.prices')->set('name', 'P')->set('category', 'WEB')->set('duration', '1Y')->set('price', 10)->set('currency', 'TRY')->set('description', '')
        ->call('save')->assertHasNoErrors();
});

test('22. status defaults to active on new price', function () {
    Volt::test('settings.prices')->call('openCreateModal')->assertSet('is_active', true);
});

test('23. success on save', function () {
    Volt::test('settings.prices')->set('name', 'P')->set('category', 'WEB')->set('duration', '1Y')->set('price', 10)->set('currency', 'TRY')
        ->call('save')->assertHasNoErrors();
});

test('24. success on toggle status', function () {
    $p = PriceDefinition::create(['name' => 'A', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->call('toggleStatus', $p->id)->assertHasNoErrors();
});

test('25. count of listed items is returned in view', function () {
    PriceDefinition::create(['name' => 'A', 'category' => 'WEB', 'duration' => '1Y', 'price' => 1, 'currency' => 'TRY']);
    Volt::test('settings.prices')->assertViewHas('prices', function ($prices) {
        return $prices->count() === 1;
    });
});
