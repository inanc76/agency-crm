<?php

use Livewire\Volt\Component;
use App\Models\MailSetting;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Mail Ayarları'])]
    class extends Component {
    use Toast;

    public string $provider = 'smtp';
    public bool $is_active = false;

    // SMTP Props
    public ?string $smtp_host = null;
    public ?int $smtp_port = 587;
    public ?string $smtp_username = null;
    public ?string $smtp_password = null;
    public bool $smtp_secure = true;
    public ?string $smtp_from_email = null;
    public ?string $smtp_from_name = null;

    // Mailgun Props
    public ?string $mailgun_api_key = null;
    public ?string $mailgun_domain = null;
    public ?string $mailgun_from_email = null;
    public ?string $mailgun_from_name = null;
    public ?string $mailgun_region = null;

    // Test Modal Props
    public bool $showTestModal = false;
    public string $test_email = '';
    public string $test_subject = 'Test E-postası';
    public string $test_body = 'Bu bir test e-postasıdır. Ayarlarınız başarıyla yapılandırıldı.';

    public function mount(): void
    {
        $settings = MailSetting::first();
        if (auth()->check()) {
            $this->test_email = auth()->user()->email;
        }

        if ($settings) {
            $this->provider = $settings->provider ?? 'smtp';
            $this->is_active = $settings->is_active;

            $this->smtp_host = $settings->smtp_host;
            $this->smtp_port = $settings->smtp_port ?? 587;
            $this->smtp_username = $settings->smtp_username;
            $this->smtp_password = $settings->smtp_password;
            $this->smtp_secure = $settings->smtp_secure ?? true;
            $this->smtp_from_email = $settings->smtp_from_email;
            $this->smtp_from_name = $settings->smtp_from_name;

            $this->mailgun_api_key = $settings->mailgun_api_key;
            $this->mailgun_domain = $settings->mailgun_domain;
            $this->mailgun_from_email = $settings->mailgun_from_email;
            $this->mailgun_from_name = $settings->mailgun_from_name;
            $this->mailgun_region = $settings->mailgun_region;
        }
    }

    public function save(): void
    {
        $data = [
            'provider' => $this->provider,
            'is_active' => $this->is_active,
            'smtp_host' => $this->smtp_host,
            'smtp_port' => $this->smtp_port,
            'smtp_username' => $this->smtp_username,
            'smtp_password' => $this->smtp_password,
            'smtp_secure' => $this->smtp_secure,
            'smtp_from_email' => $this->smtp_from_email,
            'smtp_from_name' => $this->smtp_from_name,
            'mailgun_api_key' => $this->mailgun_api_key,
            'mailgun_domain' => $this->mailgun_domain,
            'mailgun_from_email' => $this->mailgun_from_email,
            'mailgun_from_name' => $this->mailgun_from_name,
            'mailgun_region' => $this->mailgun_region,
        ];

        $settings = MailSetting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            MailSetting::create($data);
        }

        $this->success('Ayarlar Kaydedildi', 'Mail ayarları başarıyla güncellendi.');
    }

    public function test(): void
    {
        $this->showTestModal = true;
    }

    public function sendTest(): void
    {
        $this->validate([
            'test_email' => 'required|email',
            'test_subject' => 'required',
            'test_body' => 'required',
            'provider' => 'required|in:smtp,mailgun',
        ]);

        try {
            // Backup original config
            $originalConfig = config('mail');

            if ($this->provider === 'smtp') {
                $this->validate([
                    'smtp_host' => 'required',
                    'smtp_port' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_from_email' => 'required|email',
                    'smtp_from_name' => 'required',
                ]);

                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $this->smtp_host,
                    'mail.mailers.smtp.port' => $this->smtp_port,
                    'mail.mailers.smtp.username' => $this->smtp_username,
                    'mail.mailers.smtp.password' => $this->smtp_password,
                    'mail.mailers.smtp.encryption' => $this->smtp_secure ? 'tls' : null,
                    'mail.from.address' => $this->smtp_from_email,
                    'mail.from.name' => $this->smtp_from_name,
                ]);
            } else {
                $this->validate([
                    'mailgun_domain' => 'required',
                    'mailgun_api_key' => 'required',
                    'mailgun_from_email' => 'required|email',
                    'mailgun_from_name' => 'required',
                ]);

                config([
                    'mail.default' => 'mailgun',
                    'mail.mailers.mailgun' => [
                        'transport' => 'mailgun',
                    ],
                    'services.mailgun.domain' => $this->mailgun_domain,
                    'services.mailgun.secret' => $this->mailgun_api_key,
                    'services.mailgun.endpoint' => $this->mailgun_region === 'EU' ? 'api.eu.mailgun.net' : 'api.mailgun.net',
                    'mail.from.address' => $this->mailgun_from_email,
                    'mail.from.name' => $this->mailgun_from_name,
                ]);
            }

            // Send Test Email
            \Illuminate\Support\Facades\Mail::raw($this->test_body, function ($message) {
                $message->to($this->test_email)
                    ->subject($this->test_subject);
            });

            // Restore config (optional for request lifecycle but good practice)
            config($originalConfig);

            $this->showTestModal = false;
            $this->success('Başarılı', "Test e-postası {$this->test_email} adresine gönderildi.");

        } catch (\Exception $e) {
            $this->error('Hata', 'Mail gönderilemedi: ' . $e->getMessage());
        }
    }
}; ?>

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('settings.index') }}"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Mail Ayarları</h1>
            <p class="text-sm text-slate-500 mt-1">SMTP ve Mailgun e-posta servis ayarlarını yapılandırın</p>
        </div>

        {{-- Main Card --}}
        <div class="bg-[#eff4ff] border border-[#bfdbfe] rounded-xl shadow-sm p-6">

            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <h2 class="text-sm font-medium text-slate-700">E-posta Servis Yapılandırması</h2>
                <div class="flex items-center gap-4">
                    {{-- Gönderim Durumu Toggle --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" wire:model.live="is_active" class="toggle toggle-success toggle-lg" />
                        <div>
                            <div class="text-xs font-medium text-slate-700">Gönderim Durumu</div>
                            <div class="text-sm font-semibold"
                                style="color: {{ $is_active ? 'var(--btn-save-bg)' : 'var(--btn-delete-bg)' }};">
                                {{ $is_active ? 'Aktif' : 'Kapalı' }}
                            </div>
                        </div>
                    </div>

                    {{-- Test Gönder Button --}}
                    <x-mary-button label="Test Gönder" icon="o-paper-airplane" class="btn-sm btn-outline"
                        wire:click="test" />
                </div>
            </div>

            {{-- SECTION 1: Service Selection --}}
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Servis Seçimi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- SMTP Option --}}
                    <label class="block cursor-pointer">
                        <input type="radio" wire:model.live="provider" value="smtp" class="peer sr-only">
                        <div
                            class="p-4 bg-white border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50/30 transition-all relative">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $provider === 'smtp' ? 'border-orange-500' : 'border-gray-300' }}">
                                        @if($provider === 'smtp')
                                            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">SMTP</div>
                                        <div class="text-xs text-gray-500">Kendi sunucunuz</div>
                                    </div>
                                </div>
                                @if($provider === 'smtp')
                                    <span class="text-xs font-semibold px-2 py-1 rounded"
                                        style="color: var(--btn-save-bg); background-color: color-mix(in srgb, var(--btn-save-bg) 15%, white);">Aktif</span>
                                @endif
                            </div>
                        </div>
                    </label>

                    {{-- MAILGUN Option --}}
                    <label class="block cursor-pointer">
                        <input type="radio" wire:model.live="provider" value="mailgun" class="peer sr-only">
                        <div
                            class="p-4 bg-white border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50/30 transition-all relative">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $provider === 'mailgun' ? 'border-orange-500' : 'border-gray-300' }}">
                                        @if($provider === 'mailgun')
                                            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">MAILGUN</div>
                                        <div class="text-xs text-gray-500">API Hizmeti</div>
                                    </div>
                                </div>
                                @if($provider === 'mailgun')
                                    <span class="text-xs font-semibold px-2 py-1 rounded"
                                        style="color: var(--btn-save-bg); background-color: color-mix(in srgb, var(--btn-save-bg) 15%, white);">Aktif</span>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- SECTION 2: Form Fields --}}
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
                    {{ $provider === 'smtp' ? 'SMTP Bağlantı Detayları' : 'Mailgun Ayarları' }}
                </h3>

                @if($provider === 'smtp')
                    <div class="space-y-4">
                        {{-- SMTP Sunucu & Port --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-mary-input label="SMTP Sunucu Adresi" wire:model="smtp_host"
                                    placeholder="örn: smtp.yandex.com" class="bg-white" />
                            </div>
                            <div>
                                <x-mary-input label="Port" wire:model="smtp_port" type="number" placeholder="587"
                                    class="bg-white" />
                            </div>
                        </div>

                        {{-- Kullanıcı Adı & Şifre --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-mary-input label="Kullanıcı Adı (E-posta)" wire:model="smtp_username"
                                    placeholder="info@domain.com" class="bg-white" />
                            </div>
                            <div>
                                <x-mary-password label="Şifre" wire:model="smtp_password" class="bg-white" />
                            </div>
                        </div>

                        {{-- Gönderen E-posta & Adı --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-mary-input label="Gönderen E-posta" wire:model="smtp_from_email"
                                    placeholder="noreply@domain.com" class="bg-white" />
                            </div>
                            <div>
                                <x-mary-input label="Gönderen Adı" wire:model="smtp_from_name" placeholder="Firma Adı"
                                    class="bg-white" />
                            </div>
                        </div>

                        {{-- SSL Checkbox --}}
                        <div class="pt-2">
                            <x-mary-checkbox label="Güvenli bağlantı (TLS/SSL)" wire:model="smtp_secure"
                                hint="Bağlantı güvenliği için önerilir" />
                        </div>
                    </div>
                @else
                            <div class="space-y-4">
                                {{-- Mailgun API Key --}}
                                <div>
                                    <x-mary-password label="Mailgun API Key" wire:model="mailgun_api_key"
                                        hint="Mailgun panelinden alacağınız gizli anahtar" class="bg-white" />
                                </div>

                                {{-- Mailgun Domain --}}
                                <div>
                                    <x-mary-input label="Mailgun Domain" wire:model="mailgun_domain" placeholder="mg.alanadi.com"
                                        class="bg-white" />
                                </div>

                                {{-- Gönderen E-posta & Adı --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-mary-input label="Gönderen E-posta" wire:model="mailgun_from_email"
                                            placeholder="teknik@alanadi.com" class="bg-white" />
                                    </div>
                                    <div>
                                        <x-mary-input label="Gönderen Adı" wire:model="mailgun_from_name" placeholder="Firma Adı"
                                            class="bg-white" />
                                    </div>
                                </div>

                                {{-- Mailgun Region --}}
                                <div>
                                    <x-mary-select label="Mailgun Bölgesi" wire:model="mailgun_region" :options="[
                        ['id' => 'US', 'name' => 'Amerika (US)'],
                        ['id' => 'EU', 'name' => 'Avrupa (EU)']
                    ]"
                                        placeholder="Sunucu bölgesi seçiniz" class="bg-white" />
                                </div>
                            </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="pt-6 border-t border-slate-200 flex justify-end">
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="theme-btn-save">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Ayarları Kaydet</span>
                </button>
            </div>
        </div>

        {{-- Test Modal --}}
        <x-mary-modal wire:model="showTestModal" title="Test E-postası Gönder" class="backdrop-blur">
            <div class="space-y-4">
                <x-mary-input label="Alıcı E-posta" wire:model="test_email" icon="o-envelope" />
                <x-mary-input label="Konu" wire:model="test_subject" icon="o-bars-3-bottom-left" />
                <x-mary-textarea label="Mesaj İçeriği" wire:model="test_body" rows="3" />
            </div>
            <x-slot:actions>
                <button type="button" wire:click="$set('showTestModal', false)" class="theme-btn-cancel">
                    İptal
                </button>
                <button type="button" wire:click="sendTest" wire:loading.attr="disabled" class="theme-btn-save">
                    <svg wire:loading wire:target="sendTest" class="w-4 h-4 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="sendTest">Gönder</span>
                </button>
            </x-slot:actions>
        </x-mary-modal>
    </div>
</div>