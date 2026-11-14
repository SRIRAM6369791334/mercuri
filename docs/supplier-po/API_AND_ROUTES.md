# Supplier Purchase Order: API & Route Reference

## Web & Controller Endpoints

### 1. Vendor Multi-Plant Lookup
- **Route**: `POST /vendor/get-branches`
- **Controller**: `PurchaseOrderController@getVendorBranches`
- **Middleware**: `auth`
- **Request Body**:
  ```json
  {
    "vendor_id": 14
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "branches": [
      {
        "id": "vendor:14",
        "address_id": null,
        "branch_name": "⭐ Primary Unit (Chennai)",
        "gst": "33AAACP5102D3Z5",
        "gst_number": "33AAACP5102D3Z5",
        "address": "SIPCOT Industrial Park, Sriperumbudur, Tamil Nadu - 602105",
        "label": "⭐ Primary Unit - GST: 33AAACP5102D3Z5 (Chennai)",
        "is_primary": true
      },
      {
        "id": "addr_8",
        "address_id": 8,
        "branch_name": "Unit II Plant - Bangalore",
        "gst": "29AAACP5102D1ZK",
        "gst_number": "29AAACP5102D1ZK",
        "address": "Peenya Industrial Area, Bangalore - 560058",
        "label": "[Unit II Plant] Bangalore - GST: 29AAACP5102D1ZK"
      }
    ]
  }
  ```

---

### 2. Advance Approval Workflow Endpoints

#### Step 1: Request Advance (Service Coordinator / Srinath)
- **Route**: `POST /po/request-advance`
- **Name**: `po.request-advance`
- **Controller**: `PurchaseOrderController@requestAdvance`
- **Allowed Roles**: `Super Admin`, `Purchase Manager`, `Service Coordinator`, `CS`, `Customer Support`, `Sales`
- **Request Body**:
  ```json
  {
    "po_no": "MES/PO/26-27/045",
    "advance_percentage": 30,
    "advance_amount": 17700.00,
    "notes": "Vendor requires 30% advance before cutting raw material."
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Advance approval request submitted to Sir / Management."
  }
  ```

#### Step 2: Management Approval (Super Admin / Sir)
- **Routes**: `POST /po/sir-approval`, `POST /po/sir-approve-advance`
- **Names**: `po.sir-approval`, `po.sir-approve-advance`
- **Controller**: `PurchaseOrderController@sirApproveAdvance`
- **Allowed Roles**: `Super Admin`, `Admin`, `Director`, `Management`
- **Request Body**:
  ```json
  {
    "po_no": "MES/PO/26-27/045",
    "action": "approve",
    "remark": "Approved for immediate bank transfer."
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Advance approved and sent to Finance."
  }
  ```

#### Step 3: Finance Release & UTR Recording
- **Routes**: `POST /po/finance-release`, `POST /po/finance-release-advance`
- **Names**: `po.finance-release`, `po.finance-release-advance`
- **Controller**: `PurchaseOrderController@financeReleaseAdvance`
- **Allowed Roles**: `Super Admin`, `Finance Manager`, `Finance`, `Accountant`, `Accounts`
- **Request Body**:
  ```json
  {
    "po_no": "MES/PO/26-27/045",
    "finance_utr_no": "HDFCR5202611130099",
    "payment_mode": "NEFT",
    "payment_date": "2026-11-13",
    "paid_amount": 17700.00,
    "remark": "NEFT transfer executed successfully."
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Advance release marked successfully with UTR reference."
  }
  ```

---

### 3. Production Milestone & Dispatch Updates
- **Route**: `POST /po/update-milestone`
- **Name**: `po.update-milestone`
- **Controller**: `PurchaseOrderController@updatePoMilestone`
- **Allowed Roles**: `Super Admin`, `Dispatch Manager`, `Purchase Manager`
- **Request Body**:
  ```json
  {
    "po_no": "MES/PO/26-27/045",
    "supplier_work_order_no": "WO-9912",
    "supplier_dispatch_date": "2026-11-20",
    "production_milestone": "in_fabrication",
    "actual_dispatch_date": null
  }
  ```

---

### 4. Notifications Endpoints
- **Route**: `GET /notifications/unread`
- **Controller**: `NotificationController@unread`
- **Route**: `POST /notifications/{id}/read`
- **Controller**: `NotificationController@markAsRead`
- **Route**: `POST /notifications/read-all`
- **Controller**: `NotificationController@markAllRead`
