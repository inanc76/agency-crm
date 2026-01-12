<?php

use App\Http\Controllers\MinioProxyController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Minio Proxy Route - serves files from Minio through Laravel
Route::get('storage/minio/{path}', [MinioProxyController::class, 'serve'])
    ->where('path', '.*')
    ->middleware(['auth'])
    ->name('minio.proxy');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Müşteri Yönetimi
Route::get('dashboard/customers', function () {
    if (! request()->has('tab')) {
        return redirect()->to(url()->current().'?tab=customers');
    }

    return view('customers.index');
})->middleware(['auth', 'verified', 'can:customers.view'])->name('customers.index');

Volt::route('dashboard/customers/create', 'customers.create')
    ->middleware(['auth', 'verified', 'can:customers.create'])
    ->name('customers.create');

Volt::route('dashboard/customers/{customer}', 'customers.create')
    ->middleware(['auth', 'verified', 'can:customers.view'])
    ->name('customers.edit');

// Kişi Yönetimi
Volt::route('dashboard/customers/contacts/create', 'customers.contacts.create')
    ->middleware(['auth', 'verified', 'can:customers.edit'])
    ->name('customers.contacts.create');

Volt::route('dashboard/customers/contacts/{contact}', 'customers.contacts.create')
    ->middleware(['auth', 'verified', 'can:customers.view'])
    ->name('customers.contacts.edit');

// Varlık Yönetimi
Volt::route('dashboard/customers/assets/create', 'customers.assets.create')
    ->middleware(['auth', 'verified', 'can:customers.edit'])
    ->name('customers.assets.create');

Volt::route('dashboard/customers/assets/{asset}', 'customers.assets.create')
    ->middleware(['auth', 'verified', 'can:customers.view'])
    ->name('customers.assets.edit');

// Hizmet Yönetimi
Volt::route('dashboard/customers/services/create', 'customers.services.create')
    ->middleware(['auth', 'verified', 'can:services.create'])
    ->name('customers.services.create');

Volt::route('dashboard/customers/services/{service}', 'customers.services.create')
    ->middleware(['auth', 'verified', 'can:services.view'])
    ->name('customers.services.edit');

// Teklif Yönetimi
Volt::route('dashboard/customers/offers/create', 'customers.offers.create')
    ->middleware(['auth', 'verified', 'can:offers.create'])
    ->name('customers.offers.create');

Volt::route('dashboard/customers/offers/{offer}', 'customers.offers.create')
    ->middleware(['auth', 'verified', 'can:offers.view'])
    ->name('customers.offers.edit');

// PDF Preview
Volt::route('dashboard/customers/offers/{offer}/pdf', 'customers.offers.pdf-preview')
    ->middleware(['auth', 'verified', 'can:offers.view'])
    ->name('offers.pdf.preview');

// Ayarlar
// Ayarlar
Volt::route('dashboard/settings', 'settings.index')
    ->middleware(['auth', 'verified', 'can:settings.view'])
    ->name('settings.index');

Volt::route('dashboard/settings/storage', 'settings.storage')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.storage');

Volt::route('dashboard/settings/panel', 'settings.panel')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.panel');

Volt::route('dashboard/settings/profile', 'settings.profile')
    ->middleware(['auth', 'verified'])
    ->name('settings.profile');

Volt::route('dashboard/settings/variables', 'variables.index')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.variables');

Volt::route('dashboard/settings/mail', 'settings.mail')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.mail');

Volt::route('dashboard/settings/prices', 'settings.prices')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.prices');

Volt::route('dashboard/settings/pdf-template', 'settings.pdf-template')
    ->middleware(['auth', 'verified', 'can:settings.edit'])
    ->name('settings.pdf-template');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

Volt::route('dashboard/settings/two-factor', 'settings.two-factor')
    ->middleware(['auth', 'verified'])
    ->name('two-factor.show');

// Debug route for testing
Route::get('debug/2fa', function () {
    if (! auth()->check()) {
        return 'User not authenticated';
    }

    $user = auth()->user();

    return [
        'user' => $user->email,
        'has_2fa' => $user->hasEnabledTwoFactorAuthentication(),
        'fortify_enabled' => Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()),
    ];
})->middleware(['auth']);
