<?php

use App\Models\User;
use App\Models\PanelSetting;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// A. Typography Component
test('typography component renders and defaults set', function () {
    Volt::test('settings.theme.typography')
        ->assertSet('font_family', 'Inter')
        ->assertSet('heading_font_size', 18);
});

test('typography component saves correctly', function () {
    Volt::test('settings.theme.typography')
        ->set('font_family', 'Roboto')
        ->set('heading_font_size', 24)
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->font_family)->toBe('Roboto');
});

// B. Inputs Component
test('inputs component renders and defaults set', function () {
    Volt::test('settings.theme.inputs')
        ->assertSet('input_focus_ring_color', '#6366f1')
        ->assertSet('label_font_size', 14);
});

test('inputs component saves correctly', function () {
    Volt::test('settings.theme.inputs')
        ->set('input_focus_ring_color', '#000000')
        ->set('label_font_size', 16)
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->input_focus_ring_color)->toBe('#000000');
});

// C. Buttons Component
test('buttons component renders and defaults set', function () {
    Volt::test('settings.theme.buttons')
        ->assertSet('btn_create_bg_color', '#4f46e5');
});

test('buttons component saves correctly', function () {
    Volt::test('settings.theme.buttons')
        ->set('btn_create_bg_color', '#aabbcc')
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->btn_create_bg_color)->toBe('#aabbcc');
});

// D. Cards Component
test('cards component renders and defaults set', function () {
    Volt::test('settings.theme.cards')
        ->assertSet('card_bg_color', '#eff4ff');
});

test('cards component saves correctly', function () {
    Volt::test('settings.theme.cards')
        ->set('card_bg_color', '#ffffff')
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->card_bg_color)->toBe('#ffffff');
});

// E. Header Component
test('header component renders and defaults set', function () {
    Volt::test('settings.theme.header')
        ->assertSet('site_name', 'MEDIACLICK')
        ->assertSet('header_bg_color', '#3D3373');
});

test('header component saves correctly', function () {
    Volt::test('settings.theme.header')
        ->set('site_name', 'New Site Name')
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->site_name)->toBe('New Site Name');
});

// F. Events
test('saving dispatches theme-updated event', function () {
    Volt::test('settings.theme.typography')
        ->call('save')
        ->assertDispatched('theme-updated');
});
