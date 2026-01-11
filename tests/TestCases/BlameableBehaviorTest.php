<?php

use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  🧪 TEST: HasBlameable Trait                                                                                    ║
 * ║  🎯 AMAÇ: Soft delete yapıldığında deleted_by kolonunun doğru dolduğunu ve silinmediğini doğrulamak.           ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

beforeEach(function () {
    seedReferenceData();
});

test('it records who deleted the model on soft delete', function () {
    // 1. Arrange: Kullanıcı ve Teklif oluştur
    $user = User::factory()->create();
    $offer = Offer::factory()->create();

    // 2. Act: Kullanıcı olarak giriş yap ve teklifi sil
    $this->actingAs($user);
    $offer->delete();

    // 3. Assert: Soft deleted ve deleted_by dolu olmalı
    $offer->refresh();

    expect($offer->deleted_at)->not->toBeNull()
        ->and($offer->deleted_by)->toBe($user->id)
        ->and($offer->isDeleted())->toBeTrue()
        ->and($offer->deletedBy->id)->toBe($user->id);

    // Helper method check
    $info = $offer->getDeletionInfo();
    expect($info)->toContain($user->name);
    expect($info)->toContain('tarafından');
});

test('it leaves deleted_by null if not authenticated', function () {
    // 1. Arrange
    $offer = Offer::factory()->create();

    // 2. Act: Giriş yapmadan sil (CLI/Scheduler simülasyonu)
    $offer->delete();

    // 3. Assert
    $offer->refresh();
    expect($offer->deleted_at)->not->toBeNull()
        ->and($offer->deleted_by)->toBeNull();
});

test('it works on other models too (Service example)', function () {
    // 1. Arrange
    $user = User::factory()->create();
    $service = \App\Models\Service::factory()->create();

    // 2. Act
    $this->actingAs($user);
    $service->delete();

    // 3. Assert
    $service->refresh();
    expect($service->deleted_at)->not->toBeNull()
        ->and($service->deleted_by)->toBe($user->id);
});
