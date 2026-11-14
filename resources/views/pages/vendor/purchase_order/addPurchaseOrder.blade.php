@extends('layouts.app')
@section('title', 'Create Purchase Order')
@section('main-content')

@can('purchase-order-create')

<style>
    .form-label,
    label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
        color: #374151;
    }

    .form-control,
    textarea {
        font-size: 12.5px;
        padding: 6px 10px;
        border-radius: 6px;
    }

    .form-select {
        font-size: 12.5px;
        padding: 6px 30px 6px 10px !important;
        border-radius: 6px;
        background-position: right 10px center !important;
        background-repeat: no-repeat !important;
        text-overflow: ellipsis;
    }

    textarea {
        min-height: 60px;
    }
    .mt-3 { margin-top: 10px !important; }
    .mt-2 { margin-top: 6px !important; }
    .mb-3 { margin-bottom: 10px !important; }

    section.bg-white {
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }
    input[readonly],
    textarea[readonly],
    select:disabled {
        background-color: #f1f5f9 !important;
        color: #475569;
        cursor: not-allowed;
    }
    .nav-tabs {
        border-bottom: 1px solid #e5e7eb;
    }

    .nav-tabs .nav-link {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        padding: 10px 16px;
    }

    .nav-tabs .nav-link.active {
        color: #111827;
        border-color: transparent transparent #2563eb;
        border-bottom: 3px solid #2563eb;
    }
    .form-group {
        margin-bottom: 10px;
    }
    .btn {
        font-size: 12px;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        border: none;
        color: #ffffff;
    }
    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff;
    }

    .quotation-table th {
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
        background-color: #f8fafc;
        vertical-align: middle;
        border: 1px solid #e2e8f0;
    }

    .quotation-table td {
        padding: 6px;
        vertical-align: middle !important;
        border: 1px solid #e2e8f0;
    }

    .form-control-sm {
        height: 32px;
        font-size: 12.5px;
        padding: 4px 8px;
    }

    .form-select-sm {
        height: 32px;
        font-size: 12.5px;
        padding: 4px 28px 4px 8px !important;
        background-position: right 8px center !important;
        background-repeat: no-repeat !important;
        text-overflow: ellipsis;
    }

    .bg-light {
        background-color: #f8fafc !important;
    }

    .addRow,
    .removeRow {
        width: 30px;
        height: 30px;
        line-height: 1;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Quotation Table Column Width Protections */
    #poQuotationTable {
        table-layout: fixed !important;
        width: 100% !important;
        min-width: 1250px !important;
    }

    .quotation-table th.col-idx,
    .quotation-table td.col-idx {
        width: 35px !important;
        min-width: 35px !important;
        max-width: 40px !important;
        text-align: center;
        box-sizing: border-box !important;
    }

    .quotation-table th.col-segment,
    .quotation-table td.col-segment,
    .quotation-table td:nth-child(2),
    .quotation-table th:nth-child(2) {
        width: 145px !important;
        min-width: 135px !important;
        max-width: 160px !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    .quotation-table th.col-model,
    .quotation-table td.col-model,
    .quotation-table td:nth-child(3),
    .quotation-table th:nth-child(3) {
        width: 280px !important;
        min-width: 260px !important;
        max-width: 290px !important;
        box-sizing: border-box !important;
        overflow: visible !important;
    }

    .quotation-table td:nth-child(3) .select2-container,
    .quotation-table .col-model .select2-container,
    .quotation-table td.col-model .select2-container {
        width: 100% !important;
        max-width: 280px !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    .quotation-table td:nth-child(3) .select2-selection--single,
    .quotation-table .col-model .select2-selection--single {
        width: 100% !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-sizing: border-box !important;
    }

    .quotation-table td:nth-child(3) .select2-selection__rendered,
    .quotation-table .col-model .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        padding-right: 32px !important;
        width: 250px !important;
        max-width: 250px !important;
        display: block !important;
        font-size: 12px;
        box-sizing: border-box !important;
    }

    .quotation-table .col-model .select2-selection__arrow {
        height: 32px !important;
        right: 4px !important;
    }

    .quotation-table .col-model .select2-selection__clear {
        margin-right: 18px !important;
        font-size: 14px !important;
        line-height: 32px !important;
        color: #ef4444 !important;
        font-weight: bold !important;
    }

    .quotation-table .accessories-wrapper {
        width: 100% !important;
        max-width: 270px !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
        word-break: break-word !important;
    }

    .quotation-table .accessories-summary-badge {
        width: 100% !important;
        max-width: 270px !important;
        box-sizing: border-box !important;
    }

    .quotation-table .select2-dropdown,
    .select2-container--open .select2-dropdown {
        min-width: 320px !important;
        max-width: 550px !important;
        width: auto !important;
        font-size: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        border: 1px solid #cbd5e1;
        z-index: 1060 !important;
    }

    .quotation-table .select2-results__option,
    .select2-results__option {
        white-space: normal !important;
        word-break: break-word !important;
        line-height: 1.35 !important;
        font-size: 12px !important;
        padding: 6px 10px !important;
    }
</style>

<section class="bg-white" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px; border-radius:10px;">
    <div class="container-fluid bg-white pt-4 pb-5">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fs-4 me-2"></i>
                    <div>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Top Back Bar -->
        <div class="col-lg-12 mb-3" style="text-align: -webkit-right;">
            <a onclick="history.back()" style="width: 15%; cursor: pointer;" class="btn btn-primary d-block">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <!-- Top Quotation Metadata Bar -->
        <div class="row mb-3">
            <div class="col-lg-3 mt-2">
                <div class="form-group">
                    <label>Reference No (ORF / Quotation)</label>
                    <input type="text" class="form-control" readonly style="background-color: #dedede;" 
                        value="{{ !empty($isOrderPo) && !empty($orf) ? ($orf->orfref ?? ('ORF #'.$orf->id)) : 'NEW-DIRECT-PO' }}">
                </div>
            </div>
            <div class="col-lg-3 mt-2">
                <div class="form-group">
                    <label>Linked Customer / Project</label>
                    <input type="text" style="background-color: #dedede;" class="form-control" 
                        value="{{ !empty($isOrderPo) && !empty($orf) ? ($orf->lead?->LeadName ?? 'N/A') : 'Direct Procurement' }}" readonly>
                </div>
            </div>
            <div class="col-lg-3 mt-2">
                <div class="form-group">
                    <label>P.O. Creation Date</label>
                    <input type="text" style="background-color: #dedede;" class="form-control" value="{{ now()->format('d-m-Y') }}" readonly>
                </div>
            </div>
            <div class="col-lg-3 mt-2">
                <div class="form-group">
                    <label>Mercuriis Issuing Branch <span style="color:red">*</span></label>
                    @php
                        $branchConfigs = !empty($mercuriisBranches) ? $mercuriisBranches : config('mercuriis_branches.branches', []);
                    @endphp
                    <select name="mercuriis_branch" id="mercuriis_branch" form="poCreationForm" class="form-select fw-bold" style="border: 2px solid #2563eb;" required>
                        @foreach ($branchConfigs as $bCode => $bData)
                            <option value="{{ $bCode }}" data-address="{{ $bData['full_address'] }}" data-gst="{{ $bData['gstin'] }}" {{ $bCode === 'Chennai' ? 'selected' : '' }}>
                                {{ $bData['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- PO Creation Form with Exact Quotation Tab Navigation -->
        <form method="POST" action="/save-po" id="poCreationForm" enctype="multipart/form-data" novalidate autocomplete="off">
            @csrf
            <input type="hidden" name="mercuriis_branch" id="hidden_mercuriis_branch" value="{{ config('mercuriis_branches.default', 'Chennai') }}">

            @if (!empty($isOrderPo) && !empty($orf))
                <input type="hidden" name="orf_id" value="{{ $orf->id }}">
                <input type="hidden" name="quotation_id" value="{{ $quotation?->id ?? $orf->quoproid }}">
                @if (!empty($orfDrawingAttachment))
                    <input type="hidden" name="orf_sigdraw" value="{{ $orfDrawingAttachment }}">
                @endif
            @endif

            <!-- Tabs: Create Purchase Order vs Terms and Conditions -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                        type="button" role="tab" aria-controls="home" aria-selected="true">
                        <i class="fas fa-file-invoice me-1"></i> Create Purchase Order
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                        type="button" role="tab" aria-controls="profile" aria-selected="false">
                        <i class="fas fa-file-contract me-1"></i> Terms and Conditions
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                
                <!-- ========================================================================= -->
                <!-- TAB 1: CREATE PURCHASE ORDER (EXACT QUOTATION ADDRESSES & TABLE LAYOUT)   -->
                <!-- ========================================================================= -->
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    
                    <!-- Supplier Selection & Addresses Grid -->
                    <div class="row">

                        <!-- Supplier Selection (Top Left) -->
                        <div class="col-lg-3 mt-3">
                            <div class="form-group">
                                <label>Select Supplier (Vendor) <span style="color:red">*</span></label>
                                <select class="form-select form-select-sm" name="primary_vendor_id" id="primary_vendor_id" required>
                                    <option value="">-- Choose Supplier --</option>
                                    @if (!empty($allVendors))
                                        @foreach ($allVendors as $v)
                                            @php
                                                $vCats = [];
                                                if (!empty($v->product_cat_id)) {
                                                    if (is_array($v->product_cat_id)) {
                                                        $vCats = $v->product_cat_id;
                                                    } else {
                                                        $dec = json_decode($v->product_cat_id, true);
                                                        $vCats = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', (string)$v->product_cat_id)));
                                                    }
                                                }
                                            @endphp
                                            <option value="{{ $v->id }}" 
                                                data-gst="{{ $v->gst_number }}" 
                                                data-address="{{ $v->address }}"
                                                data-name="{{ $v->company_name }}"
                                                data-categories="{{ json_encode(array_values(array_map('strval', $vCats))) }}"
                                                {{ (string)old('primary_vendor_id') === (string)$v->id ? 'selected' : '' }}>
                                                {{ $v->company_name }} ({{ $v->vendor_id }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" name="supplier_address_id" id="supplier_address_id" value="">
                            </div>
                        </div>

                        <!-- Supplier Branch Selection by GST -->
                        <div class="col-lg-2 mt-3">
                            <div class="form-group">
                                <label>Supplier Branch (by GST)</label>
                                <select class="form-select form-select-sm" id="supplier_branch_dropdown">
                                    <option value="">-- Select Address --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="col-lg-2 mt-3"></div>

                        <!-- Shipping / Consignee Destination Scope (Right) -->
                        <div class="col-lg-3 mt-3">
                            <div class="form-group">
                                <label>Delivery Destination Scope <span style="color:red">*</span></label>
                                <div class="d-flex gap-3 align-items-center mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping_type" id="shipping_type_warehouse" value="warehouse" {{ empty($defaultCustomerAddress) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark" for="shipping_type_warehouse" style="cursor:pointer;">
                                            Warehouse
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping_type" id="shipping_type_customer" value="customer_site" {{ !empty($defaultCustomerAddress) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-success" for="shipping_type_customer" style="cursor:pointer;">
                                            Customer Site
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Lookup (when Customer Site is selected) -->
                        <div class="col-lg-2 mt-3" id="customer_site_box" style="{{ !empty($defaultCustomerAddress) ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label>Select Customer Site</label>
                                <select class="form-select form-select-sm" name="customer_id" id="customer_select_dropdown">
                                    <option value="">-- Choose Customer --</option>
                                    @if (!empty($allCustomers))
                                        @foreach ($allCustomers as $cust)
                                            <option value="{{ $cust->id }}" 
                                                data-gst="{{ $cust->gstno }}" 
                                                data-address="{{ $cust->address ?: $cust->communication_address }}" 
                                                data-name="{{ $cust->customername }}">
                                                {{ $cust->customername }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Billing Address vs Shipping Address -->
                        <div class="col-lg-3 mt-2">
                            <div class="form-group">
                                <label>Supplier Billing Address <span style="color:red">*</span></label>
                                <select class="form-select form-select-sm mb-1 text-primary fw-semibold" id="supplier_address_dropdown" style="display:none; font-size: 11px; background-color: #f0f7ff; border-color: #93c5fd;">
                                    <option value="">-- Choose Registered Address --</option>
                                </select>
                                <textarea class="form-control" name="supplier_billing_address" id="supplier_billing_address" required placeholder="Supplier billing / factory address">{{ old('supplier_billing_address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-2 mt-2">
                            <div class="form-group">
                                <label>Supplier GSTIN <span style="color:red">*</span></label>
                                <input type="text" name="supplier_gst" id="supplier_gst" class="form-control form-control-sm text-uppercase" placeholder="Supplier GSTIN" value="{{ old('supplier_gst') }}" required>
                                <small class="d-block mt-1" id="supplier_gst_status" style="font-size: 10px;"></small>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="col-lg-2 mt-2"></div>

                        <div class="col-lg-3 mt-2">
                            <div class="form-group">
                                <label>Delivery Full Address <span style="color:red">*</span></label>
                                <select class="form-select form-select-sm mb-1 text-success fw-semibold" id="destination_address_dropdown" style="display:none; font-size: 11px; background-color: #f0fdf4; border-color: #86efac;">
                                    <option value="">-- Choose Destination Address --</option>
                                </select>
                                <textarea class="form-control" name="shipping_address" id="shipping_address" required placeholder="Full delivery destination address">{{ old('shipping_address', $defaultCustomerAddress ?: config('mercuriis_branches.branches.' . config('mercuriis_branches.default', 'Chennai') . '.full_address')) }}</textarea>
                                <input type="hidden" name="customer_site_id" id="customer_site_id" value="{{ old('customer_site_id') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 mt-2">
                            <div class="form-group">
                                <label>Destination GSTIN <span style="color:red">*</span></label>
                                <input type="text" name="shipping_gst" id="shipping_gst" class="form-control form-control-sm text-uppercase" value="{{ old('shipping_gst', $defaultCustomerGst ?: config('mercuriis_branches.branches.' . config('mercuriis_branches.default', 'Chennai') . '.gstin')) }}" required>
                                <small class="d-block mt-1" id="destination_gst_status" style="font-size: 10px;"></small>
                            </div>
                        </div>

                    </div>

                    <!-- Scope of Supply Line Items Table (EXACT QUOTATION TABLE LAYOUT) -->
                    <div class="mt-4">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-boxes me-1"></i> Scope of Supply: Line Items (Quotation Format)
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered quotation-table align-middle" id="poQuotationTable">
                            <thead class="table-light">
                                <tr class="text-center align-middle">
                                    <th rowspan="2" class="col-idx" style="width: 35px; min-width: 35px;">#</th>
                                    <th rowspan="2" class="col-segment" style="width: 14%; min-width: 140px;">Segment</th>
                                    <th rowspan="2" class="col-model" style="width: 20%; min-width: 180px; max-width: 240px;">Model</th>
                                    <th rowspan="2" style="width: 10%;">Part No</th>
                                    <th rowspan="2" style="width: 9%;">HSN Code</th>
                                    <th rowspan="2" style="width: 6%;">Qty</th>
                                    <th rowspan="2" style="width: 10%;">Supplier Rate (₹)</th>
                                    <th colspan="2">Discount</th>
                                    <th rowspan="2" style="width: 9%;">Taxable Total</th>
                                    <th rowspan="2" style="width: 6%;">Tax %</th>
                                    <th rowspan="2" style="width: 8%;">Tax Amount</th>
                                    <th rowspan="2" style="width: 11%;">Total + Tax (₹)</th>
                                    <th rowspan="2" style="width: 45px;">Action</th>
                                </tr>
                                <tr class="text-center">
                                    <th style="width: 65px;">%</th>
                                    <th style="width: 85px;">Price (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="poProductRows">
                                @if (!empty($isOrderPo) && $orderItems->isNotEmpty())
                                    @foreach ($orderItems as $idx => $item)
                                        @php
                                            $catId = $item->catepro ?? $item->productDetails?->Productcategory;
                                            $category = App\Models\ProductCategory::find($catId);
                                            $prod = $item->productDetails;
                                            $suppRate = (float)($prod?->rate ?? $item->ratee ?? 0);
                                            $itemGst = (float)($item->tax ?? $prod?->gst ?? 18);
                                            $reqQty = (int)($item->quanty ?? 1);
                                            $partNo = $prod?->partno ?? $item->partno ?? '';
                                            $hsnCode = $prod?->hsn ?: ($prod?->hsncode ?? '8428');
                                            $autoDesc = trim($prod?->productdes ?: ($prod?->productname ?? ''));
                                            $catProducts = App\Models\Product::where('Productcategory', $catId)->orderBy('productmodel')->orderBy('productname')->get();
                                            $prodFullName = $prod?->productname ?? ('Product ' . $item->product);
                                            $prodShortModel = !empty($prod?->productmodel) ? trim($prod->productmodel) : trim($prodFullName);
                                            if (strlen($prodShortModel) > 28) {
                                                $prodShortModel = mb_substr($prodShortModel, 0, 25) . '...';
                                            }
                                            $savedAccList = [];
                                            if (!empty($item->accessories)) {
                                                foreach ($item->accessories as $qAcc) {
                                                    $savedAccList[] = [
                                                        'id' => $qAcc->accessory_id,
                                                        'name' => trim($qAcc->name ?? ''),
                                                        'quantity' => $qAcc->quantity ?? 1
                                                    ];
                                                }
                                            }
                                        @endphp
                                        <tr class="poRow">
                                            <td class="text-center fw-bold rowIdx col-idx">{{ $idx + 1 }}</td>
                                            <td class="col-segment">
                                                <select name="catId[]" class="form-select form-select-sm categorySelect" required>
                                                    <option value="{{ $catId }}">{{ $category?->category_name ?? 'Segment ' . $catId }}</option>
                                                    @if (!empty($categories))
                                                        @foreach ($categories->where('id', '!=', $catId) as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="col-model" style="width: 20%; max-width: 240px;">
                                                <select name="proId[]" class="form-select form-select-sm productSelect" data-selected="{{ $item->product }}" data-saved-accessories="{{ json_encode($savedAccList) }}" title="{{ $prodFullName }}" required>
                                                    @if ($catProducts->isNotEmpty())
                                                        @foreach ($catProducts as $cp)
                                                            @php
                                                                $cpRate = (float)($cp->rate ?? 0);
                                                                $cpGst = (float)($cp->gst ?? 18);
                                                                $cpPart = $cp->partno ?? '';
                                                                $cpHsn = $cp->hsn ?? '8428';
                                                                $cpDesc = trim($cp->productdes ?: ($cp->productname ?? ''));
                                                                $cpModel = !empty($cp->productmodel) ? trim($cp->productmodel) : trim($cp->productname);
                                                                if (strlen($cpModel) > 28) {
                                                                    $cpModel = mb_substr($cpModel, 0, 25) . '...';
                                                                }
                                                            @endphp
                                                            <option value="{{ $cp->id }}" 
                                                                data-model="{{ $cp->productmodel }}"
                                                                data-name="{{ $cp->productname }}"
                                                                data-rate="{{ $cp->id == $item->product ? $suppRate : $cpRate }}" 
                                                                data-gst="{{ $cp->id == $item->product ? $itemGst : $cpGst }}" 
                                                                data-partno="{{ $cp->id == $item->product ? $partNo : $cpPart }}" 
                                                                data-hsn="{{ $cp->id == $item->product ? $hsnCode : $cpHsn }}" 
                                                                data-desc="{{ $cpDesc }}" 
                                                                data-search="{{ $cp->productmodel }} {{ $cp->productname }} {{ $cp->partno }}"
                                                                title="{{ $cp->productname }}"
                                                                {{ $cp->id == $item->product ? 'selected' : '' }}>
                                                                {{ $cp->productname }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{ $item->product }}" 
                                                            data-model="{{ $prodShortModel }}"
                                                            data-name="{{ $prodFullName }}"
                                                            data-rate="{{ $suppRate }}" 
                                                            data-gst="{{ $itemGst }}" 
                                                            data-partno="{{ $partNo }}" 
                                                            data-hsn="{{ $hsnCode }}" 
                                                            data-desc="{{ $autoDesc }}" 
                                                            data-search="{{ $prodShortModel }} {{ $prodFullName }} {{ $partNo }}"
                                                            title="{{ $prodFullName }}" selected>
                                                            {{ $prodFullName }}
                                                        </option>
                                                    @endif
                                                </select>
                                                <input type="hidden" name="pro_des[]" class="pro_des" value="{{ $autoDesc }}">
                                                <input type="hidden" name="vendorId[]" class="line_vendor_id" value="">
                                                <input type="hidden" class="base-rate" value="{{ $suppRate }}">

                                                {{-- Mapped accessories for the selected product show here as checkboxes --}}
                                                <div class="accessories-wrapper mt-1 p-2 border rounded bg-white shadow-sm" style="display:none; font-size:11px; max-height: 280px; overflow-y: auto;">
                                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-2">
                                                        <strong class="text-secondary" style="font-size: 11px;"><i class="fas fa-cog me-1"></i>Select Accessories:</strong>
                                                        <button type="button" class="btn-close btn-close-accessories" style="font-size: 8px;" title="Close"></button>
                                                    </div>
                                                    <div class="accessories-list d-flex flex-column gap-1"></div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top bg-light p-1 rounded">
                                                        <span class="badge bg-secondary selected-acc-count" style="font-size: 10px;">0 Selected</span>
                                                        <button type="button" class="btn btn-sm btn-success btn-apply-accessories py-0 px-3 fw-bold" style="font-size: 11px; height: 26px;">
                                                            <i class="fas fa-check me-1"></i> OK
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Summary badge when accessories box is closed --}}
                                                <div class="accessories-summary-badge mt-1 d-flex align-items-center justify-content-between p-1 px-2 border rounded bg-light" style="display:none; font-size:11px; cursor: pointer;">
                                                    <span class="summary-text text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>0 Accessories</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-semibold btn-reopen-accessories" style="font-size: 10px; text-decoration: underline;">Change</button>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="part_no[]" class="form-control form-control-sm part_no" value="{{ $partNo }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="hsn[]" class="form-control form-control-sm hsn" value="{{ $hsnCode }}" placeholder="HSN">
                                            </td>
                                            <td>
                                                <input type="number" name="qty[]" class="form-control form-control-sm qty text-center fw-bold" value="{{ $reqQty }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="unit_price[]" class="form-control form-control-sm rate text-end fw-bold" value="{{ $suppRate }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="99" name="discount[]" class="form-control form-control-sm requestdis-percent text-center" placeholder="%" value="0.00">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm requestdis-amount text-end" placeholder="Amt" value="0.00">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm lbt text-end bg-light fw-bold" readonly value="0.00">
                                                <input type="hidden" name="sub_total[]" class="product_subtotal">
                                            </td>
                                            <td>
                                                <input type="number" name="gst[]" class="form-control form-control-sm tax text-center" value="{{ $itemGst }}" readonly required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm taxamt text-end bg-light" readonly value="0.00">
                                            </td>
                                            <td>
                                                <input type="text" name="product_total[]" class="form-control form-control-sm total text-end bg-light fw-bold text-dark" readonly value="0.00">
                                                <input type="hidden" name="roundoff[]" class="line_roundoff" value="0">
                                            </td>
                                            <td class="text-center">
                                                @if ($loop->first)
                                                    <button type="button" class="btn btn-primary btn-sm addRow rounded-circle" title="Add Row">
                                                        +
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm removeRow rounded-circle" title="Remove Row">
                                                        ✕
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- STANDALONE ROWS (PRESERVES MULTIPLE ROWS ON VALIDATION REDIRECT) --}}
                                    @php
                                        $oldCatIds = old('catId');
                                        $rowIndices = (is_array($oldCatIds) && count($oldCatIds) > 0) ? array_keys($oldCatIds) : [0];
                                        $totalStandaloneRows = count($rowIndices);
                                    @endphp
                                    @foreach ($rowIndices as $idxPosition => $rIdx)
                                        @php
                                            $oldCatId = old("catId.$rIdx");
                                            $oldProId = old("proId.$rIdx");
                                            $oldProducts = $oldCatId ? App\Models\Product::where('Productcategory', $oldCatId)->orderBy('productmodel')->orderBy('productname')->get() : collect();
                                        @endphp
                                        <tr class="poRow">
                                            <td class="text-center fw-bold rowIdx col-idx">{{ $idxPosition + 1 }}</td>
                                            <td class="col-segment">
                                                <select name="catId[]" class="form-select form-select-sm categorySelect" required>
                                                    <option value="">-- Choose Segment --</option>
                                                    @if (!empty($categories))
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ (string)$oldCatId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="col-model" style="width: 20%; max-width: 240px;">
                                                <select name="proId[]" class="form-select form-select-sm productSelect" required>
                                                    @if ($oldProducts->isNotEmpty())
                                                        <option value="">-- Choose Model --</option>
                                                        @foreach ($oldProducts as $op)
                                                            @php
                                                                $opRate = (float)($op->rate ?? 0);
                                                                $opGst = (float)($op->gst ?? 18);
                                                                $opPart = $op->partno ?? '';
                                                                $opHsn = $op->hsn ?? '8428';
                                                                $opDesc = trim($op->productdes ?: ($op->productname ?? ''));
                                                                $opModel = !empty($op->productmodel) ? trim($op->productmodel) : trim($op->productname);
                                                            @endphp
                                                            <option value="{{ $op->id }}"
                                                                data-model="{{ $opModel }}"
                                                                data-name="{{ $op->productname }}"
                                                                data-rate="{{ $opRate }}"
                                                                data-gst="{{ $opGst }}"
                                                                data-partno="{{ $opPart }}"
                                                                data-hsn="{{ $opHsn }}"
                                                                data-desc="{{ $opDesc }}"
                                                                data-search="{{ $opModel }} {{ $op->productname }} {{ $opPart }}"
                                                                title="{{ $op->productname }}"
                                                                {{ (string)$oldProId === (string)$op->id ? 'selected' : '' }}>
                                                                {{ $op->productname }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="">-- Select Segment First --</option>
                                                    @endif
                                                </select>
                                                <input type="hidden" name="pro_des[]" class="pro_des" value="{{ old("pro_des.$rIdx", '') }}">
                                                <input type="hidden" name="vendorId[]" class="line_vendor_id" value="{{ old("vendorId.$rIdx", '') }}">
                                                <input type="hidden" class="base-rate" value="{{ old("unit_price.$rIdx", '0.00') }}">

                                                {{-- Mapped accessories for the selected product show here as checkboxes --}}
                                                <div class="accessories-wrapper mt-1 p-2 border rounded bg-white shadow-sm" style="display:none; font-size:11px; max-height: 280px; overflow-y: auto;">
                                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-2">
                                                        <strong class="text-secondary" style="font-size: 11px;"><i class="fas fa-cog me-1"></i>Select Accessories:</strong>
                                                        <button type="button" class="btn-close btn-close-accessories" style="font-size: 8px;" title="Close"></button>
                                                    </div>
                                                    <div class="accessories-list d-flex flex-column gap-1"></div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top bg-light p-1 rounded">
                                                        <span class="badge bg-secondary selected-acc-count" style="font-size: 10px;">0 Selected</span>
                                                        <button type="button" class="btn btn-sm btn-success btn-apply-accessories py-0 px-3 fw-bold" style="font-size: 11px; height: 26px;">
                                                            <i class="fas fa-check me-1"></i> OK
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Summary badge when accessories box is closed --}}
                                                <div class="accessories-summary-badge mt-1 d-flex align-items-center justify-content-between p-1 px-2 border rounded bg-light" style="display:none; font-size:11px; cursor: pointer;">
                                                    <span class="summary-text text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>0 Accessories</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-semibold btn-reopen-accessories" style="font-size: 10px; text-decoration: underline;">Change</button>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="part_no[]" class="form-control form-control-sm part_no" value="{{ old("part_no.$rIdx", '') }}" placeholder="Part No" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="hsn[]" class="form-control form-control-sm hsn" value="{{ old("hsn.$rIdx", '8428') }}" placeholder="HSN">
                                            </td>
                                            <td>
                                                <input type="number" name="qty[]" class="form-control form-control-sm qty text-center fw-bold" value="{{ old("qty.$rIdx", '1') }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="unit_price[]" class="form-control form-control-sm rate text-end fw-bold" value="{{ old("unit_price.$rIdx", '0.00') }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="99" name="discount[]" class="form-control form-control-sm requestdis-percent text-center" placeholder="%" value="{{ old("discount.$rIdx", '0.00') }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm requestdis-amount text-end" placeholder="Amt" value="0.00">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm lbt text-end bg-light fw-bold" readonly value="0.00">
                                                <input type="hidden" name="sub_total[]" class="product_subtotal">
                                            </td>
                                            <td>
                                                <input type="number" name="gst[]" class="form-control form-control-sm tax text-center" value="{{ old("gst.$rIdx", '18') }}" readonly required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm taxamt text-end bg-light" readonly value="0.00">
                                            </td>
                                            <td>
                                                <input type="text" name="product_total[]" class="form-control form-control-sm total text-end bg-light fw-bold text-dark" readonly value="0.00">
                                                <input type="hidden" name="roundoff[]" class="line_roundoff" value="0">
                                            </td>
                                            <td class="text-center">
                                                @if ($totalStandaloneRows <= 1)
                                                    <button type="button" class="btn btn-primary btn-sm addRow rounded-circle" title="Add Row">
                                                        +
                                                    </button>
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <button type="button" class="btn btn-danger btn-sm removeRow rounded-circle" title="Remove Row">✕</button>
                                                        @if ($loop->last)
                                                            <button type="button" class="btn btn-primary btn-sm addRow rounded-circle" title="Add Row">+</button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Special Instructions Under Product Table -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="fw-bold text-dark"><i class="fas fa-comment-alt text-warning me-1"></i> Special Instructions (Printed in highlighted callout box on PO Document)</label>
                                <textarea name="special_instructions" class="form-control" rows="2" placeholder="Enter special dispatch, packaging, marking, or delivery instructions...">{{ $orfSpecialInstructions ?? '' }}</textarea>
                                <small class="text-muted">Will be highlighted on the official PO PDF in an amber callout box</small>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial Summary (EXACT QUOTATION SUMMARY STYLE) -->
                    <div class="row mt-4">
                        <!-- Left: Advance Amount Highlight Box -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <label class="fw-bold text-primary mb-1">
                                    <i class="fas fa-coins me-1"></i> Supplier Advance Payment Tracking
                                </label>
                                <p class="small text-muted mb-2">Advance percentage is auto-extracted from Payment Terms or customized in the Terms tab.</p>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border">
                                    <div>
                                        <span class="fw-bold text-dark fs-6 d-block" id="advance_ratio_text">(20% Advance)</span>
                                        <small class="text-muted">Calculated on Net PO Value</small>
                                    </div>
                                    <div class="text-end">
                                        <input type="text" id="advance_amount_display" class="form-control form-control-sm text-end fw-bold text-success fs-6" style="width: 170px;" readonly value="₹0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Standard Quotation Summary Stack -->
                        <div class="col-md-6">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Subtotal (Basic Total)</label>
                                <input type="text" id="subtotal" name="subtotal" class="form-control form-control-sm w-50 text-end fw-bold" readonly value="0.00">
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Overall Discount</label>
                                <div class="d-flex w-50 gap-2">
                                    <input type="number" step="0.01" min="0" max="100" id="discount_percentage" name="discount_percentage" class="form-control form-control-sm text-center" placeholder="%" value="0.00">
                                    <input type="text" id="discount_amount_display" class="form-control form-control-sm text-end" readonly value="0.00">
                                </div>
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Freight / Transportation (₹)</label>
                                <input type="number" step="0.01" min="0" id="freight_charges" name="freight_charges" class="form-control form-control-sm w-50 text-end fw-bold" value="0.00">
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Unloading Charges (₹)</label>
                                <input type="number" step="0.01" min="0" id="unloading_charges" name="unloading_charges" class="form-control form-control-sm w-50 text-end fw-bold" value="0.00">
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Tax (GST)</label>
                                <input type="text" id="taxtotal" name="taxtotal" class="form-control form-control-sm w-50 text-end fw-bold" readonly value="0.00">
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="fw-bold">Round Off (₹)</label>
                                <input type="text" id="round_off" name="round_off" class="form-control form-control-sm w-50 text-end text-muted fw-semibold" readonly value="0.00">
                            </div>

                            <div class="mb-2 d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                                <label class="fw-bold fs-6 text-dark mb-0">Net PO Value (Grand Total)</label>
                                <input type="text" id="grandtotal" name="grandtotal" class="form-control form-control-sm w-50 text-end fw-bold fs-6 text-success" readonly value="0.00">
                            </div>

                            <div class="mt-2 text-end">
                                <small class="text-muted fw-bold d-block" style="font-size: 11px;">Amount in Words:</small>
                                <span id="amount_in_words_display" class="fst-italic text-secondary fw-semibold" style="font-size: 11.5px;">Zero Rupees Only</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 1 Navigation Bar (Save button is only on Terms & Conditions tab) -->
                    <div class="col-12 d-flex justify-content-end align-items-center mt-4 pt-2 border-top">
                        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="goToTerms()">
                            Next: Terms &amp; Conditions <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- TAB 2: TERMS AND CONDITIONS (EXACT QUOTATION TERMS LAYOUT)                -->
                <!-- ========================================================================= -->
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    
                    <div class="row mt-3">
                        
                        <!-- Column 1: Commercials & Advance -->
                        <div class="col-md-4">
                            <div class="form-group mt-2">
                                <label>Payment Terms <span style="color:red">*</span></label>
                                <select id="payment_terms" name="payment_terms" class="form-select" required>
                                    <option value="">-- Choose Payment Terms --</option>
                                    <option value="custom" class="fw-bold text-primary">+ Add Custom Payment Term</option>
                                    @if(isset($paymentTermsMaster) && $paymentTermsMaster->isNotEmpty())
                                        <optgroup label="Segment / Master Payment Terms">
                                            @foreach($paymentTermsMaster as $pt)
                                                <option value="{{ $pt->content }}" data-category="{{ $pt->product_category_id }}">{{ $pt->content }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    <optgroup label="Standard Payment Terms">
                                        <option value="20% Advance & 80% Against Delivery" selected>20% Advance &amp; 80% Against Delivery</option>
                                        <option value="30% Advance & 70% Before Dispatch">30% Advance &amp; 70% Before Dispatch</option>
                                        <option value="50% Advance & 50% Against Proforma">50% Advance &amp; 50% Against Proforma</option>
                                        <option value="100% Against Proforma Invoice (PI)">100% Against Proforma Invoice (PI)</option>
                                        <option value="Credit 30 Days">Credit 30 Days</option>
                                    </optgroup>
                                </select>
                                <div id="custom_payment_terms_wrapper" class="mt-2" style="display:none;">
                                    <textarea name="custom_payment_terms" id="custom_payment_terms" class="form-control" rows="2" placeholder="Enter custom payment terms (e.g. 40% Advance, 60% Against Delivery)..."></textarea>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Committed Lead Time <span style="color:red">*</span></label>
                                <select name="lead_time" id="lead_time" class="form-select" required>
                                    <option value="">-- Choose Lead Time --</option>
                                    <option value="custom" class="fw-bold text-primary">+ Add Custom Lead Time</option>
                                    @if(isset($categories) && $categories->isNotEmpty())
                                        <optgroup label="Segment Master Lead Times">
                                            @foreach($categories->filter(fn($c) => !empty($c->dispatch_time)) as $cat)
                                                <option value="{{ $cat->dispatch_time }}" data-category="{{ $cat->id }}">{{ $cat->category_name }}: {{ Str::limit($cat->dispatch_time, 40) }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    <optgroup label="Standard Lead Times">
                                        <option value="Ex-Stock">Ex-Stock</option>
                                        <option value="10-15 Days">10-15 Days</option>
                                        <option value="2 Weeks">2 Weeks</option>
                                        <option value="4 Weeks" selected>4 Weeks</option>
                                        <option value="6 Weeks">6 Weeks</option>
                                        <option value="6-8 Weeks">6-8 Weeks</option>
                                        <option value="8 Weeks">8 Weeks</option>
                                        <option value="8-10 Weeks">8-10 Weeks</option>
                                        <option value="10-12 Weeks">10-12 Weeks</option>
                                        <option value="14 to 16 Weeks">14 to 16 Weeks</option>
                                    </optgroup>
                                </select>
                                <div id="custom_lead_time_wrapper" class="mt-2" style="display:none;">
                                    <input type="text" name="custom_lead_time" id="custom_lead_time" class="form-control" placeholder="Enter custom lead time (e.g. 3-4 Weeks from advance)...">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Advance Payment % (Auto-calculated)</label>
                                <div class="input-group">
                                    <input type="number" step="0.5" min="0" max="100" name="advance_percentage" id="advance_percentage" class="form-control fw-bold" value="20.00">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Extracted from Payment Terms or custom override</small>
                            </div>
                        </div>

                        <!-- Column 2: Warranty, Scope & Standard Terms -->
                        <div class="col-md-4">
                            <div class="form-group mt-2">
                                <label>Warranty Period</label>
                                <select name="warranty_terms" id="warranty_terms" class="form-select">
                                    <option value="">-- Choose Warranty Term --</option>
                                    <option value="custom" class="fw-bold text-primary">+ Add Custom Warranty Term</option>
                                    @if(isset($warrantyTermsMaster) && $warrantyTermsMaster->isNotEmpty())
                                        <optgroup label="Segment / Master Warranty Terms">
                                            @foreach($warrantyTermsMaster as $wt)
                                                <option value="{{ $wt->content }}" data-category="{{ $wt->product_category_id }}" data-product="{{ $wt->product_id }}">{{ $wt->content }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    <optgroup label="Standard Warranty Terms">
                                        <option value="12 Months Standard Manufacturer Warranty" selected>12 Months Standard Manufacturer Warranty</option>
                                        <option value="24 Months Standard Manufacturer Warranty">24 Months Standard Manufacturer Warranty</option>
                                        <option value="6 Months Standard Manufacturer Warranty">6 Months Standard Manufacturer Warranty</option>
                                    </optgroup>
                                </select>
                                <div id="custom_warranty_terms_wrapper" class="mt-2" style="display:none;">
                                    <input type="text" name="custom_warranty_terms" id="custom_warranty_terms" class="form-control" placeholder="Enter custom warranty term (e.g. 18 Months comprehensive warranty)...">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Installation Scope <span style="color:red">*</span></label>
                                <select name="installation_scope" id="installation_scope" class="form-select">
                                    <option value="">-- Choose Installation Scope --</option>
                                    <option value="custom" class="fw-bold text-primary">+ Add Custom Installation Scope</option>
                                    @if(isset($installationScopesMaster) && $installationScopesMaster->isNotEmpty())
                                        <optgroup label="Master Installation Scopes">
                                            @foreach($installationScopesMaster as $ism)
                                                <option value="{{ $ism->name }}">{{ $ism->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    <optgroup label="Standard Installation Scopes">
                                        <option value="In Supplier Scope (Included in Unit Rate)" selected>In Supplier Scope (Included in Unit Rate)</option>
                                        <option value="Not in Supplier Scope (Supply Only)">Not in Supplier Scope (Supply Only)</option>
                                        <option value="Customer Scope at Site">Customer Scope at Site</option>
                                        <option value="Mercuriis Scope (To be handled by Mercuriis team)">Mercuriis Scope (Handled by Mercuriis)</option>
                                    </optgroup>
                                </select>
                                <div id="custom_installation_scope_wrapper" class="mt-2" style="display:none;">
                                    <input type="text" name="custom_installation_scope" id="custom_installation_scope" class="form-control" placeholder="Enter custom installation scope...">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="fw-semibold text-secondary"><i class="fas fa-file-contract text-primary me-1"></i> Standard Terms &amp; Conditions</label>
                                <div class="p-2 border rounded bg-light small text-secondary">
                                    <i class="fas fa-check-circle text-success me-1"></i> Mercuriis Standard Supplier Terms &amp; Conditions are automatically attached to this Purchase Order.
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: Logistics & Brand Visibility -->
                        <div class="col-md-4">
                            <div class="form-group mt-2">
                                <label>Supplier Committed Dispatch / Delivery Date</label>
                                <input type="date" name="committed_dispatch_date" id="supplier_dispatch_date" class="form-control">
                                <small class="text-muted d-block mt-1"><i class="fas fa-bell text-warning me-1"></i>Calculated due date remains based on PO date and lead time.</small>
                            </div>

                            <div class="form-group mt-3">
                                <div class="form-check p-3 border rounded bg-light d-flex align-items-center gap-2">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="hide_brand_name" value="1" id="hide_brand_name">
                                    <label class="form-check-label fw-bold text-dark mb-0 small" for="hide_brand_name" style="cursor:pointer;">
                                        <i class="fas fa-eye-slash text-muted me-1"></i> Hide Make/Brand on Supplier PO Document
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Section 2: Product Technical Specifications & Parameters -->
                    <div class="card mt-4 border shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-microchip me-1 text-primary"></i> 2. Product Technical Specifications &amp; Fabrication Parameters
                            </h6>
                            <span class="badge bg-primary text-white">Included in PO PDF</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Technical Specifications / Scope of Supply Parameters</label>
                                    <textarea name="technical_specification" id="technical_specification" class="form-control" rows="4" placeholder="Enter product technical parameters (e.g., Capacity, Lift Height, Fork Dimensions, Wheel Material, Battery/Motor Specifications, Paint/Finish, etc.)..."></textarea>
                                    <small class="text-muted">Custom engineering parameters printed directly on the Supplier Purchase Order document.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Section (Section 3: Supplier Quotes, Section 4: Technical Drawings & Photos) -->
                    <div class="card mt-4 border shadow-sm">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-paperclip me-1 text-primary"></i> 3 &amp; 4. Procurement Attachments &amp; Technical Drawings</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Supplier Quotations (PDF / Excel / Images)</label>
                                    <input type="file" name="quote_attachments[]" multiple class="form-control" accept=".pdf,.xls,.xlsx,.csv,.png,.jpg,.jpeg">
                                    <small class="text-muted d-block mt-1">Upload supplier proforma or quote files</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Approved Technical Drawing &amp; Photos</label>
                                    @if (!empty($orfDrawingAttachment))
                                        <div class="d-flex align-items-center mb-1 p-1 border rounded bg-light">
                                            <span class="text-truncate me-2 small flex-grow-1">📄 {{ $orfDrawingAttachment }}</span>
                                            <a href="/uploads/po_files/{{ $orfDrawingAttachment }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2">View</a>
                                        </div>
                                        <input type="hidden" name="existing_drawing_attachment" value="{{ $orfDrawingAttachment }}">
                                    @endif
                                    <input type="file" name="drawing_attachment" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.dwg">
                                    <small class="text-muted d-block mt-1">Fabrication drawing, GA, or product photo</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Technical Specs Sheet / Other Files (Excel / PDF)</label>
                                    <input type="file" name="po_attachment" class="form-control" accept=".pdf,.xls,.xlsx,.csv,.png,.jpg,.jpeg,.doc,.docx">
                                    <small class="text-muted d-block mt-1">Excel specification sheet or supplementary files</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Submit Bar -->
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3 pt-2 border-top">
                        <button type="button" class="btn btn-secondary" onclick="goToHome()">
                            &larr; Back to Line Items
                        </button>
                        <button type="submit" class="btn btn-warning px-5 fw-bold btn-save-po" id="btnSubmitPo">
                            <i class="fas fa-check-circle me-1"></i> Save Purchase Order
                        </button>
                    </div>

                </div>

            </div>

        </form>

    </div>
</section>

@endcan

@endsection

@section('scripts')
<script>
    // Master Product Categories for Segment Filtering
    window.allProductCategories = [
        @if (!empty($categories))
            @foreach ($categories as $cat)
                { id: "{{ $cat->id }}", name: "{{ addslashes($cat->category_name) }}" },
            @endforeach
        @endif
    ];

    function filterCategoryDropdowns(targetSelect, preserveCurrent) {
        let $vendorOpt = $('#primary_vendor_id').find(':selected');
        let rawCats = $vendorOpt.data('categories');
        let allowed = [];
        if (rawCats) {
            if (typeof rawCats === 'string') {
                try { allowed = JSON.parse(rawCats); } catch(e) { allowed = rawCats.split(',').map(s => s.trim()); }
            } else if (Array.isArray(rawCats)) {
                allowed = rawCats;
            }
        }
        allowed = allowed.map(String).filter(Boolean);

        let $selects = targetSelect ? $(targetSelect) : $('.categorySelect');

        $selects.each(function() {
            let $sel = $(this);
            let currentVal = String($sel.val() || '');
            let allCats = window.allProductCategories || [];
            let validCats = allCats;

            if (allowed.length > 0) {
                validCats = allCats.filter(c => allowed.includes(String(c.id)));
            }

            // If the current category is already selected and still valid in the vendor's list, keep it completely intact!
            let isCurrentValValid = currentVal && validCats.some(c => String(c.id) === currentVal);
            if (isCurrentValValid) {
                // Keep current selection intact without rebuilding options or firing change event
                return;
            }

            let html = '<option value="">-- Choose Segment --</option>';
            validCats.forEach(function(c) {
                html += `<option value="${c.id}">${c.name}</option>`;
            });

            if (preserveCurrent && currentVal && !validCats.some(c => String(c.id) === currentVal)) {
                let existingName = $sel.find(`option[value="${currentVal}"]`).text() || ('Segment ' + currentVal);
                html += `<option value="${currentVal}">${existingName}</option>`;
            }

            $sel.html(html);

            if (currentVal && (validCats.some(c => String(c.id) === currentVal) || preserveCurrent)) {
                $sel.val(currentVal);
            } else if (allowed.length === 1 && !currentVal) {
                $sel.val(allowed[0]).trigger('change');
            } else {
                $sel.val('');
                if (currentVal) {
                    $sel.trigger('change');
                }
            }
        });
    }

    // Tab Navigation
    function goToTerms() {
        var tab = new bootstrap.Tab(document.querySelector('#profile-tab'));
        tab.show();
    }

    function goToHome() {
        var tab = new bootstrap.Tab(document.querySelector('#home-tab'));
        tab.show();
    }

    // Warehouse branch address definitions (Mercuriis Dual Branches)
    function updateWarehouseAddress() {
        let $branch = $('#mercuriis_branch').find(':selected');
        let addr = $branch.data('address') || '';
        if ($('input[name="shipping_type"]:checked').val() === 'warehouse') {
            $('#shipping_address').val(addr);
            $('#shipping_address').data('address-locked', true);
            $('#shipping_gst').val($branch.data('gst') || '');
            $('#customer_site_id').val('');
            $('#customer_select_dropdown').val('');
        }
    }

    $('#mercuriis_branch').on('change', function() {
        updateWarehouseAddress();
    });

    $('#primary_vendor_id').on('change', function() {
        let vendorId = $(this).val();
        let $opt = $(this).find(':selected');
        let initialGst = $opt.data('gst') || '';
        let initialAddr = $opt.data('address') || '';

        $('#supplier_gst').val(initialGst);
        $('#supplier_billing_address').val(initialAddr);
        $('#supplier_billing_address').data('address-locked', false);
        $('#supplier_address_id').val('');
        $('.line_vendor_id').val(vendorId || '');

        filterCategoryDropdowns(null, true);

        if (!vendorId) {
            $('#supplier_branch_dropdown').empty().append('<option value="">-- Select Address --</option>');
            $('#supplier_address_dropdown').hide().empty();
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
                        let gst = b.gst || b.gst_number || '';
                        let addr = b.address || b.label || '';
                        let opt = $('<option>').val(b.id).text(addr)
                            .data('gst', gst)
                            .data('address', addr)
                            .data('address-id', b.address_id || '');
                        $('#supplier_branch_dropdown').append(opt);
                    });
                    // Select primary branch by default and populate address
                    let firstB = res.branches[0];
                    $('#supplier_branch_dropdown').val(firstB.id);
                    $('#supplier_address_id').val(firstB.address_id || '');
                    let firstGst = firstB.gst || firstB.gst_number || initialGst;
                    if (firstGst) $('#supplier_gst').val(firstGst);
                    if (firstB.address) $('#supplier_billing_address').val(firstB.address).data('address-locked', false);
                } else {
                    $('#supplier_branch_dropdown').append('<option value="">-- Select Address --</option>');
                }
            },
            error: function() {
                $('#supplier_branch_dropdown').empty().append('<option value="">-- Select Address --</option>');
            }
        });
    });

    $('#supplier_branch_dropdown').on('change', function() {
        let $opt = $(this).find(':selected');
        let branchGst = $opt.data('gst') || '';
        let branchAddr = $opt.data('address') || '';
        let branchAddrId = $opt.data('address-id') || '';

        // Prevent debounced lookupAddressesByGst from triggering and overwriting Branch 2 address
        clearTimeout($('#supplier_gst').data('gstTimer'));

        $('#supplier_gst').val(branchGst);
        $('#supplier_address_id').val(branchAddrId);
        if (branchAddr) {
            $('#supplier_billing_address').val(branchAddr).data('address-locked', true);
        }
        $('#supplier_address_dropdown').hide();
        $('#supplier_gst_status').html('<span class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>Branch address applied</span>');
    });

    // 1.1 Helper: Dynamic Address Fetcher by GSTIN
    function lookupAddressesByGst(gstInput, $dropdown, $textarea, $statusEl) {
        let gstNo = (gstInput || '').trim().replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        let lookupSequence = ($dropdown.data('lookup-sequence') || 0) + 1;
        $dropdown.data('lookup-sequence', lookupSequence);
        $dropdown.data('lookup-gst', gstNo);
        if (gstNo.length < 8) {
            $dropdown.hide().empty();
            if ($statusEl) $statusEl.html('');
            return;
        }

        if ($statusEl) {
            $statusEl.html('<span class="text-primary"><i class="fas fa-spinner fa-spin me-1"></i>Fetching addresses for GST...</span>');
        }

        $.ajax({
            url: '/api/get-addresses-by-gst/' + encodeURIComponent(gstNo),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                let gstField = $dropdown.attr('id') === 'destination_address_dropdown' ? $('#shipping_gst') : $('#supplier_gst');
                if ($dropdown.data('lookup-sequence') !== lookupSequence ||
                    gstNo !== (gstField.val() || '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase()) {
                    return;
                }
                $dropdown.empty();
                if (res && res.found && res.addresses && res.addresses.length > 0) {
                    let optionsHtml = `<option value="">-- Select from ${res.count} Address${res.count > 1 ? 'es' : ''} --</option>`;
                    res.addresses.forEach(function(item, idx) {
                        let cleanLbl = (item.label || item.address || '')
                            .replace(/⭐/g, '')
                            .replace(/\[Primary\]/gi, '')
                            .replace(/\(Primary\)/gi, '')
                            .replace(/^Primary\s*[-:]?\s*/gi, '')
                            .trim();
                        optionsHtml += `<option value="${idx}">${cleanLbl}</option>`;
                    });
                    $dropdown.html(optionsHtml).data('address-items', res.addresses).slideDown(150);

                    if (!$textarea.data('address-locked')) {
                        $dropdown.val('0');
                        let primaryItem = res.addresses[0];
                        let addrText = primaryItem.address;
                        if (primaryItem.company_name && !addrText.startsWith(primaryItem.company_name)) {
                            addrText = primaryItem.company_name + '\n' + addrText;
                        }
                        if (gstNo && !addrText.includes(gstNo)) {
                            addrText += '\nGSTIN: ' + gstNo;
                        }
                        $textarea.val(addrText);
                        if ($textarea.is('#shipping_address')) {
                            $('#customer_site_id').val(primaryItem.address_id || '');
                        }
                    }

                    if (res.addresses.length > 1) {
                        if ($statusEl) {
                            $statusEl.html(`<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Address loaded (${res.count} available in dropdown)</span>`);
                        }

                        $('#supplier_billing_address, #shipping_address').on('input', function() {
                            $(this).data('address-locked', true);
                            if ($(this).is('#shipping_address')) $('#customer_site_id').val('');
                        });
                    } else {
                        if ($statusEl) {
                            $statusEl.html('<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Address applied</span>');
                        }
                    }
                } else {
                    $dropdown.hide();
                    if ($statusEl) {
                        $statusEl.html('<span class="text-muted"><i class="fas fa-info-circle me-1"></i>No saved addresses found for this GST</span>');
                    }
                }
            },
            error: function() {
                if ($statusEl) $statusEl.html('');
            }
        });
    }

    // Bind Destination GST lookup
    $('#shipping_gst').on('input change paste', function() {
        let val = $(this).val();
        clearTimeout($(this).data('gstTimer'));
        let timer = setTimeout(function() {
            lookupAddressesByGst(val, $('#destination_address_dropdown'), $('#shipping_address'), $('#destination_gst_status'));
        }, 350);
        $(this).data('gstTimer', timer);
    });

    $(document).on('change', '#destination_address_dropdown', function() {
        let idx = $(this).val();
        let items = $(this).data('address-items') || [];
        let item = items[idx];
        if (item) {
            let addrText = item.address;
            if (item.company_name && !addrText.startsWith(item.company_name)) {
                addrText = item.company_name + '\n' + addrText;
            }
            let gstNo = $('#shipping_gst').val().trim().toUpperCase();
            if (gstNo && !addrText.includes(gstNo)) {
                addrText += '\nGSTIN: ' + gstNo;
            }
            $('#shipping_address').val(addrText);
            $('#shipping_address').data('address-locked', true);
            $('#customer_site_id').val(item.customer_id ? item.address_id : '');
        }
    });

    // Bind Supplier GST lookup
    $('#supplier_gst').on('input change paste', function() {
        let val = $(this).val();
        clearTimeout($(this).data('gstTimer'));
        let timer = setTimeout(function() {
            lookupAddressesByGst(val, $('#supplier_address_dropdown'), $('#supplier_billing_address'), $('#supplier_gst_status'));
        }, 350);
        $(this).data('gstTimer', timer);
    });

    $(document).on('change', '#supplier_address_dropdown', function() {
        let idx = $(this).val();
        let items = $(this).data('address-items') || [];
        let item = items[idx];
        if (item) {
            let addrText = item.address;
            let gstNo = $('#supplier_gst').val().trim().toUpperCase();
            if (item.company_name && !addrText.startsWith(item.company_name)) {
                addrText = item.company_name + '\n' + addrText;
            }
            if (gstNo && !addrText.includes(gstNo)) {
                addrText += '\nGSTIN: ' + gstNo;
            }
            $('#supplier_billing_address').val(addrText);
            $('#supplier_billing_address').data('address-locked', true);
            $('#supplier_address_id').val('');
        }
    });

    // 2. Shipping Destination Toggle
    $('input[name="shipping_type"]').on('change', function() {
        let type = $(this).val();
        if (type === 'warehouse') {
            $('#customer_site_box').slideUp(200);
            updateWarehouseAddress();
        } else {
            $('#customer_site_box').slideDown(200);
        }
    });

    $('#customer_select_dropdown').on('change', function() {
        let $opt = $(this).find(':selected');
        let cGst = $opt.data('gst') || '';
        let cAddr = $opt.data('address') || '';
        let cName = $opt.data('name') || '';
        $('#customer_site_id').val('');

        if (cGst) {
            $('#shipping_gst').val(cGst);
            $('#shipping_address').data('address-locked', false);
            lookupAddressesByGst(cGst, $('#destination_address_dropdown'), $('#shipping_address'), $('#destination_gst_status'));
        } else if (cAddr) {
            let fullFormatted = cName ? (cName + '\n' + cAddr) : cAddr;
            $('#shipping_address').val(fullFormatted);
            $('#shipping_address').data('address-locked', true);
            $('#destination_address_dropdown').hide().empty();
        }
    });

    // 3. Terms & Conditions Custom Dropdowns and Auto-Fetch
    let isTermsUserOverridden = {
        payment: false,
        warranty: false,
        leadTime: false,
        installation: false,
        specs: false
    };

    function extractAndSetAdvancePct(val) {
        if (!val) return;
        let match = val.match(/(\d+)%\s*advance/i);
        if (match && match[1]) {
            $('#advance_percentage').val(parseFloat(match[1]).toFixed(2));
        } else if (/advance/i.test(val) && /100%/i.test(val)) {
            $('#advance_percentage').val((100).toFixed(2));
        } else if (/credit/i.test(val)) {
            $('#advance_percentage').val((0).toFixed(2));
        }
        recalculateAllTotals();
    }

    $(document).on('change', '#payment_terms', function(e) {
        let val = $(this).val();
        if (e.originalEvent) isTermsUserOverridden.payment = true;
        if (val === 'custom') {
            $('#custom_payment_terms_wrapper').slideDown(150);
            $('#custom_payment_terms').focus();
            val = $('#custom_payment_terms').val();
        } else {
            $('#custom_payment_terms_wrapper').slideUp(150);
        }
        extractAndSetAdvancePct(val);
    });

    $(document).on('input change', '#custom_payment_terms', function() {
        if ($('#payment_terms').val() === 'custom') {
            isTermsUserOverridden.payment = true;
            extractAndSetAdvancePct($(this).val());
        }
    });

    $(document).on('change', '#lead_time', function(e) {
        if (e.originalEvent) isTermsUserOverridden.leadTime = true;
        if ($(this).val() === 'custom') {
            $('#custom_lead_time_wrapper').slideDown(150);
            $('#custom_lead_time').focus();
        } else {
            $('#custom_lead_time_wrapper').slideUp(150);
        }
    });

    $(document).on('input', '#custom_lead_time', function() {
        isTermsUserOverridden.leadTime = true;
    });

    $(document).on('change', '#warranty_terms', function(e) {
        if (e.originalEvent) isTermsUserOverridden.warranty = true;
        if ($(this).val() === 'custom') {
            $('#custom_warranty_terms_wrapper').slideDown(150);
            $('#custom_warranty_terms').focus();
        } else {
            $('#custom_warranty_terms_wrapper').slideUp(150);
        }
    });

    $(document).on('input', '#custom_warranty_terms', function() {
        isTermsUserOverridden.warranty = true;
    });

    $(document).on('change', '#installation_scope', function(e) {
        if (e.originalEvent) isTermsUserOverridden.installation = true;
        if ($(this).val() === 'custom') {
            $('#custom_installation_scope_wrapper').slideDown(150);
            $('#custom_installation_scope').focus();
        } else {
            $('#custom_installation_scope_wrapper').slideUp(150);
        }
    });

    $(document).on('input', '#custom_installation_scope', function() {
        isTermsUserOverridden.installation = true;
    });

    $(document).on('input', '#technical_specification', function() {
        isTermsUserOverridden.specs = true;
    });

    function autoFetchTermsForSegment(catId, prodId) {
        if (!catId && !prodId) return;

        let url = '/vendor/get-segment-terms/' + (catId || 0);
        if (prodId) {
            url += '/' + prodId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res || res.status !== 'success') return;

                // 1. Payment Terms
                if (!isTermsUserOverridden.payment && res.default_payment_term) {
                    let $pt = $('#payment_terms');
                    let termVal = res.default_payment_term.trim();
                    let matchOpt = $pt.find('option').filter(function() {
                        return $(this).val().trim() === termVal || $(this).text().trim() === termVal;
                    });
                    if (matchOpt.length) {
                        $pt.val(matchOpt.first().val());
                    } else {
                        let optGroup = $pt.find('optgroup[label*="Segment"]');
                        if (optGroup.length) {
                            optGroup.prepend(new Option(termVal, termVal));
                        } else {
                            $pt.append(new Option(termVal, termVal));
                        }
                        $pt.val(termVal);
                    }
                    $('#custom_payment_terms_wrapper').hide();
                    extractAndSetAdvancePct($pt.val());
                }

                // 2. Warranty Terms
                if (!isTermsUserOverridden.warranty && res.default_warranty_term) {
                    let $wt = $('#warranty_terms');
                    let warVal = res.default_warranty_term.trim();
                    let matchOpt = $wt.find('option').filter(function() {
                        return $(this).val().trim() === warVal || $(this).text().trim() === warVal;
                    });
                    if (matchOpt.length) {
                        $wt.val(matchOpt.first().val());
                    } else {
                        let optGroup = $wt.find('optgroup[label*="Segment"]');
                        if (optGroup.length) {
                            optGroup.prepend(new Option(warVal, warVal));
                        } else {
                            $wt.append(new Option(warVal, warVal));
                        }
                        $wt.val(warVal);
                    }
                    $('#custom_warranty_terms_wrapper').hide();
                }

                // 3. Lead Time
                if (!isTermsUserOverridden.leadTime && res.lead_time) {
                    let $lt = $('#lead_time');
                    let ltVal = res.lead_time.trim();
                    let matchOpt = $lt.find('option').filter(function() {
                        return $(this).val().trim() === ltVal || $(this).text().trim() === ltVal;
                    });
                    if (matchOpt.length) {
                        $lt.val(matchOpt.first().val());
                    } else {
                        let optGroup = $lt.find('optgroup[label*="Segment"]');
                        if (optGroup.length) {
                            optGroup.prepend(new Option(ltVal, ltVal));
                        } else {
                            $lt.append(new Option(ltVal, ltVal));
                        }
                        $lt.val(ltVal);
                    }
                    $('#custom_lead_time_wrapper').hide();
                }

                // 4. Technical Specifications
                if (!isTermsUserOverridden.specs && res.technical_specification) {
                    $('#technical_specification').val(res.technical_specification);
                }
            }
        });
    }

    // 4. Quotation Calculation Engine
    function recalculateAllTotals() {
        let grossSubtotal = 0;
        let totalTaxable = 0;
        let totalGst = 0;

        let globalDiscountPct = parseFloat($('#discount_percentage').val()) || 0;
        let freight = parseFloat($('#freight_charges').val()) || 0;
        let unloading = parseFloat($('#unloading_charges').val()) || 0;
        let advancePct = parseFloat($('#advance_percentage').val()) || 0;

        let rows = [];
        $('.poRow').each(function() {
            let $row = $(this);
            let qty = parseFloat($row.find('.qty').val()) || 0;
            let rate = parseFloat($row.find('.rate').val()) || 0;
            let lineBasic = Math.round(qty * rate * 100) / 100;

            let rowDisPct = parseFloat($row.find('.requestdis-percent').val());
            if (isNaN(rowDisPct) || rowDisPct < 0) rowDisPct = 0;

            let rowDisAmt = Math.round(lineBasic * rowDisPct / 100 * 100) / 100;
            $row.find('.requestdis-amount').val(rowDisAmt.toFixed(2));

            let afterLineDiscount = Math.max(0, lineBasic - rowDisAmt);
            $row.find('.product_subtotal').val(lineBasic.toFixed(2));
            grossSubtotal += lineBasic;
            totalTaxable += afterLineDiscount;
            rows.push({ row: $row, afterLineDiscount: afterLineDiscount });
        });

        let remainingTaxable = totalTaxable;
        let overallDiscountAmt = Math.round(remainingTaxable * globalDiscountPct / 100 * 100) / 100;
        $('#discount_amount_display').val(overallDiscountAmt.toFixed(2));

        let allocatedDiscount = 0;
        rows.forEach(function(line, index) {
            let allocated = index === rows.length - 1
                ? overallDiscountAmt - allocatedDiscount
                : (remainingTaxable > 0 ? overallDiscountAmt * line.afterLineDiscount / remainingTaxable : 0);
            allocated = Math.round(allocated * 100) / 100;
            allocatedDiscount += index === rows.length - 1 ? 0 : allocated;
            let taxable = Math.max(0, Math.round((line.afterLineDiscount - allocated) * 100) / 100);
            let gstRate = parseFloat(line.row.find('.tax').val()) || 18;
            let gstAmount = Math.round(taxable * gstRate / 100 * 100) / 100;
            let total = Math.round((taxable + gstAmount) * 100) / 100;
            line.row.find('.lbt').val(taxable.toFixed(2));
            line.row.find('.taxamt').val(gstAmount.toFixed(2));
            line.row.find('.total').val(total.toFixed(2));
            totalGst += gstAmount;
        });

        totalTaxable = rows.reduce(function(sum, line) {
            return sum + parseFloat(line.row.find('.lbt').val() || 0);
        }, 0);
        let rawGrandTotal = totalTaxable + totalGst + freight + unloading;
        let grandTotal = Math.round(rawGrandTotal);
        let roundDiff = (grandTotal - rawGrandTotal).toFixed(2);
        let roundDisplay = (roundDiff > 0 ? '+' : '') + roundDiff;

        $('.line_roundoff').val('0.00');
        $('#round_off').val((grandTotal - rawGrandTotal).toFixed(2));
        if (rows.length) {
            rows[0].row.find('.line_roundoff').val(roundDiff);
        }
        let advanceAmount = (grandTotal * advancePct) / 100;

        $('#subtotal').val(grossSubtotal.toFixed(2));
        $('#taxtotal').val(totalGst.toFixed(2));
        $('#round_off').val(roundDisplay);
        $('#grandtotal').val(grandTotal.toFixed(2));
        $('#advance_amount_display').val('₹' + advanceAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#advance_ratio_text').text('(' + advancePct + '% Advance)');
        $('#amount_in_words_display').text(convertNumberToIndianWords(grandTotal));
    }

    function convertNumberToIndianWords(number) {
        let n = Math.round(number);
        if (isNaN(n) || n <= 0) return 'Zero Rupees Only';
        const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        function toWords(num) {
            if (num === 0) return '';
            if (num < 20) return ones[num] + ' ';
            if (num < 100) return tens[Math.floor(num / 10)] + ' ' + (num % 10 !== 0 ? ones[num % 10] + ' ' : '');
            if (num < 1000) return ones[Math.floor(num / 100)] + ' Hundred ' + (num % 100 !== 0 ? toWords(num % 100) : '');
            if (num < 100000) return toWords(Math.floor(num / 1000)) + 'Thousand ' + (num % 1000 !== 0 ? toWords(num % 1000) : '');
            if (num < 10000000) return toWords(Math.floor(num / 100000)) + 'Lakh ' + (num % 100000 !== 0 ? toWords(num % 100000) : '');
            return toWords(Math.floor(num / 10000000)) + 'Crore ' + (num % 10000000 !== 0 ? toWords(num % 10000000) : '');
        }
        return 'Rupees ' + toWords(n).trim() + ' Only';
    }

    // Input event listener for calculation
    $(document).on('input change', '.qty, .rate, .requestdis-percent, .tax, #discount_percentage, #freight_charges, #unloading_charges, #advance_percentage', function() {
        recalculateAllTotals();
    });

    $('#poCreationForm').on('submit', function(e) {
        const form = this;

        // 1. Sync hidden mercuriis branch
        if ($('#mercuriis_branch').length) {
            $('#hidden_mercuriis_branch').val($('#mercuriis_branch').val());
        }

        // 1.1 Pre-sync all productSelect elements from Select2 instances
        $('#poProductRows .poRow').each(function() {
            let $sel = $(this).find('.productSelect');
            let currentVal = $sel.val();
            if (!currentVal) {
                let s2Data = $sel.select2('data');
                if (s2Data && s2Data[0] && s2Data[0].id) {
                    currentVal = s2Data[0].id;
                    $sel.val(currentVal);
                    $sel.find('option').prop('selected', false);
                    let $opt = $sel.find(`option[value="${currentVal}"]`);
                    if ($opt.length) {
                        $opt.prop('selected', true);
                    }
                }
            }
        });

        // 2. Auto-clean any trailing empty product rows
        // If user added extra rows by clicking '+' but left them completely empty without choosing a product
        if ($('#poProductRows .poRow').length > 1) {
            let hasAtLeastOneFilled = false;
            $('#poProductRows .poRow').each(function() {
                if ($(this).find('.productSelect').val()) {
                    hasAtLeastOneFilled = true;
                }
            });
            if (hasAtLeastOneFilled) {
                $('#poProductRows .poRow').each(function() {
                    let $r = $(this);
                    let proVal = $r.find('.productSelect').val();
                    let rateVal = parseFloat($r.find('.rate').val()) || 0;
                    let partVal = ($r.find('.part_no').val() || '').trim();
                    // If this row has no product selected and 0 rate and no part no, remove it
                    if (!proVal && rateVal === 0 && !partVal) {
                        $r.remove();
                    }
                });
                if (typeof reindexRows === 'function') {
                    reindexRows();
                }
                recalculateAllTotals();
            }
        }

        // 3. Map accessories (only active checked accessories for the selected product)
        $('#poProductRows .poRow').each(function(index) {
            let proVal = $(this).find('.productSelect').val();
            $(this).find('.accessory-checkbox').each(function() {
                if ($(this).is(':checked') && proVal) {
                    $(this).attr('name', `accessories[${index}][]`);
                } else {
                    $(this).removeAttr('name');
                }
            });
            $(this).find('.accessory-qty').each(function() {
                let $accItem = $(this).closest('.accessory-item');
                let isChecked = $accItem.find('.accessory-checkbox').is(':checked');
                if (isChecked && proVal) {
                    $(this).attr('name', `accessory_quantity[${index}][${$(this).data('acc-id')}]`);
                } else {
                    $(this).removeAttr('name');
                }
            });
        });

        // 4. Ensure Tab 2 has sensible defaults if user hasn't explicitly set them
        if (!$('#payment_terms').val()) {
            $('#payment_terms').val('20% Advance & 80% Against Delivery').trigger('change');
        }
        if (!$('#lead_time').val()) {
            $('#lead_time').val('4 Weeks');
        }
        if (!$('#installation_scope').val()) {
            $('#installation_scope').val('In Supplier Scope (Included in Unit Rate)');
        }

        // 5. Validation Check
        let errors = [];

        // Vendor
        if (!$('#primary_vendor_id').val()) {
            errors.push({ field: document.getElementById('primary_vendor_id'), msg: 'Please select a Supplier (Vendor).' });
        }
        // Billing Address
        if (!$('#supplier_billing_address').val().trim()) {
            errors.push({ field: document.getElementById('supplier_billing_address'), msg: 'Supplier billing address is required.' });
        }
        // Supplier GST
        if (!$('#supplier_gst').val().trim()) {
            errors.push({ field: document.getElementById('supplier_gst'), msg: 'Supplier GSTIN is required.' });
        }
        // Shipping Address
        if (!$('#shipping_address').val().trim()) {
            errors.push({ field: document.getElementById('shipping_address'), msg: 'Delivery destination address is required.' });
        }
        // Shipping GST
        if (!$('#shipping_gst').val().trim()) {
            errors.push({ field: document.getElementById('shipping_gst'), msg: 'Destination GSTIN is required.' });
        }

        // Customer Site check
        if ($('input[name="shipping_type"]:checked').val() === 'customer_site' && !$('#customer_site_id').val() && !$('#shipping_address').val().trim()) {
            errors.push({ field: document.getElementById('shipping_address'), msg: 'Please select a Customer Site destination address.' });
        }

        // Product Rows check
        let validProductRowCount = 0;
        $('#poProductRows .poRow').each(function(idx) {
            let cat = $(this).find('.categorySelect').val();
            let pro = $(this).find('.productSelect').val();
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let rate = parseFloat($(this).find('.rate').val()) || 0;
            let hsn = ($(this).find('.hsn').val() || '').trim();

            if (!cat) {
                errors.push({ field: $(this).find('.categorySelect')[0], msg: `Row #${idx + 1}: Please select a Segment.` });
            } else if (!pro) {
                errors.push({ field: $(this).find('.productSelect')[0], msg: `Row #${idx + 1}: Please select a Model.` });
            } else if (qty <= 0) {
                errors.push({ field: $(this).find('.qty')[0], msg: `Row #${idx + 1}: Quantity must be at least 1.` });
            } else if (rate < 0) {
                errors.push({ field: $(this).find('.rate')[0], msg: `Row #${idx + 1}: Supplier rate cannot be negative.` });
            } else if (!hsn) {
                errors.push({ field: $(this).find('.hsn')[0], msg: `Row #${idx + 1}: HSN Code is mandatory.` });
            } else {
                validProductRowCount++;
            }
        });

        if (validProductRowCount === 0 && errors.length === 0) {
            errors.push({ field: $('#poProductRows .poRow:first .categorySelect')[0], msg: 'Purchase Order must have at least one product line item.' });
        }

        // Custom terms check
        if ($('#payment_terms').val() === 'custom' && !$('#custom_payment_terms').val().trim()) {
            errors.push({ field: document.getElementById('custom_payment_terms'), msg: 'Please enter custom payment terms.' });
        }
        if ($('#lead_time').val() === 'custom' && !$('#custom_lead_time').val().trim()) {
            errors.push({ field: document.getElementById('custom_lead_time'), msg: 'Please enter custom lead time.' });
        }

        // Clear existing errors
        $('.po-validation-error').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.select2-selection').css('border', '');

        if (errors.length > 0) {
            e.preventDefault();
            e.stopPropagation();

            // Display errors
            errors.forEach(function(err) {
                let field = err.field;
                if (!field) return;
                $(field).addClass('is-invalid');
                let s2 = $(field).next('.select2');
                if (s2.length) {
                    s2.find('.select2-selection').css('border', '1.5px solid #dc3545');
                }
                let wrapper = $(field).closest('.form-group, td, div');
                wrapper.append(`<div class="invalid-feedback d-block po-validation-error" style="font-size:11px;font-weight:600;">${err.msg}</div>`);
            });

            // Focus first invalid field and switch tab if necessary
            let firstErr = errors[0];
            if (firstErr && firstErr.field) {
                let pane = $(firstErr.field).closest('.tab-pane');
                if (pane.length && !pane.hasClass('active')) {
                    if (pane.attr('id') === 'home') {
                        goToHome();
                    } else if (pane.attr('id') === 'profile') {
                        goToTerms();
                    } else {
                        let tabBtn = document.querySelector(`[data-bs-target="#${pane.attr('id')}"]`);
                        if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                    }
                }
                setTimeout(function() {
                    firstErr.field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if ($(firstErr.field).hasClass('select2-hidden-accessible')) {
                        $(firstErr.field).select2('open');
                    } else {
                        firstErr.field.focus();
                    }
                }, 200);
            }

            return false;
        }

        // Allow form submit, indicate progress without disabling submit synchronously
        $('.btn-save-po').html('<i class="fas fa-spinner fa-spin me-1"></i> Saving Purchase Order...').css('pointer-events', 'none');
        return true;
    });

    // Discount Amount to Discount % sync
    $(document).on('input', '.requestdis-amount', function() {
        let $row = $(this).closest('tr');
        let qty = parseFloat($row.find('.qty').val()) || 0;
        let rate = parseFloat($row.find('.rate').val()) || 0;
        let lineBasic = qty * rate;
        let amt = parseFloat($(this).val()) || 0;

        if (lineBasic > 0) {
            let pct = (amt / lineBasic) * 100;
            $row.find('.requestdis-percent').val(pct.toFixed(2));
        }
        recalculateAllTotals();
    });

    // 5. Product Select & Auto-Fill Sync with Recalculation
    $(document).on('change', '.categorySelect', function() {
        let $row = $(this).closest('tr');
        let catId = $(this).val();
        let $proSelect = $row.find('.productSelect');

        $row.removeData('currentAccProductId');
        $row.find('.accessories-list').html('');
        $row.find('.accessories-wrapper').hide();
        $row.find('.accessories-summary-badge').hide();
        $row.find('.base-rate').val('0.00');
        $row.find('.rate').val('0.00');
        $row.find('.part_no').val('');
        $row.find('.hsn').val('8428');
        $row.find('.pro_des').val('');
        $row.find('.line_vendor_id').val($('#primary_vendor_id').val() || '');

        if ($proSelect.hasClass('select2-hidden-accessible')) {
            $proSelect.select2('destroy');
        }

        if (!catId) {
            $proSelect.html('<option value="">-- Select Segment First --</option>');
            recalculateAllTotals();
            return;
        }

        autoFetchTermsForSegment(catId, null);

        $proSelect.html('<option value="">Loading Models...</option>');

        $.ajax({
            url: '/get-products/' + catId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                let products = res.productData || res || [];
                let optionsHtml = '<option value="">-- Choose Model --</option>';

                if (Array.isArray(products) && products.length > 0) {
                    products.forEach(function(p) {
                        let pFullName = (p.productname || p.productmodel || '').trim();
                        let pModel = (p.productmodel || '').trim();
                        let pRate = parseFloat(p.rate) || 0;
                        let pGst = parseFloat(p.gst) || 18;
                        let pPart = p.partno || '';
                        let pHsn = p.hsn || '8428';
                        let pDesc = (p.productdes || p.productname || '').trim();
                        let pSearch = (pModel + ' ' + pFullName + ' ' + pPart).trim();

                        optionsHtml += `<option value="${p.id}" 
                            data-model="${pModel.replace(/"/g, '&quot;')}"
                            data-name="${pFullName.replace(/"/g, '&quot;')}"
                            data-rate="${pRate.toFixed(2)}" 
                            data-gst="${pGst}" 
                            data-partno="${pPart}" 
                            data-hsn="${pHsn}" 
                            data-desc="${pDesc.replace(/"/g, '&quot;')}" 
                            data-search="${pSearch.replace(/"/g, '&quot;')}" 
                            title="${pFullName.replace(/"/g, '&quot;')}">${pFullName}</option>`;
                    });
                } else {
                    optionsHtml = '<option value="">-- No Models Found --</option>';
                }

                $proSelect.html(optionsHtml);
                if (typeof initProductSearch === 'function') {
                    initProductSearch($proSelect[0]);
                }
                recalculateAllTotals();
            },
            error: function() {
                $proSelect.html('<option value="">-- Error Loading Models --</option>');
            }
        });
    });

    $(document).on('change select2:select', '.productSelect', function(e) {
        let $row = $(this).closest('tr');
        let prodId = $(this).val() || (e && e.params && e.params.data ? e.params.data.id : null);
        if (prodId) {
            $(this).val(prodId);
            $(this).find('option').prop('selected', false);
            let $matchedOpt = $(this).find(`option[value="${prodId}"]`);
            if ($matchedOpt.length) {
                $matchedOpt.prop('selected', true);
            }
        }
        let $opt = $(this).find('option:selected');
        if ((!$opt.length || !$opt.val()) && prodId) {
            $opt = $(this).find('option[value="' + prodId + '"]');
        }

        if (prodId) {
            let rate = parseFloat($opt.data('rate')) || 0;
            let gst = parseFloat($opt.data('gst')) || parseFloat($opt.data('tax')) || 18;
            let partNo = $opt.data('partno') || '';
            let hsn = $opt.data('hsn') || '8428';
            let desc = $opt.data('desc') || $opt.attr('title') || '';
            let fullName = $opt.attr('title') || $opt.data('name') || $opt.text() || '';

            $row.find('.base-rate').val(rate.toFixed(2));
            if (rate > 0 || !$row.find('.rate').val() || $row.find('.rate').val() == '0.00' || $row.find('.rate').val() == '0') {
                $row.find('.rate').val(rate.toFixed(2));
            }
            $row.find('.tax').val(gst);
            if (partNo) $row.find('.part_no').val(partNo);
            if (hsn) $row.find('.hsn').val(hsn);
            $row.find('.pro_des').val(desc || fullName);
            $(this).attr('title', fullName);

            fetchAccessoriesForRow($row, prodId);
            let catId = $row.find('.categorySelect').val();
            autoFetchTermsForSegment(catId, prodId);
        } else {
            $row.removeData('currentAccProductId');
            $row.find('.accessories-list').html('');
            $row.find('.accessories-wrapper').hide();
            $row.find('.accessories-summary-badge').hide();
        }

        recalculateAllTotals();
    });

    // 5.1 Fetch Accessories with Unique Row Context
    function fetchAccessoriesForRow(tr, productId) {
        let accWrapper = tr.find('.accessories-wrapper');
        let accList = tr.find('.accessories-list');
        let summaryBadge = tr.find('.accessories-summary-badge');

        let existingXhr = tr.data('accessoriesXhr');
        if (existingXhr && existingXhr.readyState !== 4) {
            existingXhr.abort();
        }

        if (!productId) {
            accList.html('');
            accWrapper.hide();
            summaryBadge.hide();
            tr.removeData('currentAccProductId');
            return;
        }

        // Avoid re-fetching if already loaded for the same product
        if (tr.data('currentAccProductId') == productId && accList.find('.accessory-item').length > 0) {
            accWrapper.slideDown(150);
            summaryBadge.hide();
            return;
        }
        tr.data('currentAccProductId', productId);

        let rowIndex = tr.index() + '_' + Date.now();

        let xhr = $.ajax({
            url: '/api/get-accessories-by-product/' + productId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                accList.html('');

                if (response.status && response.accessories && response.accessories.length > 0) {
                    let seenIds = new Set();
                    let uniqueAccessories = response.accessories.filter(function(acc) {
                        if (seenIds.has(acc.id)) return false;
                        seenIds.add(acc.id);
                        return true;
                    });

                    // Check for pre-selected accessories
                    let savedAccAttr = tr.find('.productSelect').data('saved-accessories');
                    let savedAccIds = [];
                    if (savedAccAttr) {
                        if (typeof savedAccAttr === 'string') {
                            try { savedAccIds = JSON.parse(savedAccAttr); } catch(e) {}
                        } else if (Array.isArray(savedAccAttr)) {
                            savedAccIds = savedAccAttr;
                        }
                    }

                    uniqueAccessories.forEach(function (acc) {
                        let uniqueInputId = `acc_${rowIndex}_${productId}_${acc.id}`;
                        let isChecked = false;
                        let accQty = 1;
                        if (Array.isArray(savedAccIds)) {
                            savedAccIds.forEach(function(s) {
                                if (typeof s === 'object' && s !== null) {
                                    if ((s.id && s.id == acc.id) || (s.name && s.name.toLowerCase().trim() === (acc.name || '').toLowerCase().trim())) {
                                        isChecked = true;
                                        if (s.quantity) accQty = parseInt(s.quantity) || 1;
                                    }
                                } else {
                                    if (s == acc.id || String(s).toLowerCase().trim() === (acc.name || '').toLowerCase().trim()) {
                                        isChecked = true;
                                    }
                                }
                            });
                        }

                        let html = `
                            <div class="accessory-item d-flex align-items-center justify-content-between gap-1 py-1 border-bottom border-light">
                                <div class="form-check m-0 d-flex align-items-center">
                                    <input class="form-check-input accessory-checkbox me-1"
                                           type="checkbox"
                                           name="accessories[${tr.index()}][]"
                                           value="${acc.id}"
                                           data-price="${acc.price}"
                                           id="${uniqueInputId}"
                                           ${isChecked ? 'checked' : ''}>
                                    <label class="form-check-label mb-0" for="${uniqueInputId}" style="cursor: pointer; font-size: 11px;">
                                        ${acc.name} <span class="text-muted" style="font-size:10px;">(₹${parseFloat(acc.price).toFixed(2)})</span>
                                    </label>
                                </div>
                                <div class="accessory-qty-wrapper d-flex align-items-center gap-1 ms-2">
                                    <span class="text-muted" style="font-size: 10px;">Qty:</span>
                                    <input type="number"
                                           class="form-control form-control-sm accessory-qty p-1 text-center"
                                           name="accessory_quantity[${tr.index()}][${acc.id}]"
                                           data-acc-id="${acc.id}"
                                           value="${accQty}"
                                           min="1"
                                           style="width: 48px; height: 24px; font-size: 11px;">
                                </div>
                            </div>
                        `;
                        accList.append(html);
                    });

                    updateAccessoryBadge(tr);
                    recalcRateFromAccessories(tr);
                    recalculateAllTotals();
                    let checkedCount = tr.find('.accessory-checkbox:checked').length;
                    if (checkedCount > 0) {
                        accWrapper.hide();
                        summaryBadge.show();
                    } else {
                        accWrapper.slideDown(150);
                        summaryBadge.hide();
                    }
                } else {
                    accList.html('');
                    accWrapper.hide();
                    summaryBadge.hide();
                    recalcRateFromAccessories(tr);
                    recalculateAllTotals();
                }
            },
            error: function (jqXHR, textStatus) {
                if (textStatus !== 'abort') {
                    accList.html('');
                    accWrapper.hide();
                    summaryBadge.hide();
                }
            }
        });

        tr.data('accessoriesXhr', xhr);
    }

    function updateAccessoryBadge(tr) {
        let checkedCount = tr.find('.accessory-checkbox:checked').length;
        let accTotal = 0;
        tr.find('.accessory-checkbox:checked').each(function () {
            let price = parseFloat($(this).data('price')) || 0;
            let qtyInput = $(this).closest('.accessory-item').find('.accessory-qty');
            let qty = parseInt(qtyInput.val()) || 1;
            if (qty < 1) qty = 1;
            accTotal += price * qty;
        });

        let countBadge = tr.find('.selected-acc-count');
        countBadge.text(checkedCount + ' Selected');
        if (checkedCount > 0) {
            countBadge.removeClass('bg-secondary').addClass('bg-primary');
        } else {
            countBadge.removeClass('bg-primary').addClass('bg-secondary');
        }

        let summaryBadge = tr.find('.accessories-summary-badge');
        if (checkedCount > 0) {
            summaryBadge.find('.summary-text').html(
                `<i class="fas fa-check-circle text-success me-1"></i><strong>${checkedCount} Accessories</strong> (+₹${accTotal.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})})`
            );
        } else {
            summaryBadge.find('.summary-text').html(`<span class="text-muted"><i class="fas fa-plus-circle me-1"></i>Add Accessories</span>`);
        }
    }

    // Click OK / Close Button
    $(document).on('click', '.btn-apply-accessories, .btn-close-accessories', function () {
        let tr = $(this).closest('tr');
        let accWrapper = tr.find('.accessories-wrapper');
        let summaryBadge = tr.find('.accessories-summary-badge');

        recalcRateFromAccessories(tr);
        recalculateAllTotals();
        updateAccessoryBadge(tr);

        accWrapper.slideUp(150);
        summaryBadge.slideDown(150);
    });

    // Click Change / Reopen Button
    $(document).on('click', '.btn-reopen-accessories, .accessories-summary-badge', function (e) {
        let tr = $(this).closest('tr');
        let accWrapper = tr.find('.accessories-wrapper');
        let summaryBadge = tr.find('.accessories-summary-badge');

        summaryBadge.hide();
        accWrapper.slideDown(150);
    });

    // When accessory checkbox or qty changes
    $(document).on('change', '.accessory-checkbox, .accessory-qty', function() {
        let tr = $(this).closest('tr');
        recalcRateFromAccessories(tr);
        updateAccessoryBadge(tr);
        recalculateAllTotals();
    });

    function recalcRateFromAccessories(tr) {
        let baseRate = parseFloat(tr.find('.base-rate').val()) || 0;
        let accTotal = 0;
        tr.find('.accessory-checkbox:checked').each(function () {
            let price = parseFloat($(this).data('price')) || 0;
            let qtyInput = $(this).closest('.accessory-item').find('.accessory-qty');
            let qty = parseInt(qtyInput.val()) || 1;
            if (qty < 1) qty = 1;
            accTotal += price * qty;
        });
        tr.find('.rate').val((baseRate + accTotal).toFixed(2));
    }

    // 6. Add & Remove Rows with sequential re-indexing
    function reindexRows() {
        let rows = $('#poProductRows .poRow');
        let totalRows = rows.length;
        rows.each(function(idx) {
            $(this).find('.rowIdx').text(idx + 1);
            let actionTd = $(this).find('td:last-child');
            if (totalRows <= 1) {
                actionTd.html('<button type="button" class="btn btn-primary btn-sm addRow rounded-circle" title="Add Row">+</button>');
            } else {
                actionTd.html(`
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-danger btn-sm removeRow rounded-circle" title="Remove Row">✕</button>
                        ${idx === totalRows - 1 ? '<button type="button" class="btn btn-primary btn-sm addRow rounded-circle" title="Add Row">+</button>' : ''}
                    </div>
                `);
            }
        });
    }

    $(document).on('click', '.addRow', function() {
        let catOptions = '';

        @if (!empty($categories))
            @foreach ($categories as $cat)
                catOptions += `<option value="{{ $cat->id }}">{{ addslashes($cat->category_name) }}</option>`;
            @endforeach
        @endif

        let newRow = `
            <tr class="poRow">
                <td class="text-center fw-bold rowIdx col-idx">1</td>
                <td class="col-segment">
                    <select name="catId[]" class="form-select form-select-sm categorySelect" required>
                        <option value="">-- Choose Segment --</option>
                        ${catOptions}
                    </select>
                </td>
                <td class="col-model" style="width: 20%; max-width: 240px;">
                    <select name="proId[]" class="form-select form-select-sm productSelect" required>
                        <option value="">-- Select Segment First --</option>
                    </select>
                    <input type="hidden" name="pro_des[]" class="pro_des" value="">
                    <input type="hidden" name="vendorId[]" class="line_vendor_id" value="">
                    <input type="hidden" class="base-rate" value="0.00">

                    {{-- Mapped accessories for the selected product show here as checkboxes --}}
                    <div class="accessories-wrapper mt-1 p-2 border rounded bg-white shadow-sm" style="display:none; font-size:11px; max-height: 280px; overflow-y: auto;">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-2">
                            <strong class="text-secondary" style="font-size: 11px;"><i class="fas fa-cog me-1"></i>Select Accessories:</strong>
                            <button type="button" class="btn-close btn-close-accessories" style="font-size: 8px;" title="Close"></button>
                        </div>
                        <div class="accessories-list d-flex flex-column gap-1"></div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top bg-light p-1 rounded">
                            <span class="badge bg-secondary selected-acc-count" style="font-size: 10px;">0 Selected</span>
                            <button type="button" class="btn btn-sm btn-success btn-apply-accessories py-0 px-3 fw-bold" style="font-size: 11px; height: 26px;">
                                <i class="fas fa-check me-1"></i> OK
                            </button>
                        </div>
                    </div>

                    {{-- Summary badge when accessories box is closed --}}
                    <div class="accessories-summary-badge mt-1 d-flex align-items-center justify-content-between p-1 px-2 border rounded bg-light" style="display:none; font-size:11px; cursor: pointer;">
                        <span class="summary-text text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>0 Accessories</span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-semibold btn-reopen-accessories" style="font-size: 10px; text-decoration: underline;">Change</button>
                    </div>
                </td>
                <td>
                    <input type="text" name="part_no[]" class="form-control form-control-sm part_no" placeholder="Part No" readonly>
                </td>
                <td>
                    <input type="text" name="hsn[]" class="form-control form-control-sm hsn" value="8428" placeholder="HSN">
                </td>
                <td>
                    <input type="number" name="qty[]" class="form-control form-control-sm qty text-center fw-bold" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="unit_price[]" class="form-control form-control-sm rate text-end fw-bold" value="0.00" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="99" name="discount[]" class="form-control form-control-sm requestdis-percent text-center" placeholder="%" value="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control form-control-sm requestdis-amount text-end" placeholder="Amt" value="0.00">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm lbt text-end bg-light fw-bold" readonly value="0.00">
                    <input type="hidden" name="sub_total[]" class="product_subtotal">
                </td>
                <td>
                    <input type="number" name="gst[]" class="form-control form-control-sm tax text-center" value="18" readonly required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm taxamt text-end bg-light" readonly value="0.00">
                </td>
                <td>
                    <input type="text" name="product_total[]" class="form-control form-control-sm total text-end bg-light fw-bold text-dark" readonly value="0.00">
                    <input type="hidden" name="roundoff[]" class="line_roundoff" value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow rounded-circle" title="Remove Row">
                        ✕
                    </button>
                </td>
            </tr>
        `;

        let $newRow = $(newRow);
        $('#poProductRows').append($newRow);
        filterCategoryDropdowns($newRow.find('.categorySelect'));
        let newSel = $newRow.find('.productSelect')[0];
        if (newSel && typeof initProductSearch === 'function') {
            initProductSearch(newSel);
        }
        reindexRows();
        recalculateAllTotals();
    });

    $(document).on('click', '.removeRow', function() {
        if ($('#poProductRows .poRow').length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
            recalculateAllTotals();
        } else {
            alert('Purchase Order must contain at least one product row.');
        }
    });

    // Model Select2 Configuration exactly matching Quotation module
    function initPoProductSelect2(select) {
        if (!select) return;
        let $sel = $(select);
        if ($sel.hasClass("select2-hidden-accessible")) {
            $sel.select2('destroy');
        }

        $sel.select2({
            placeholder: "Select Product",
            allowClear: true,
            width: '100%',
            dropdownParent: $sel.closest('td')
        });
    }
    window.initProductSearch = initPoProductSelect2;

    // Initialize calculation on load
    $(document).ready(function() {
        if (typeof reindexRows === 'function') {
            reindexRows();
        }
        if ($('#primary_vendor_id').val()) {
            filterCategoryDropdowns(null, true);
        }
        document.querySelectorAll('.productSelect').forEach(select => {
            if (typeof initProductSearch === 'function') {
                initProductSearch(select);
            }
            let $row = $(select).closest('tr');
            let val = $(select).val() || $(select).data('selected');
            if (val) {
                fetchAccessoriesForRow($row, val);
            }
        });
        recalculateAllTotals();
    });
</script>
@endsection