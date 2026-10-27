<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $pdf;
    public $pdfPath;
    public $fromMail;
    public $drawingPaths;

    public function __construct($quotation, $pdf, $fromMail, $drawingPaths = null)
    {
        $this->quotation = $quotation;
        $this->pdf = $pdf;
        $this->fromMail = $fromMail;
        $this->drawingPaths = $drawingPaths;
    }

    public function build()
    {
        $mail = $this->from($this->fromMail, 'Mercuriis Storage & Equipments Private Limited')
            ->subject('Quotation - ' . $this->quotation->quota_id)
            ->view('emails.quotationmail')
            ->attachData(
                $this->pdf->output(),
                'Quotation-' . $this->quotation->quota_id . '.pdf'
            );

        // Attach drawings ONLY if available
        if (!empty($this->drawingPaths) && is_array($this->drawingPaths)) {
            foreach ($this->drawingPaths as $path) {
                if (file_exists($path)) {
                    $mail->attach($path);
                }
            }
        }

        return $mail;
    }
}