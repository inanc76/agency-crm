<?php

namespace App\Livewire\Settings\Traits;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

/**
 * 🛡️ MAIL SETTINGS TRAIT
 * ---------------------------------------------------------
 * ARCHITECTURE: Logic Layer (Trait)
 * RESPONSIBILITY: SMTP/Mailgun configuration management and test mail functionality.
 * SECURITY: Requires 'settings.edit' permission for saving.
 * ---------------------------------------------------------
 */
trait HasMailSettings
{
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

    /**
     * Initialize mail settings from database.
     */
    public function mountHasMailSettings(): void
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

    /**
     * Save mail settings to database.
     * 🔐 SECURITY: Restricted to 'settings.edit' permission.
     * ⚠️ PERSISTENCE WARNING: Changes saved here immediately overwrite system mail configuration 
     * and affect all outgoing transactional emails.
     */
    public function save(): void
    {
        $this->authorize('settings.edit');

        /**
         * DYNAMIC VALIDATION (Required If):
         * Rules are context-aware. If provider is 'smtp', SMTP fields are mandated.
         * If provider is 'mailgun', API Key and Domain become mandatory.
         */
        $this->validate([
            'provider' => 'required|in:smtp,mailgun',
            'is_active' => 'boolean',
            'smtp_host' => 'required_if:provider,smtp',
            'smtp_port' => 'required_if:provider,smtp|nullable|integer',
            'smtp_from_email' => 'required|email',
            'smtp_from_name' => 'required',
            'mailgun_api_key' => 'required_if:provider,mailgun',
            'mailgun_domain' => 'required_if:provider,mailgun',
        ]);

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

        MailSetting::updateOrCreate([], $data);

        $this->success('Ayarlar Kaydedildi', 'Mail ayarları başarıyla güncellendi.');
    }

    /**
     * Open test mail modal.
     */
    public function test(): void
    {
        $this->showTestModal = true;
    }

    /**
     * Send a test email using current unsaved settings.
     */
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
            Mail::raw($this->test_body, function ($message) {
                $message->to($this->test_email)
                    ->subject($this->test_subject);
            });

            // Restore config
            config($originalConfig);

            $this->showTestModal = false;
            $this->success('Başarılı', "Test e-postası {$this->test_email} adresine gönderildi.");

        } catch (\Exception $e) {
            $this->error('Hata', 'Mail gönderilemedi: ' . $e->getMessage());
        }
    }
}
