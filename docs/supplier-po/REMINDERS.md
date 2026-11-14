# Automated 7-Day Dispatch Reminders & Notifications

## 1. Requirement & Architecture

Mercuriis requires proactive tracking of supplier fabrication and dispatch timelines. 
The system provides:
- Visual alerting on the Tracking screen: a **yellow badge** appears when the `supplier_dispatch_date` is within 7 days from today.
- Automated email and in-app notifications: sent **7 days prior** to the promised dispatch date.
- Idempotent execution: multiple scheduler runs on the same day never spam users.
- Date-shift resets: if the supplier dispatch date is delayed or rescheduled in Edit PO, the reminder flag resets to `null` so the system can alert again 7 days before the new date.

---

## 2. Notification Flow & Channels

```
Daily 09:00 Cron
       │
       ▼
php artisan po:dispatch-reminders
       │
       ├─► Queries active POs where:
       │     - supplier_dispatch_date BETWEEN today AND today + 7 days
       │     - dispatch_reminder_sent_at IS NULL
       │     - lifecycle_status = 'active'
       │     - production_milestone NOT IN ('dispatched', 'delivered')
       │
       ├─► Identifies Recipients:
       │     1. PO Creator / Owner (Srinath / Service Coordinator)
       │     2. Super Admins
       │
       ├─► Dispatches SupplierDispatchReminderNotification:
       │     - Mail Channel: Rich HTML email with PO number, vendor, dispatch date, and tracking link.
       │     - Database Channel: Stored in `notifications` table for real-time topbar bell count.
       │
       ├─► Updates PurchaseOrder:
       │     - Sets `dispatch_reminder_sent_at = now()` across all rows for this PO.
       │
       └─► Logs Audit Entry:
             - Inserts row into `po_status_history` with action `dispatch_reminder_sent`.
```

---

## 3. Artisan Command Specification

- **Command Name**: `po:dispatch-reminders`
- **Class**: `App\Console\Commands\SendSupplierDispatchReminders`
- **Output Sample**:
  ```text
  Checking POs with supplier_dispatch_date between 2026-11-13 and 2026-11-20...
   -> Reminded PO: MES/PO/26-27/012 | Vendor: Pondy Oxides | Due in 7 days (2026-11-20)
  Successfully sent dispatch reminders for 1 purchase orders.
  ```

---

## 4. Scheduler Configuration

In `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('po:dispatch-reminders')
        ->dailyAt('09:00')
        ->timezone('Asia/Kolkata')
        ->withoutOverlapping()
        ->runInBackground();
}
```

### Production Crontab Setup
Add this entry to your server's crontab (`crontab -e`):
```cron
* * * * * cd /path/to/mercuri && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Topbar Bell Notification UI

- Located in `resources/views/layouts/topbar.blade.php`.
- Displays dynamic red unread count badge.
- Clicking opens a modern dropdown with:
  - Header: Unread count + "Mark all read" button.
  - Notification items: PO number, vendor name, remaining days, time elapsed, and deep link to `/order-po-tracking`.
  - Individual "mark-as-read" click handler that marks the notification as read via AJAX without page reload.
