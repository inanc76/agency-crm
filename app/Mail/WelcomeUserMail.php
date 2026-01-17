<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $token;

    public string $setupUrl;

    public bool $isReset;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token, bool $isReset = false)
    {
        $this->user = $user;
        $this->token = $token;
        $this->isReset = $isReset;
        $this->setupUrl = url('/setup-password/'.$token.'?email='.urlencode($user->email));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $service = app(\App\Services\MailTemplateService::class);
        $key = $this->isReset ? 'password_reset' : 'welcome_email';

        $data = $service->render($key, [
            '{{user.name}}' => $this->user->name,
            '{{user.email}}' => $this->user->email,
            '{{setup_url}}' => $this->setupUrl,
        ]);

        return new Envelope(
            subject: $data['subject'] ?: ($this->isReset ? 'Şifre Sıfırlama İsteği' : 'Hoş Geldiniz - Şifrenizi Belirleyin'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $service = app(\App\Services\MailTemplateService::class);
        $key = $this->isReset ? 'password_reset' : 'welcome_email';

        $data = $service->render($key, [
            '{{user.name}}' => $this->user->name,
            '{{user.email}}' => $this->user->email,
            '{{setup_url}}' => $this->setupUrl,
        ]);

        if ($data['content']) {
            return new Content(
                htmlString: $data['content'],
            );
        }

        // Emergency fallback if DB is empty
        return new Content(
            htmlString: '<h1>Sistem Mesajı</h1><p>Şablon bulunamadı. Lütfen yönetici ile iletişime geçin.</p>',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
