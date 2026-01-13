<?php

use App\Models\Offer;
use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\PanelSetting;
use Illuminate\Support\Str;
use Livewire\Volt\Volt;
use App\Mail\NewOfferRequestMail;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Ensure settings exist
    PanelSetting::create([
        'is_active' => true,
    ]);
});

test('public offer page can be viewed with valid token', function () {
    $offer = Offer::factory()->create([
        'tracking_token' => Str::uuid(),
        'valid_until' => now()->addDays(10),
        'is_pdf_downloadable' => true
    ]);

    $response = $this->get(route('offer.download', $offer->tracking_token));

    $response->assertStatus(200);
    $response->assertSeeLivewire('public.offer-download');
    $response->assertSee($offer->number);
});

test('public offer page returns 404 with invalid token', function () {
    $response = $this->get(route('offer.download', 'invalid-token'));

    $response->assertStatus(404);
});

test('it shows expiry message when offer is expired and blocking is enabled', function () {
    $offer = Offer::factory()->create([
        'tracking_token' => Str::uuid(),
        'valid_until' => now()->subDays(1),
        'is_downloadable_after_expiry' => false
    ]);

    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->assertSet('isExpired', true)
        ->assertSet('isBlocked', true)
        ->assertSee('Teklif Geçerlilik Süresi Doldu');
});

test('it allows download when expired if blocking is disabled', function () {
    $offer = Offer::factory()->create([
        'tracking_token' => Str::uuid(),
        'valid_until' => now()->subDays(1),
        'is_downloadable_after_expiry' => true
    ]);

    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->assertSet('isExpired', true)
        ->assertSet('isBlocked', false)
        ->assertSee('Teklifiniz Hazır');
});

test('it sends new offer request email', function () {
    Mail::fake();

    // Create an admin user to receive the mail
    $role = \App\Models\Role::firstOrCreate(['name' => 'admin', 'description' => 'Admin Role']);
    User::factory()->create(['email' => 'admin@example.com', 'role_id' => $role->id]);

    $offer = Offer::factory()->create([
        'tracking_token' => Str::uuid(),
        'valid_until' => now()->subDays(1),
        'is_downloadable_after_expiry' => false
    ]);

    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->set('company_name', 'Test Company')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->call('requestNewOffer');

    Mail::assertSent(NewOfferRequestMail::class, function ($mail) use ($offer) {
        return $mail->offer->id === $offer->id &&
            $mail->data['company_name'] === 'Test Company';
    });
});

test('it allows downloading attachments', function () {
    Storage::fake('s3');

    $offer = Offer::factory()->create([
        'tracking_token' => Str::uuid(),
        'valid_until' => now()->addDays(10),
        'is_attachments_downloadable' => true
    ]);

    $attachment = \App\Models\OfferAttachment::create([
        'offer_id' => $offer->id,
        'file_name' => 'test.pdf',
        'file_path' => 'offers/test.pdf',
        'file_size' => 1024,
        'title' => 'Test Attachment',
        'file_type' => 'OTHER'
    ]);

    // Mock MinioService
    $minioMock = Mockery::mock(\App\Services\MinioService::class);
    $minioMock->shouldReceive('downloadFile')
        ->with('offers/test.pdf', 'test.pdf')
        ->andReturn(response()->streamDownload(function () {
            echo 'content';
        }, 'test.pdf'));

    $this->app->instance(\App\Services\MinioService::class, $minioMock);

    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->call('downloadAttachment', $attachment->id)
        ->assertHasNoErrors();
});
