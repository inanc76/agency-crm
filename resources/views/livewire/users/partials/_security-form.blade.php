{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Güvenlik Ayarları Formu (_security-form.blade.php)
SORUMLULUK : Kullanıcının şifre ve güvenlik ayarlarını yönetir.

BAĞIMLILIKLAR (Variables):
@var $password, $sendPasswordEmail, $userId
@var $isViewMode
@var $user
-------------------------------------------------------------------------
--}}

@if(!$isViewMode)
    <div class="theme-card p-6 shadow-sm">
        <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Güvenlik Ayarları</h2>
        @if(!$userId)
            <div class="space-y-3">
                <x-mary-checkbox wire:model.live="sendPasswordEmail" label="Kullanıcıya şifre belirleme maili gönder"
                    class="checkbox-primary" />

                @if(!$sendPasswordEmail)
                    <x-mary-input wire:model="password" label="Şifre *" type="password" placeholder="Minimum 8 karakter" />
                @endif
            </div>
        @else
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <x-mary-input wire:model="password" label="Yeni Şifre" type="password"
                        placeholder="Değiştirmek için yeni şifre girin" />
                </div>
                <button type="button" wire:click="sendPasswordReset"
                    wire:confirm="Kullanıcıya şifre sıfırlama maili gönderilecek. Onaylıyor musunuz?"
                    class="theme-btn-save h-[42px] px-4 whitespace-nowrap cursor-pointer">
                    <x-mary-icon name="o-envelope" class="w-4 h-4 mr-2" />
                    Sıfırlama Maili Yolla
                </button>
            </div>
        @endif
    </div>
@endif