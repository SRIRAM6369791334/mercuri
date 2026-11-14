# Supplier Purchase Order: Field Dictionary & Mappings

## 1. Header Information

| Field Name | UI Control | Validation Rules | DB Column | Notes / Source |
| :--- | :--- | :--- | :--- | :--- |
| **PO Number** | Read-only input | Generated server-side, format `MES/PO/YY-YY/NNN` | `po_no` | Auto-incremented sequence; locks table row during creation. |
| **PO Date** | Date Picker | Required, default `today` (`Y-m-d`) | `po_date` | Date of issuance. |
| **Supplier (Vendor)** | Searchable Select | Required, exists in `vendor_details_tb` | `vendor_id` | Fetches associated product categories, GSTIN, and default billing address. |
| **Supplier Branch** | Dependent Select | Optional | `supplier_address_id` | Populated via `/vendor/get-branches`. Selects Primary Unit or secondary plant from `vendor_addresses`. |
| **Supplier Billing Address** | Textarea | Required, max 2000 chars | `supplier_billing_address` | Snapshot of vendor's address at PO creation time. |
| **Supplier GSTIN** | Text Input | Required, 15-char valid GST regex | `supplier_gst` | Snapshot of vendor's GSTIN. |
| **Mercuriis Issuing Branch** | Select | Required | `mercuriis_branch` | Loaded from `config('mercuriis_branches.branches')` (Chennai Podappai HQ / Coimbatore). |
| **Delivery Destination Scope**| Radio Buttons | Required (`warehouse` or `customer_site`) | `shipping_type` | `warehouse`: goods arrive at Mercuriis branch. `customer_site`: direct delivery. |
| **Customer Site Dropdown** | Dependent Select | Required if scope is `customer_site` | `customer_site_id` | Populated from customer's multi-plant addresses (`customer_addresses`). |
| **Delivery Destination Address** | Textarea | Required | `shipping_address` | Full delivery site address. |
| **Destination GSTIN** | Text Input | Required | `shipping_gst` | GSTIN of warehouse or customer site. |

---

## 2. Product Line Items

| Field Name | UI Control | Validation Rules | DB Column | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **S.No (#)** | Label | Auto-numbered (1, 2, ...) | N/A | Row index indicator. |
| **Product Category / Segment** | Dropdown | Required, filtered by vendor's allowed categories | `cat_id` | Storage, Equipments, Material Handling, etc. |
| **Product Model / Description** | Dependent Select | Required, filtered by chosen category | `product_id` | Searchable select. |
| **Part Number** | Read-only input | Auto-filled from Product Master | `part_no` | Manufacturer part number. |
| **HSN Code** | Read-only / text | Mandatory, min 2, max 20 chars | `hsn_code` | Block save if missing with message: `Row #N: HSN Code is mandatory.`. |
| **Quantity** | Numeric input | Required, integer or decimal > 0 | `product_qty` | Order quantity. |
| **Supplier Rate (₹)** | Currency input | Required, numeric >= 0, 2 decimals | `unit_price` | Negotiated unit purchase price. |
| **Discount %** | Number input | Optional, 0.00 to 100.00 | `discount_percentage` | Direct line discount. NO "Allowed Discount" ceiling checks. |
| **Tax % GST** | Dropdown | Required (0%, 5%, 12%, 18%, 28%) | `gst_percentage` | Auto-selected from product master or editable. |
| **Tax Amount (₹)** | Read-only input | Computed server-side | `gst_amount` | GST amount for this line. |
| **Total + Tax (₹)** | Read-only input | Computed server-side | `total_amount` | Line Total (`taxable + gst_amount`). |
| **Accessories** | Modal / Drawer | Optional array of accessory IDs + quantities | `po_line_accessories` | Synced to child table. |

---

## 3. Commercial & Summary Fields

| Field Name | UI Control | Validation Rules | DB Column | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Subtotal (Basic Total)** | Read-only stack | Sum of `product_qty * unit_price` | `product_total_price` | Sum of all line item gross values. |
| **Line Discount Total** | Read-only stack | Sum of line discounts | `discount_amount` | Cumulative line discount amount. |
| **Freight Charges** | Number input | Optional, numeric >= 0, default 0 | `freight_charges` | Separate transportation fee. |
| **Unloading / Install Charges** | Number input | Optional, numeric >= 0, default 0 | `unloading_charges` | Optional site installation charges. |
| **GST Total** | Read-only stack | Sum of all line GST | `gst_amount` | Cumulative GST amount. |
| **Round Off (₹)** | Read-only stack | Computed | `roundoff` | Difference between exact total and rounded integer. |
| **Net PO Value (Grand Total)**| Read-only stack | Computed server-side | Accessor / `total_amount` | Final payable amount. |
| **Amount in Words** | Text badge | Computed live via JS & helper | N/A | Rendered in Indian Currency words. |
| **Committed Lead Time** | Single Dropdown | Standard options (2, 4, 6, 8, 10-12 Weeks, Ex-Stock) | `lead_time` | Exactly one select in Commercial Terms tab. |
| **Payment Terms** | Dropdown | Standard options (20%, 30%, 50%, 100%, Credit 30 Days) | `payment_terms` | Automatically drives advance percentage. |
| **Advance Percentage** | Hidden / Badge | 0 to 100 | `advance_percentage` | Parsed from selected payment terms. |
| **Advance Amount (₹)** | Highlighted Box | Computed: `Grand Total * advance_pct / 100` | `advance_amount` | Live recomputing and stored on PO row. |
| **Special Instructions** | Textarea | Optional, max 5000 chars | `special_instructions` | Printed on PO PDF in an amber highlighted box. |
| **Supplier Dispatch Date** | Date Picker | Format `Y-m-d` | `supplier_dispatch_date` | Target delivery date. Alerts sent 7 days prior. |
| **Reminder Sent Timestamp** | System | Managed by scheduler | `dispatch_reminder_sent_at` | Reset to `null` if dispatch date is modified. |
| **Technical Specifications** | Textarea | Optional | `technical_specification` | Full engineering notes. |
| **Attachments** | File Uploads | PDF, PNG, JPG <= 20MB | `drawing_attachment`, `quote_attachments`, `files` | Stored in `public/uploads/po_files`. |
