<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Müşteri Yönetimi
Route::get('dashboard/customers', function () {
    if (!request()->has('tab')) {
        return redirect()->to(url()->current() . '?tab=customers');
    }
    return view('customers.index');
})->middleware(['auth', 'verified'])->name('customers.index');

// Ayarlar
// Ayarlar
Volt::route('dashboard/settings', 'settings.index')
    ->middleware(['auth', 'verified'])
    ->name('settings.index');

Volt::route('dashboard/settings/storage', 'settings.storage')
    ->middleware(['auth', 'verified'])
    ->name('settings.storage');

Volt::route('dashboard/settings/panel', 'settings.panel')
    ->middleware(['auth', 'verified'])
    ->name('settings.panel');

Volt::route('dashboard/settings/profile', 'settings.profile')
    ->middleware(['auth', 'verified'])
    ->name('settings.profile');

Volt::route('dashboard/settings/variables', 'variables.index')
    ->middleware(['auth', 'verified'])
    ->name('settings.variables');

Volt::route('dashboard/settings/mail', 'settings.mail')
    ->middleware(['auth', 'verified'])
    ->name('settings.mail');




Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
