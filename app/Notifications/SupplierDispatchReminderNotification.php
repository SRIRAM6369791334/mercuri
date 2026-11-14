<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseOrder;

class SupplierDispatchReminderNotification extends Notification
{
    use Queueable;

    public $po;
    public $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseOrder $po, int $daysRemaining = 7)
    {
        $this->po = $po;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Deliver to database and email
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $vendorName = $this->po->vendorDetails?->company_name ?? ('Vendor #' . $this->po->vendor_id);
        $dispatchDate = $this->po->supplier_dispatch_date ? date('d-m-Y', strtotime($this->po->supplier_dispatch_date)) : 'N/A';
        $trackingUrl = url('/order-po-tracking?search=' . urlencode($this->po->po_no));

        return (new MailMessage)
            ->subject("[Reminder] Supplier Dispatch Due in {$this->daysRemaining} Days: PO {$this->po->po_no}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Purchase Order **{$this->po->po_no}** placed with **{$vendorName}** is scheduled for dispatch on **{$dispatchDate}** ({$this->daysRemaining} days remaining).")
            ->line("Committed Lead Time: **" . ($this->po->lead_time ?: 'Standard') . "** | Current Milestone: **" . ($this->po->production_milestone ?: 'Pending') . "**")
            ->action('View in PO Tracking', $trackingUrl)
            ->line('Please verify supplier production readiness, inspection status, and dispatch schedule.');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $vendorName = $this->po->vendorDetails?->company_name ?? ('Vendor #' . $this->po->vendor_id);
        $dispatchDate = $this->po->supplier_dispatch_date ? date('d-m-Y', strtotime($this->po->supplier_dispatch_date)) : 'N/A';

        return [
            'type' => 'supplier_dispatch_reminder',
            'po_no' => $this->po->po_no,
            'vendor_name' => $vendorName,
            'supplier_dispatch_date' => $this->po->supplier_dispatch_date,
            'formatted_dispatch_date' => $dispatchDate,
            'days_remaining' => $this->daysRemaining,
            'lead_time' => $this->po->lead_time,
            'production_milestone' => $this->po->production_milestone,
            'message' => "PO {$this->po->po_no} ({$vendorName}) dispatch due on {$dispatchDate} (in {$this->daysRemaining} days).",
            'url' => '/order-po-tracking?search=' . urlencode($this->po->po_no),
        ];
    }
}
