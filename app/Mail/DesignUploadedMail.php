<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DesignUploadedMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public $filePaths;  // Multiple file paths

    public function __construct($data, $filePaths)
    {
        $this->data = $data;
        $this->filePaths = $filePaths;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Design Uploaded'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.design_created',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->filePaths as $path) {
            $attachments[] = Attachment::fromPath(public_path($path));
        }

        return $attachments;
    }
}
