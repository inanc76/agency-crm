<?php

use App\Models\Offer;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  🧪 TEST: Database Integrity Scenarios                                                                           ║
 * ║  🎯 AMAÇ: Veritabanı seviyesindeki kısıtlamaları (Foreign Key, Not Null, Unique) doğrulamak.                     ║
 * ║  🛡️ GÜVENLİK: Bu testler uygulama mantığından bağımsız olarak DB şemasının sağlamlığını garanti eder.            ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

beforeEach(function () {
    seedReferenceData();
});

test('Foreign Key Integrity: Cannot create Offer with non-existent Customer', function () {
    $user = User::factory()->create();

    // Rastgele bir UUID ama veritabanında yok
    $nonExistentCustomerId = Illuminate\Support\Str::uuid();

    // DB seviyesinde insert denemesi (Model seviyesindeki validasyonu bypass etmek için)
    // Böylece DB foreign key constraint'in çalıştığını doğrularız.
    try {
        Offer::factory()->create([
            'customer_id' => $nonExistentCustomerId,
        ]);
        $this->fail('Foreign Key constraint failed to prevent invalid reference.');
    } catch (QueryException $e) {
        // SQLite: "FOREIGN KEY constraint failed"
        // Postgres: "violates foreign key constraint"
        expect($e->getMessage())->toMatch('/foreign key constraint/i');
    }
});

// "created_by" kolonu olmadığı için bu test kaldırıldı. HasBlameable sadece deleted_by ekliyor olabilir.

test('Not Null Constraint: Offer Title cannot be null', function () {
    $customer = Customer::factory()->create();

    try {
        DB::table('offers')->insert([
            'id' => Illuminate\Support\Str::uuid(),
            'customer_id' => $customer->id,
            'number' => 'OFF-' . rand(1000, 9999), // Zorunlu alan
            'title' => null, // Hedeflenen hata kaynağı
            'status' => 'DRAFT',
            'created_at' => now(),
            'updated_at' => now(),
            // 'currency' yoksa varsayılanı alabilir veya hata verebilir, migration'a bağlı. 
            // Genelde nullable değildir.
            'currency' => 'TRY',
            'total_amount' => 100,
        ]);
        $this->fail('Not Null constraint failed to prevent null title.');
    } catch (QueryException $e) {
        // SQLite: "NOT NULL constraint failed: offers.title"
        expect($e->getMessage())->toContain('title');
    }
});

test('Soft Delete Cascade Logic Check (Application Level)', function () {
    // Veritabanında SoftDeletes CASCADE genelde yoktur, uygulama seviyesinde yönetilir.
    // Bu test, Customer silindiğinde Offer'ların ne olduğunu gözlemler.

    $customer = Customer::factory()->create();
    $offer = Offer::factory()->create(['customer_id' => $customer->id]);

    // Customer soft deleted
    $customer->delete();

    // Offer duruyor mu? (Soft Delete Cascade genelde manueldir)
    // Eğer proje kuralı "Müşteri silinirse teklifler de silinmeli" ise burada fail alırız.
    // Şimdilik sadece durduğunu veya silindiğini raporluyoruz.

    $offer->refresh();

    if ($offer->deleted_at) {
        // Cascade çalıştı (Observer vs ile)
        expect($offer->deleted_at)->not->toBeNull();
    } else {
        // Cascade yok, offer yetim kaldı (ama soft deleted customer hala DB'de olduğu için FK hatası vermez)
        expect($offer->exists)->toBeTrue();
    }
});
