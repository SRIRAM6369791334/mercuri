# Supplier Purchase Order Module - Engineering Changelog

## [2026-11-13] - Comprehensive Supplier PO & Tracking Overhaul

### Database & Migrations
- **Added**: Migration `2026_11_13_044934_create_notifications_table.php` to support Laravel database notifications.
- **Added**: Migration `2026_11_13_050000_add_po_branch_and_status_history_tables.php` creating `po_status_history` table and adding branch snapshot fields (`supplier_address_id`, `customer_site_id`, `dispatch_reminder_sent_at`, `po_date`) to `purchase_order_tb`.
- **Added**: Migration `2026_11_13_060000_add_supplier_po_integrity_fields.php` creating `po_line_accessories` table and adding `lifecycle_status`, `gst_percentage`, `overall_discount_percentage`, `overall_discount_amount`, `taxable_amount`, `committed_dispatch_date`, and `actual_dispatch_date`.
- **Added**: Migration `2026_11_13_070000_make_to_status_nullable_in_po_status_history.php` to ensure status history inserts are resilient when status is transitional.

### Server-Side Calculations & Business Logic
- **Added**: `App\Support\PurchaseOrderTotals` class for pure server-side calculation of line gross, line discounts, overall discount distribution, line taxable values, line GST amounts, freight, unloading, round off, and net grand total.
- **Updated**: `PurchaseOrderController@savePo` and `editPo` to enforce strict server-side calculation inside DB transactions.
- **Added**: Accessors and mutators in `PurchaseOrder` model for `grand_total` and `advance_utr_number`.

### Validation & Request Handling
- **Updated**: `StorePurchaseOrderRequest` and `UpdatePurchaseOrderRequest` with mandatory HSN validation requiring row-specific error naming (`Row #N HSN Code is mandatory.`).
- **Updated**: Removed mandatory validation for `terms_condition_id`.
- **Updated**: Enforced customer site selection when delivery destination is set to `customer_site`.

### Supplier Branch & Multi-Plant Selection
- **Updated**: `/vendor/get-branches` endpoint in `PurchaseOrderController@getVendorBranches` to return both primary office and secondary factories from `vendor_addresses` with consistent `addr_{id}` keys.
- **Fixed**: Branch 2 address wipeout bug in `addPurchaseOrder.blade.php` and `editPurchaseOrder.blade.php` by clearing GST timers and preventing debounced lookups from overwriting selected plant addresses.

### Advance Approval & Tracking Workflow
- **Updated**: `requestAdvance` endpoint to permit CS Coordinator / Srinath (`Service Coordinator` role) to initiate advance requests.
- **Updated**: `sirApproveAdvance` endpoint allowing management approval and rejection with reasons.
- **Updated**: `financeReleaseAdvance` endpoint allowing Finance to record payment mode, payment date, and mandatory UTR reference number.
- **Added**: Complete audit trail logging in `po_status_history` across all three steps.
- **Updated**: Registered route aliases `po.sir-approve-advance` and `po.finance-release-advance`.

### Automated 7-Day Dispatch Reminders
- **Added**: Notification `SupplierDispatchReminderNotification` with mail and database channels.
- **Added**: Artisan command `po:dispatch-reminders` (`App\Console\Commands\SendSupplierDispatchReminders`).
- **Updated**: `app/Console/Kernel.php` to schedule `po:dispatch-reminders` daily at 09:00 Asia/Kolkata.
- **Updated**: `editPo` to reset `dispatch_reminder_sent_at` to `null` whenever the supplier dispatch date is modified.
- **Updated**: Topbar navigation bell in `resources/views/layouts/topbar.blade.php` to display unread badge and dropdown list with "mark as read" capability.

### UI & Official PO PDF
- **Updated**: Title on PDF and screens strictly styled as "PURCHASE ORDER".
- **Updated**: Branch details pulled dynamically from `config/mercuriis_branches.php` (Chennai Podappai HQ & Coimbatore).
- **Added**: Special Instructions callout box rendered in highlighted styling on PDF when non-empty.
- **Added**: Round Off and live Amount in Words (Indian numbering convention) across UI and PDF.
- **Removed**: Obsolete "Choose Template" select; replaced with standard terms card.

### Automated Testing & Synchronization
- **Added**: Comprehensive test suite `tests/Feature/SupplierPurchaseOrderModuleTest.php` covering all 8 business requirements.
- **Verified**: 100% test pass rate (8 passed, 43 assertions) in both `C:\New Folder\mercuri` and `C:\Projects\mer2`.
