<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        if (!isset($this->data['note'])) {
            $this->data['note'] = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Teklif Talebi [' . $this->offer->number . '] - ' . $this->data['company_name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.offers.new_request_notification',
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
