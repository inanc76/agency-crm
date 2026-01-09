<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{state};

state(['showUserMenu' => false]);

$toggleUserMenu = function () {
    $this->showUserMenu = !$this->showUserMenu;
};

$handleSignOut = function () {
    auth()->logout();
    return redirect('/login');
};

?>

<header class="bg-[var(--header-bg)] text-white h-[var(--header-height)] flex items-center shadow-sm">
    <!-- Logo Section - Left side -->
    <div class="flex items-center justify-center px-4 w-64">
        <h1 class="text-xl font-bold text-white text-center">MEDIACLICK</h1>
    </div>

    <!-- Navigation Menu - Oval Style -->
    <div class="flex items-center px-6">
        <div class="flex bg-white/10 backdrop-blur-sm rounded-full p-1.5 shadow-lg border border-white/20">
            <a href="/dashboard"
                class="flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-300 font-medium text-sm {{ request()->is('dashboard') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)] shadow-md transform scale-105' : 'text-white/90 hover:bg-white/15 hover:text-white hover:scale-102' }}">
                <span>Dashboard</span>
            </a>

            <a href="/dashboard/customers"
                class="flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-300 font-medium text-sm {{ request()->is('dashboard/customers*') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)] shadow-md transform scale-105' : 'text-white/90 hover:bg-white/15 hover:text-white hover:scale-102' }}">
                <span>Müşteriler</span>
            </a>

            <a href="/dashboard/settings"
                class="flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-300 font-medium text-sm {{ request()->is('dashboard/settings*') ? 'bg-[var(--header-active-bg)] text-[var(--header-active-text)] shadow-md transform scale-105' : 'text-white/90 hover:bg-white/15 hover:text-white hover:scale-102' }}">
                <span>Ayarlar</span>
            </a>
        </div>
    </div>

    <!-- Right side - User menu -->
    <div class="flex items-center space-x-4 ml-auto px-6">
        <!-- Notification Bell -->
        <button class="relative p-2 rounded-lg hover:bg-white/10 transition-colors">
            <x-heroicon-o-bell class="w-6 h-6 text-white" />
            <span class="absolute top-1 right-1 w-2 h-2 rounded-full"
                style="background-color: var(--notification-badge);"></span>
        </button>

        <!-- User menu -->
        <div class="relative">
            <button wire:click="toggleUserMenu"
                class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-white/10 transition-all duration-200">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-md"
                    style="background: linear-gradient(to bottom right, var(--avatar-gradient-start), var(--avatar-gradient-end));">
                    <span class="text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                </div>
                <div class="text-left hidden sm:block">
                    <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-white/60">Admin User</div>
                </div>
                @if ($showUserMenu)
                    <x-heroicon-o-chevron-up class="w-4 h-4 text-white/70" />
                @else
                    <x-heroicon-o-chevron-down class="w-4 h-4 text-white/70" />
                @endif
            </button>

            <!-- Dropdown menu -->
            @if ($showUserMenu)
                <div
                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-[var(--card-border)] py-2 z-50 overflow-hidden">
                    <!-- User Info Header -->
                    <div class="px-4 py-3 border-b border-[var(--card-border)]"
                        style="background: linear-gradient(to right, var(--dropdown-header-start), var(--dropdown-header-end));">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                style="background: linear-gradient(to bottom right, var(--avatar-gradient-start), var(--avatar-gradient-end));">
                                <span class="text-sm font-semibold text-white">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-[var(--color-text-heading)]">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-[var(--color-text-muted)]">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        <a href="/dashboard/settings/profile"
                            class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm text-[var(--color-text-base)] hover:bg-[var(--dropdown-hover-bg)] transition-colors">
                            <x-heroicon-o-user-circle class="w-5 h-5 text-[var(--color-text-muted)]" />
                            <span>Hesabım</span>
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="border-t border-[var(--card-border)] pt-1">
                        <button wire:click="handleSignOut"
                            class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm text-[var(--color-danger)] hover:bg-[var(--color-danger-muted)] transition-colors">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 text-[var(--color-danger)]" />
                            <span>Çıkış Yap</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Click outside to close menu -->
    @if ($showUserMenu)
        <div class="fixed inset-0 z-40" wire:click="toggleUserMenu"></div>
    @endif
</header>