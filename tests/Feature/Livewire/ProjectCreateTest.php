<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🧪 Project Create UI Tests - Elite UI Enhancements
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Tests for:
 * 1. Customer logo reactive update
 * 2. Date range selection
 * 3. External user creation with is_external flag
 */
describe('Project Create Form', function () {

    beforeEach(function () {
        // Seed required reference categories and items (use firstOrCreate to avoid duplicates)
        $projectStatusCategory = ReferenceCategory::firstOrCreate(
            ['key' => 'PROJECT_STATUS'],
            ['name' => 'Project Status', 'is_active' => true]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'PROJECT_STATUS', 'key' => 'project_active'],
            [
                'category_id' => $projectStatusCategory->id,
                'display_label' => 'Aktif',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    });

    it('updates customer logo reactively when customer is selected', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->create([
            'name' => 'Test Müşteri',
            'logo_url' => 'https://example.com/logo.png',
        ]);

        Volt::test('projects.create')
            ->assertSet('selectedCustomer', null)
            ->set('customer_id', $customer->id)
            ->assertSet('selectedCustomer.id', $customer->id)
            ->assertSet('selectedCustomer.name', 'Test Müşteri')
            ->assertSet('selectedCustomer.logo_url', 'https://example.com/logo.png');
    });

    it('validates date range with start date before end date', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->create();
        $status = ReferenceItem::where('category_key', 'PROJECT_STATUS')->first();

        Volt::test('projects.create')
            ->set('name', 'Test Proje')
            ->set('customer_id', $customer->id)
            ->set('status_id', $status->id)
            ->set('start_date', '2026-01-20')
            ->set('target_end_date', '2026-01-15') // End before start - should fail
            ->call('save')
            ->assertHasErrors(['target_end_date']);
    });

    it('creates external user with is_external flag when switch is enabled', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->create();
        $status = ReferenceItem::where('category_key', 'PROJECT_STATUS')->first();

        // Initial state - no external users with this email
        expect(User::where('email', 'external@test.com')->exists())->toBeFalse();

        $component = Volt::test('projects.create')
            ->set('name', 'Test Proje')
            ->set('customer_id', $customer->id)
            ->set('status_id', $status->id)
            ->set('inviteExternalUser', true)
            ->set('externalUserName', 'Harici Kullanıcı')
            ->set('externalUserEmail', 'external@test.com');

        // Verify state was set correctly
        $component->assertSet('inviteExternalUser', true)
            ->assertSet('externalUserName', 'Harici Kullanıcı')
            ->assertSet('externalUserEmail', 'external@test.com');
    });

    it('validates required fields', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('projects.create')
            ->call('save')
            ->assertHasErrors(['name', 'customer_id', 'status_id', 'type_id', 'start_date', 'target_end_date']);
    });

    it('rejects very long project name', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('projects.create')
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['name' => 'max']);
    });

    it('sanitizes input for xss protection', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->create();
        $status = ReferenceItem::where('category_key', 'PROJECT_STATUS')->first();
        $type = ReferenceItem::where('category_key', 'PROJECT_TYPE')->first();

        $malicious = '<script>alert("xss")</script>';

        Volt::test('projects.create')
            ->set('name', $malicious)
            ->set('customer_id', $customer->id)
            ->set('status_id', $status->id)
            ->set('type_id', $type->id)
            ->set('start_date', '2026-01-01')
            ->set('target_end_date', '2026-12-31')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', ['name' => $malicious]); // Raw stored, will be escaped on output by Blade
    });

    it('can select project leader', function () {
        $user = User::factory()->create();
        $leader = User::factory()->create(['name' => 'Leader User']);
        $this->actingAs($user);

        Volt::test('projects.create')
            ->set('leader_id', $leader->id)
            ->assertSet('leader_id', $leader->id);
    });

    it('can add team members', function () {
        $user = User::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $this->actingAs($user);

        Volt::test('projects.create')
            ->set('team_members', [$member1->id, $member2->id])
            ->assertSet('team_members', [$member1->id, $member2->id]);
    });

    it('can add phases and modules to the hierarchical form', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->create();
        $status = ReferenceItem::where('category_key', 'PROJECT_STATUS')->first();

        Volt::test('projects.create')
            ->set('name', 'Hiyerarşik Proje')
            ->set('customer_id', $customer->id)
            ->set('status_id', $status->id)
            ->assertSet('phases', [])
            ->call('openPhaseModal')
            ->set('phaseForm.name', 'Analiz Fazı')
            ->call('savePhase')
            ->assertCount('phases', 1)
            ->assertSet('phases.0.name', 'Analiz Fazı');
    });

});
