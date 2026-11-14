# Supplier Purchase Order: Test Suite & Quality Assurance

## 1. Overview
The Supplier Purchase Order module is thoroughly guarded by comprehensive automated feature tests in `tests/Feature/SupplierPurchaseOrderModuleTest.php`.

The suite tests every business requirement with realistic database fixtures, authenticated sessions, role permissions, and edge cases.

---

## 2. Test Cases Index

| # | Test Method Name | Verified Requirements |
| :- | :--- | :--- |
| 1 | `test_server_side_calculation_with_multi_row_discount_gst_freight_and_roundoff` | Tests multi-row creation with line discount, GST, freight, unloading, roundoff, and auto advance calculation. Enforces that client-manipulated totals are ignored and recomputed server-side. |
| 2 | `test_hsn_code_is_mandatory_with_row_specific_error_naming` | Tests that leaving the HSN code empty blocks saving with a validation error naming the row (e.g. `Row #1: HSN Code is mandatory.`). |
| 3 | `test_customer_site_delivery_requires_customer_site_selection` | Verifies that choosing `shipping_type = customer_site` without selecting a valid customer site is blocked by validation. |
| 4 | `test_po_creation_without_terms_condition_id` | Verifies that `terms_condition_id` is completely optional and POs save cleanly with default standard terms without prompting or requiring a template ID. |
| 5 | `test_supplier_branch_get_branches_api_returns_primary_and_secondary_branches` | Tests `/vendor/get-branches` returns both the registered office address and secondary factory units from `vendor_addresses`. |
| 6 | `test_dispatch_reminder_command_is_idempotent_and_delivers_notification` | Tests that `po:dispatch-reminders` alerts on POs due in 7 days, creates database and mail notifications, and running twice does not produce duplicates. |
| 7 | `test_dispatch_date_change_resets_reminder_sent_timestamp` | Verifies that amending the dispatch date in Edit PO resets `dispatch_reminder_sent_at` to `null` so reminders are rescheduled. |
| 8 | `test_advance_approval_flow_roles_and_po_status_history` | Tests the full 3-step advance approval lifecycle: CS requests advance $\rightarrow$ Super Admin approves $\rightarrow$ Finance releases with UTR $\rightarrow$ status changes to `advance_released` and status history audit trail is created. |

---

## 3. Running the Test Suite

Run the full suite in the active environment:
```bash
php artisan test --filter=SupplierPurchaseOrderModuleTest
```

Expected output:
```text
   PASS  Tests\Feature\SupplierPurchaseOrderModuleTest
  ✓ server side calculation with multi row discount gst freight and roundoff                                     0.46s  
  ✓ hsn code is mandatory with row specific error naming                                                         0.25s  
  ✓ customer site delivery requires customer site selection                                                      0.13s  
  ✓ po creation without terms condition id                                                                       0.18s  
  ✓ supplier branch get branches api returns primary and secondary branches                                      0.11s  
  ✓ dispatch reminder command is idempotent and delivers notification                                            0.15s  
  ✓ dispatch date change resets reminder sent timestamp                                                          0.16s  
  ✓ advance approval flow roles and po status history                                                            0.18s  

  Tests:    8 passed (43 assertions)
  Duration: 1.83s
```
