<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Provider Selection & Status --}}
    <div class="space-y-6">
        <x-mary-card title="Servis Sağlayıcı" shadow class="bg-white border-slate-100">
            <div class="space-y-4">
                <x-mary-select label="Sağlayıcı Seçin" :options="[
        ['id' => 'smtp', 'name' => 'SMTP (Standart)'],
        ['id' => 'mailgun', 'name' => 'Mailgun API'],
    ]" wire:model.live="provider" icon="o-server-stack" />

                <x-mary-toggle label="Servis Aktif" wire:model="is_active"
                    hint="Bu servis üzerinden gönderim yapılsın mı?" class="toggle-success" />
            </div>
        </x-mary-card>

        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
            <div class="flex gap-3">
                <x-mary-icon name="o-information-circle" class="w-5 h-5 text-blue-500" />
                <p class="text-xs text-blue-700 leading-relaxed">
                    <strong>Bilgi:</strong> Ayarları kaydettikten sonra sistem otomatik olarak bu konfigürasyonu
                    kullanmaya başlar. Önce "Test Gönder" butonunu kullanarak bağlantıyı doğrulamanız önerilir.
                </p>
            </div>
        </div>
    </div>

    {{-- Provider Specific Configuration --}}
    <div class="lg:col-span-2">
        @if($provider === 'smtp')
            <x-mary-card title="SMTP Ayarları" shadow class="bg-white border-slate-100 overflow-visible">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-mary-input label="Host" wire:model="smtp_host" placeholder="mail.example.com"
                        hint="SMTP sunucu adresi" />
                    <x-mary-input label="Port" type="number" wire:model="smtp_port" placeholder="587"
                        hint="TLS için 587, SSL için 465" />
                    <x-mary-input label="Kullanıcı Adı" wire:model="smtp_username" placeholder="user@example.com" />
                    <x-mary-password label="Şifre" wire:model="smtp_password" />
                    <div class="md:col-span-2">
                        <x-mary-checkbox label="SSL/TLS Sertifikası Kullan" wire:model="smtp_secure"
                            hint="Güvenli bağlantı için önerilir" />
                    </div>
                    <hr class="md:col-span-2 border-slate-50 my-2">
                    <x-mary-input label="Gönderen E-posta" wire:model="smtp_from_email" placeholder="noreply@example.com" />
                    <x-mary-input label="Gönderen Adı" wire:model="smtp_from_name" placeholder="Agency CRM" />
                </div>
            </x-mary-card>
        @else
            <x-mary-card title="Mailgun API Ayarları" shadow class="bg-white border-slate-100 overflow-visible">
                <div class="grid grid-cols-1 gap-5">
                    <x-mary-input label="Domain" wire:model="mailgun_domain" placeholder="mg.example.com"
                        hint="Mailgun üzerinde doğrulanmış domain adresiniz" />
                    <x-mary-password label="API Key" wire:model="mailgun_api_key" placeholder="key-..." />
                    <x-mary-select label="Region" :options="[['id' => 'US', 'name' => 'United States (US)'], ['id' => 'EU', 'name' => 'Europe (EU)']]" wire:model="mailgun_region" />

                    <hr class="border-slate-50 my-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-mary-input label="Gönderen E-posta" wire:model="mailgun_from_email"
                            placeholder="noreply@example.com" />
                        <x-mary-input label="Gönderen Adı" wire:model="mailgun_from_name" placeholder="Agency CRM" />
                    </div>
                </div>
            </x-mary-card>
        @endif
    </div>
</div>