<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOfferRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public \App\Models\Offer $offer;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\Offer $offer, array $data)
    {
        $this->offer = $offer;
        $this->data = $data;
        // Ensure note key exists
        if (! isset($this->data['note'])) {
            $this->data['note'] = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $service = app(\App\Services\MailTemplateService::class);
        $data = $service->render('new_offer_request', [
            '{{offer.number}}' => $this->offer->number,
            '{{company_name}}' => $this->data['company_name'],
            '{{name}}' => $this->data['name'],
            '{{phone}}' => $this->data['phone'] ?: '-',
            '{{email}}' => $this->data['email'],
            '{{note}}' => $this->data['note'] ?? '-',
            '{{offer.view_url}}' => route('customers.offers.edit', $this->offer->id),
        ]);

        return new Envelope(
            subject: $data['subject'] ?: ('Yeni Teklif Talebi ['.$this->offer->number.'] - '.$this->data['company_name']),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $service = app(\App\Services\MailTemplateService::class);
        $data = $service->render('new_offer_request', [
            '{{offer.number}}' => $this->offer->number,
            '{{company_name}}' => $this->data['company_name'],
            '{{name}}' => $this->data['name'],
            '{{phone}}' => $this->data['phone'] ?: '-',
            '{{email}}' => $this->data['email'],
            '{{note}}' => $this->data['note'] ?? '-',
            '{{offer.view_url}}' => route('customers.offers.edit', $this->offer->id),
        ]);

        if ($data['content']) {
            return new Content(
                htmlString: $data['content'],
            );
        }

        return new Content(
            htmlString: '<h1>Sistem Mesajı</h1><p>Şablon bulunamadı.</p>',
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
