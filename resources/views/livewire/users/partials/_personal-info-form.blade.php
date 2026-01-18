{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Kişisel Bilgiler Formu (_personal-info-form.blade.php)
SORUMLULUK : Kullanıcının temel kimlik bilgilerini (Ad, E-posta, Unvan, Telefon) yönetir.

BAĞIMLILIKLAR (Variables):
@var $name, $email, $phone, $title
@var $isViewMode
@var $user
-------------------------------------------------------------------------
--}}

<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Kişisel Bilgiler</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($isViewMode)
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-[var(--color-text-base)]">Ad
                    Soyad</label>
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
            <x-mary-input wire:model="name" label="Ad Soyad *" placeholder="Kullanıcının tam adı" />

            <x-mary-input wire:model="email" label="E-posta *" type="email" placeholder="kullanici@example.com" />

            <x-mary-input wire:model="phone" label="Telefon" placeholder="+90 555 123 45 67" />

            <x-mary-input wire:model="title" label="Unvan" placeholder="Proje Müdürü" />
        @endif
    </div>
</div>