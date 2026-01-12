<?php

use App\Models\PanelSetting;
use App\Models\User;
use App\Services\MinioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('settings.edit');
    $this->actingAs($this->user);
});

test('pdf template settings page is accessible', function () {
    $this->get(route('settings.pdf-template'))
        ->assertOk()
        ->assertSee('Teklif Şablonu');
});

test('can save pdf settings', function () {
    Volt::test('settings.pdf-template')
        ->set('pdf_font_family', 'Roboto')
        ->set('pdf_header_bg_color', '#121212')
        ->call('save')
        ->assertHasNoErrors();

    expect(PanelSetting::where('is_active', true)->first()->pdf_font_family)->toBe('Roboto');
});

test('can upload and remove pdf logo via MinioService', function () {
    $file = \Illuminate\Http\UploadedFile::fake()->image('logo.png');

    // Mock MinioService
    $this->mock(MinioService::class, function ($mock) {
        $mock->shouldReceive('uploadFile')
            ->once() // Expect ONE upload call
            ->with(\Mockery::any(), 'template') // Expect any file object (due to Livewire wrapping) and 'template' folder
            ->andReturn(['path' => 'template/logo-hash.png', 'url' => 'http://minio/template/logo-hash.png']);

        $mock->shouldReceive('deleteFile')
            ->once() // Expect ONE delete call
            ->with('template/logo-hash.png')
            ->andReturn(true);

        // getFileUrl might be called by the component mount or computed property, so we allow it
        $mock->shouldReceive('getFileUrl')->andReturn('http://minio/template/logo-hash.png');
    });

    // 1. Upload
    Volt::test('settings.pdf-template')
        ->set('pdf_logo', $file)
        ->call('save')
        ->assertHasNoErrors();

    $setting = PanelSetting::where('is_active', true)->first();
    expect($setting->pdf_logo_path)->toBe('template/logo-hash.png');

    // 2. Remove
    Volt::test('settings.pdf-template')
        ->call('removeLogo')
        ->assertHasNoErrors();

    $setting->refresh();
    expect($setting->pdf_logo_path)->toBeNull();
});
