<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🛡️ MİSYON SİGMA - KULLANICI FORM                                             ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Kullanıcı Ekleme/Düzenleme Sayfası                                                       ║
 * ║  🎯 ANA GÖREV: Kullanıcı CRUD işlemleri (Create/Read/Update/Delete)                                             ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Form Yönetimi: Kullanıcı bilgileri formu                                                                     ║
 * ║  • Mail Gönderimi: Hoş geldin maili gönderme                                                                    ║
 * ║  • 2FA Reset: Kullanıcının 2FA ayarlarını sıfırlama                                                            ║
 * ║  • Status Toggle: Kullanıcıyı aktif/pasif yapma                                                                 ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Role;
use App\Models\ReferenceItem;
use Mary\Traits\Toast;
use App\Livewire\Users\Traits\HasUserActions;

new
    #[Layout('components.layouts.app')]
    class extends Component {
    use Toast, WithFileUploads, HasUserActions;

    // User Data
    public ?User $user = null;
    public string $userId = '';
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $title = '';
    public string $password = '';
    public bool $sendPasswordEmail = true;
    public string $roleId = '';
    public ?string $departmentId = null;
    public $avatar = ''; // Stored path
    public $avatarFile; // Uploaded file

    // UI State
    public bool $isViewMode = false;
    public string $activeTab = 'info';

    public function mount(?User $user = null): void
    {
        $this->user = $user ?? new User;

        if ($this->user->exists) {
            $this->userId = $this->user->id;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->phone = $this->user->phone ?? '';
            $this->title = $this->user->title ?? '';
            $this->roleId = $this->user->role_id ?? '';
            $this->departmentId = $this->user->department_id;
            $this->avatar = $this->user->avatar ?? '';
            $this->isViewMode = true;
        }
    }

    public function with(): array
    {
        return [
            'roles' => Role::all(),
            'departments' => ReferenceItem::whereHas('category', function ($q) {
                $q->where('key', 'DEPARTMENT');
            })->get(),
        ];
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">

    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('users.index') }}"
            class="inline-flex items-center gap-2 text-[var(--color-text-base)] hover:text-[var(--color-text-heading)] mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Kullanıcı Listesi</span>
        </a>

        {{-- Header Section --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-heading)]">
                    @if($isViewMode)
                        {{ $name }}
                    @elseif($userId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Kullanıcı Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Kullanıcı</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $userId }}</span>
                        @if($user->status === 'active')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Pasif
                            </span>
                        @endif
                    @else
                        <p class="text-sm opacity-60">Yeni kullanıcı bilgilerini girin</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="resetTwoFactor"
                        wire:confirm="Bu kullanıcının 2FA ayarlarını sıfırlamak istediğinize emin misiniz?"
                        wire:key="btn-reset-2fa" class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-shield-exclamation" class="w-4 h-4" />
                        2FA Sıfırla
                    </button>
                    <button type="button" wire:click="toggleStatus" wire:key="btn-toggle-status"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        @if($user->status === 'active')
                            <x-mary-icon name="o-user-minus" class="w-4 h-4" />
                            Pasife Al
                        @else
                            <x-mary-icon name="o-user-plus" class="w-4 h-4" />
                            Aktif Et
                        @endif
                    </button>
                    <button type="button" wire:click="delete"
                        wire:confirm="Bu kullanıcıyı silmek istediğinize emin misiniz?" wire:key="btn-delete"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel" class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled" wire:key="btn-save"
                        class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($userId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Kullanıcı Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'activity')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'activity' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Aktivite Geçmişi
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout: 80% - 20% --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (80%) --}}
            <div class="col-span-8 space-y-6">
                @if($activeTab === 'info')
                    {{-- Personal Info Card --}}
                    @include('livewire.users.partials._personal-info-form')

                    {{-- Security Settings Card --}}
                    @include('livewire.users.partials._security-form')

                    {{-- Role & Department Settings Card --}}
                    @include('livewire.users.partials._permissions-form')

                @endif

                @if($activeTab === 'activity')
                    <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                        <x-mary-icon name="o-clock" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Aktivite geçmişi yakında eklenecek</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="col-span-4 space-y-6">
                {{-- Avatar Card --}}
                @include('livewire.users.partials._avatar-form')

                {{-- Registration Info Card --}}
                @include('livewire.users.partials._registration-info')
            </div>
        </div>
    </div>
</div>