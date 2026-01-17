<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Offer;
use App\Models\Customer;
use App\Models\OfferSection;
use App\Models\OfferItem;
use Livewire\Volt\Volt;

beforeEach(function () {
    // Admin yetkisi olan bir kullanıcı oluştur
    $this->user = User::factory()->create([
        'role_id' => Role::firstOrCreate(['name' => 'admin'])->id
    ]);
    $this->user->givePermissionTo('offers.view'); // Eğer yetki kontrolü varsa
});

test('pdf preview page renders correctly', function () {
    // İlgili modellerin factory'si yoksa manuel oluşturmak veya factory oluşturmak gerekebilir. 
    // Mevcut OfferFactory var, ancak Section ve Item için kontrol edelim.
    // Eğer Section/Item factory yoksa create() içinde relation kullanacağız veya factory oluşturacağız.
    // Şimdilik basitçe Offer üzerinden gidelim, section mantığını manuel ekleyelim.

    $customer = Customer::factory()->create();
    $offer = Offer::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'Test Teklifi',
        'currency' => 'USD',
        'vat_rate' => 20
    ]);

    // Section ve Item ekle (Model üzerinden manuel, factory yoksa)
    // Model yapısını tam bilmediğimiz için create metodlarına güveniyoruz.
    // Offer -> hasMany Sections -> hasMany Items

    /* 
      Projenin mevcut yapısında OfferSection ve OfferItem modellerinin factory'si yok gibi görünüyor.
      Bu yüzden manuel oluşturacağız.
    */

    $section = $offer->sections()->create([
        'title' => 'Web Geliştirme',
        'description' => 'Frontend ve Backend işleri',
        'sort_order' => 1
    ]);

    $item = $section->items()->create([
        'offer_id' => $offer->id, // offer_id zorunlu
        'service_name' => 'Homepage Design',
        'description' => 'Test Description',
        'price' => 1000,
        'quantity' => 1,
        'currency' => 'USD',
        'duration' => 1,
        'type' => 'service', // Varsayım
        'sort_order' => 1
    ]);

    $this->actingAs($this->user)
        ->get(route('offers.pdf.preview', $offer))
        ->assertOk()
        ->assertSee('Test Teklifi')
        ->assertSee('Web Geliştirme')
        ->assertSee('Homepage Design')
        ->assertSee('1.000'); // Fiyat formatı
});

test('calculations are correct in mount state', function () {
    $customer = Customer::factory()->create();
    $offer = Offer::factory()->create([
        'customer_id' => $customer->id,
        'currency' => 'EUR',
        'vat_rate' => 20
    ]);

    $section = $offer->sections()->create(['title' => 'Section 1', 'sort_order' => 1]);

    // 2 adet item ekleyelim: 100 * 2 = 200 EUR
    $section->items()->create(['offer_id' => $offer->id, 'service_name' => 'Item 1', 'description' => 'Desc 1', 'price' => 100, 'quantity' => 2, 'currency' => 'EUR', 'duration' => 1, 'sort_order' => 1]);
    // 1 adet item: 500 * 1 = 500 EUR
    $section->items()->create(['offer_id' => $offer->id, 'service_name' => 'Item 2', 'description' => 'Desc 2', 'price' => 500, 'quantity' => 1, 'currency' => 'EUR', 'duration' => 1, 'sort_order' => 2]);

    // Toplam Subtotal: 700 EUR
    // KDV (%20): 140 EUR
    // Genel Toplam: 840 EUR

    $component = Volt::test('customers.offers.pdf-preview', ['offer' => $offer]);

    // $sections array yapısını kontrol et
    $sections = $component->get('sections');

    expect($sections)->toBeArray()
        ->and($sections[0]['title'])->toBe('Section 1')
        ->and($sections[0]['subtotal'])->toEqual(700)
        ->and($sections[0]['vat_amount'])->toEqual(140)
        ->and($sections[0]['total_with_vat'])->toEqual(840);

    $component->assertSee('700')
        ->assertSee('140')
        ->assertSee('840');
});

test('partial components do not break on missing data', function () {
    // İçi boş bir teklif (Section/Item yok)
    $customer = Customer::factory()->create();
    $offer = Offer::factory()->create(['customer_id' => $customer->id]);

    Volt::test('customers.offers.pdf-preview', ['offer' => $offer])
        ->assertOk()
        ->assertSee($customer->name) // Header çalışıyor mu?
        ->assertSee('Yönetici Özeti'); // Summary partial çalışıyor mu?
    // Items partial döngüye girmediği için hata vermemeli
});
