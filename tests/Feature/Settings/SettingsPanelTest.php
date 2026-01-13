<?php

use App\Models\PanelSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('settings.edit');
    $this->user->givePermissionTo('settings.view');
    $this->actingAs($this->user);
});

// A. Base Page Tests
test('1. theme settings panel page is accessible', function () {
    $this->get(route('settings.panel'))->assertOk();
});

test('2. activeTab switches correctly', function () {
    Volt::test('settings.panel')
        ->set('activeTab', 'theme')
        ->assertSet('activeTab', 'theme');
});

// B. Header & Sidebar Component Tests
test('3. header settings persistence and validation', function () {
    $component = Volt::test('settings.theme.header')
        ->set('site_name', 'TEST_APP')
        ->set('header_bg_color', '#123456')
        ->set('header_border_width', 10)
        ->call('save')
        ->assertHasNoErrors();

    $settings = PanelSetting::first();
    expect($settings->site_name)->toBe('TEST_APP');
    expect($settings->header_bg_color)->toBe('#123456');
    expect($settings->header_border_width)->toBe(10);
});

test('4. header color validation (HEX regex)', function () {
    Volt::test('settings.theme.header')
        ->set('header_bg_color', 'invalid')
        ->call('save')
        ->assertHasErrors(['header_bg_color' => 'regex']);
});

test('5. header border width range validation', function () {
    Volt::test('settings.theme.header')
        ->set('header_border_width', 21)
        ->call('save')
        ->assertHasErrors(['header_border_width' => 'max']);
});

// C. Buttons Component Tests
test('6. button settings persistence', function () {
    Volt::test('settings.theme.buttons')
        ->set('btn_create_bg_color', '#ff0000')
        ->set('btn_save_bg_color', '#00ff00')
        ->call('save')
        ->assertHasNoErrors();

    $settings = PanelSetting::first();
    expect($settings->btn_create_bg_color)->toBe('#ff0000');
    expect($settings->btn_save_bg_color)->toBe('#00ff00');
});

// D. Typography Component Tests
test('7. typography settings persistence', function () {
    Volt::test('settings.theme.typography')
        ->set('font_family', 'Geist')
        ->set('heading_font_size', 24)
        ->call('save')
        ->assertHasNoErrors();

    $settings = PanelSetting::first();
    expect($settings->font_family)->toBe('Geist');
    expect($settings->heading_font_size)->toBe(24);
});

test('8. typography font size range validation', function () {
    Volt::test('settings.theme.typography')
        ->set('heading_font_size', 73)
        ->call('save')
        ->assertHasErrors(['heading_font_size' => 'max']);
});

// E. Global Actions & Cache
test('9. reset to defaults works across components', function () {
    // Set some custom values first
    Volt::test('settings.panel')
        ->set('dashboard_stats_1_color', '#999999')
        ->call('save');

    expect(PanelSetting::first()->dashboard_stats_1_color)->toBe('#999999');

    // Reset
    Volt::test('settings.panel')
        ->call('resetToDefaults');

    expect(PanelSetting::first()->dashboard_stats_1_color)->toBe('#3b82f6'); // Default
});

test('10. cache is cleared on save', function () {
    Cache::shouldReceive('forget')
        ->once()
        ->with('theme_settings');

    Volt::test('settings.panel')
        ->call('save');
});

test('11. theme update event is dispatched on save', function () {
    Volt::test('settings.theme.header')
        ->call('save')
        ->assertDispatched('theme-updated');
});

// F. Dashboard Stats (Panel Component)
test('12. dashboard stats colors persistence', function () {
    Volt::test('settings.panel')
        ->set('dashboard_stats_1_color', '#cc0000')
        ->set('dashboard_stats_2_color', '#00cc00')
        ->set('dashboard_stats_3_color', '#0000cc')
        ->call('save')
        ->assertHasNoErrors();

    $settings = PanelSetting::first();
    expect($settings->dashboard_stats_1_color)->toBe('#cc0000');
    expect($settings->dashboard_stats_2_color)->toBe('#00cc00');
    expect($settings->dashboard_stats_3_color)->toBe('#0000cc');
});

test('13. dashboard card colors persistence', function () {
    Volt::test('settings.panel')
        ->set('dashboard_card_bg_color', '#eeeeee')
        ->set('dashboard_card_text_color', '#333333')
        ->call('save')
        ->assertHasNoErrors();

    $settings = PanelSetting::first();
    expect($settings->dashboard_card_bg_color)->toBe('#eeeeee');
    expect($settings->dashboard_card_text_color)->toBe('#333333');
});
