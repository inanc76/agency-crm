<?php

use App\Models\User;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('settings.edit');
    $this->actingAs($this->user);
});

// A. Category Management (1-8)
test('1. variable settings page is accessible', function () {
    $this->get(route('settings.variables'))->assertOk();
});

test('2. can create a new reference category', function () {
    Volt::test('settings.variables')->set('categoryName', 'Gender')->set('categoryKey', 'GENDER')->call('saveCategory')->assertHasNoErrors();
    expect(ReferenceCategory::where('key', 'GENDER')->exists())->toBeTrue();
});

test('3. category name is required', function () {
    Volt::test('settings.variables')->set('categoryName', '')->call('saveCategory')->assertHasErrors(['categoryName' => 'required']);
});

test('4. category key is required', function () {
    Volt::test('settings.variables')->set('categoryKey', '')->call('saveCategory')->assertHasErrors(['categoryKey' => 'required']);
});

test('5. search categories by name works', function () {
    ReferenceCategory::create(['name' => 'Apple', 'key' => 'APPLE']);
    ReferenceCategory::create(['name' => 'Banana', 'key' => 'BANANA']);
    Volt::test('settings.variables')->set('search', 'App')->assertSee('Apple')->assertDontSee('Banana');
});

test('6. search categories by key works', function () {
    ReferenceCategory::create(['name' => 'Fruit', 'key' => 'FRUIT']);
    Volt::test('settings.variables')->set('search', 'FRUIT')->assertSee('Fruit');
});

test('7. editing a category loads its data', function () {
    $cat = ReferenceCategory::create(['name' => 'Old', 'key' => 'OLD']);
    Volt::test('settings.variables')->call('editCategory', $cat->id)->assertSet('categoryName', 'Old');
});

test('8. deleting a category removes it from DB', function () {
    $cat = ReferenceCategory::create(['name' => 'Delete', 'key' => 'DEL']);
    Volt::test('settings.variables')->call('deleteCategory', $cat->id);
    expect(ReferenceCategory::count())->toBe(0);
});

// B. Item Management (9-16)
test('9. selecting a category sets selectedCategoryKey', function () {
    ReferenceCategory::create(['name' => 'Test', 'key' => 'TEST']);
    Volt::test('settings.variables')->call('selectCategory', 'TEST')->assertSet('selectedCategoryKey', 'TEST');
});

test('10. can create a reference item in category', function () {
    ReferenceCategory::create(['name' => 'G', 'key' => 'GENDER']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'GENDER')->set('key', 'MALE')->set('display_label', 'Male')->set('selectedColor', 'blue')->call('saveItem')->assertHasNoErrors();
    expect(ReferenceItem::where('key', 'MALE')->exists())->toBeTrue();
});

test('11. item key is required', function () {
    Volt::test('settings.variables')->set('key', '')->call('saveItem')->assertHasErrors(['key' => 'required']);
});

test('12. item display_label is required', function () {
    Volt::test('settings.variables')->set('display_label', '')->call('saveItem')->assertHasErrors(['display_label' => 'required']);
});

test('13. item color metadata can be saved', function () {
    ReferenceCategory::create(['name' => 'C', 'key' => 'COLOR']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'COLOR')->set('key', 'RED')->set('display_label', 'Red')->set('selectedColor', 'red-500')->call('saveItem');
    expect(ReferenceItem::where('key', 'RED')->first()->metadata['color'])->toBe('red-500');
});

test('14. editing item loads its data', function () {
    ReferenceCategory::create(['name' => 'G', 'key' => 'GENDER']);
    $item = ReferenceItem::create(['category_key' => 'GENDER', 'key' => 'M', 'display_label' => 'Male']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'GENDER')->call('editItem', $item->id)->assertSet('display_label', 'Male');
});

test('15. deleting item removes it from DB', function () {
    ReferenceCategory::create(['name' => 'G', 'key' => 'GENDER']);
    $item = ReferenceItem::create(['category_key' => 'GENDER', 'key' => 'M', 'display_label' => 'Male']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'GENDER')->call('deleteItem', $item->id);
    expect(ReferenceItem::count())->toBe(0);
});

test('16. marking item as default works', function () {
    ReferenceCategory::create(['name' => 'S', 'key' => 'STATUS']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'STATUS')->set('key', 'A')->set('display_label', 'Active')->set('is_default', true)->set('selectedColor', 'blue')->call('saveItem');
    expect(ReferenceItem::where('key', 'A')->first()->is_default)->toBeTrue();
});

// C. UI & UX (17-21)
test('17. category counts are displayed in sidebar', function () {
    $cat = ReferenceCategory::create(['name' => 'Fruit', 'key' => 'FRUIT']);
    ReferenceItem::create(['category_key' => 'FRUIT', 'key' => 'APPLE', 'display_label' => 'Apple']);
    Volt::test('settings.variables')->assertSee('1'); // Just check if the count '1' is seen in categories list
});

test('18. move item up changes sort order', function () {
    ReferenceCategory::create(['name' => 'S', 'key' => 'SORT']);
    $i1 = ReferenceItem::create(['category_key' => 'SORT', 'key' => '1', 'display_label' => 'A', 'sort_order' => 1]);
    $i2 = ReferenceItem::create(['category_key' => 'SORT', 'key' => '2', 'display_label' => 'B', 'sort_order' => 2]);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'SORT')->call('moveItemUp', $i2->id);
    expect($i2->fresh()->sort_order)->toBe(1);
});

test('19. move item down changes sort order', function () {
    ReferenceCategory::create(['name' => 'S', 'key' => 'SORT']);
    $i1 = ReferenceItem::create(['category_key' => 'SORT', 'key' => '1', 'display_label' => 'A', 'sort_order' => 1]);
    $i2 = ReferenceItem::create(['category_key' => 'SORT', 'key' => '2', 'display_label' => 'B', 'sort_order' => 2]);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'SORT')->call('moveItemDown', $i1->id);
    expect($i1->fresh()->sort_order)->toBe(2);
});

test('20. saving category works', function () {
    Volt::test('settings.variables')->set('categoryName', 'T')->set('categoryKey', 'T')->call('saveCategory')->assertHasNoErrors();
});

test('21. saving item works', function () {
    ReferenceCategory::create(['name' => 'T', 'key' => 'T']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'T')->set('key', 'I')->set('display_label', 'L')->set('selectedColor', 'blue')->call('saveItem')->assertHasNoErrors();
});

// D. Edge Cases (22-25)
test('22. category key must be uppercase (automatically converted)', function () {
    Volt::test('settings.variables')->set('categoryName', 'Lower')->set('categoryKey', 'lower')->call('saveCategory');
    expect(ReferenceCategory::where('key', 'LOWER')->exists())->toBeTrue();
});

test('23. item key must be unique within category', function () {
    ReferenceCategory::create(['name' => 'G', 'key' => 'GENDER']);
    ReferenceItem::create(['category_key' => 'GENDER', 'key' => 'MALE', 'display_label' => 'Male']);
    Volt::test('settings.variables')->set('selectedCategoryKey', 'GENDER')->set('key', 'MALE')->set('display_label', 'Duplicate')->call('saveItem')->assertHasErrors(['key']);
});

test('24. searching with no results shows empty message', function () {
    Volt::test('settings.variables')->set('search', 'NOTFOUND')->assertSee('Kategori bulunamadı.');
});

test('25. available colors list is populated from service', function () {
    expect(count(app(ReferenceDataService::class)->getColorSchemes()))->toBeGreaterThan(0);
});
