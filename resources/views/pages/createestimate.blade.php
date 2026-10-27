@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
<style>
    .form-label,
label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #374151;
}

.form-control,
.form-select,
textarea {
    font-size: 12.5px;
    padding: 6px 10px;
    border-radius: 6px;
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
    /* padding: 6px 14px; */
    border-radius: 8px;
    font-weight: 600;
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    border: none;
}

.quotation-table th {
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.quotation-table td {
    padding: 6px;
}

.form-control-sm,
.form-select-sm {
    height: 32px;
    font-size: 13px;
    padding: 4px 8px;
}

.bg-light {
    background-color: #f5f7fa !important;
}

.addRow,
.removeRow {
    width: 30px;
    height: 30px;
    line-height: 1;
    font-weight: bold;
}



.quotation-table .select2-container {
    width: 100% !important;
}

.quotation-table .select2-selection--single {
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
}

.quotation-table .select2-selection__rendered {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    padding-right: 20px !important;
    width:280px;
}

/* Fix dropdown arrow alignment */
.quotation-table .select2-selection__arrow {
    height: 32px !important;
}

/* Prevent row height expansion */
.quotation-table td {
    vertical-align: middle !important;
}
.select2.is-invalid .select2-selection {
    border-color: #dc3545 !important;
}
/* .productSelect .select2-selection__rendered {
    width:280px;
} */
    </style>
    @php
        $countries = DB::table('countries')->where('id', 76)->get(); // Fetching all countries from the database
    @endphp
    <section class="bg-white"
        style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;border-radius:10px;">
        <div class="container-fluid bg-white pt-4 pb-5">
            <div class="alert alert-warning alert-dismissible fade show d-none myalert" role="alert" id="myalert">
                <span class="error-text"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-lg-12 mb-3" style="text-align: -webkit-right;"><a onclick="history.back()" style="width: 15%;" class="btn btn-primary d-block">Back</a></div>

            <div class="row mb-3">
                <div class="col-lg-3 mt-2">
                    <div class="form-group">
                        <label>Reference No</label>

                        <input type="text" name="" placeholder="Quotation No" class="form-control" readonly
                            style="background-color: #dedede;" value="{{ $lead->lead_id }}">

                    </div>
                </div>
                <div class="col-lg-3 mt-2">
                    <div class="form-group">
                        <label>Customer Name </label>
                        <input type="text" name="" onblur="namevalidation(this)" style="background-color: #dedede;"
                            placeholder="Client Name" class="form-control" value="{{ $lead->LeadName }}" readonly>
                    </div>
                </div>

                <div class="col-lg-3 mt-2">
                    <div class="form-group">
                        <label>Quotation Date</label>
                        <input type="text" name="" placeholder="" style="background-color: #dedede;" class="form-control"
                            value="{{ now()->format('d-m-Y') }}" readonly>
                    </div>
                </div>

            </div>

            <form method="POST" action="{{ route('createestimate') }}" id="create-quotation-form" novalidate autocomplete="off" data-form-type="other" data-lpignore="true">
                @csrf

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">Create Quotation</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">Terms and
                            Conditions</button>
                    </li>

                </ul>


                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">

                            <input type="hidden" name="leadno" value="{{ $lead->id }}" class="form-control mt-2" readonly>
                            <input type="hidden" name="edit_mode" value="0">


                            <div class="col-lg-3 mt-3">
                                <div class="form-group">
                                    <label>Billing Address</label>

                                    <textarea class="form-control" style="background-color: #dedede;" name="billingad"
                                        id="billingAddress" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">{{ $lead->billaddress }}</textarea>

                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Billing Country <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " style="background-color: #dedede;" name="billingcountry"
                                        id="country" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                            </div>
                            <div class="col-lg-3 mt-3">
                                <div class="form-group">
                                    <label>Shipping Address</label>
                                    <textarea class="form-control" name="placeofsupply" id="shippingAddress" required autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Shipping Country <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " name="shippingcountry" id="shipcountry" required autocomplete="off" data-lpignore="true" data-form-type="other">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Billing State <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " name="state_id" id="state_list"
                                        style="background-color: #dedede;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                                        <option value=""> -- Choose State -- </option>
                                        @if($product = App\Models\Statelist::get())
                                            @foreach ($product as $p)
                                                <option value="{{ $p->id }}" {{ $p->id == $lead->state_id ? 'selected' : '' }}>
                                                    {{ $p->state }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Billing City <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " name="citylist" id="city_list"
                                        style="background-color: #dedede;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                                        <option value=""> -- Choose City -- </option>
                                        @if($product = App\Models\Citylist::get())
                                            @foreach ($product as $p)
                                                <option value="{{ $p->id }}" {{ $p->id == $lead->citylist ? 'selected' : '' }}>
                                                    {{ $p->city_name }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                            </div>

                            @php
                                $defShippingState = isset($lead) ? ($lead->shistate_id ?: $lead->state_id) : '';
                                $defShippingCity  = isset($lead) ? ($lead->shcitylist ?: $lead->citylist) : '';
                                $isSameAddress    = isset($lead) && ($lead->same_as_billing_address == 1 || empty($lead->shistate_id));
                            @endphp
                            <div class="col-lg-2 mt-2" style="text-align: center;">
                                <input type="checkbox" name="same_as_billing" id="sameAsBilling" onclick="toggleAddress()" {{ $isSameAddress ? 'checked' : '' }}>
                                Same as Billing Address
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Shipping State <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " name="shistate_id" id="state_listsh" required data-selected="{{ $defShippingState }}" autocomplete="off" data-lpignore="true" data-form-type="other">
                                        <option value=""> -- Choose State -- </option>
                                        @if($product = App\Models\Statelist::get())
                                            @foreach ($product as $p)
                                                <option value="{{ $p->id }}" {{ $p->id == $defShippingState ? 'selected' : '' }}>
                                                    {{ $p->state }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Shipping City <span
                                            style="color:red">*</span></label>
                                    <select class="form-select " name="shcitylist" id="shicity_list" required data-selected="{{ $defShippingCity }}" autocomplete="off" data-lpignore="true" data-form-type="other">
                                        <option value=""> -- Choose City -- </option>
                                        @if($city = App\Models\Citylist::get())
                                            @foreach ($city as $c)
                                                <option value="{{ $c->id }}" {{ $c->id == $defShippingCity ? 'selected' : '' }}>
                                                    {{ $c->city_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Billing Postal Code <span
                                            style="color:red">*</span></label>
                                    <input type="decimal" name="postalcode" value="{{ $lead->postalcode }}" id="postalcode"
                                        class="form-control" style="background-color: #dedede;" placeholder="Postal Code"
                                        readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Billing GST No </label>
                                    <input type="text" name="billgs" id="billgs" style="background-color: #dedede;"
                                        value="{{ $lead->gst }}" class="form-control" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-2">
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Shipping Postal Code <span
                                            style="color:red">*</span></label>
                                    <input type="decimal" name="shippostalcode" id="shippostalcode" class="form-control"
                                        required autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other"
                                        placeholder="Postal Code">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Shipping GST No</label>
                                    <input type="text" name="gstnum" id="gstnum" class="form-control" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                                </div>
                            </div>
                            @php
                                $categories = App\Models\ProductCategory::get();
                            @endphp
                            @php

                                $allowedDiscountValue = $settings->allowed_discount_status != 'active' ? '0' : $settings->allowed_discount ?? '';

                            @endphp
                            @php

    $leadCategory = App\Models\ProductCategory::find($lead->category);
    $isSpecial = $leadCategory && $leadCategory->sub_category === 'special';
      $design = null;

    if ($isSpecial) {
        $design = App\Models\Design::where('idlead', $lead->id)->first();
    }

     $quotationData = null;

    if ($design && $design->quotation) {
        $excelPath = storage_path('app/public/'.$design->quotation);
        // $quotationData = readQuotationTotals($excelPath);
        $quotationData = App\Helpers\QuotationHelper::readQuotationTotals($excelPath);
    }
@endphp

@if($isSpecial)

    <div class="card mt-3">
        <div class="card-header">
            <strong>Design Attachments</strong>
        </div>

        <div class="card-body">
            <div class="row g-2 align-items-end">

    <div class="col-md-2">
        <label class="fw-bold">Basic Total</label>
        <input type="number" id="basic_total"
               class="form-control"
               value="{{ $quotationData['basic_total'] ?? 0 }}"
               readonly>
    </div>

    <div class="col-md-2">
        <label class="fw-bold">Total</label>
        <input type="number" id="total"
               class="form-control"
               value="{{ $quotationData['total'] ?? 0 }}"
               readonly>
    </div>

    <div class="col-md-2">
        <label class="fw-bold">Allowed Disc %</label>
        <input type="number"
               class="form-control bg-light text-center"
               value="{{ $allowedDiscountValue }}"
               readonly>
    </div>
    <input type="hidden" name="allowed_discount"
       value="{{ $allowedDiscountValue }}">


    <!-- Requested Discount % -->
    <div class="col-md-1">
        <label class="fw-bold">Disc %</label>
        <input type="number" id="disc_percent"
               class="form-control text-center"
               step="0.01">
    </div>

    <!-- Requested Discount Amount -->
    <div class="col-md-2">
        <label class="fw-bold">Disc ₹</label>
        <input type="number" id="disc_amount"
               class="form-control text-center"
               step="0.01">
    </div>

    <div class="col-md-1">
        <label class="fw-bold">GST</label>
        <input type="number" id="gst"
               class="form-control"
               readonly>
    </div>

    <div class="col-md-2">
        <label class="fw-bold">Grand Total</label>
        <input type="number" id="grand_total"
               class="form-control fw-bold text-success"
               readonly>
    </div>

</div>
<input type="hidden" name="sp_subtotal" id="sp_subtotal">
<input type="hidden" name="sp_tax_total" id="sp_tax_total">
<input type="hidden" name="sp_grand_total" id="sp_grand_total">
<input type="hidden" name="sp_request_discount" id="sp_request_discount">
<input type="hidden" name="sp_request_disamt" id="sp_request_disamt">
<input type="hidden" name="sp_basic_total" id="sp_basic_total">

            {{-- <div class="row g-3 mb-3">

    <div class="col-md-4">
        <label class="fw-bold">Basic Total</label>
        <input type="text" class="form-control"
               value="{{ number_format($quotationData['basic_total'] ?? 0, 2) }}"
               >
    </div>

    <div class="col-md-4">
        <label class="fw-bold">Installation Charges</label>
        <input type="text" class="form-control"
               value="{{ number_format($quotationData['installation_charges'] ?? 0, 2) }}"
               >
    </div>

    <div class="col-md-4">
        <label class="fw-bold">Freight Charges</label>
        <input type="text" class="form-control"
               value="{{ number_format($quotationData['freight_charges'] ?? 0, 2) }}"
               >
    </div>


    <div class="col-md-4">
        <label class="fw-bold">Total</label>
        <input type="text" class="form-control"
               value="{{ number_format($quotationData['total'] ?? 0, 2) }}">
    </div>
     <div class="col-md-4">
        <label class="fw-bold">Allowed Discount</label>
      <input type="number" name="alloweddiss"
               class="form-control form-control-sm  text-center bg-light"
               readonly value="{{ $allowedDiscountValue }}">
     </div>
     <div class="col-md-4">
        <label class="fw-bold">Requested Discount</label>
      <input type="number" name="requesteddisc"
               class="form-control form-control-sm  text-center bg-light">
     </div>

    <div class="col-md-4">
        <label class="fw-bold">GST (18%)</label>
        <input type="text" class="form-control"
               value="{{ number_format($quotationData['gst'] ?? 0, 2) }}">
    </div>

    <div class="col-md-4">
        <label class="fw-bold">Grand Total</label>
        <input type="text" class="form-control fw-bold text-success"
               value="{{ number_format($quotationData['grand_total'] ?? 0, 2) }}">
    </div>

</div> --}}

            {{-- <div class="row g-3 mb-3">

                <div class="col-md-4">
                    <label class="fw-bold">Basic Total</label>
                    <div>{{ number_format($quotationData['basic_total'] ?? 0, 2) }}</div>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">Installation Charges</label>
                    <div>{{ number_format($quotationData['installation_charges'] ?? 0, 2) }}</div>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">Freight Charges</label>
                    <div>{{ number_format($quotationData['freight_charges'] ?? 0, 2) }}</div>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">Total</label>
                    <div>{{ number_format($quotationData['total'] ?? 0, 2) }}</div>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">GST (18%)</label>
                    <div>{{ number_format($quotationData['gst'] ?? 0, 2) }}</div>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">Grand Total</label>
                    <div class="text-success fw-bold">
                        {{ number_format($quotationData['grand_total'] ?? 0, 2) }}
                    </div>
                </div>

            </div> --}}

            @if($design)


                {{-- QUOTATION --}}
                {{-- <div class="mb-3">
                    <label class="fw-bold">Quotation</label><br>

                    @if($design->quotation)
                        <a href="{{ asset('storage/'.$design->quotation) }}"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            View Quotation
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div> --}}

                {{-- DRAWING --}}
                {{-- <div class="mb-3">
                    <label class="fw-bold">Drawing</label><br>

                    @if($design->drawing)
                        <a href="{{ asset('storage/'.$design->drawing) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm">
                            View Drawing
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div> --}}

            @else
                {{-- <div class="alert alert-warning">
                    Design record not found for this lead.
                </div> --}}
            @endif

        </div>
    </div>

@endif

@if(!$isSpecial)
                            <table class="table table-bordered quotation-table align-middle">
    {{-- <thead class="table-light">
        <tr class="text-center">
            <th>Segment</th>
            <th>Model</th>
            <th>Part No</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Allowed Dis %</th>
            <th>Req Dis %</th>
            <th>Total Amt</th>
            <th>Tax %</th>
            <th>Tax Amt</th>
            <th>Total + Tax</th>
            <th>Add Product</th>
        </tr>
    </thead> --}}
    <thead class="table-light">
    <tr class="text-center align-middle">
        <th rowspan="2">Segment</th>
        <th rowspan="2">Model</th>
        <th rowspan="2">Part No</th>
        <th rowspan="2">Qty</th>
        <th rowspan="2">Price</th>
        <th rowspan="2">Allowed Dis %</th>

        <!-- MAIN HEADER -->
        <th colspan="2">Req Dis %</th>

        <th rowspan="2">Total Price</th>
        <th rowspan="2">Tax %</th>
        <th rowspan="2">Tax Price</th>
        <th rowspan="2">Total + Tax</th>
        <th rowspan="2">Add Product</th>
    </tr>

    <tr class="text-center">
        <!-- SUB HEADERS -->
        <th style="width:70px">%</th>
        <th style="width:100px">Price</th>
    </tr>
</thead>


    <tbody id="productRows">
        @php
            $firstSelectedProduct = '';
            if (isset($leadProducts) && $leadProducts->count() === 1) {
                $firstSelectedProduct = $leadProducts->first()->singleproduct;
            } elseif (!empty($lead->products) && !str_contains($lead->products, ',')) {
                $firstSelectedProduct = $lead->products;
            }
        @endphp

        <tr class="productRow" data-sub-category-id="{{ $lead->subcategory_id ?? '' }}">
            <td style="width: 14%;">
               <select name="product_category_id[]" class="form-select form-select-sm categorySelect" data-sub-category-id="{{ $lead->subcategory_id ?? '' }}" required>
                <option value=""> -- Choose Segment -- </option>

                @foreach($categories as $cat)
                    @if($cat->id == $lead->category)
                        <option value="{{ $cat->id }}"
                                data-name="{{ $cat->category_name }}"
                                data-dispatch-time="{{ $cat->dispatch_time }}"
                                data-product-keyword="{{ $cat->product_keyword }}"
                                data-keyword-dispatch-time="{{ $cat->keyword_dispatch_time }}"
                                selected>
                            {{ $cat->category_name }}
                        </option>
                    @endif
                @endforeach
               </select>
            </td>

            <td style="width: 20%;">
                <select name="product_id[]" class="form-select form-select-sm productSelect" data-selected="{{ $firstSelectedProduct }}" required>
                    <option value="">Select Product</option>
                </select>

                {{-- Stores the plain product price before any accessory is added --}}
                <input type="hidden" class="base-rate" value="">

                {{-- Mapped accessories for the selected product show here as checkboxes --}}
                <div class="accessories-wrapper mt-1 p-2 border rounded bg-white shadow-sm" style="display:none; font-size:11px; max-height: 280px; overflow-y: auto;">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-2">
                        <strong class="text-secondary" style="font-size: 11px;"><i class="bi bi-gear me-1"></i>Select Accessories:</strong>
                        <button type="button" class="btn-close btn-close-accessories" style="font-size: 8px;" title="Close"></button>
                    </div>
                    <div class="accessories-list d-flex flex-column gap-1"></div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top bg-light p-1 rounded">
                        <span class="badge bg-secondary selected-acc-count" style="font-size: 10px;">0 Selected</span>
                        <button type="button" class="btn btn-sm btn-success btn-apply-accessories py-0 px-3 fw-bold" style="font-size: 11px; height: 26px;">
                            <i class="bi bi-check2 me-1"></i> OK
                        </button>
                    </div>
                </div>

                {{-- Summary badge when accessories box is closed --}}
                <div class="accessories-summary-badge mt-1 d-flex align-items-center justify-content-between p-1 px-2 border rounded bg-light" style="display:none; font-size:11px; cursor: pointer;">
                    <span class="summary-text text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>0 Accessories</span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-semibold btn-reopen-accessories" style="font-size: 10px; text-decoration: underline;">Change</button>
                </div>
            </td>

            <td style="width: 5%;"><input type="text" name="part_no[]" class="form-control form-control-sm" readonly></td>

            <td style="width: 5%;"><input type="number" name="quantity[]" class="form-control form-control-sm qty text-center" min="1" value="1" required></td>

            <td style="width: 7%;"><input type="number" name="rate[]" class="form-control form-control-sm rate text-end" min="0" step="any" required></td>

            <td>
                <input type="number" name="allowdis[]"
                       class="form-control form-control-sm allowdis text-center bg-light"
                       readonly value="{{ $allowedDiscountValue }}">
            </td>
            <td>
                <!-- Discount % -->
                <input type="number"
                       name="requestdis[]"
                       class="form-control form-control-sm requestdis-percent text-center"
                       placeholder="%"
                       min="0" max="99" step="any">
            </td>

            <td>
                <!-- Discount Amount -->
                <input type="number"
                       name="requestdis_amount[]"
                       class="form-control form-control-sm requestdis-amount text-end"
                       placeholder="Amt">
            </td>

            <td style="width: 7%;"><input type="text" name="lbt[]" class="form-control form-control-sm lbt text-end bg-light" readonly></td>

            <td><input type="number" name="tax[]" class="form-control form-control-sm tax text-center" min="0" max="99" required></td>

            <td style="width: 6%;"><input type="text" name="taxamt[]" class="form-control form-control-sm taxamt text-end bg-light" readonly></td>

            <td><input type="text" name="total[]" class="form-control form-control-sm total text-end bg-light" readonly></td>

            <td class="text-center">
                <button type="button" class="btn btn-primary btn-sm addRow rounded-circle">
                    +
                </button>
            </td>
        </tr>
    </tbody>
</table>

<div class="row mt-4">
    <div class="col-md-6"></div>

    <div class="col-md-6">
        <div class="mb-2 d-flex justify-content-between">
            <label>Subtotal</label>
            <input type="text" id="subtotal" name="subtotal"  class="form-control form-control-sm w-50" readonly>
        </div>


        <div class="mb-2 d-flex justify-content-between">
            <label>Tax</label>
            <input type="text" id="taxtotal" name="taxtotal" class="form-control form-control-sm w-50" readonly>
        </div>

        <div class="mb-2 d-flex justify-content-between">
            <label>Total</label>
            <input type="text" id="grandtotal" name="grandtotal" class="form-control form-control-sm w-50" readonly>
        </div>
    </div>
</div>

@endif

                            {{-- <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Product Category</label>
                                    @if ($drop = App\Models\ProductCategory::where('id', $lead->category)->first())
                                        <input type="text" name="catepro" class="form-control"
                                            value="{{ $drop->category_name }}" placeholder="" style="background-color: #dedede;"
                                            readonly>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4 mt-3">
                                <div class="form-group">
                                    <label>Product </label>
                                    <input type="hidden" name="product" class="form-control"
                                        style="background-color: #dedede;" value="{{ $lead->products }}" placeholder=""
                                        readonly>

                                    @if ($prod = App\Models\Product::where('id', $lead->products)->first())
                                        <textarea name="" class="form-control" style="background-color: #dedede;" placeholder=""
                                            readonly> {{ $prod->productname }}</textarea>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Part No </label>
                                    @if ($prod = App\Models\Product::where('id', $lead->products)->first())
                                        <input type="text" name="part_no" class="form-control" value="{{ $prod->partno }}"
                                            placeholder="" style="background-color: #dedede;" readonly>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Quantity </label>
                                    <input type="text" name="quanty" style="text-transform:uppercase;"
                                        value="{{ $quotations ? $quotations->quantitys : '' }}" class="form-control">
                                </div>
                            </div>
                             @php

                                $allowedDiscountValue = $settings->allowed_discount_status != 'active' ? '0' : $settings->allowed_discount ?? '';

                            @endphp
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Allowed Discount % </label>
                                    <input type="text" name="allowdis" class="form-control" readonly style="background-color: #dedede;" value="{{ $allowedDiscountValue }}">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Requested Discount % </label>
                                    <input type="text" name="requestdis" style="text-transform:uppercase" min="0" max="99"
                                        oninput="this.value = this.value.slice(0, 2)" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Rate</label>
                                    @if ($prod = App\Models\Product::where('id', $lead->products)->first())


                                        <input type="text" name="ratee"
                                            style="text-transform:uppercase;background-color: #dedede;"
                                            value="{{ $prod->rate }}" class="form-control" readonly>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Line Basic Total</label>
                                    <input type="text" name="lbt"
                                        style="text-transform:uppercase;background-color: #dedede;" readonly
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Tax(%)</label>
                                    @if ($prod = App\Models\Product::where('id', $lead->products)->first())
                                        <input type="text" name="tax"
                                            style="text-transform:uppercase;background-color: #dedede;" value="{{ (int) $prod->gst }}"
                                            readonly class="form-control">
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Tax Amount</label>
                                    <input type="text" name="taxamt"
                                        style="text-transform:uppercase;background-color: #dedede;" readonly
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3">
                                <div class="form-group">
                                    <label>Grand Total</label>
                                    <input type="text" name="grandtotal"
                                        style="text-transform:uppercase;background-color: #dedede;" readonly
                                        class="form-control">
                                </div>
                            </div> --}}
<div class="text-end mt-3">
    <button type="button" class="btn btn-primary" onclick="goToTerms()">
        Next → Terms & Conditions
    </button>
</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mt-3" style="display: flex;flex-direction: column;">
                                    <label for="termSelect" class="form-label">
                                        Payment Terms <span style="color:red">*</span>
                                    </label>

                                    <!-- Select2 dropdown -->
                                    <select id="termSelect" name="termscondition" class="form-control" required>
                                        <option value="">Select Term</option>
                                        <option value="new">+ Add Custom Payment Term</option>
                                        @foreach($paymentTerms as $term)
                                            <option value="{{ $term->id }}" data-id="{{ $term->id }}">
                                                {{ $term->content }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <textarea id="newTermContent" name="newtermscondition" class="form-control mt-2" placeholder="Enter custom payment term" style="display:none;"></textarea>
                                </div>


                                <div class="form-group mt-3">
                                    <label>Freight/Transportation <span style="color:red">*</span></label>
                                    <select name="freight_category_id" id="freight_category_id" class="form-select freight_selection quotation-master-select" data-placeholder="Search or Select Freight/Transportation" required>
                                        <option value="">-- Choose --</option>
                                        <option value="new">+ Add Custom Freight/Transportation</option>
                                        @if ($freightCat = App\Models\FreightCategory::where(function ($query) use ($lead) {
                                            $query->where('product_category_id', $lead->category)
                                                ->orWhereNull('product_category_id');
                                        })->get())
                                            @foreach ($freightCat as $data)
                                                <option value="{{ $data->id }}" data-id="{{ $data->id }}">
                                                    {{ $data->categoty }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="text" class="form-control mt-2" name="freight_category"
                                        style="display: none;" placeholder="Type here">
                                </div>

                                <div class="form-group">
                                    <label class="mt-3">Included or Extra <span style="color:red;" id="freight_sub_cat_star" class="d-none">*</span></label>
                                    <select class="form-select quotation-master-select"
                                        name="freight_sub_category_id" id="freight_sub_category_id" data-placeholder="Search or Select Included or Extra">
                                        <option value="">-- Choose --</option>
                                        @if ($freightCat = App\Models\FreightSubCategory::where(function ($query) use ($lead) {
                                            $query->where('product_category_id', $lead->category)
                                                ->orWhereNull('product_category_id');
                                        })->orderByRaw("CASE WHEN sub_category = 'Included' THEN 1 WHEN sub_category = 'Extra' THEN 2 ELSE 3 END")->get()->unique('sub_category'))
                                            @foreach ($freightCat as $data)
                                                <option value="{{ $data->id }}" data-id="{{ $data->id }}">
                                                    {{ $data->sub_category }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group mt-3">
                                    <label>Unloading at site</label>
                                    <select class="form-select quotation-master-select" name="unloading" data-placeholder="Search or Select Unloading at site">
                                        <option value=""> -- Choose -- </option>
                                        <option value="new">+ Add Custom Unloading at site</option>
                                        @foreach (App\Models\QuotationUnloadingOption::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control mt-2 quotation-master-new-input" name="new_unloading"
                                        style="display: none;" placeholder="Enter custom unloading option">
                                </div>


                            </div>
                             <div class="col-md-4">
                                <div class="form-group mt-3" style="display: flex;flex-direction: column;">
                                    <label for="warrantySelect" class="form-label">
                                        Warranty Terms <span style="color:red">*</span>
                                    </label>

                                    <!-- Select2 dropdown -->
                                    <select id="warrantySelect" name="warranty" class="form-control" required>
                                        <option value="">Select Warranty Term</option>
                                        <option value="new">+ Add Custom Warranty Term</option>
                                        @foreach($warrantyTerms as $term)
                                            <option value="{{ $term->id }}" data-id="{{ $term->id }}">
                                                {{ $term->content }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <textarea id="newwarContent" name="newwarcondition" class="form-control mt-2" placeholder="Enter custom warranty term" style="display:none;"></textarea>
                                </div>

                                @php
                                    $isSuperAdmin = Auth::check() && (Auth::user()->role === 'Super Admin');
                                @endphp
                                <div class="form-group mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="mb-0">Additional Warranty</label>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" id="include_warranty_in_basic" name="include_warranty_in_basic" value="1" {{ old('include_warranty_in_basic') ? 'checked' : '' }}>
                                            <label class="form-check-label small text-muted" for="include_warranty_in_basic">Include in Basic Total</label>
                                        </div>
                                    </div>
                                    <select class="form-select" id="additional_warranty_months_select" name="additional_warranty_months">
                                        @if($isSuperAdmin)
                                            <option value="custom" data-pct="0">+ Custom Period / Percentage</option>
                                        @endif
                                        <option value="0" data-pct="0" selected>None (0 Months - Standard Only)</option>
                                        <option value="6" data-pct="2.5">6 Months (Default 2.5%)</option>
                                        <option value="12" data-pct="5.0">12 Months (Default 5.0%)</option>
                                        <option value="18" data-pct="7.5">18 Months (Default 7.5%)</option>
                                        <option value="24" data-pct="10.0">24 Months (Default 10.0%)</option>
                                    </select>

                                    @if($isSuperAdmin)
                                        <div id="warranty_dynamic_inputs_wrapper" class="mt-2 p-2 border rounded bg-light" style="display: none;">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-6">
                                                    <label class="small text-muted mb-1">Warranty Rate (%)</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" min="0" max="100" step="0.01" class="form-control" id="additional_warranty_percentage_input" name="additional_warranty_percentage" placeholder="Rate %" value="0.00">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small text-muted mb-1">Calculated Charge (₹)</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="additional_warranty_charge_final" name="additional_warranty_charge" placeholder="Charge ₹" value="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1" id="warranty_charge_display">
                                                Additional Warranty: None (₹0.00)
                                            </small>
                                        </div>
                                    @else
                                        <input type="hidden" id="additional_warranty_percentage_input" name="additional_warranty_percentage" value="0.00">
                                        <input type="hidden" id="additional_warranty_charge_final" name="additional_warranty_charge" value="0.00">
                                        <small class="text-muted d-block mt-1" id="warranty_charge_display">
                                            Additional Warranty: None (₹0.00)
                                        </small>
                                    @endif
                                </div>

                                @php
                                    $selectedDov = old('dov', optional($dovs->first())->value);
                                @endphp
                                <div class="form-group mt-3">
    <label>Date of Validity</label>

    <select class="form-select" name="dov" id="dovSelect" onchange="handleDovChange()">
        <option value="">-- Select --</option>
        <option value="new" {{ $selectedDov === 'new' ? 'selected' : '' }}>+ Add Custom</option>
        @foreach($dovs as $dov)
            <option value="{{ $dov->value }}" {{ $selectedDov === $dov->value ? 'selected' : '' }}>{{ $dov->value }}</option>
        @endforeach
    </select>

    <!-- Add Custom Input -->
    <input type="text"
           class="form-control mt-2"
           name="dov_new"
           id="dovNew"
           placeholder="Enter custom validity (e.g. 60 Days)"
           value="{{ old('dov_new') }}"
           style="display:none;">
</div>

                                @php
                                    $leadDispatchTime = optional($leadCategory)->dispatch_time;
                                @endphp
                                <div class="form-group mt-3">
                                    <label>Lead Time</label>
                                    <input type="text"
                                           class="form-control"
                                           id="dispatchTimeInput"
                                           name="dispatch_time"
                                           value="{{ old('dispatch_time', $leadDispatchTime) }}"
                                           data-default-dispatch-time="{{ $leadDispatchTime }}"
                                           readonly>
                                </div>

                             </div>
                            <div class="col-md-4">
                                <div class="form-group mt-3">
                                    <label>Currency</label>
                                    <input type="text" class="form-control" name="currency" value="INR">
                                </div>

                                <div class="form-group mt-3">
                                    <label>Installation Scope</label>
                                    <select class="form-select quotation-master-select" name="installation" data-placeholder="Search or Select Installation Scope">
                                        <option value=""> -- Choose -- </option>
                                        <option value="new">+ Add Custom Installation Scope</option>
                                        @foreach (App\Models\QuotationInstallationScope::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control mt-2 quotation-master-new-input" name="new_installation"
                                        style="display: none;" placeholder="Enter custom installation scope">
                                </div>

                                 <div class="form-group mt-3">
                                     <div class="d-flex justify-content-between align-items-center mb-1">
                                         <label class="mb-0">Installation Charge <span style="color:red">*</span></label>
                                         <div class="form-check form-check-inline mb-0">
                                             <input class="form-check-input" type="checkbox" id="include_installation_in_basic" name="include_installation_in_basic" value="1" {{ old('include_installation_in_basic') ? 'checked' : '' }}>
                                             <label class="form-check-label small text-muted" for="include_installation_in_basic">Include in Basic Total</label>
                                         </div>
                                     </div>
                                     <select class="form-select" id="installation_charge_type" name="installation_charge_type" onchange="handleInstallationChargeChange()">
                                         <option value="custom" id="opt_inst_custom">
                                             + Add Custom Amount
                                         </option>
                                         <option value="inst_15k" id="opt_inst_15k">
                                             Scissor Lifts / Dock Leveler (₹15,000.00)
                                         </option>
                                         <option value="inst_30k" id="opt_inst_30k">
                                             Goods Lifts (₹30,000.00)
                                         </option>
                                         <option value="none" id="opt_inst_none" selected>
                                             None (₹0.00)
                                         </option>
                                     </select>

                                     <div id="custom_inst_wrapper" class="mt-2" style="display: none;">
                                         <input type="number" min="0" step="0.01" class="form-control" id="custom_installation_charge" name="custom_installation_charge" placeholder="Enter custom installation charge (₹)" value="">
                                     </div>

                                     <input type="hidden" id="default_installation_charge_input" name="default_installation_charge" value="0.00">
                                     <input type="hidden" id="installation_charge_final" name="installation_charge" value="0.00">

                                     <small class="text-muted d-block mt-1" id="installation_default_display">
                                         Installation Charge: None (₹0.00)
                                     </small>
                                 </div>

                                 <div class="form-group mt-3">
                                     <div class="d-flex justify-content-between align-items-center mb-1">
                                         <label class="mb-0">Freight Charge <span style="color:red">*</span></label>
                                         <div class="form-check form-check-inline mb-0">
                                             <input class="form-check-input" type="checkbox" id="include_freight_in_basic" name="include_freight_in_basic" value="1" {{ old('include_freight_in_basic') ? 'checked' : '' }}>
                                             <label class="form-check-label small text-muted" for="include_freight_in_basic">Include in Basic Total</label>
                                         </div>
                                     </div>
                                     <select class="form-select" id="freight_charge_type" name="freight_charge_type" onchange="handleFreightChargeChange()">
                                         <option value="custom">
                                             + Add Custom Amount
                                         </option>
                                         <option value="actuals" id="opt_freight_actuals" selected>
                                             Extra at Actuals
                                         </option>
                                     </select>

                                     <div id="custom_freight_wrapper" class="mt-2" style="display: none;">
                                         <input type="number" min="0" step="0.01" class="form-control" id="custom_freight_charge" name="custom_freight_charge" placeholder="Enter custom freight charge (₹)" value="">
                                     </div>

                                     <input type="hidden" id="default_freight_charge_input" name="default_freight_charge" value="0.00">
                                     <input type="hidden" id="freight_charge_final" name="freight_charge" value="0.00">

                                     <small class="text-muted d-block mt-1" id="freight_default_display">
                                         Default used for this quotation: Extra at Actuals
                                     </small>
                                 </div>
                                {{-- <div class="form-group mt-3">
                                    <input type="checkbox"> <label>Budgetary</label>
                                </div> --}}
                                {{-- <div class="form-group mt-3">
                                    <label>ORC</label>
                                    <select class="form-select quotation-master-select" name="agent" data-placeholder="Search or Select ORC">
                                        <option value=""> -- Choose -- </option>
                                        @foreach (App\Models\QuotationOrcOption::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                        <option value="new">+ Add Custom ORC</option>
                                    </select>
                                    <input type="text" class="form-control mt-2 quotation-master-new-input" name="new_agent"
                                        style="display: none;" placeholder="Enter custom ORC option">
                                </div>
                                <div class="form-group mt-3">
                                    <label>ORC Remarks <span style="color:red">*</span></label>
                                    <textarea class="form-control" name="remarks" required></textarea>
                                </div> --}}
                                <div class="form-group mt-3">
    <label>ORC</label>
    <select class="form-select quotation-master-select" name="agent" id="agent">
        <option value=""> -- Choose -- </option>
        <option value="new">+ Add Custom ORC</option>
        @foreach (App\Models\QuotationOrcOption::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $option)
            <option value="{{ $option->id }}">{{ $option->name }}</option>
        @endforeach
    </select>

    <input type="text"
           class="form-control mt-2 quotation-master-new-input"
           name="new_agent"
           style="display:none;"
           placeholder="Enter custom ORC option">
</div>

<div class="form-group mt-3">
    <label>
        ORC Remarks
        <span style="color:red;" id="remarksRequired" class="d-none">*</span>
    </label>

    <textarea class="form-control" name="remarks" id="remarks"></textarea>
</div>
                                <div class="form-group mt-3">
                                    <label>Quotation Status</label>
                                    <select class="form-select quotation-master-select" name="quto_status" id="quto_status" data-placeholder="Search or Select Quotation Status">
                                        <option value="">-- Choose --</option>
                                        <option value="new">+ Add Custom Quotation Status</option>
                                        @foreach (App\Models\QuotationStatus::orderBy('name')->get() as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control mt-2 quotation-master-new-input" name="new_quto_status"
                                        style="display: none;" placeholder="Enter custom quotation status">
                                </div>
                                <div class="form-group mt-3 d-none" id="quto_status_remarks_wrapper">
                                    <label>Status Remarks <span style="color:red">*</span></label>
                                    <textarea class="form-control" name="quto_status_remarks" id="quto_status_remarks"></textarea>
                                </div>
                                {{-- <div class="form-group mt-3">
                                    <label>Amount</label>
                                    <input type="number" class="form-control" name="ammt">
                                </div> --}}
                            </div>
                        </div>
                            <div class="text-start mt-3">
    <button type="button" class="btn btn-secondary" onclick="goToQuotation()">
        ← Back to Quotation
    </button>
</div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-warning px-5">Save Quotation</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
<script>
function goToTerms() {
    var tab = new bootstrap.Tab(document.querySelector('#profile-tab'));
    tab.show();
}

function goToQuotation() {
    var tab = new bootstrap.Tab(document.querySelector('#home-tab'));
    tab.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-quotation-form');
    if (!form) return;

    function isEmptyField(field) {
        if (field.disabled || !field.required) return false;
        if (field.type === 'checkbox' || field.type === 'radio') return !field.checked;
        return String(field.value || '').trim() === '';
    }

    function errorMessageFor(field) {
        const tableCell = field.closest('td');
        const tableHeader = tableCell
            ? tableCell.closest('table')?.querySelector(`th:nth-child(${tableCell.cellIndex + 1})`)?.textContent
            : '';
        const label = field.closest('.form-group')?.querySelector('label')?.textContent
            || tableHeader
            || 'This field';

        return `${label.replace('*', '').trim()} is required.`;
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const select2Container = field.nextElementSibling?.classList?.contains('select2')
            ? field.nextElementSibling
            : null;
        if (select2Container) select2Container.classList.remove('is-invalid');
        const wrapper = field.closest('.form-group') || field.closest('td') || field.parentElement;
        wrapper?.querySelector('.quotation-validation-error')?.remove();
    }

    function showFieldError(field) {
        clearFieldError(field);
        field.classList.add('is-invalid');
        const select2Container = field.nextElementSibling?.classList?.contains('select2')
            ? field.nextElementSibling
            : null;
        if (select2Container) select2Container.classList.add('is-invalid');
        const wrapper = field.closest('.form-group') || field.closest('td') || field.parentElement;
        const error = document.createElement('div');
        error.className = 'invalid-feedback d-block quotation-validation-error';
        error.textContent = errorMessageFor(field);
        wrapper?.appendChild(error);
    }

    function openTabFor(field) {
        const pane = field.closest('.tab-pane');
        if (!pane || pane.classList.contains('active')) return;
        const tabButton = document.querySelector(`[data-bs-target="#${pane.id}"]`);
        if (tabButton) bootstrap.Tab.getOrCreateInstance(tabButton).show();
    }

    form.addEventListener('submit', function (event) {
        // Give each checked accessory a name matching its row's CURRENT position,
        // so it lines up with product_id[]/rate[]/etc on the server side even if
        // rows were added or removed after the checkboxes were first rendered.
        document.querySelectorAll('#productRows .productRow').forEach(function (row, index) {
            row.querySelectorAll('.accessory-checkbox').forEach(function (cb) {
                cb.name = 'accessories[' + index + '][]';
            });
            row.querySelectorAll('.accessory-qty').forEach(function (qtyInput) {
                let accId = qtyInput.dataset.accId;
                if (accId) {
                    qtyInput.name = 'accessory_quantity[' + index + '][' + accId + ']';
                }
            });
        });

        const fields = Array.from(form.querySelectorAll('[required]'));
        const invalidFields = fields.filter(isEmptyField);
        fields.forEach(clearFieldError);

        if (!invalidFields.length) return;

        event.preventDefault();
        invalidFields.forEach(showFieldError);

        const firstInvalid = invalidFields[0];
        openTabFor(firstInvalid);

        setTimeout(function () {
            if ($(firstInvalid).hasClass('select2-hidden-accessible')) {
                $(firstInvalid).select2('open');
            } else {
                firstInvalid.focus();
            }
        }, 150);
    });

    form.addEventListener('input', (e) => clearFieldError(e.target));
    form.addEventListener('change', (e) => clearFieldError(e.target));
});

function toggleQuotationStatusRemarks() {
    const statusField = document.getElementById('quto_status');
    const remarksWrapper = document.getElementById('quto_status_remarks_wrapper');
    const remarksField = document.getElementById('quto_status_remarks');
    
    if (!statusField || !remarksWrapper || !remarksField) return;
    const needsRemarks = ['3', '4', '5'].includes(statusField.value);
    remarksWrapper.classList.toggle('d-none', !needsRemarks);
    remarksField.required = needsRemarks;
}

document.addEventListener('DOMContentLoaded', function () {
    const statusField = document.getElementById('quto_status');
    if (statusField) {
        statusField.addEventListener('change', toggleQuotationStatusRemarks);
        toggleQuotationStatusRemarks();
    }
});

function handleDovChange() {
    let select = document.getElementById('dovSelect');
    let input = document.getElementById('dovNew');
    if (!select || !input) return;

    if (select.value === 'new') {
        input.style.display = 'block';
        input.required = true;
    } else {
        input.style.display = 'none';
        input.required = false;
    }
}
document.addEventListener('DOMContentLoaded', handleDovChange);

$(document).ready(function () {
    $('.quotation-master-select').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                placeholder: $(this).data('placeholder') || 'Search or Select',
                allowClear: true,
                width: '100%'
            });
        }
    });

    function toggleQuotationMasterInput(select) {
        const wrapper = $(select).closest('.form-group');
        const input = wrapper.find('input[name="freight_category"], input[name="freight_sub_category"], .quotation-master-new-input').first();
        const isNew = $(select).val() === 'new';
        input.toggle(isNew).prop('required', isNew);
        if (!isNew) input.val('');
    }

    $('.quotation-master-select').each(function () { toggleQuotationMasterInput(this); });
    $(document).on('change', '.quotation-master-select', function () { toggleQuotationMasterInput(this); });

    function toggleRemarksRequired() {
        let selectedText = $('#agent option:selected').text().trim();
        if (selectedText === 'Yes') {
            $('#remarks').prop('required', true);
            $('#remarksRequired').removeClass('d-none');
        } else {
            $('#remarks').prop('required', false);
            $('#remarksRequired').addClass('d-none');
        }
    }
    $('#agent').change(toggleRemarksRequired);

    window.copyAddress = function() {
        let billingAddr = $('#billingAddress').val();
        let billingCountry = $('#country').val();
        let billingState = $('#state_list').val();
        let billingCity = $('#city_list').val();
        let billingPostal = $('#postalcode').val();
        let billingGst = $('#billgs').val();

        if (billingAddr) $('#shippingAddress').val(billingAddr);
        if (billingCountry) $('#shipcountry').val(billingCountry);
        if (billingPostal) $('#shippostalcode').val(billingPostal);
        if (billingGst) $('#gstnum').val(billingGst);

        if (billingCity) {
            $('#shicity_list').attr('data-selected', billingCity);
        }

        if (billingState) {
            $('#state_listsh').attr('data-selected', billingState);
            $('#state_listsh').val(billingState).trigger('change');
        }

        setTimeout(function() {
            if (billingCity) $('#shicity_list').val(billingCity);
        }, 200);
        setTimeout(function() {
            if (billingCity) $('#shicity_list').val(billingCity);
        }, 600);
    };

    window.toggleAddress = function() {
        let checkbox = document.getElementById('sameAsBilling');
        if (checkbox && checkbox.checked) {
            copyAddress();
        }
    };

    let initialCity = $('#shicity_list').attr('data-selected');
    if (initialCity) {
        $('#shicity_list').val(initialCity);
    }

    $(document).ajaxComplete(function (event, xhr, settings) {
        if (settings.url && settings.url.indexOf('shifandfreports') !== -1) {
            let selCity = $('#shicity_list').attr('data-selected');
            if (selCity) {
                $('#shicity_list').val(selCity);
            }
        }
    });

    if ($('#sameAsBilling').is(':checked')) {
        copyAddress();
    }
});

// Main Calculations & Dynamic Row Handling Script
$(document).ready(function () {

    // 1. Single Row Calculation
    function calculateRow(tr) {
        let qtyVal = tr.find('.qty').val();
        let qty = parseFloat(qtyVal);

        if (isNaN(qty) || qty <= 0) {
            if (tr.find('.rate').val() !== '') {
                qty = 1;
                tr.find('.qty').val(1);
            } else {
                qty = 0;
            }
        }

        let rate = parseFloat(tr.find('.rate').val()) || 0;
        let grossTotal = qty * rate;

        let discPercentInput = tr.find('.requestdis-percent');
        let discAmtInput = tr.find('.requestdis-amount');

        let discPercent = parseFloat(discPercentInput.val()) || 0;
        let discAmount = parseFloat(discAmtInput.val()) || 0;

        if (document.activeElement === discAmtInput[0]) {
            if (grossTotal > 0) {
                discPercent = (discAmount / grossTotal) * 100;
                discPercentInput.val(discPercent.toFixed(2));
            }
        } else {
            discAmount = (grossTotal * discPercent) / 100;
            discAmtInput.val(discAmount.toFixed(2));
        }

        let lbt = grossTotal - discAmount;
        if (lbt < 0) lbt = 0;
        tr.find('.lbt').val(lbt.toFixed(2));

        let taxPercent = parseFloat(tr.find('.tax').val()) || 0;
        let taxAmt = (lbt * taxPercent) / 100;
        tr.find('.taxamt').val(taxAmt.toFixed(2));

        let total = lbt + taxAmt;
        tr.find('.total').val(total.toFixed(2));

        calculateGrandTotals();
    }

    // 2. Grand Totals Calculation
    function calculateGrandTotals() {
        let subtotal = 0;
        let taxtotal = 0;
        let grandtotal = 0;

        $('tr').has('.rate').each(function () {
            subtotal += parseFloat($(this).find('.lbt').val()) || 0;
            taxtotal += parseFloat($(this).find('.taxamt').val()) || 0;
            grandtotal += parseFloat($(this).find('.total').val()) || 0;
        });

        $('#subtotal, .subtotal').val(subtotal.toFixed(2));
        $('#taxtotal, .taxtotal').val(taxtotal.toFixed(2));
        $('#grandtotal, .grandtotal').val(grandtotal.toFixed(2));

        if (typeof window.handleAdditionalWarrantyChange === 'function') {
            window.handleAdditionalWarrantyChange();
        }
    }

    // Input changes trigger calculation
    $(document).on('input change', '.qty, .rate, .requestdis-percent, .requestdis-amount, .tax', function () {
        let tr = $(this).closest('tr');
        calculateRow(tr);
    });

    // 3. Fetch Accessories with UNIQUE ID per Row
    function fetchAccessoriesForRow(tr, productId) {
        let accWrapper = tr.find('.accessories-wrapper');
        let accList = tr.find('.accessories-list');

        let existingXhr = tr.data('accessoriesXhr');
        if (existingXhr && existingXhr.readyState !== 4) {
            existingXhr.abort();
        }

        accList.html('');
        accWrapper.hide();

        if (!productId) return;

        // Generate a unique row key using row index or random timestamp
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

                    uniqueAccessories.forEach(function (acc) {
                        // UNIQUE ID incorporating rowIndex to avoid collision across rows
                        let uniqueInputId = `acc_${rowIndex}_${productId}_${acc.id}`;

                        let html = `
                            <div class="accessory-item d-flex align-items-center justify-content-between gap-1 py-1 border-bottom border-light">
                                <div class="form-check m-0 d-flex align-items-center">
                                    <input class="form-check-input accessory-checkbox me-1"
                                           type="checkbox"
                                           name="accessories[${tr.index()}][]"
                                           value="${acc.id}"
                                           data-price="${acc.price}"
                                           id="${uniqueInputId}">
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
                                           value="1"
                                           min="1"
                                           style="width: 48px; height: 24px; font-size: 11px;">
                                </div>
                            </div>
                        `;
                        accList.append(html);
                    });

                    let summaryBadge = tr.find('.accessories-summary-badge');
                    updateAccessoryBadge(tr);
                    accWrapper.slideDown(150);
                    summaryBadge.hide();
                }
            },
            error: function (jqXHR, textStatus) {
                if (textStatus !== 'abort') {
                    accList.html('');
                    accWrapper.hide();
                    tr.find('.accessories-summary-badge').hide();
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
                `<i class="bi bi-check-circle-fill text-success me-1"></i><strong>${checkedCount} Accessories</strong> (+₹${accTotal.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})})`
            );
        } else {
            summaryBadge.find('.summary-text').html(`<span class="text-muted"><i class="bi bi-plus-circle me-1"></i>Add Accessories</span>`);
        }
    }

    // Click OK / Close Button
    $(document).on('click', '.btn-apply-accessories, .btn-close-accessories', function () {
        let tr = $(this).closest('tr');
        let accWrapper = tr.find('.accessories-wrapper');
        let summaryBadge = tr.find('.accessories-summary-badge');

        recalcRateFromAccessories(tr);
        calculateRow(tr);
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

    // Product Selection Event
    $(document).on('change', '.productSelect', function () {
        let tr = $(this).closest('tr');
        let productId = $(this).val();
        let selectedOption = $(this).find('option:selected');

        let baseRate = parseFloat(selectedOption.data('rate')) || 0;
        tr.find('.base-rate').val(baseRate);
        tr.find('.rate').val(baseRate.toFixed(2));

        if (!tr.find('.qty').val() || tr.find('.qty').val() == '0') {
            tr.find('.qty').val(1);
        }

        fetchAccessoriesForRow(tr, productId);
        calculateRow(tr);
    });

    // Accessory Checkbox & Quantity Change Events
    $(document).on('change', '.accessory-checkbox', function () {
        let tr = $(this).closest('tr');
        let qtyInput = $(this).closest('.accessory-item').find('.accessory-qty');
        if ($(this).is(':checked')) {
            let val = parseInt(qtyInput.val()) || 0;
            if (val <= 0) {
                qtyInput.val(1);
            }
        }
        recalcRateFromAccessories(tr);
        calculateRow(tr);
        updateAccessoryBadge(tr);
    });

    $(document).on('change input', '.accessory-qty', function () {
        let val = parseInt($(this).val()) || 0;
        let checkbox = $(this).closest('.accessory-item').find('.accessory-checkbox');

        if (val > 0) {
            checkbox.prop('checked', true);
        } else {
            checkbox.prop('checked', false);
        }

        let tr = $(this).closest('tr');
        recalcRateFromAccessories(tr);
        calculateRow(tr);
        updateAccessoryBadge(tr);
    });

    // Installation Charge JS logic
    window.recalculateProductInstallationDefault = function () {
        let hasGoodsLift = false;
        let has15kKeyword = false;

        $('#productRows .productSelect').each(function () {
            let selectedOptionText = $(this).find('option:selected').text() || '';
            let textLower = selectedOptionText.toLowerCase();

            if (textLower.indexOf('goods lift') !== -1 || textLower.indexOf('goods lifts') !== -1) {
                hasGoodsLift = true;
            }
            if (textLower.indexOf('dock leveler') !== -1 || textLower.indexOf('dock levelers') !== -1 ||
                textLower.indexOf('scissor lift') !== -1 || textLower.indexOf('scissor lifts') !== -1) {
                has15kKeyword = true;
            }
        });

        let instTypeSelect = $('#installation_charge_type');
        if (!instTypeSelect.data('userManuallySet')) {
            if (hasGoodsLift) {
                instTypeSelect.val('inst_30k');
            } else if (has15kKeyword) {
                instTypeSelect.val('inst_15k');
            } else {
                instTypeSelect.val('none');
            }
        }

        handleInstallationChargeChange();
    };

    $(document).on('change', '#installation_charge_type', function () {
        $(this).data('userManuallySet', true);
    });

    window.handleInstallationChargeChange = function () {
        let type = $('#installation_charge_type').val();

        if (type === 'inst_30k') {
            $('#custom_inst_wrapper').hide();
            $('#default_installation_charge_input').val('30000.00');
            $('#installation_charge_final').val('30000.00');
            $('#installation_default_display').text('Installation Charge: Goods Lifts (₹30,000.00)');
        } else if (type === 'inst_15k') {
            $('#custom_inst_wrapper').hide();
            $('#default_installation_charge_input').val('15000.00');
            $('#installation_charge_final').val('15000.00');
            $('#installation_default_display').text('Installation Charge: Scissor Lifts / Dock Leveler (₹15,000.00)');
        } else if (type === 'none') {
            $('#custom_inst_wrapper').hide();
            $('#default_installation_charge_input').val('0.00');
            $('#installation_charge_final').val('0.00');
            $('#installation_default_display').text('Installation Charge: None (₹0.00)');
        } else if (type === 'custom') {
            $('#custom_inst_wrapper').show();
            let customVal = parseFloat($('#custom_installation_charge').val()) || 0;
            $('#installation_charge_final').val(customVal.toFixed(2));
            $('#installation_default_display').text('Custom installation charge: ₹' + customVal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        }
    };

    $(document).on('input change', '#custom_installation_charge', function () {
        if ($('#installation_charge_type').val() === 'custom') {
            let customVal = parseFloat($(this).val()) || 0;
            $('#installation_charge_final').val(customVal.toFixed(2));
            $('#installation_default_display').text('Custom installation charge: ₹' + customVal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        }
    });

    window.handleFreightChargeChange = function () {
        let type = $('#freight_charge_type').val();

        if (type === 'custom') {
            $('#custom_freight_wrapper').show();
            let customInput = $('#custom_freight_charge').val().trim();
            if (customInput === '' || isNaN(parseFloat(customInput))) {
                $('#freight_charge_final').val('0.00');
                $('#freight_default_display').text('Default used for this quotation: Extra at Actuals');
            } else {
                let customVal = parseFloat(customInput) || 0;
                $('#freight_charge_final').val(customVal.toFixed(2));
                $('#freight_default_display').text('Custom freight charge: ₹' + customVal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
            }
        } else {
            $('#custom_freight_wrapper').hide();
            $('#freight_charge_final').val('0.00');
            $('#freight_default_display').text('Default used for this quotation: Extra at Actuals');
        }
    };

    $(document).on('input change', '#custom_freight_charge', function () {
        if ($('#freight_charge_type').val() === 'custom') {
            let customInput = $(this).val().trim();
            if (customInput === '' || isNaN(parseFloat(customInput))) {
                $('#freight_charge_final').val('0.00');
                $('#freight_default_display').text('Default used for this quotation: Extra at Actuals');
            } else {
                let customVal = parseFloat(customInput) || 0;
                $('#freight_charge_final').val(customVal.toFixed(2));
                $('#freight_default_display').text('Custom freight charge: ₹' + customVal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
            }
        }
    });

    $(document).on('change', '.productSelect', function () {
        recalculateProductInstallationDefault();
    });

    window.handleFreightCategoryChange = function () {
        let select = $('select[name="freight_category_id"]');
        let selectedText = select.find('option:selected').text().trim().toLowerCase();

        let isMercuriis = selectedText.indexOf('mercuriis') !== -1;

        if (isMercuriis) {
            $('#freight_sub_cat_star').removeClass('d-none');
            $('#freight_sub_category_id').attr('required', true);
        } else {
            $('#freight_sub_cat_star').addClass('d-none');
            $('#freight_sub_category_id').removeAttr('required');
        }
    };

    $(document).on('change', 'select[name="freight_category_id"]', function () {
        handleFreightCategoryChange();
    });

    window.handleAdditionalWarrantyChange = function () {
        let select = $('#additional_warranty_months_select');
        let val = select.val();
        let opt = select.find('option:selected');
        let defaultPct = parseFloat(opt.data('pct')) || 0;

        let basicTotal = 0;
        $('tr').has('.rate').each(function () {
            basicTotal += parseFloat($(this).find('.lbt').val()) || 0;
        });

        if (val === '0') {
            $('#warranty_dynamic_inputs_wrapper').hide();
            $('#additional_warranty_percentage_input').val('0.00').data('userModified', false);
            $('#additional_warranty_charge_final').val('0.00');
            $('#warranty_charge_display').text('Additional Warranty: None (₹0.00)');
        } else if (val === 'custom') {
            $('#warranty_dynamic_inputs_wrapper').show();
            let currPct = parseFloat($('#additional_warranty_percentage_input').val()) || 0;
            let currCharge = parseFloat($('#additional_warranty_charge_final').val()) || 0;
            if (currPct > 0 && currCharge === 0) {
                currCharge = (basicTotal * currPct) / 100;
                $('#additional_warranty_charge_final').val(currCharge.toFixed(2));
            }
            $('#warranty_charge_display').html('Custom Additional Warranty: <strong>₹' + currCharge.toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong> (' + currPct.toFixed(2) + '% of ₹' + basicTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ')');
        } else {
            $('#warranty_dynamic_inputs_wrapper').show();
            let months = parseInt(val) || 0;
            let isUserModified = $('#additional_warranty_percentage_input').data('userModified');
            let effectivePct = isUserModified ? (parseFloat($('#additional_warranty_percentage_input').val()) || defaultPct) : defaultPct;

            let charge = (basicTotal * effectivePct) / 100;
            $('#additional_warranty_percentage_input').val(effectivePct.toFixed(2));
            $('#additional_warranty_charge_final').val(charge.toFixed(2));
            $('#warranty_charge_display').html('Additional Warranty: <strong>' + months + ' Months</strong> (₹' + charge.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' &bull; ' + effectivePct.toFixed(2) + '% of ₹' + basicTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ')');
        }
    };

    $(document).on('change', '#additional_warranty_months_select', function () {
        $('#additional_warranty_percentage_input').data('userModified', false);
        handleAdditionalWarrantyChange();
    });

    $(document).on('input change', '#additional_warranty_percentage_input', function () {
        let pct = parseFloat($(this).val()) || 0;
        $(this).data('userModified', true);

        let basicTotal = 0;
        $('tr').has('.rate').each(function () {
            basicTotal += parseFloat($(this).find('.lbt').val()) || 0;
        });

        let charge = (basicTotal * pct) / 100;
        $('#additional_warranty_charge_final').val(charge.toFixed(2));

        let months = $('#additional_warranty_months_select').val();
        let monthLabel = (months && months !== '0' && months !== 'custom') ? months + ' Months' : 'Custom';
        $('#warranty_charge_display').html('Additional Warranty: <strong>' + monthLabel + '</strong> (₹' + charge.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' &bull; ' + pct.toFixed(2) + '% of ₹' + basicTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ')');
    });

    $(document).on('input change', '#additional_warranty_charge_final', function () {
        let charge = parseFloat($(this).val()) || 0;

        let basicTotal = 0;
        $('tr').has('.rate').each(function () {
            basicTotal += parseFloat($(this).find('.lbt').val()) || 0;
        });

        let pct = basicTotal > 0 ? ((charge / basicTotal) * 100) : 0;
        $('#additional_warranty_percentage_input').val(pct.toFixed(2)).data('userModified', true);

        let months = $('#additional_warranty_months_select').val();
        let monthLabel = (months && months !== '0' && months !== 'custom') ? months + ' Months' : 'Custom';
        $('#warranty_charge_display').html('Additional Warranty: <strong>' + monthLabel + '</strong> (₹' + charge.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' &bull; ' + pct.toFixed(2) + '% of ₹' + basicTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ')');
    });

    // Run initial calculation for existing rows
    $('tr').has('.rate').each(function () {
        calculateRow($(this));
    });
    recalculateProductInstallationDefault();
    handleFreightCategoryChange();
    handleAdditionalWarrantyChange();
});
</script>
@endsection