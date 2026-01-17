<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $this->setupUrl = url('/setup-password/' . $token . '?email=' . urlencode($user->email));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isReset
            ? 'Şifre Sıfırlama İsteği'
            : 'Hoş Geldiniz - Şifrenizi Belirleyin',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'setupUrl' => $this->setupUrl,
                'isReset' => $this->isReset,
            ]
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
