<?php

use App\Models\User;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

beforeEach(function () {
    // Config'i doğru şekilde set et
    config([
        'fortify.features' => [
            Features::twoFactorAuthentication([
                'confirm' => true,
                'confirmPassword' => true,
            ]),
        ]
    ]);
});

test('two factor settings page can be rendered', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertOk()
        ->assertSee('İki Faktörlü Doğrulama')
        ->assertSee('Devre Dışı');
});

test('two factor settings page is accessible without password confirmation', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->actingAs($user)
        ->get(route('two-factor.show'));

    // password.confirm middleware kaldırıldı, direkt erişim mümkün
    $response->assertOk();
});

test('two factor settings page returns forbidden response when two factor is disabled', function () {
    config(['fortify.features' => []]);

    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'));

    $response->assertForbidden();
});


/**
 * 🛡️ ZIRHLI TEST: Secret Verification & State Persistence
 * Scenario: Kullanıcı 2FA'yi etkinleştirdiğinde veritabanı mühürlenmeli (secret set edilmeli).
 */
test('user can enable two factor authentication (generates secrets)', function () {
    // Clean user
    $user = User::factory()->withoutTwoFactor()->create();
    $this->actingAs($user);

    $component = Volt::test('settings.two-factor')
        ->call('enable');

    // 1. UI Check: Modal açılmalı
    $component->assertSet('showModal', true)
        ->assertSet('showVerificationStep', false);

    // 2. State Check: QR Code check handled in another test.

    // 3. DB Check (State Persistence)
    $user->refresh();
    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->not->toBeNull();
    // Confirmed At MUST BE NULL because we enabled confirmation
    expect($user->two_factor_confirmed_at)->toBeNull();
});

/**
 * 🛡️ ZIRHLI TEST: UI Feedback & QR Code
 * Scenario: QR Kod arayüzde SVG olarak render edilmeli.
 */
test('ui displays qr code and manual key when enabled', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $this->actingAs($user);

    Volt::test('settings.two-factor')
        ->call('enable')
        ->assertSet('showModal', true)
        ->assertSeeHtml('<svg'); // QR code presence
});

/**
 * 🛡️ ZIRHLI TEST: Recovery Codes (Burn-Once Logic Simulation)
 * Scenario: 2FA aktif edildiğinde kurtarma kodları üretilmeli (encrypted).
 */
test('recovery codes are generated on enable', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $this->actingAs($user);

    Volt::test('settings.two-factor')
        ->call('enable');

    $user->refresh();
    // Decrypt success proves valid encryption
    $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    expect($codes)->toBeArray()
        ->and(count($codes))->toBeGreaterThan(0);
});

/**
 * 🛡️ ZIRHLI TEST: Secret Verification (Invalid Code)
 * Scenario: Yanlış kod girildiğinde doğrulama reddedilmeli.
 */
test('confirmation fails with invalid code', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $this->actingAs($user);

    $component = Volt::test('settings.two-factor')
        ->call('enable')
        ->set('code', '000000') // Yanlış kod
        ->call('confirmTwoFactor');

    $component->assertHasErrors();

    $user->refresh();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

/**
 * 🛡️ ZIRHLI TEST: Disable Action & Database Cleanup
 * Scenario: 2FA devre dışı bırakıldığında tüm hassas veriler temizlenmeli.
 */
test('user can disable two factor authentication', function () {
    // Manually create an ENABLED user state (valid encrypted data)
    $user = User::factory()->withoutTwoFactor()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($user);

    // We do NOT set 'twoFactorEnabled' because it is locked.
    // Mount will pick up the state from DB.
    Volt::test('settings.two-factor')
        ->call('disable');

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

/**
 * 🛡️ ZIRHLI TEST: UI Feedback (Confirmation Success)
 * Scenario: Başarılı onayla UI 'Etkin' durumuna güncellenmeli.
 */
test('confirmation succeeds with mocked action', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $this->actingAs($user);

    // Mock the action to succeed
    $this->mock(Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication::class, function ($mock) {
        $mock->shouldReceive('__invoke')->once();
    });

    $component = Volt::test('settings.two-factor')
        ->call('enable')
        ->set('code', '123456')
        ->call('confirmTwoFactor');

    $component->assertSet('showModal', false)
        ->assertSet('twoFactorEnabled', true)
        ->assertSee('Etkin');
});

// Eksik test (T10)

test('regenerate recovery codes functionality works', function () {
    // Enabled 2FA user oluştur
    $user = User::factory()->withoutTwoFactor()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['old-code-1', 'old-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($user);

    // Eski kodları al
    $oldCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    // Yeni kodlar üret
    $component = Volt::test('settings.two-factor')
        ->call('regenerateRecoveryCodes');

    // Kullanıcıyı yenile ve yeni kodları kontrol et
    $user->refresh();
    $newCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    // Kodlar değişmiş olmalı
    expect($newCodes)->not->toBe($oldCodes);
    expect($newCodes)->toBeArray();
    expect(count($newCodes))->toBeGreaterThan(0);

    // UI'da başarı mesajı görünmeli
    $component->assertSee('Kurtarma kodları yenilendi');
});