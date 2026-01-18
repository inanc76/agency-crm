{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Rol ve İzin Formu (_permissions-form.blade.php)
SORUMLULUK : Kullanıcının rol ve organizasyon bilgilerini yönetir.

BAĞIMLILIKLAR (Variables):
@var $roles
@var $departments
@var $roleId
@var $departmentId
@var $isViewMode
@var $user
-------------------------------------------------------------------------
--}}

<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Rol ve Organizasyon</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($isViewMode)
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Rol</label>
                <div class="text-sm font-medium text-[var(--color-text-heading)]">
                    {{ $user->role?->name ?? 'Rol atanmamış' }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Departman</label>
                <div class="text-sm font-medium text-[var(--color-text-heading)]">
                    @if($user->department)
                        <span
                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $this->getTailwindColor($user->department->metadata['color'] ?? null) }}">
                            {{ $user->department->display_label }}
                        </span>
                    @else
                        -
                    @endif
                </div>
            </div>
        @else
            <x-mary-select wire:model="roleId" label="Rol" :options="$roles" option-value="id" option-label="name"
                placeholder="Rol seçin" />

            <x-mary-select wire:model="departmentId" label="Departman" :options="$departments" option-value="id"
                option-label="display_label" placeholder="Departman seçin" />
        @endif
    </div>
</div>