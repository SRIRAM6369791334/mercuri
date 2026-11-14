# Supplier Purchase Order: Database & Schema Specification

## 1. Tables Overview

The module interacts with the following primary and auxiliary tables:
- `purchase_order_tb`: Stores line items and order-level attributes.
- `vendor_addresses`: Stores secondary factory/plant units per vendor.
- `customer_addresses`: Stores client delivery site addresses.
- `po_line_accessories`: Tracks accessories associated with specific PO product lines.
- `po_status_history`: Complete audit trail of all advance approvals, dispatch reminder logs, and amendments.
- `notifications`: Standard Laravel database notifications table for alert dispatches.

---

## 2. Table Schemas & Key Columns

### `purchase_order_tb`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, PRIMARY | Unique line item ID |
| `po_no` | VARCHAR(100) | INDEX | PO Reference sequence (e.g. `MES/PO/26-27/001`) |
| `po_date` | DATE | NULLABLE | Date of issuance |
| `vendor_id` | BIGINT UNSIGNED | INDEX | Foreign key to `vendor_details_tb` |
| `supplier_address_id`| BIGINT UNSIGNED | NULLABLE | Selected vendor plant from `vendor_addresses` |
| `supplier_billing_address` | TEXT | | Snapshot of vendor address at issuance |
| `supplier_gst` | VARCHAR(50) | | Snapshot of vendor GSTIN at issuance |
| `cat_id` | BIGINT UNSIGNED | INDEX | Product Category ID |
| `product_id` | BIGINT UNSIGNED | INDEX | Product ID from `products` |
| `product_qty` | DECIMAL(12,3)| | Ordered quantity |
| `unit_price` | DECIMAL(14,2)| | Negotiated unit rate |
| `product_total_price` | DECIMAL(14,2)| | Gross line value (`qty * unit_price`) |
| `discount_percentage` | DECIMAL(5,2) | DEFAULT 0 | Negotiated line discount % |
| `discount_amount` | DECIMAL(14,2)| DEFAULT 0 | Computed discount amount |
| `overall_discount_percentage`| DECIMAL(5,2)| DEFAULT 0 | Global order discount % |
| `overall_discount_amount` | DECIMAL(14,2)| DEFAULT 0 | Allocated overall discount |
| `taxable_amount` | DECIMAL(14,2)| NULLABLE | Line taxable amount |
| `gst_percentage` | DECIMAL(5,2) | DEFAULT 18 | GST rate % |
| `gst_amount` | DECIMAL(14,2)| | Line GST amount |
| `sub_total` | DECIMAL(14,2)| | Line gross / taxable |
| `total_amount` | DECIMAL(14,2)| | Line total (`taxable + gst`) |
| `freight_charges` | DECIMAL(14,2)| DEFAULT 0 | Separate freight charges |
| `unloading_charges` | DECIMAL(14,2)| DEFAULT 0 | Separate unloading charges |
| `roundoff` | DECIMAL(10,2)| DEFAULT 0 | Round off difference |
| `mercuriis_branch` | VARCHAR(100)| | Chennai HQ or Coimbatore |
| `shipping_type` | VARCHAR(50) | DEFAULT 'warehouse'| `warehouse` or `customer_site` |
| `customer_site_id` | BIGINT UNSIGNED | NULLABLE | Foreign key to `customer_addresses` |
| `shipping_address` | TEXT | | Full delivery site address |
| `shipping_gst` | VARCHAR(50) | | Delivery site GSTIN |
| `lead_time` | VARCHAR(100)| | Standard lead time selection |
| `payment_terms` | VARCHAR(255)| | Standard commercial payment terms |
| `advance_percentage` | DECIMAL(5,2) | DEFAULT 0 | Advance % extracted from payment terms |
| `advance_amount` | DECIMAL(14,2)| DEFAULT 0 | Computed advance amount |
| `advance_status` | VARCHAR(50) | NULLABLE | Status: `pending_request`, `pending_sir_approval`, `approved_by_sir`, `rejected_by_sir`, `advance_released`, `not_required` |
| `sir_approved_by` | BIGINT UNSIGNED | NULLABLE | Approver user ID |
| `sir_approved_at` | TIMESTAMP | NULLABLE | Approval timestamp |
| `sir_remark` | TEXT | NULLABLE | Management remarks |
| `finance_released_by`| BIGINT UNSIGNED| NULLABLE | Finance user ID |
| `finance_released_at`| DATE | NULLABLE | Release date |
| `finance_utr_no` | VARCHAR(100)| NULLABLE | Bank transaction reference / UTR |
| `finance_payment_mode`| VARCHAR(50)| DEFAULT 'NEFT' | Payment mode |
| `finance_remark` | TEXT | NULLABLE | Finance remarks |
| `supplier_dispatch_date` | DATE | NULLABLE | Promised dispatch date |
| `committed_dispatch_date` | DATE | NULLABLE | Alias for promised dispatch date |
| `actual_dispatch_date` | DATE | NULLABLE | Actual delivery / dispatch date |
| `dispatch_reminder_sent_at`| TIMESTAMP | NULLABLE | Tracks 7-day reminder dispatch |
| `production_milestone`| VARCHAR(50)| DEFAULT 'po_issued'| Production state |
| `lifecycle_status` | VARCHAR(30) | DEFAULT 'active', INDEX | Active / cancelled / archived |
| `special_instructions` | TEXT | NULLABLE | Printed in highlighted callout on PDF |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft deletes |

---

### `po_status_history`
Tracks transitions across all workflows.
- `id`: BIGINT AUTO_INCREMENT
- `po_no`: VARCHAR(100) INDEX
- `from_status`: VARCHAR(255) NULLABLE
- `to_status`: VARCHAR(255) NULLABLE
- `action`: VARCHAR(100) (`po_created`, `requested_advance`, `sir_approved`, `sir_rejected`, `finance_released`, `dispatch_reminder_sent`, `milestone_updated`)
- `user_id`: BIGINT UNSIGNED NULLABLE (Foreign key to `users`)
- `remarks`: TEXT NULLABLE
- `created_at`, `updated_at`: TIMESTAMP

---

### `po_line_accessories`
- `id`: BIGINT AUTO_INCREMENT
- `purchase_order_line_id`: BIGINT UNSIGNED FOREIGN KEY to `purchase_order_tb.id`
- `accessory_id`: BIGINT UNSIGNED NULLABLE
- `name`: VARCHAR(255)
- `part_no`: VARCHAR(100) NULLABLE
- `hsn`: VARCHAR(50) NULLABLE
- `unit_price`: DECIMAL(14,2)
- `quantity`: DECIMAL(12,3)
- `total`: DECIMAL(14,2)
