<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Offer;
use Illuminate\Support\Facades\Schema;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  🧪 TEST: Destruction & Recovery Scenarios                                                                       ║
 * ║  🎯 AMAÇ: Soft Deletes ve Blameable mekanizmalarının "Silme" ve "Geri Yükleme" döngüsünü doğrulamak.           ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

beforeEach(function () {
    seedReferenceData();
});

test('User Destruction & Recovery Cycle', function () {
    // 1. Creation
    $admin = User::factory()->create();
    $victim = User::factory()->create();

    // 2. Destruction (Soft Delete with Blame)
    $this->actingAs($admin);
    $victim->delete();

    // 3. Verification - Destruction
    expect($victim->fresh()->deleted_at)->not->toBeNull()
        ->and($victim->fresh()->deleted_by)->toBe($admin->id)
        ->and(Schema::hasColumn('users', 'deleted_by'))->toBeTrue();

    // 4. Recovery
    $victim->restore();

    // 5. Verification - Recovery
    expect($victim->fresh()->deleted_at)->toBeNull()
        ->and($victim->fresh()->exists)->toBeTrue();
});

test('Customer Destruction & Recovery Cycle', function () {
    $admin = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($admin);
    $customer->delete();

    expect($customer->fresh()->deleted_at)->not->toBeNull()
        ->and($customer->fresh()->deleted_by)->toBe($admin->id);

    // Helper method check from HasBlameable
    expect($customer->isDeleted())->toBeTrue();
    expect($customer->deletedBy->id)->toBe($admin->id);

    $customer->restore();

    expect($customer->fresh()->deleted_at)->toBeNull()
        ->and($customer->isDeleted())->toBeFalse();
});

test('Service Destruction & Recovery Cycle', function () {
    $admin = User::factory()->create();
    $service = Service::factory()->create();

    $this->actingAs($admin);
    $service->delete();

    expect($service->fresh()->deleted_at)->not->toBeNull()
        ->and($service->fresh()->deleted_by)->toBe($admin->id);

    $service->restore();

    expect($service->fresh()->deleted_at)->toBeNull();
});

test('Offer Destruction & Recovery Cycle', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin);
    $offer->delete();

    expect($offer->fresh()->deleted_at)->not->toBeNull()
        ->and($offer->fresh()->deleted_by)->toBe($admin->id);

    $offer->restore();

    expect($offer->fresh()->deleted_at)->toBeNull();
});

test('Blameable works without authenticated user (System Delete)', function () {
    $offer = Offer::factory()->create();

    // No auth
    $offer->delete();

    expect($offer->fresh()->deleted_at)->not->toBeNull()
        ->and($offer->fresh()->deleted_by)->toBeNull();
});
