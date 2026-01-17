<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🛡️ MİSYON SİGMA - KULLANICI YÖNETİMİ                                         ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Kullanıcı Yönetim Paneli                                                                  ║
 * ║  🎯 ANA GÖREV: Kullanıcı listeleme, ekleme, düzenleme ve yönetici işlemleri                                    ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Arama: İsim ve e-posta ile arama                                                                             ║
 * ║  • Filtreleme: Aktif/Pasif durum filtresi                                                                       ║
 * ║  • 2FA Reset: Kullanıcının 2FA ayarlarını sıfırlama                                                            ║
 * ║  • Status Toggle: Kullanıcıyı aktif/pasif yapma                                                                 ║
 * ║  • CRUD: Kullanıcı ekleme, düzenleme                                                                            ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    use WithPagination;
    use Toast;

    // Search & Filter
    public string $search = '';
    public string $statusFilter = 'all';
    public string $roleFilter = 'all';
    public int $perPage = 25;

    // Reset pagination when filtering
    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedRoleFilter() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    private function getQuery(): Builder
    {
        return User::query()
            ->when($this->search, function (Builder $q) {
                return $q->where(function ($query) {
                    $query->where('name', 'ilike', '%' . $this->search . '%')
                          ->orWhere('email', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $q) {
                return $q->where('status', $this->statusFilter);
            })
            ->when($this->roleFilter !== 'all', function (Builder $q) {
                return $q->where('role_id', $this->roleFilter);
            })
            ->orderBy('name');
    }

    public function with(): array
    {
        return [
            'users' => $this->getQuery()->with('role')->paginate($this->perPage),
            'roles' => Role::orderBy('name')->get(),
        ];
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('settings.index') }}"
            class="inline-flex items-center gap-2 text-[var(--color-text-base)] hover:text-[var(--color-text-heading)] mb-6 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-5 h-5" />
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text-heading)]">Kullanıcı Yönetimi</h1>
                <p class="text-[var(--color-text-base)] text-sm mt-1">Sistem kullanıcılarını yönetin</p>
            </div>
            <button onclick="window.location.href='{{ route('users.create') }}'" 
                class="theme-btn-save gap-2">
                <x-mary-icon name="o-plus" class="w-4 h-4" />
                Yeni Kullanıcı
            </button>
        </div>

        {{-- Filter Panel --}}
        <div class="theme-card p-4 mb-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-64">
                    <x-mary-input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="İsim veya e-posta ile ara..." 
                        icon="o-magnifying-glass"
                        class="input-sm" />
                </div>

                {{-- Status Filter --}}
                <div class="min-w-32">
                    <x-mary-select 
                        wire:model.live="statusFilter" 
                        :options="[
                            ['value' => 'all', 'label' => 'Tüm Durumlar'],
                            ['value' => 'active', 'label' => 'Aktif'],
                            ['value' => 'inactive', 'label' => 'Pasif']
                        ]"
                        option-value="value"
                        option-label="label"
                        class="select-sm" />
                </div>

                {{-- Role Filter --}}
                <div class="min-w-32">
                    <x-mary-select 
                        wire:model.live="roleFilter" 
                        :options="collect([['value' => 'all', 'label' => 'Tüm Roller']])->concat($roles->map(fn($role) => ['value' => $role->id, 'label' => $role->name]))"
                        option-value="value"
                        option-label="label"
                        class="select-sm" />
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="theme-card shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)]">Kullanıcı</th>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)]">Unvan</th>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)]">E-posta</th>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)]">Telefon</th>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)] text-center">Durum</th>
                            <th class="px-6 py-3 font-semibold text-[var(--color-text-base)] text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                                onclick="window.location.href='{{ route('users.edit', $user) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @php
                                                $gravatarUrl = $user->getGravatarUrl(36);
                                            @endphp
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-sm font-semibold overflow-hidden"
                                                style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                                                <img src="{{ $gravatarUrl }}" 
                                                     alt="{{ $user->name }}"
                                                     class="w-full h-full object-cover rounded-full"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                                    {{ $user->initials() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-[13px] group-hover:opacity-80 transition-opacity font-medium"
                                                style="color: var(--list-card-link-color);">
                                                {{ $user->name }}
                                            </div>
                                            @if($user->role)
                                                <div class="text-xs text-[var(--color-text-muted)]">{{ $user->role->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-base)]">
                                    {{ $user->title ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-base)]">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-base)]">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Pasif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- 2FA Status Icon (Bilgilendirme) --}}
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-orange-600" 
                                            title="2FA Durumu">
                                            <x-mary-icon name="o-shield-exclamation" class="w-4 h-4" />
                                        </span>

                                        {{-- Status Icon (Bilgilendirme) --}}
                                        @if($user->status === 'active')
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-green-600" 
                                                title="Aktif Kullanıcı">
                                                <x-mary-icon name="o-user-plus" class="w-4 h-4" />
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-red-600" 
                                                title="Pasif Kullanıcı">
                                                <x-mary-icon name="o-user-minus" class="w-4 h-4" />
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[var(--color-text-muted)]">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-mary-icon name="o-users" class="w-12 h-12 opacity-20 mb-4" />
                                        <div class="font-medium">Henüz kullanıcı kaydı bulunmuyor</div>
                                        <div class="text-xs opacity-60 mt-1">Yeni kullanıcı ekleyerek başlayın</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[var(--color-text-muted)]">Göster:</span>
                    <select wire:model.live="perPage"
                        class="select select-xs bg-white border-slate-300 text-xs w-18 h-8 min-h-0 focus:outline-none focus:border-slate-400">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                </div>
                <div>{{ $users->links() }}</div>
                <div class="text-xs text-[var(--color-text-muted)]">
                    Toplam {{ $users->total() }} kullanıcı
                </div>
            </div>
        </div>
    </div>
</div>