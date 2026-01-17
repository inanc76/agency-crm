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
use App\Models\User;
use App\Models\Role;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

new 
#[Layout('components.layouts.app')]
class extends Component
{
    use Toast;

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
    public string $avatar = '';

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
            $this->avatar = $this->user->avatar ?? '';
            $this->isViewMode = true;
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = !$this->isViewMode;
    }

    public function cancel(): void
    {
        if ($this->user->exists) {
            $this->isViewMode = true;
            // Reset form to original values
            $this->mount($this->user);
        } else {
            $this->redirect(route('users.index'), navigate: true);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->userId ?: 'NULL'),
            'phone' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:255',
            'password' => $this->userId ? 'nullable|min:8' : ($this->sendPasswordEmail ? 'nullable' : 'required|min:8'),
        ]);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'title' => $this->title,
            'role_id' => $this->roleId ?: null,
            'avatar' => $this->avatar,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            // Update existing user
            $this->user->update($userData);
            $this->success('Başarılı', 'Kullanıcı güncellendi.');
            $this->isViewMode = true;
        } else {
            // Create new user
            if (!$this->password && $this->sendPasswordEmail) {
                // Generate temporary password for user creation
                $userData['password'] = Hash::make(Str::random(32));
            }
            
            $user = User::create($userData);
            
            if ($this->sendPasswordEmail && !$this->password) {
                // Send welcome email with setup link
                try {
                    $controller = new \App\Http\Controllers\UserSetupController();
                    $response = $controller->sendWelcomeEmail($user);
                    $responseData = json_decode($response->getContent(), true);
                    
                    if ($responseData['success']) {
                        $this->success('Başarılı', 'Kullanıcı oluşturuldu ve hoş geldin maili gönderildi.');
                    } else {
                        $this->warning('Uyarı', 'Kullanıcı oluşturuldu ancak mail gönderilemedi: ' . $responseData['message']);
                    }
                } catch (\Exception $e) {
                    $this->warning('Uyarı', 'Kullanıcı oluşturuldu ancak mail gönderilemedi: ' . $e->getMessage());
                }
            } else {
                $this->success('Başarılı', 'Kullanıcı oluşturuldu.');
            }

            $this->redirect(route('users.edit', $user), navigate: true);
        }
    }

    public function delete(): void
    {
        if (!$this->user->exists) {
            return;
        }

        $this->user->delete();
        $this->success('Başarılı', 'Kullanıcı silindi.');
        $this->redirect(route('users.index'), navigate: true);
    }

    public function resetTwoFactor(): void
    {
        if (!$this->user->exists) {
            return;
        }

        $this->user->resetTwoFactor();
        $this->success('Başarılı', $this->user->name . ' kullanıcısının 2FA ayarları sıfırlandı.');
    }

    public function toggleStatus(): void
    {
        if (!$this->user->exists) {
            return;
        }

        if ($this->user->status === 'active') {
            $this->user->deactivate();
            $this->success('Başarılı', $this->user->name . ' kullanıcısı pasife alındı.');
        } else {
            $this->user->activate();
            $this->success('Başarılı', $this->user->name . ' kullanıcısı aktif edildi.');
        }

        $this->user->refresh();
    }

    public function with(): array
    {
        return [
            'roles' => Role::all(),
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
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Kullanıcı</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $userId }}</span>
                        @if($user->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
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
                    <button type="button" wire:click="resetTwoFactor" wire:confirm="Bu kullanıcının 2FA ayarlarını sıfırlamak istediğinize emin misiniz?"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-shield-exclamation" class="w-4 h-4" />
                        2FA Sıfırla
                    </button>
                    <button type="button" wire:click="toggleStatus" 
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        @if($user->status === 'active')
                            <x-mary-icon name="o-user-minus" class="w-4 h-4" />
                            Pasife Al
                        @else
                            <x-mary-icon name="o-user-plus" class="w-4 h-4" />
                            Aktif Et
                        @endif
                    </button>
                    <button type="button" wire:click="delete" wire:confirm="Bu kullanıcıyı silmek istediğinize emin misiniz?"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
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
                    <div class="theme-card p-6 shadow-sm">
                        <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Kişisel Bilgiler</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($isViewMode)
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Ad Soyad</label>
                                    <div class="text-sm font-medium text-[var(--color-text-heading)]">{{ $name }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">E-posta</label>
                                    <div class="text-sm font-medium text-[var(--color-text-heading)]">{{ $email }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Telefon</label>
                                    <div class="text-sm font-medium text-[var(--color-text-heading)]">{{ $phone ?: '-' }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Unvan</label>
                                    <div class="text-sm font-medium text-[var(--color-text-heading)]">{{ $title ?: '-' }}</div>
                                </div>
                            @else
                                <x-mary-input 
                                    wire:model="name" 
                                    label="Ad Soyad *" 
                                    placeholder="Kullanıcının tam adı" />
                                
                                <x-mary-input 
                                    wire:model="email" 
                                    label="E-posta *" 
                                    type="email"
                                    placeholder="kullanici@example.com" />
                                
                                <x-mary-input 
                                    wire:model="phone" 
                                    label="Telefon" 
                                    placeholder="+90 555 123 45 67" />
                                
                                <x-mary-input 
                                    wire:model="title" 
                                    label="Unvan" 
                                    placeholder="Proje Müdürü" />
                            @endif
                        </div>
                    </div>

                    {{-- Security Settings Card --}}
                    @if(!$isViewMode)
                        <div class="theme-card p-6 shadow-sm">
                            <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Güvenlik Ayarları</h2>
                            @if(!$userId)
                                <div class="space-y-3">
                                    <x-mary-checkbox 
                                        wire:model.live="sendPasswordEmail" 
                                        label="Kullanıcıya şifre belirleme maili gönder" 
                                        class="checkbox-primary" />
                                    
                                    @if(!$sendPasswordEmail)
                                        <x-mary-input 
                                            wire:model="password" 
                                            label="Şifre *" 
                                            type="password"
                                            placeholder="Minimum 8 karakter" />
                                    @endif
                                </div>
                            @else
                                <x-mary-input 
                                    wire:model="password" 
                                    label="Yeni Şifre" 
                                    type="password"
                                    placeholder="Değiştirmek için yeni şifre girin" />
                            @endif
                        </div>
                    @endif

                    {{-- Role Settings Card --}}
                    <div class="theme-card p-6 shadow-sm">
                        <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Rol ve Yetkiler</h2>
                        @if($isViewMode)
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Rol</label>
                                <div class="text-sm font-medium text-[var(--color-text-heading)]">
                                    {{ $user->role?->name ?? 'Rol atanmamış' }}
                                </div>
                            </div>
                        @else
                            <x-mary-select 
                                wire:model="roleId" 
                                label="Rol" 
                                :options="$roles"
                                option-value="id"
                                option-label="name"
                                placeholder="Rol seçin" />
                        @endif
                    </div>
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
                <div class="theme-card p-6 shadow-sm sticky top-6">
                    <h2 class="text-base font-bold mb-4 text-center text-[var(--color-text-heading)]">Kayıt Bilgileri</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Kullanıcı ID</label>
                            <div class="flex items-center justify-center gap-2">
                                <code class="text-[10px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-[var(--color-text-base)]">{{ $userId ?: 'YENİ' }}</code>
                            </div>
                        </div>
                        @if($user->exists)
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Oluşturulma</label>
                                <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">{{ $user->created_at?->format('d.m.Y H:i') }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Son Güncelleme</label>
                                <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">{{ $user->updated_at?->format('d.m.Y H:i') }}</div>
                            </div>
                            @if($user->role)
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Rol</label>
                                    <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">{{ $user->role->name }}</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>