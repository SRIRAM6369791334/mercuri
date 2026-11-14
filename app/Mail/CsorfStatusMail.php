<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CsorfStatusMail extends Mailable
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
        $rawStatus = (string) ($this->data['status'] ?? '');
        if ($rawStatus === '1' || strtolower($rawStatus) === 'approved') {
            $status = 'Approved';
        } elseif ($rawStatus === '2' || strtolower($rawStatus) === 'rejected') {
            $status = 'Rejected';
        } else {
            $status = ucfirst(strtolower($rawStatus ?: 'Updated'));
        }

        $customerName = trim((string) ($this->data['customer_name'] ?? ''));

        if ($customerName !== '') {
            $subject = "ORF Status Update – {$status} – {$customerName}";
        } else {
            $subject = "ORF Status Update – {$status}";
        }

        return $this->subject($subject)
                    ->view('emails.csorf_status')   // Reference the email view
                    ->with(['data' => $this->data]);
    }
}
