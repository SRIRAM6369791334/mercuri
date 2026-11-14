<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\PoStatusHistory;
use App\Notifications\SupplierDispatchReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendSupplierDispatchReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:dispatch-reminders {--force : Force send even if already sent today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 7-day supplier dispatch reminder notifications to Srinath (CS Coordinator) and Super Admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today('Asia/Kolkata');
        $sevenDaysLater = $today->copy()->addDays(7)->format('Y-m-d');
        $todayStr = $today->format('Y-m-d');

        $this->info("Checking POs with supplier_dispatch_date between {$todayStr} and {$sevenDaysLater}...");

        $query = PurchaseOrder::with(['vendorDetails'])
            ->whereNotNull('supplier_dispatch_date')
            ->where('supplier_dispatch_date', '<=', $sevenDaysLater)
            ->where('supplier_dispatch_date', '>=', $todayStr)
            ->where(function ($q) {
                $q->whereNull('production_milestone')
                  ->orWhereNotIn('production_milestone', ['dispatched', 'delivered', 'cancelled']);
            });

        if (!$this->option('force')) {
            $query->whereNull('dispatch_reminder_sent_at');
        }

        // Distinct by po_no
        $poList = $query->get()->unique('po_no');

        if ($poList->isEmpty()) {
            $this->info("No eligible purchase orders found for dispatch reminder.");
            return 0;
        }

        $this->info("Found {$poList->count()} PO(s) requiring dispatch reminders.");

        // Find recipients: Super Admin + Srinath (CS Coordinator / Service Coordinator)
        $recipients = User::where(function ($q) {
            $q->where('role', 'Super Admin')
              ->orWhere('role', 'Service Coordinator')
              ->orWhere('role', 'Cs')
              ->orWhere('email', 'like', '%srinath%')
              ->orWhere('id', 3);
        })->get()->unique('id');

        if ($recipients->isEmpty()) {
            $recipients = User::take(1)->get(); // fallback
        }

        $sentCount = 0;

        foreach ($poList as $po) {
            $dispatchDate = Carbon::parse($po->supplier_dispatch_date, 'Asia/Kolkata');
            $daysRemaining = max(0, $today->diffInDays($dispatchDate, false));

            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new SupplierDispatchReminderNotification($po, (int)$daysRemaining));
                } catch (\Throwable $e) {
                    Log::warning("Failed to deliver dispatch reminder email to {$recipient->email}: " . $e->getMessage());
                }
            }

            // Mark as sent on all rows for this po_no
            PurchaseOrder::where('po_no', $po->po_no)->update([
                'dispatch_reminder_sent_at' => now(),
            ]);

            // Log in status history
            PoStatusHistory::create([
                'po_no' => $po->po_no,
                'from_status' => $po->advance_status,
                'to_status' => $po->advance_status ?: ($po->lifecycle_status ?: 'active'),
                'action' => 'dispatch_reminder_sent',
                'user_id' => null,
                'remarks' => "Automated 7-day reminder sent. Dispatch due in {$daysRemaining} day(s) on {$po->supplier_dispatch_date}."
            ]);

            $this->line(" -> Reminded PO: {$po->po_no} | Vendor: " . ($po->vendorDetails?->company_name ?? $po->vendor_id) . " | Due in {$daysRemaining} days ({$po->supplier_dispatch_date})");
            $sentCount++;
        }

        $this->info("Successfully sent dispatch reminders for {$sentCount} purchase orders.");
        return 0;
    }
}
