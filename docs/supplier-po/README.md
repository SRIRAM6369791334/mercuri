# Supplier Purchase Order (PO) & Tracking Module

## Overview
The **Supplier Purchase Order Module** for Mercuriis Storage & Equipments Pvt Ltd provides end-to-end procurement orchestration. It bridges Customer Sales Orders/Quotations to Supplier Procurement, enforces strict multi-row commercial calculations, manages multi-plant vendor addresses, coordinates three-tier advance finance approval (CS Coordinator $\rightarrow$ Super Admin $\rightarrow$ Finance), sends automated 7-day dispatch reminders, and outputs official branded Purchase Order PDFs.

---

## Key Capabilities
1. **Purchase Order Creation & Editing (`/create-po`, `/edit-po/{id}`)**:
   - Header with dynamic PO Number, PO Date, Supplier search, Multi-Plant Supplier Branch selection, Mercuriis Issuing Branch selection, Delivery Destination (Warehouse vs Customer Site).
   - Removed obsolete "Choose Template" dropdown; standardized terms card auto-applied.
   - Dynamic product line items matching the Quotation module with live dependent category/model filtering.
   - Mandatory HSN code validation with row-specific error messages (`Row #N: HSN Code is mandatory.`).
   - Server-side recalculated totals: Gross Subtotal, Line Discounts, Overall Discount, Freight & Transportation, Unloading & Installation, GST (per-item rates), Round Off, and Grand Total.
   - Auto-computed Advance Amount based on standard commercial payment terms.

2. **Supplier Branch & Multi-Address Support**:
   - Vendor master multi-branch lookup (`/vendor/get-branches`) pulls registered office + all secondary factories (`vendor_addresses`).
   - Snapshot persistence of selected vendor branch address and GSTIN onto the PO record so future vendor edits do not corrupt issued POs.
   - Debounce protection preventing GST lookup timers from accidentally overwriting user-selected plant branches.

3. **Advance Approval & Tracking Workflow (`/order-po-tracking`)**:
   - **Step 1 (Request)**: Service Coordinator (Srinath) requests advance with optional remarks. Status becomes `pending_sir_approval`.
   - **Step 2 (Approval)**: Super Admin (Sir / Management) approves or rejects with reasons. Status becomes `approved_by_sir` or `rejected_by_sir`.
   - **Step 3 (Payment Release)**: Finance team records bank payment mode (NEFT/RTGS/IMPS), payment date, and mandatory UTR reference number. Status becomes `advance_released` and milestone shifts to `in_fabrication`.
   - Complete audit trail preserved in `po_status_history`.

4. **Automated 7-Day Dispatch Reminders (`po:dispatch-reminders`)**:
   - Artisan command runs daily at 09:00 Asia/Kolkata.
   - Scans POs where dispatch date is within 7 days.
   - Idempotently creates database notifications and dispatches emails to Srinath and Super Admins.
   - Real-time unread bell badge and notification dropdown in the top navigation bar.
   - Resetting logic: changing the dispatch date in Edit PO resets the reminder flag for future alerting.

5. **Official Purchase Order PDF Generation (`/po-pdf/{id}`)**:
   - Formatted strictly as "PURCHASE ORDER" (never "Invoice").
   - Mercuriis Issuing Branch header with dynamic GSTIN and warehouse address.
   - Vendor billing and delivery destination details.
   - Full product breakdown with HSN, Part No, Qty, Rate, Discount, Taxable, GST, and Total.
   - Pricing stack with Freight, Unloading, GST breakdown, Round Off, Net PO Value, and live Amount in Words.
   - Standard Commercial Terms and highlighted Special Instructions callout box.

---

## Directory Architecture
```
docs/supplier-po/
├── README.md              # System overview, capabilities, and setup
├── FIELDS.md              # Dictionary of all fields, inputs, validations, and DB mappings
├── CALCULATIONS.md        # Comprehensive server-side financial math specification
├── API_AND_ROUTES.md      # REST/AJAX endpoints, payloads, roles, and status codes
├── DATABASE.md            # Schema, migrations, indexes, foreign keys, and soft deletes
├── REMINDERS.md           # 7-day alert command, cron scheduler, bell notification UI
├── TESTING.md             # Automated test suite (PHPUnit/Pest), fixtures, and coverage
└── CHANGELOG.md           # Detailed engineering log of all modifications
```

---

## Quick Start & Verification

### 1. Database Migrations
Run pending migrations:
```bash
php artisan migrate --force
```

### 2. Run Test Suite
Verify that all 8 feature test suites pass cleanly:
```bash
php artisan test --filter=SupplierPurchaseOrderModuleTest
```

### 3. Dispatch Reminder Daemon
Trigger manual execution of the dispatch reminder engine:
```bash
php artisan po:dispatch-reminders
```
Ensure cron is configured in your production crontab:
```cron
* * * * * cd /path/to/mercuri && php artisan schedule:run >> /dev/null 2>&1
```
