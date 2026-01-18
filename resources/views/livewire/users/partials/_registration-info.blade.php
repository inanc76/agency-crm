{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Kayıt Bilgileri (_registration-info.blade.php)
SORUMLULUK : Kullanıcının sistem kayıt detaylarını (ID, Tarih, Rol) görüntüler.

BAĞIMLILIKLAR (Variables):
@var $userId
@var $user
-------------------------------------------------------------------------
--}}

<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-center text-[var(--color-text-heading)]">Kayıt Bilgileri
    </h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Kullanıcı
                ID</label>
            <div class="flex items-center justify-center gap-2">
                <code
                    class="text-[10px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-[var(--color-text-base)]">{{ $userId ?: 'YENİ' }}</code>
            </div>
        </div>
        @if($user->exists)
            <div>
                <label
                    class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Oluşturulma</label>
                <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">
                    {{ $user->created_at?->format('d.m.Y H:i') }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Son
                    Güncelleme</label>
                <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">
                    {{ $user->updated_at?->format('d.m.Y H:i') }}
                </div>
            </div>
            @if($user->role)
                <div>
                    <label
                        class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)] text-center">Rol</label>
                    <div class="text-sm font-medium text-center text-[var(--color-text-heading)]">
                        {{ $user->role->name }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>