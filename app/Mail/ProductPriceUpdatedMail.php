<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductPriceUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $emailSubject;

    public function __construct(array $data, ?string $subject = null)
    {
        $this->data = $data;
        $this->emailSubject = $subject ?: ('[MHE Special] Product Price & Details Updated: ' . ($data['product_name'] ?? 'Product'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.product_price_updated',
            with: $this->data
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
