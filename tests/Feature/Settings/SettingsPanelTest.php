<?php

use App\Models\User;
use App\Models\PanelSetting;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('settings.edit');
    $this->actingAs($this->user);
});

test('1. theme settings panel page is accessible', function () {
    $this->get(route('settings.panel'))->assertOk();
});

test('2. activeTab defaults to theme', function () {
    Volt::test('settings.panel')->assertSet('activeTab', 'theme');
});

test('3. switching activeTab to design works', function () {
    Volt::test('settings.panel')->set('activeTab', 'style-guide')->assertSet('activeTab', 'style-guide');
});

test('4. dashboard settings are persisted', function () {
    Volt::test('settings.panel')
        ->set('dashboard_stats_1_color', '#123456')
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::first()->dashboard_stats_1_color)->toBe('#123456');
});

test('5. reset works', function () {
    Volt::test('settings.panel')
        ->set('dashboard_stats_1_color', '#000000')
        ->call('resetToDefaults');

    expect(PanelSetting::first()->dashboard_stats_1_color)->toBe('#3b82f6'); // Default
});
