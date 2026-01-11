{{--
🚀 MAILGUN FORM PARTIAL
---------------------------------------------------------
RENDERING: Conditional (Loaded only when $provider === 'mailgun')
MAP:
- $mailgun_api_key -> mail_settings.mailgun_api_key
- $mailgun_domain -> mail_settings.mailgun_domain
- $mailgun_from_email -> mail_settings.mailgun_from_email
- $mailgun_from_name -> mail_settings.mailgun_from_name
- $mailgun_region -> mail_settings.mailgun_region
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm">
    <h3 class="text-xs font-bold uppercase tracking-wider mb-6 opacity-60 text-skin-base">Mailgun Ayarları</h3>

    <div class="space-y-4">
        {{-- API Key --}}
        <x-mary-password label="Mailgun API Key" wire:model="mailgun_api_key"
            hint="Mailgun panelinden alacağınız gizli anahtar" />

        {{-- Domain --}}
        <x-mary-input label="Mailgun Domain" wire:model="mailgun_domain" placeholder="mg.alanadi.com" />

        {{-- From info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-mary-input label="Gönderen E-posta" wire:model="mailgun_from_email" placeholder="teknik@alanadi.com" />
            <x-mary-input label="Gönderen Adı" wire:model="mailgun_from_name" placeholder="Firma Adı" />
        </div>

        {{-- Region --}}
        <x-mary-select label="Mailgun Bölgesi" wire:model="mailgun_region" :options="[
        ['id' => 'US', 'name' => 'Amerika (US)'],
        ['id' => 'EU', 'name' => 'Avrupa (EU)']
    ]" placeholder="Sunucu bölgesi seçiniz" />
    </div>
</div>