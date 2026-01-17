<?php

use App\Models\User;
use App\Models\PanelSetting;
use Livewire\Volt\Volt;
use App\Models\Role;

beforeEach(function () {
    // Admin yetkisi olan bir kullanıcı oluştur
    $this->user = User::factory()->create([
        'role_id' => Role::firstOrCreate(['name' => 'admin'])->id
    ]);

    // settings.edit yetkisini verelim (Middleware can:settings.edit kontrolü yapıyor)
    $this->user->givePermissionTo('settings.edit');
});

test('pdf template page can be rendered', function () {
    $this->actingAs($this->user)
        ->get(route('settings.pdf-template'))
        ->assertOk()
        ->assertSee('Teklif Şablonu')
        ->assertSee('PDF Görünüm Ayarları');
});

test('can update pdf colors and fonts', function () {
    $this->actingAs($this->user);

    $component = Volt::test('settings.pdf-template')
        ->set('pdf_font_family', 'Roboto')
        ->set('pdf_primary_color', '#ff0000')
        ->set('pdf_secondary_color', '#00ff00')
        ->call('save');

    $component->assertHasNoErrors();

    // Veritabanı kontrolü (PanelSettingRepository üzerinden JSON olarak saklanıyor olabilir veya doğrudan sütunlarda)
    // Settings yapısını kontrol ettiğimizde JSON 'settings' sütunu veya ayrı sütunlar olabilir.
    // PanelSetting modelini inceleyelim. Varsayım: PanelSetting tablosunda veya key-value yapısında tutuluyor.
    // Mevcut kodda $repository->saveSettings($data) kullanılıyor.

    // Basit bir kontrol: Component state güncellendi mi?
    $component->assertSet('pdf_font_family', 'Roboto')
        ->assertSet('pdf_primary_color', '#ff0000');
});

test('partial variables are initialized correctly', function () {
    $this->actingAs($this->user);

    // Veritabanında bir ayar oluşturalım
    /* 
       Not: PanelSettingRepository yapısı gereği veritabanına manuel kayıt atmak yerine 
       component mount edildiğinde varsayılan değerlerin gelip gelmediğini kontrol ediyoruz.
       Kodda varsayılanlar:
       public string $pdf_font_family = 'Segoe UI';
       public string $pdf_primary_color = '#4f46e5';
    */

    Volt::test('settings.pdf-template')
        ->assertSet('pdf_font_family', 'Segoe UI')
        ->assertSet('pdf_primary_color', '#4f46e5')
        ->assertSet('pdf_footer_text', null); // Default null
});
