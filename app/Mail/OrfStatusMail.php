<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrfStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;  // Store the email data

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('ORF Status Update')
                    ->view('emails.orf_status')   // Reference the email view
                    ->with(['data' => $this->data]);
    }
}
