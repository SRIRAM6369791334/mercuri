
@extends('layouts.app')
@section('title', 'Edit Purchase Order ' . ($firstPo->po_no ?? ''))
@section('main-content')

@can('purchase-order-edit')

    <style>
        /* ==========================================================================
           Executive B2B Procurement Design System (Decent, Clean, Professional)
           ========================================================================== */
        .po-page-wrapper {
            font-family: "Poppins", sans-serif !important;
            color: #1e293b;
        }
        .po-page-wrapper input,
        .po-page-wrapper select,
        .po-page-wrapper textarea,
        .po-page-wrapper button,
        .po-page-wrapper .form-control,
        .po-page-wrapper .form-select,
        .po-page-wrapper label,
        .po-page-wrapper table {
            font-family: "Poppins", sans-serif !important;
        }
        .po-header-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.03);
        }
        .po-header-title {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .po-header-title i {
            color: #2563eb;
            font-size: 1.15rem;
        }
        .po-header-subtitle {
            color: #64748b;
            font-size: 0.825rem;
            margin-top: 2px;
        }
        .po-btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 7px 16px;
            border-radius: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .po-btn-back:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* Enterprise Cards */
        .po-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
        }
        .po-card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .po-card-title {
            color: #0f172a;
            font-size: 0.925rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .po-card-title i {
            color: #2563eb;
            font-size: 0.95rem;
        }
        .po-card-badge {
            background: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .po-card-body {
            padding: 20px;
        }

        /* Form Elements */
        .po-label {
            font-size: 0.76rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
            display: block;
        }
        .po-label .req {
            color: #ef4444;
            font-weight: 700;
            margin-left: 2px;
        }
        .po-form .form-control,
        .po-form .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.52rem 0.8rem;
            font-size: 0.875rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .po-form .form-control:focus,
        .po-form .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }
        .po-form .form-control[readonly],
        .po-form .form-control:disabled {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #475569;
            font-weight: 500;
        }
        .po-hint {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 4px;
            display: block;
        }

        /* Destination Type Options */
        .po-dest-pill {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 14px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .po-dest-pill:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Line Items Container */
        .po-items-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .po-items-heading {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .po-btn-add-item {
            background: #ffffff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            font-weight: 600;
            font-size: 0.825rem;
            padding: 6px 14px;
            border-radius: 6px;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .po-btn-add-item:hover {
            background: #eff6ff;
            border-color: #2563eb;
            color: #1d4ed8;
        }

        /* Individual Line Item Box */
        .po-item-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.02);
            position: relative;
            transition: border-color 0.15s ease;
        }
        .po-item-row:hover {
            border-color: #cbd5e1;
        }
        .po-item-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .po-item-number {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.74rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.3px;
        }
        .po-item-delete-btn {
            background: #ffffff;
            border: 1px solid #fecaca;
            color: #dc2626;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 4px;
            transition: all 0.15s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .po-item-delete-btn:hover {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        /* Executive Calculation Summary */
        .po-calc-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 24px;
            margin-top: 10px;
        }
        .po-calc-metric {
            display: flex;
            flex-direction: column;
        }
        .po-metric-title {
            font-size: 0.74rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .po-metric-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }
        .po-grand-total-block {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 18px;
        }
        .po-grand-total-val {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }

        /* Special Instructions Advisory Callout */
        .po-advisory-card {
            background: #fffdf5;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
            padding: 14px 16px;
        }

        /* Primary Action Button */
        .po-submit-action-btn {
            background: #1e3a8a;
            color: #ffffff;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 36px;
            border-radius: 6px;
            box-shadow: 0 2px 4px 0 rgba(30, 58, 138, 0.2);
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .po-submit-action-btn:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 8px 0 rgba(30, 58, 138, 0.3);
            transform: translateY(-1px);
            color: #ffffff;
        }
    </style>

    <div class="po-page-wrapper">
        <div class="container-fluid mt-3 mb-4">
            
            <!-- Page Header Bar -->
            <div class="po-header-bar d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="po-header-title">
                        <i class="fas fa-edit"></i> Edit Purchase Order: {{ $firstPo->po_no ?? '' }}
                    </h4>
                    <div class="po-header-subtitle">Update supplier details, destination, commercial terms and line item pricing</div>
                </div>
                <div>
                    <a onclick="return window.history.back()" class="po-btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <form method="POST" action="/edit-po" enctype="multipart/form-data" class="po-form edit-po-form" id="editPoForm">
                @csrf

                <!-- ========================================================================= -->
                <!-- 1. SUPPLIER DETAILS & SHIP-TO DETAILS (ALIGNED SIDE BY SIDE AT TOP)       -->
                <!-- ========================================================================= -->
                <div class="row g-3 mb-4">
                    
                    <!-- LEFT CARD: SUPPLIER DETAILS -->
                    <div class="col-lg-6">
                        <div class="po-card h-100">
                            <div class="po-card-header">
                                <h6 class="po-card-title">
                                    <i class="fas fa-building"></i> Supplier Details &amp; Billing Branch
                                </h6>
                                <span class="po-card-badge">Supplier Master</span>
                            </div>
                            <div class="po-card-body">
                                
                                <!-- Supplier Selection -->
                                <div class="mb-3">
                                    <label class="po-label">Select Supplier (Vendor) <span class="req">*</span></label>
                                    <select class="form-select" name="primary_vendor_id" id="primary_vendor_id" required>
                                        <option value="">-- Choose Supplier --</option>
                                        @if (!empty($allVendors))
                                            @foreach ($allVendors as $v)
                                                <option value="{{ $v->id }}" 
                                                    data-gst="{{ $v->gst_number }}" 
                                                    data-address="{{ $v->address }}"
                                                    data-name="{{ $v->company_name }}"
                                                    {{ ($firstPo->vendor_id == $v->id) ? 'selected' : '' }}>
                                                    {{ $v->company_name }} ({{ $v->vendor_id }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Supplier Billing Address Dropdown (Branch by GST) -->
                                <div class="mb-3">
                                    <label class="po-label">Supplier Billing Address / Branch (by GST) <span class="req">*</span></label>
                                    <select class="form-select" id="supplier_branch_dropdown">
                                        <option value="">-- Main Branch / Current Billing Address --</option>
                                    </select>
                                    <span class="po-hint">Branch details automatically loaded based on Supplier GST</span>
                                </div>

                                <!-- Supplier GSTIN -->
                                <div class="mb-3">
                                    <label class="po-label">Supplier GSTIN <span class="req">*</span></label>
                                    <input type="text" name="supplier_gst" id="supplier_gst" class="form-control text-uppercase" 
                                        value="{{ $firstPo->supplier_gst ?? $firstPo->vendorDetails?->gst_number }}" required>
                                </div>

                                <!-- Supplier Billing Full Address -->
                                <div class="mb-0">
                                    <label class="po-label">Supplier Billing Address <span class="req">*</span></label>
                                    <textarea name="supplier_billing_address" id="supplier_billing_address" class="form-control" rows="3" required>{{ $firstPo->supplier_billing_address ?? $firstPo->vendorDetails?->address }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT CARD: SHIP-TO DETAILS -->
                    <div class="col-lg-6">
                        <div class="po-card h-100">
                            <div class="po-card-header">
                                <h6 class="po-card-title">
                                    <i class="fas fa-truck"></i> Ship-To Details &amp; Destination
                                </h6>
                                <span class="po-card-badge">Consignee</span>
                            </div>
                            <div class="po-card-body">

                                <!-- Delivery Type Selector -->
                                <div class="mb-3">
                                    <label class="po-label">Delivery Destination Type <span class="req">*</span></label>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <div class="form-check po-dest-pill flex-fill">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="shipping_type" id="shipping_type_warehouse" value="warehouse" 
                                                {{ ($firstPo->shipping_type ?? 'warehouse') == 'warehouse' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark mb-0" for="shipping_type_warehouse" style="cursor:pointer;">
                                                <i class="fas fa-warehouse text-primary me-1"></i> Mercuriis Warehouse (Padappai)
                                            </label>
                                        </div>
                                        <div class="form-check po-dest-pill flex-fill">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="shipping_type" id="shipping_type_customer" value="customer_site"
                                                {{ ($firstPo->shipping_type ?? '') == 'customer_site' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark mb-0" for="shipping_type_customer" style="cursor:pointer;">
                                                <i class="fas fa-map-marker-alt text-primary me-1"></i> Direct Customer Site Delivery
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Lookup Row -->
                                <div id="customer_site_box" class="p-3 mb-3 rounded bg-light border" style="{{ ($firstPo->shipping_type ?? '') == 'customer_site' ? '' : 'display: none;' }}">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-6">
                                            <label class="po-label mb-1">Select Customer</label>
                                            <select class="form-select form-select-sm" id="customer_select_dropdown">
                                                <option value="">-- Choose Customer --</option>
                                                @if (!empty($allCustomers))
                                                    @foreach ($allCustomers as $cust)
                                                        <option value="{{ $cust->id }}" 
                                                            data-gst="{{ $cust->gstno }}" 
                                                            data-address="{{ $cust->address ?: $cust->communication_address }}" 
                                                            data-name="{{ $cust->customername }}">
                                                            {{ $cust->customername }} ({{ $cust->gstno ?: 'No GST' }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="po-label mb-1">Or Customer GSTIN</label>
                                            <input type="text" class="form-control form-control-sm text-uppercase" id="search_customer_gst" placeholder="Search GSTIN">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-bold" id="btn_fetch_gst" title="Fetch Address by GST">
                                                <i class="fas fa-search"></i> Fetch
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destination GSTIN -->
                                <div class="mb-3">
                                    <label class="po-label">Destination GSTIN <span class="req">*</span></label>
                                    <input type="text" name="shipping_gst" id="shipping_gst" class="form-control text-uppercase" 
                                        value="{{ $firstPo->shipping_gst ?? '33AATCM0824C1ZQ' }}" required>
                                </div>

                                <!-- Shipping / Delivery Full Address -->
                                <div class="mb-0">
                                    <label class="po-label">Delivery Full Address <span class="req">*</span></label>
                                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required>{{ $firstPo->shipping_address ?? "Mercuriis Storage & Equipments Pvt Ltd\n89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu\nGSTIN: 33AATCM0824C1ZQ" }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- 2. PAYMENT TERMS, LEAD TIME & COMMERCIAL CONDITIONS (AT TOP)             -->
                <!-- ========================================================================= -->
                <div class="po-card mb-4">
                    <div class="po-card-header">
                        <h6 class="po-card-title">
                            <i class="fas fa-file-contract"></i> Commercial Terms &amp; Lead Time
                        </h6>
                        <span class="po-card-badge">Commercials</span>
                    </div>
                    <div class="po-card-body">
                        <div class="row g-3">
                            
                            <!-- Payment Terms -->
                            <div class="col-md-4">
                                <label class="po-label">Payment Terms <span class="req">*</span></label>
                                <input type="text" name="payment_terms" id="payment_terms" list="payment_terms_options" class="form-control" 
                                    value="{{ $firstPo->payment_terms ?? '50% Advance & 50% Against Proforma' }}" required>
                                <datalist id="payment_terms_options">
                                    <option value="100% Advance with Purchase Order">
                                    <option value="50% Advance & 50% Against Proforma">
                                    <option value="30% Advance & 70% before Dispatch">
                                    <option value="20% Advance & 80% against Delivery">
                                    <option value="Payment within 15 days from Invoice Date">
                                    <option value="Payment within 30 days from Invoice Date">
                                    <option value="Payment within 45 days from Invoice Date">
                                    <option value="Payment against Delivery">
                                    <option value="100% Against Proforma Invoice">
                                    <option value="As per Agreed Contract Terms">
                                </datalist>
                                <span class="po-hint">Preset options or custom payment terms</span>
                            </div>

                            <!-- Lead Time -->
                            <div class="col-md-4">
                                <label class="po-label">Committed Lead Time <span class="req">*</span></label>
                                <input type="text" name="lead_time" id="lead_time" list="lead_time_options" class="form-control" 
                                    value="{{ $firstPo->lead_time ?? '4 Weeks' }}" required>
                                <datalist id="lead_time_options">
                                    <option value="Immediate / Ex-Stock">
                                    <option value="1-2 Weeks">
                                    <option value="2-3 Weeks">
                                    <option value="3-4 Weeks">
                                    <option value="4 Weeks">
                                    <option value="5-6 Weeks">
                                    <option value="6-8 Weeks">
                                    <option value="8-10 Weeks">
                                    <option value="10-12 Weeks">
                                    <option value="Custom Duration">
                                </datalist>
                                <span class="po-hint">Production &amp; dispatch schedule</span>
                            </div>

                            <!-- Warranty Terms -->
                            <div class="col-md-4">
                                <label class="po-label">Warranty Period</label>
                                <input type="text" name="warranty_terms" class="form-control" 
                                    value="{{ $firstPo->warranty_terms ?? '12 Months Standard Manufacturer Warranty' }}">
                            </div>

                            <!-- Installation Scope -->
                            <div class="col-md-4">
                                <label class="po-label">Installation Scope <span class="req">*</span></label>
                                <select name="installation_scope" id="installation_scope" class="form-select">
                                    <option value="In Supplier Scope (Included in Unit Rate)" {{ ($firstPo->installation_scope ?? '') == 'In Supplier Scope (Included in Unit Rate)' ? 'selected' : '' }}>In Supplier Scope (Included in Unit Rate)</option>
                                    <option value="Not in Supplier Scope (Supply Only)" {{ ($firstPo->installation_scope ?? '') == 'Not in Supplier Scope (Supply Only)' ? 'selected' : '' }}>Not in Supplier Scope (Supply Only)</option>
                                    <option value="Customer Scope at Site" {{ ($firstPo->installation_scope ?? '') == 'Customer Scope at Site' ? 'selected' : '' }}>Customer Scope at Site</option>
                                    <option value="Mercuriis Scope" {{ ($firstPo->installation_scope ?? '') == 'Mercuriis Scope' ? 'selected' : '' }}>Mercuriis Scope</option>
                                </select>
                                <span class="po-hint">Installation scope (no separate charges)</span>
                            </div>

                            <!-- Terms Template -->
                            <div class="col-md-4">
                                <label class="po-label">Terms Template <span class="req">*</span></label>
                                <select class="form-select terms_condition_id" name="terms_condition_id" required>
                                    <option value="">-- Choose Terms Template --</option>
                                    @if ($status = App\Models\Term::where("whichterm", 2)->get())
                                        @foreach ($status as $data)
                                            <option value="{{ $data->id }}" {{ ($firstPo->terms_condition_id == $data->id) ? 'selected' : '' }}>{{ $data->content }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Option to Hide Make/Brand Names -->
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check mt-3 p-2 px-3 border rounded bg-light w-100 d-flex align-items-center gap-2">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="hide_brand_name" value="1" id="hide_brand_name"
                                        {{ !empty($firstPo->hide_brand_name) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark mb-0 small" for="hide_brand_name" style="cursor:pointer;">
                                        <i class="fas fa-eye-slash text-muted me-1"></i> Hide Make/Brand on Supplier PO
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- 3. SCOPE OF SUPPLY: PRODUCTS, SPECS & CATEGORIES                          -->
                <!-- ========================================================================= -->
                <div class="po-items-section-header">
                    <h5 class="po-items-heading">
                        <i class="fas fa-boxes text-primary"></i> Scope of Supply: Line Items
                    </h5>
                    <button type="button" class="po-btn-add-item" id="po_add_product_plus">
                        <i class="fas fa-plus"></i> Add Line Item
                    </button>
                </div>

                <div id="po_products_container">
                    @foreach($poDetails as $key => $poItem)
                        <div class="po-item-row po-form-box">
                            <div class="po-item-meta">
                                <span class="po-item-number"><i class="fas fa-cube me-1"></i> Item #{{ $loop->iteration }}</span>
                                @if ($loop->index > 0)
                                    <button type="button" class="po-item-delete-btn po-remove-btn">
                                        <i class="fas fa-trash-alt"></i> Remove Item
                                    </button>
                                @endif
                            </div>

                            <input type="hidden" name="po_id[]" value="{{ $poItem->id }}">
                            <input type="hidden" name="po_number[]" value="{{ $poItem->po_no }}">

                            <div class="row g-2">
                                <!-- Product Category -->
                                <div class="col-lg-4">
                                    <div class="mb-2">
                                        <label class="po-label">Product Category <span class="req">*</span></label>
                                        <select class="form-select product_category_po" name="catId[]" required>
                                            <option value="">-- Choose Category --</option>
                                            @if ($categories = App\Models\ProductCategory::get())
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ $cat->id == $poItem->cat_id ? 'selected' : '' }}>
                                                        {{ $cat->category_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- Products Dropdown -->
                                <div class="col-lg-5">
                                    <div class="mb-2">
                                        <label class="po-label">Product <span class="req">*</span></label>
                                        <select class="form-select products_dropdown_po" name="proId[]" required>
                                            <option value="">-- Choose Product --</option>
                                            @if ($products = App\Models\Product::with('stock')->where('Productcategory', $poItem->cat_id)->get())
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->id }}" 
                                                        {{ $p->id == $poItem->product_id ? 'selected' : '' }}
                                                        data-existquantity="{{ $p->stock ? $p->stock->available_stock : 0 }}" 
                                                        data-rate="{{ $p->rate }}" 
                                                        data-gst="{{ $p->gst }}">
                                                        {{ $p->productname }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- Vendor Override -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label class="po-label">Vendor Override</label>
                                        <select class="form-select vendor_dropdown_po" name="vendorId[]">
                                            <option value="">-- Same as Primary --</option>
                                            @if (!empty($allVendors))
                                                @foreach ($allVendors as $v)
                                                    <option value="{{ $v->id }}" {{ $v->id == $poItem->vendor_id ? 'selected' : '' }}>
                                                        {{ $v->company_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label class="po-label">Quantity <span class="req">*</span></label>
                                        <input type="number" name="qty[]" class="form-control po_qty fw-bold" value="{{ $poItem->product_qty }}" min="1" required>
                                    </div>
                                </div>

                                <!-- Unit Price -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label class="po-label">Supplier Unit Rate (₹) <span class="req">*</span></label>
                                        <input type="number" step="0.01" name="unit_price[]" class="form-control po_unit_price fw-bold" value="{{ $poItem->unit_price }}" required>
                                    </div>
                                </div>

                                <!-- GST % -->
                                <div class="col-lg-2">
                                    <div class="mb-2">
                                        <label class="po-label">GST (%) <span class="req">*</span></label>
                                        <input type="text" name="gst[]" class="form-control po_gst" 
                                            value="{{ (int)($poItem->productDetails?->gst ?? 18) }}" required readonly>
                                    </div>
                                </div>

                                <!-- Line Total (Inc. GST) -->
                                <div class="col-lg-4">
                                    <div class="mb-2">
                                        <label class="po-label">Line Total (Inc. GST) <span class="req">*</span></label>
                                        <input type="text" name="product_total[]" class="form-control po_pro_tot_amt fw-bold" 
                                            value="{{ $poItem->total_amount }}" readonly>
                                        <input type="hidden" name="sub_total[]" class="product_subtotal" value="{{ $poItem->sub_total ?? $poItem->product_total_price }}">
                                    </div>
                                </div>

                                <!-- Product Description & Specifications -->
                                <div class="col-lg-12">
                                    <div class="mb-0">
                                        <label class="po-label">Product Description &amp; Technical Specifications</label>
                                        <textarea name="pro_des[]" class="form-control po_pro_des" rows="2">{{ trim($poItem->product_description ?? '') }}</textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ========================================================================= -->
                <!-- 4. CHARGES, DISCOUNT & TOTAL SUMMARY                                      -->
                <!-- ========================================================================= -->
                <div class="po-card mb-4">
                    <div class="po-card-header">
                        <h6 class="po-card-title">
                            <i class="fas fa-calculator"></i> Commercial Calculation &amp; Summary
                        </h6>
                        <span class="po-card-badge">Summary</span>
                    </div>
                    <div class="po-card-body">
                        <div class="row g-3">
                            
                            <!-- Gross Items Subtotal -->
                            <div class="col-md-3">
                                <label class="po-label">Gross Items Subtotal (₹)</label>
                                <input type="text" id="gross_items_subtotal" class="form-control fw-bold" readonly value="0.00">
                            </div>

                            <!-- Discount % -->
                            <div class="col-md-3">
                                <label class="po-label">Discount %</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="discount_percentage" 
                                        class="form-control fw-bold" value="{{ $firstPo->discount_percentage ?? 0.00 }}" placeholder="0">
                                    <span class="input-group-text bg-light text-muted fw-bold">%</span>
                                </div>
                                <small class="text-danger fw-bold d-block mt-1" id="discount_deduction_label">Less Discount: -₹0.00</small>
                            </div>

                            <!-- Freight / Transportation Charges -->
                            <div class="col-md-3">
                                <label class="po-label">Freight / Transportation (₹)</label>
                                <input type="number" step="0.01" min="0" name="freight_charges" id="freight_charges" 
                                    class="form-control fw-bold" value="{{ $firstPo->freight_charges ?? 0.00 }}" placeholder="0.00">
                                <span class="po-hint">Direct transport charges</span>
                            </div>

                            <!-- Unloading Charges -->
                            <div class="col-md-3">
                                <label class="po-label">Unloading Charges (₹)</label>
                                <input type="number" step="0.01" min="0" name="unloading_charges" id="unloading_charges" 
                                    class="form-control fw-bold" value="{{ $firstPo->unloading_charges ?? 0.00 }}" placeholder="0.00">
                                <span class="po-hint">Material handling at site</span>
                            </div>

                            <!-- Executive Grand Total Display -->
                            <div class="col-md-12">
                                <div class="po-calc-panel">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-4">
                                            <div class="po-calc-metric">
                                                <span class="po-metric-title">Net Taxable Value</span>
                                                <span class="po-metric-value" id="net_taxable_display">₹0.00</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="po-calc-metric">
                                                <span class="po-metric-title">Total GST Applicable</span>
                                                <span class="po-metric-value text-primary" id="total_gst_display">₹0.00</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="po-grand-total-block text-md-end">
                                                <span class="po-metric-title d-block">PO Grand Total (INR)</span>
                                                <span class="po-grand-total-val" id="grand_total_display">₹0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- 5. SPECIAL INSTRUCTIONS & ATTACHMENTS                                      -->
                <!-- ========================================================================= -->
                <div class="po-card mb-4">
                    <div class="po-card-header">
                        <h6 class="po-card-title">
                            <i class="fas fa-paperclip"></i> Instructions &amp; Document Attachments
                        </h6>
                        <span class="po-card-badge">Attachments</span>
                    </div>
                    <div class="po-card-body">
                        <div class="row g-3">
                            
                            <!-- Special Instructions -->
                            <div class="col-md-12">
                                <div class="po-advisory-card">
                                    <label class="po-label text-warning-emphasis mb-1">
                                        <i class="fas fa-info-circle me-1"></i> Special Instructions (Highlighted in PO PDF)
                                    </label>
                                    <textarea name="special_instructions" class="form-control mt-1" rows="2" placeholder="Special instructions for the supplier">{{ $firstPo->special_instructions ?? '' }}</textarea>
                                    <span class="po-hint text-muted">Special instructions will appear prominently in the official PO document.</span>
                                </div>
                            </div>

                            <!-- Supplier Quotations (PDF & Excel) -->
                            <div class="col-md-6">
                                <label class="po-label">Supplier Quotations <span class="badge bg-light text-dark border">PDF / Excel</span></label>
                                <input type="file" name="quote_attachments[]" multiple class="form-control" accept=".pdf,.xls,.xlsx,.csv">
                                <input type="hidden" name="existing_quote_attachments" value="{{ $firstPo->quote_attachments ?? '' }}">
                                @php
                                    $existingQuotes = !empty($firstPo->quote_attachments) ? json_decode($firstPo->quote_attachments, true) : [];
                                @endphp
                                @if (!empty($existingQuotes) && is_array($existingQuotes))
                                    <div class="mt-2">
                                        <span class="po-hint fw-bold">Existing Quotation Files:</span>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @foreach ($existingQuotes as $qf)
                                                <a href="/uploads/po_files/{{ $qf }}" target="_blank" class="badge bg-light text-dark border p-1 text-decoration-none">
                                                    <i class="fas fa-file-pdf text-danger me-1"></i> {{ $qf }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <span class="po-hint">Upload supplier quotation sheets (PDF / Excel)</span>
                            </div>

                            <!-- Approved Drawing -->
                            <div class="col-md-6">
                                <label class="po-label">Approved Drawing <span class="badge bg-light text-dark border">PDF / DWG / Image</span></label>
                                <input type="file" name="drawing_attachment" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.dwg">
                                <input type="hidden" name="existing_drawing_attachment" value="{{ $firstPo->drawing_attachment ?? '' }}">
                                @if (!empty($firstPo->drawing_attachment))
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <span class="text-truncate d-inline-block small fw-bold text-success" style="max-width: 200px;">
                                            <i class="fas fa-check-circle"></i> {{ $firstPo->drawing_attachment }}
                                        </span>
                                        <a href="/uploads/po_files/{{ $firstPo->drawing_attachment }}" target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                @endif
                                <span class="po-hint">Upload approved technical / fabrication drawing</span>
                            </div>

                            <!-- General File Attachment -->
                            <div class="col-md-6">
                                <label class="po-label">Other File Attachment</label>
                                <input type="file" name="po_attachment" class="form-control">
                                <span class="po-hint">Upload any additional PO documents</span>
                            </div>

                            @if (!empty($firstPo->files))
                                <div class="col-md-6">
                                    <label class="po-label">Existing PO Attachment</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="existing_file_name" value="{{ $firstPo->files }}">
                                        <span class="text-truncate d-inline-block small fw-bold text-dark" style="max-width: 250px;">{{ $firstPo->files }}</span>
                                        <a href="/uploads/po_files/{{ $firstPo->files }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Current File
                                        </a>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <button type="submit" class="po-submit-action-btn edit-po-btn">
                        <i class="fas fa-save"></i> Update Purchase Order
                    </button>
                </div>
            </form>

        </div>
    </div>

@endcan

@endsection

@section('scripts')

    <script>
        // Warehouse Default Details
        const defaultWarehouseAddress = "Mercuriis Storage & Equipments Pvt Ltd\n89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu\nGSTIN: 33AATCM0824C1ZQ";
        const defaultWarehouseGst = "33AATCM0824C1ZQ";

        // =========================================================================
        // 1. SUPPLIER SELECTION & BRANCH FETCH
        // =========================================================================
        $('#primary_vendor_id').on('change', function() {
            let vendorId = $(this).val();
            let $opt = $(this).find(':selected');
            let initialGst = $opt.data('gst') || '';
            let initialAddr = $opt.data('address') || '';

            if (!$('#supplier_gst').val()) $('#supplier_gst').val(initialGst);
            if (!$('#supplier_billing_address').val()) $('#supplier_billing_address').val(initialAddr);

            if (!vendorId) {
                $('#supplier_branch_dropdown').empty().append('<option value="">-- Select Supplier First --</option>');
                return;
            }

            $.ajax({
                url: '/vendor/get-branches',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    vendor_id: vendorId
                },
                success: function(res) {
                    $('#supplier_branch_dropdown').empty();
                    if (res.status === 'success' && res.branches && res.branches.length > 0) {
                        res.branches.forEach(function(b, idx) {
                            $('#supplier_branch_dropdown').append(
                                `<option value="${b.vendor_id}" data-gst="${b.gst || ''}" data-address="${b.address || ''}">${b.label || b.branch_name}</option>`
                            );
                        });
                    } else {
                        $('#supplier_branch_dropdown').append('<option value="">Main Branch (Registered Address)</option>');
                    }
                },
                error: function() {
                    $('#supplier_branch_dropdown').empty().append('<option value="">Main Branch (Registered Address)</option>');
                }
            });
        });

        $('#supplier_branch_dropdown').on('change', function() {
            let $opt = $(this).find(':selected');
            let gst = $opt.data('gst') || '';
            let addr = $opt.data('address') || '';
            if (gst) $('#supplier_gst').val(gst);
            if (addr) $('#supplier_billing_address').val(addr);
        });

        // Trigger branch lookup on document ready if vendor selected
        $(document).ready(function() {
            if ($('#primary_vendor_id').val()) {
                let vendorId = $('#primary_vendor_id').val();
                $.ajax({
                    url: '/vendor/get-branches',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        vendor_id: vendorId
                    },
                    success: function(res) {
                        $('#supplier_branch_dropdown').empty();
                        if (res.status === 'success' && res.branches && res.branches.length > 0) {
                            res.branches.forEach(function(b) {
                                let curGst = $('#supplier_gst').val();
                                let isSel = (b.gst && b.gst === curGst) ? 'selected' : '';
                                $('#supplier_branch_dropdown').append(
                                    `<option value="${b.vendor_id}" data-gst="${b.gst || ''}" data-address="${b.address || ''}" ${isSel}>${b.label || b.branch_name}</option>`
                                );
                            });
                        } else {
                            $('#supplier_branch_dropdown').append('<option value="">Main Branch (Registered Address)</option>');
                        }
                    }
                });
            }
        });

        // =========================================================================
        // 2. SHIPPING DESTINATION TOGGLE & CUSTOMER LOOKUP
        // =========================================================================
        $('input[name="shipping_type"]').on('change', function() {
            if ($(this).val() === 'customer_site') {
                $('#customer_site_box').slideDown();
            } else {
                $('#customer_site_box').slideUp();
                $('#shipping_address').val(defaultWarehouseAddress);
                $('#shipping_gst').val(defaultWarehouseGst);
            }
        });

        $('#customer_select_dropdown').on('change', function() {
            let $opt = $(this).find(':selected');
            let gst = $opt.data('gst') || '';
            let addr = $opt.data('address') || '';
            let name = $opt.data('name') || '';

            if (gst) $('#search_customer_gst').val(gst);
            if (addr) {
                let fullText = name ? (name + "\n" + addr) : addr;
                $('#shipping_address').val(fullText);
            }
            if (gst) {
                $('#shipping_gst').val(gst);
            }
        });

        $('#btn_fetch_gst').on('click', function() {
            let gst = $('#search_customer_gst').val().trim();
            if (!gst) {
                alert('Please enter a Customer GST Number');
                return;
            }

            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '/customer/get-by-gst',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    gst_no: gst
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Fetch');
                    if (res.status === 'success' && res.customer) {
                        let c = res.customer;
                        let text = (c.name ? c.name + "\n" : "") + (c.address || "");
                        $('#shipping_address').val(text);
                        $('#shipping_gst').val(c.gst || gst);
                    } else {
                        alert(res.message || 'No customer found for this GST');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Fetch');
                    alert(xhr.responseJSON?.message || 'Customer with this GST not found in system.');
                }
            });
        });

        // =========================================================================
        // 3. ROW TOTALS & CHARGES CALCULATION
        // =========================================================================
        function recalculateAllTotals() {
            let grossItemsSubtotal = 0;
            let totalGstAmt = 0;

            let discountPct = parseFloat($('#discount_percentage').val()) || 0;
            if (discountPct < 0) discountPct = 0;
            if (discountPct > 100) discountPct = 100;

            let freight = parseFloat($('#freight_charges').val()) || 0;
            let unloading = parseFloat($('#unloading_charges').val()) || 0;

            $('.po-form-box').each(function() {
                let $box = $(this);
                let qty = parseFloat($box.find('.po_qty').val()) || 0;
                let rate = parseFloat($box.find('.po_unit_price').val()) || 0;
                let gst = parseFloat($box.find('.po_gst').val()) || 0;

                let lineSubtotal = qty * rate;
                grossItemsSubtotal += lineSubtotal;

                let lineDiscount = (lineSubtotal * discountPct) / 100;
                let lineTaxable = lineSubtotal - lineDiscount;
                let lineGstAmt = (lineTaxable * gst) / 100;
                let lineTotal = lineTaxable + lineGstAmt;

                totalGstAmt += lineGstAmt;

                $box.find('.product_subtotal').val(lineSubtotal.toFixed(2));
                $box.find('.po_pro_tot_amt').val(lineTotal.toFixed(2));
            });

            let discountAmt = (grossItemsSubtotal * discountPct) / 100;
            let netTaxable = grossItemsSubtotal - discountAmt;
            let grandTotal = netTaxable + freight + unloading + totalGstAmt;

            $('#gross_items_subtotal').val('₹' + grossItemsSubtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#discount_deduction_label').text('Less Discount (' + discountPct + '%): -₹' + discountAmt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#net_taxable_display').text('₹' + netTaxable.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#total_gst_display').text('₹' + totalGstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#grand_total_display').text('₹' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        $(document).on('input change', '.po_qty, .po_unit_price, .po_gst, #discount_percentage, #freight_charges, #unloading_charges', function() {
            recalculateAllTotals();
        });

        $(document).ready(function() {
            recalculateAllTotals();
        });

        // =========================================================================
        // 4. ADD & REMOVE PRODUCT ROW
        // =========================================================================
        $('#po_add_product_plus').on('click', function() {
            let nextIndex = $('.po-form-box').length + 1;
            let rowHtml = `
            <div class="po-item-row po-form-box">
                <div class="po-item-meta">
                    <span class="po-item-number"><i class="fas fa-cube me-1"></i> Item #${nextIndex}</span>
                    <button type="button" class="po-item-delete-btn po-remove-btn">
                        <i class="fas fa-trash-alt"></i> Remove Item
                    </button>
                </div>
                <input type="hidden" name="po_id[]" value="0">
                <input type="hidden" name="po_number[]" value="{{ $firstPo->po_no ?? '' }}">
                <div class="row g-2">
                    <div class="col-lg-4">
                        <div class="mb-2">
                            <label class="po-label">Product Category <span class="req">*</span></label>
                            <select class="form-select product_category_po" name="catId[]" required>
                                <option value="">-- Choose Category --</option>
                                @if ($status = App\Models\ProductCategory::get())
                                    @foreach ($status as $data)
                                        <option value="{{ $data->id }}">{{ $data->category_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mb-2">
                            <label class="po-label">Product <span class="req">*</span></label>
                            <select class="form-select products_dropdown_po" name="proId[]" required>
                                <option value="">-- Choose Category First --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label class="po-label">Vendor Override</label>
                            <select class="form-select vendor_dropdown_po" name="vendorId[]">
                                <option value="">-- Same as Primary --</option>
                                @if (!empty($allVendors))
                                    @foreach ($allVendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label class="po-label">Quantity <span class="req">*</span></label>
                            <input type="number" name="qty[]" class="form-control po_qty fw-bold" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label class="po-label">Supplier Unit Rate (₹) <span class="req">*</span></label>
                            <input type="number" step="0.01" name="unit_price[]" class="form-control po_unit_price fw-bold" value="0.00" required>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="mb-2">
                            <label class="po-label">GST (%) <span class="req">*</span></label>
                            <input type="text" name="gst[]" class="form-control po_gst" value="18" required readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-2">
                            <label class="po-label">Line Total (Inc. GST) <span class="req">*</span></label>
                            <input type="text" name="product_total[]" class="form-control po_pro_tot_amt fw-bold" readonly>
                            <input type="hidden" name="sub_total[]" class="product_subtotal">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-0">
                            <label class="po-label">Product Description &amp; Technical Specifications</label>
                            <textarea name="pro_des[]" class="form-control po_pro_des" rows="2" placeholder="Item description and technical specifications"></textarea>
                        </div>
                    </div>
                </div>
            </div>`;
            $('#po_products_container').append(rowHtml);
            recalculateAllTotals();
        });

        $(document).on('click', '.po-remove-btn', function() {
            if ($('.po-form-box').length > 1) {
                $(this).closest('.po-form-box').remove();
                // Renumber items
                $('.po-form-box').each(function(i) {
                    $(this).find('.po-item-number').html('<i class="fas fa-cube me-1"></i> Item #' + (i + 1));
                });
                recalculateAllTotals();
            } else {
                alert('At least one line item is required.');
            }
        });

        // Dynamic Product Dropdown on Category Change
        $(document).on('change', '.product_category_po', function() {
            let catId = $(this).val();
            let $prodSelect = $(this).closest('.po-form-box').find('.products_dropdown_po');
            $prodSelect.empty().append('<option value="">Loading products...</option>');

            if (!catId) {
                $prodSelect.empty().append('<option value="">-- Choose Category First --</option>');
                return;
            }

            $.ajax({
                url: '/get-pro-details',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: catId
                },
                success: function(res) {
                    $prodSelect.empty().append('<option value="">-- Choose Product --</option>');
                    if (res && res.length > 0) {
                        res.forEach(function(p) {
                            let stock = p.stock ? p.stock.available_stock : 0;
                            $prodSelect.append(`<option value="${p.id}" data-rate="${p.rate}" data-gst="${p.gst}" data-existquantity="${stock}">${p.productname}</option>`);
                        });
                    }
                }
            });
        });

        $(document).on('change', '.products_dropdown_po', function() {
            let $opt = $(this).find(':selected');
            let rate = $opt.data('rate') || 0;
            let gst = $opt.data('gst') || 18;
            let $box = $(this).closest('.po-form-box');

            $box.find('.po_unit_price').val(rate);
            $box.find('.po_gst').val(gst);
            recalculateAllTotals();
        });

    </script>

@endsection