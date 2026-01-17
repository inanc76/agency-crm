<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Offer;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Admin User
    $this->admin = User::factory()->create([
        'role_id' => Role::firstOrCreate(['name' => 'admin'])->id
    ]);
    foreach (['projects.view', 'projects.create', 'offers.view', 'offers.create'] as $perm) {
        $this->admin->givePermissionTo($perm);
    }

    // Create Settings
    \App\Models\PanelSetting::create(['is_active' => true]);
});

test('End-to-End Flow: Project -> Offer -> Preview -> Public Download', function () {
    // 1. Create Project
    $customer = Customer::factory()->create();
    $project = Project::factory()->create([
        'customer_id' => $customer->id,
        'name' => 'E2E Test Project'
    ]);

    expect($project)->exists()->toBeTrue();

    // 2. Create Offer (Linked to Project if applicable, or just Customer)
    // Assuming Offer has project_id or just customer_id logic.
    // Based on previous files, Offer has customer_id. 
    // We will create an offer and associate logic if needed.

    $offer = Offer::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'E2E Offer',
        'is_pdf_downloadable' => true,
        'tracking_token' => \Illuminate\Support\Str::uuid(),
        'valid_until' => now()->addDays(7)
    ]);

    // Add Items
    $section = $offer->sections()->create(['title' => 'Main Section']);
    $section->items()->create([
        'offer_id' => $offer->id,
        'service_name' => 'E2E Service',
        'description' => 'E2E Description',
        'price' => 5000,
        'quantity' => 1,
        'currency' => 'USD',
        'duration' => 1,
        'sort_order' => 1
    ]);

    expect($offer->items)->count()->toBe(1);

    // 3. Check PDF Preview (Zeta)
    $this->actingAs($this->admin)
        ->get(route('offers.pdf.preview', $offer))
        ->assertOk()
        ->assertSee('E2E Offer')
        ->assertSee('5.000'); // Check price formatting

    // 4. Public Download (Eta) - As Guest
    auth()->logout();

    $this->assertGuest();

    $response = $this->get(route('offer.download', $offer->tracking_token));

    $response->assertOk()
        ->assertSeeLivewire('public.offer-download')
        ->assertSee('Teklifiniz Hazır')
        ->assertSee($offer->number);

    // 5. Trigger Download Action
    Volt::test('public.offer-download', ['token' => $offer->tracking_token])
        ->call('downloadPdf')
        ->assertHasNoErrors();

});
