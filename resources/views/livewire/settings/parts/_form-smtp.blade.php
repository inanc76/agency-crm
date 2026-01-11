{{--
🚀 SMTP FORM PARTIAL
---------------------------------------------------------
RENDERING: Conditional (Loaded only when $provider === 'smtp')
MAP:
- $smtp_host -> mail_settings.smtp_host
- $smtp_port -> mail_settings.smtp_port
- $smtp_username -> mail_settings.smtp_username
- $smtp_password -> mail_settings.smtp_password
- $smtp_secure -> mail_settings.smtp_secure
- $smtp_from_email -> mail_settings.smtp_from_email
- $smtp_from_name -> mail_settings.smtp_from_name
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm">
    <h3 class="text-xs font-bold uppercase tracking-wider mb-6 opacity-60 text-skin-base">SMTP Bağlantı Detayları</h3>

    <div class="space-y-4">
        {{-- Server & Port --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-mary-input label="SMTP Sunucu Adresi" wire:model="smtp_host" placeholder="örn: smtp.yandex.com" />
            <x-mary-input label="Port" wire:model="smtp_port" type="number" placeholder="587" />
        </div>

        {{-- Auth --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-mary-input label="Kullanıcı Adı (E-posta)" wire:model="smtp_username" placeholder="info@domain.com" />
            <x-mary-password label="Şifre" wire:model="smtp_password" />
        </div>

        {{-- From info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-mary-input label="Gönderen E-posta" wire:model="smtp_from_email" placeholder="noreply@domain.com" />
            <x-mary-input label="Gönderen Adı" wire:model="smtp_from_name" placeholder="Firma Adı" />
        </div>

        {{-- Security --}}
        <div class="pt-2">
            <x-mary-checkbox label="Güvenli bağlantı (TLS/SSL)" wire:model="smtp_secure"
                hint="Bağlantı güvenliği için önerilir" />
        </div>
    </div>
</div>