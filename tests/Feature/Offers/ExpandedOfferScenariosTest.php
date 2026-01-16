<?php

use App\Models\Asset;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\PriceDefinition;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Volt\Volt;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\MinioService;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    seedReferenceData();
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('offers.create');
    $this->user->givePermissionTo('offers.edit');
    $this->user->givePermissionTo('offers.delete');
    $this->user->givePermissionTo('offers.view');
    actingAs($this->user);

    $this->customer = Customer::create([
        'name' => 'Test Customer',
        'id' => Str::uuid()->toString(),
    ]);
});

test('it saves download settings correctly', function () {
    $component = Volt::test('modals.offer-form')
        ->set('customer_id', $this->customer->id)
        ->set('title', 'Settings Test')
        ->set('valid_until', now()->addDays(30))
        ->set('sections', [['title' => 'S1', 'items' => [['service_name' => 'I1', 'price' => 10, 'quantity' => 1, 'description' => '', 'currency' => 'USD', 'duration' => 1]]]])
        // Set new settings
        ->set('is_pdf_downloadable', false)
        ->set('is_attachments_downloadable', false)
        ->set('is_downloadable_after_expiry', true);

    $component->call('save');

    $offer = Offer::where('title', 'Settings Test')->first();
    expect($offer)->not->toBeNull();
    expect($offer->is_pdf_downloadable)->toBeFalse();
    expect($offer->is_attachments_downloadable)->toBeFalse();
    expect($offer->is_downloadable_after_expiry)->toBeTrue();
});

test('it can add and remove sections', function () {
    $component = Volt::test('modals.offer-form');

    // Initial state: 1 section
    expect($component->get('sections'))->toHaveCount(1);

    // Add 2 more sections
    $component->call('addSection');
    $component->call('addSection');
    expect($component->get('sections'))->toHaveCount(3);

    // Check titles of new sections
    $sections = $component->get('sections');
    expect($sections[1]['title'])->toBe('Teklif Bölümü - 2');
    expect($sections[2]['title'])->toBe('Teklif Bölümü - 3');

    // Remove middle section (index 1)
    $component->call('removeSection', 1);
    expect($component->get('sections'))->toHaveCount(2);

    // Check re-indexing not necessarily strict on titles but array values
    $sections = $component->get('sections');
    // The last one typically shifts up or just re-indexed
    expect($sections)->toHaveCount(2);
});

test('it prevents removing the last section', function () {
    $component = Volt::test('modals.offer-form');

    // Starts with 1 section
    $component->call('removeSection', 0);

    // Should still have 1
    expect($component->get('sections'))->toHaveCount(1);
    // Should have error or warning toast (hard to test toast, but count is key)
});

test('integration: full offer creation with sections and settings', function () {
    // Mock Minio for attachment during save if needed, but we don't upload here

    $component = Volt::test('modals.offer-form')
        ->set('customer_id', $this->customer->id)
        ->set('title', 'Full Integration Test')
        ->set('valid_until', now()->addDays(15))
        ->set('is_pdf_downloadable', true)
        ->set('is_downloadable_after_expiry', true)
        // Section 1
        ->set('sections', [
            [
                'title' => 'Design Phase',
                'description' => 'Initial designs',
                'items' => [
                    ['service_name' => 'Logo', 'price' => 500, 'quantity' => 1, 'currency' => 'USD', 'duration' => 1, 'description' => '']
                ]
            ]
        ]);

    // Add Section 2 via method
    $component->call('addSection');

    // Manually populate Section 2
    $sections = $component->get('sections');
    $sections[1]['title'] = 'Dev Phase';
    // Ensure all keys are present
    $sections[1]['items'][] = ['service_name' => 'Coding', 'price' => 1000, 'quantity' => 1, 'currency' => 'USD', 'duration' => 1, 'description' => ''];

    $component->set('sections', $sections); // Push back changes

    $component->call('save');

    $offer = Offer::where('title', 'Full Integration Test')->with('sections.items')->first();

    expect($offer)->not->toBeNull();
    expect($offer->sections)->toHaveCount(2);
    expect($offer->sections[0]->title)->toBe('Design Phase');
    expect($offer->sections[1]->title)->toBe('Dev Phase');
    expect($offer->is_downloadable_after_expiry)->toBeTrue();
    expect($offer->total_amount)->toEqual(1500 * 1.20); // 1500 + 20% VAT
});

test('it handles attachment logic correctly', function () {
    Storage::fake('minio');
    $file = UploadedFile::fake()->create('specs.pdf', 1000, 'application/pdf');

    $minioMock = Mockery::mock(MinioService::class);
    $minioMock->shouldReceive('uploadFile')->andReturn([
        'path' => 'offers/specs.pdf',
        'url' => 'http://minio/offers/specs.pdf',
    ]);
    $this->app->instance(MinioService::class, $minioMock);

    $component = Volt::test('modals.offer-form')
        ->set('attachmentTitle', 'Specs')
        ->set('attachmentPrice', 0)
        ->set('attachmentFile', $file)
        ->call('saveAttachment');

    $attachments = $component->get('attachments');
    expect($attachments)->toHaveCount(1);
    expect($attachments[0]['title'])->toBe('Specs');
});
