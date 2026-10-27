<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OrfCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     *
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $orfRef = $this->data['orf_ref'] ?? 'ORF';
        $customer = $this->data['customer'] ?? 'Customer';

        return new Envelope(
            subject: "{$orfRef} - {$customer}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orf_created', // Make sure the view exists in resources/views/emails/orf_created.blade.php
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $file) {

                $fullPath = public_path($file->file_path);

                if (file_exists($fullPath)) {
                    $attachments[] = Attachment::fromPath($fullPath)
                        ->as(basename($file->file_path));
                }
            }
        }

        return $attachments;
    }
//     public function attachments(): array
// {
//     $attachments = [];

//     if (!empty($this->data['attcuspo'])) {
//         $attachments[] = Attachment::fromPath(public_path($this->data['attcuspo']))
//             ->as('Customer_PO.pdf') // Rename the attachment if needed
//             ->withMime('application/pdf');
//     }

//     if (!empty($this->data['sigdraw'])) {
//         $attachments[] = Attachment::fromPath(public_path($this->data['sigdraw']))
//             ->as('Signature_Draw.pdf') // Rename the attachment if needed
//             ->withMime('application/pdf');
//     }

//     return $attachments;
// }
}
