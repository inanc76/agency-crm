{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Profil Fotoğrafı Formu (_avatar-form.blade.php)
SORUMLULUK : Kullanıcının profil fotoğrafı yükleme ve önizleme işlemlerini yönetir.

BAĞIMLILIKLAR (Variables):
@var $avatarFile, $avatar, $email
@var $isViewMode
@var $user
-------------------------------------------------------------------------
--}}

<div class="theme-card p-6 shadow-sm sticky top-6 z-10">
    <h2 class="text-base font-bold mb-4 text-center text-[var(--color-text-heading)]">Profil Fotoğrafı
    </h2>

    <div class="flex flex-col items-center">
        {{-- Avatar Preview --}}
        <div
            class="w-32 h-32 rounded-full border-4 border-[var(--card-bg)] shadow-md flex items-center justify-center mb-4 overflow-hidden relative group">
            @if($avatarFile)
                <img src="{{ $avatarFile->temporaryUrl() }}" class="w-full h-full object-cover">
            @elseif($avatar)
                <img src="{{ asset('storage/' . $avatar) }}" class="w-full h-full object-cover">
            @else
                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($email))) }}?s=256&d=mp"
                    class="w-full h-full object-cover opacity-90">
            @endif
        </div>

        {{-- Actions --}}
        @if(!$isViewMode)
            <div class="flex items-center gap-2">
                <label class="cursor-pointer">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--dropdown-hover-bg)]/50 hover:bg-[var(--dropdown-hover-bg)] text-skin-heading rounded-lg text-sm font-medium transition-colors">
                        <x-mary-icon name="o-camera" class="w-4 h-4" />
                        {{ $avatar ? 'Değiştir' : 'Yükle' }}
                    </span>
                    <input type="file" wire:model="avatarFile" accept="image/png,image/jpeg,image/jpg" class="hidden">
                </label>

                @if($avatar)
                    <button type="button" wire:click="deleteAvatar"
                        wire:confirm="Profil fotoğrafını kaldırmak istediğinize emin misiniz?"
                        class="theme-btn-delete p-2 rounded-lg" title="Fotoğrafı Kaldır">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                    </button>
                @endif
            </div>

            <p class="text-xs mt-2 text-center opacity-40 text-[var(--color-text-base)]">
                PNG, JPG (Max 1MB)
            </p>

            @error('avatarFile')
                <p class="text-[var(--color-danger)] text-xs mt-2">{{ $message }}</p>
            @enderror
        @endif
    </div>
</div>