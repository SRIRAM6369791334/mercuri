<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $firstPo->po_no }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            border-bottom: 2px solid #1b5583;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .logo-img {
            max-width: 110px;
            max-height: 48px;
        }
        .company-name {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1b5583;
            margin-top: 3px;
        }
        .po-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1b5583;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .po-subtitle {
            font-size: 8pt;
            color: #64748b;
            text-align: right;
            font-weight: bold;
        }
        .meta-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 8pt;
            border: 1px solid #e2e8f0;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            background: #f1f5f9;
            width: 20%;
        }
        .meta-value {
            font-weight: bold;
            color: #0f172a;
            width: 30%;
        }
        .address-table {
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
        }
        .address-table td {
            width: 33.33%;
            border: 1px solid #cbd5e1;
            padding: 6px;
            vertical-align: top;
            font-size: 8pt;
            background: #ffffff;
        }
        .address-title {
            font-weight: bold;
            color: #1b5583;
            font-size: 8pt;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .section-bar {
            background: #1b5583;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 4px 6px;
            text-transform: uppercase;
            margin-top: 6px;
            margin-bottom: 0;
        }
        .items-table {
            border: 1px solid #cbd5e1;
            margin-bottom: 6px;
        }
        .items-table th {
            background: #1b5583;
            color: #ffffff;
            font-weight: bold;
            font-size: 8pt;
            padding: 5px 4px;
            border: 1px solid #0f395c;
            text-align: center;
        }
        .items-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-size: 8pt;
            vertical-align: top;
        }
        .totals-table {
            width: 50%;
            margin-left: auto;
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
        }
        .totals-table td {
            padding: 4px 6px;
            font-size: 8pt;
            border: 1px solid #cbd5e1;
        }
        .totals-table .t-label {
            background: #f8fafc;
            font-weight: bold;
            color: #334155;
            width: 60%;
        }
        .totals-table .t-val {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        .grand-row td {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            font-size: 9pt;
            border-top: 2px solid #0284c7;
        }
        .words-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 6px;
            font-size: 8pt;
            margin-bottom: 8px;
        }
        .commercial-box {
            border: 1px solid #cbd5e1;
            background: #fafafa;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        .commercial-title {
            font-weight: bold;
            color: #1b5583;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .special-instructions-card {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            padding: 8px 10px;
            margin-bottom: 8px;
        }
        .special-instructions-title {
            color: #b45309;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .special-instructions-content {
            color: #78350f;
            font-weight: bold;
            font-size: 8pt;
        }
        .sign-table {
            margin-top: 10px;
        }
        .sign-table td {
            width: 33.33%;
            border: 1px dashed #94a3b8;
            padding: 8px;
            text-align: center;
            background: #fafafa;
        }
        .sign-title {
            font-size: 7pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 26px;
        }
        .sign-name {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
        }
        .footer-text {
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            margin-top: 10px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>

@php
    $poDate = $firstPo->created_at ? \Carbon\Carbon::parse($firstPo->created_at)->format('d-m-Y') : date('d-m-Y');
    $poNumber = $firstPo->po_no;

    // Totals accumulation
    $totalQty = 0;
    $basicTotal = 0;
    foreach ($poDetails as $item) {
        $totalQty += (int) $item->product_qty;
        $basicTotal += (float) ($item->product_total_price ?: ($item->product_qty * $item->unit_price));
    }

    $discountPct = (float)($firstPo->discount_percentage ?? 0);
    $discountAmt = (float)($firstPo->discount_amount ?: (($basicTotal * $discountPct) / 100));
    $netTaxable = $basicTotal - $discountAmt;
    $freightCharges = (float)($firstPo->freight_charges ?? 0);
    $unloadingCharges = (float)($firstPo->unloading_charges ?? 0);

    $gstTotal = 0;
    foreach ($poDetails as $item) {
        $lineBasic = (float) ($item->product_total_price ?: ($item->product_qty * $item->unit_price));
        $lineTaxable = $lineBasic - (($lineBasic * $discountPct) / 100);
        $lineGstRate = (float) ($item->productDetails?->gst ?? 18);
        $gstTotal += ($lineTaxable / 100) * $lineGstRate;
    }

    $grandTotal = $netTaxable + $freightCharges + $unloadingCharges + $gstTotal;

    $companyName = "MERCURIIS STORAGE & EQUIPMENTS PVT LTD";
    $companyBillingAddress = "89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu, India.\nGSTIN: 33AATCM0824C1ZQ | Phone: +91 99620 55076 | Email: info@mercuriis.com";

    // Shipping address determination
    $shippingAddress = $firstPo->shipping_address ?: $companyBillingAddress;
    $shippingGst = $firstPo->shipping_gst ?: "33AATCM0824C1ZQ";

    $hideBrand = (bool)($firstPo->hide_brand_name);
@endphp

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <img src="{{ public_path('assets/images/logom.jpeg') }}" class="logo-img" alt="Mercuriis Logo">
                <div class="company-name">MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD</div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <h1 class="po-title">PURCHASE ORDER</h1>
                <div class="po-subtitle">Official Procurement Order &amp; Supply Contract</div>
            </td>
        </tr>
    </table>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">P.O. Number:</td>
            <td class="meta-value" style="color: #1b5583;">{{ $poNumber }}</td>
            <td class="meta-label">P.O. Date:</td>
            <td class="meta-value">{{ $poDate }}</td>
        </tr>
        <tr>
            <td class="meta-label">Vendor Code:</td>
            <td class="meta-value">{{ $vendor?->vendor_id ?? 'VEN-' . str_pad($vendor?->id ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="meta-label">Committed Lead Time:</td>
            <td class="meta-value" style="color: #c2410c;">{{ $firstPo->lead_time ?: '4 Weeks' }}</td>
        </tr>
    </table>

    <!-- Addresses Block -->
    <table class="address-table">
        <tr>
            <!-- 1. VENDOR / SUPPLIER WITH BRANCH & GST -->
            <td>
                <div class="address-title">Supplier / Vendor Details</div>
                <strong>{{ $vendor?->company_name ?? 'N/A' }}</strong><br>
                @if (!empty($firstPo->supplier_billing_address))
                    {!! nl2br(e($firstPo->supplier_billing_address)) !!}<br>
                @elseif (!empty($vendor?->address))
                    {!! nl2br(e($vendor->address)) !!}<br>
                @endif
                <strong>Supplier GSTIN:</strong> {{ $firstPo->supplier_gst ?: ($vendor?->gst_number ?? '-') }}<br>
                @if (!empty($vendor?->contact_name))
                    <strong>Contact:</strong> {{ $vendor->contact_name }} {{ $vendor?->phone ? '(' . $vendor->phone . ')' : '' }}<br>
                @endif
                @if (!empty($vendor?->email))
                    <strong>Email:</strong> {{ $vendor->email }}
                @endif
            </td>

            <!-- 2. BILLING ADDRESS (PURCHASER) -->
            <td>
                <div class="address-title">Purchaser / Billing Address</div>
                <strong>MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD</strong><br>
                89, Balaji Nagar, Athanancheri,<br>
                Padappai, Chennai - 601301,<br>
                Tamil Nadu, India.<br>
                <strong>GSTIN:</strong> 33AATCM0824C1ZQ<br>
                <strong>Email:</strong> info@mercuriis.com
            </td>

            <!-- 3. SHIP-TO / DELIVERY DESTINATION -->
            <td style="background: #fdfefe;">
                <div class="address-title" style="color: #0369a1;">Ship To / Delivery Destination</div>
                <div style="font-weight: bold; color: #1b5583; margin-bottom: 2px;">
                    MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD
                </div>
                <div>
                    {!! nl2br(e($shippingAddress)) !!}
                </div>
                <div style="margin-top: 3px;">
                    <strong>Destination GSTIN:</strong> {{ $shippingGst }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Scope of Supply Table -->
    <div class="section-bar">Scope of Supply &amp; Bill of Materials</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 14%;">Part No / SKU</th>
                <th style="width: 44%; text-align: left;">Description of Goods &amp; Specifications</th>
                <th style="width: 8%;">HSN</th>
                <th style="width: 6%;">Qty</th>
                <th style="width: 11%;">Unit Rate (₹)</th>
                <th style="width: 13%;">Basic Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($poDetails as $index => $item)
                @php
                    $prod = $item->productDetails;
                    $itemQty = (int) $item->product_qty;
                    $unitPrice = (float) $item->unit_price;
                    $lineBasic = (float) ($item->product_total_price ?: ($itemQty * $unitPrice));
                    
                    // Filter specifications if hideBrand is true
                    $specs = ($prod && $prod->specifications) ? $prod->specifications : collect();
                    if ($hideBrand) {
                        $specs = $specs->filter(function($s) {
                            return !in_array(strtoupper(trim($s->description)), ['MAKE', 'BRAND', 'MANUFACTURER']);
                        });
                    }
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-weight: bold; color: #1b5583;">
                        {{ $prod?->partno ?: ($prod?->sku ?: '-') }}
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ $prod?->productname ?? 'N/A' }}</strong>
                        @if (!empty($item->product_description))
                            <div style="font-size: 7.5pt; color: #475569; margin-top: 2px;">{!! nl2br(e($item->product_description)) !!}</div>
                        @elseif (!empty($prod?->productdes))
                            <div style="font-size: 7.5pt; color: #475569; margin-top: 2px;">{{ $prod->productdes }}</div>
                        @endif
                        @if ($specs->isNotEmpty())
                            <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">
                                <em>Specs:</em>
                                @foreach ($specs as $spec)
                                    <span>{{ $spec->description ? $spec->description . ': ' : '' }}{{ $spec->specification }}{{ $spec->unit ? ' ' . $spec->unit : '' }}</span>{{ !$loop->last ? ' | ' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $prod?->hsn ?: '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $itemQty }}</td>
                    <td style="text-align: right;">{{ number_format($unitPrice, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($lineBasic, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div class="words-box">
                    <strong>Amount in Words:</strong><br>
                    <span style="font-weight: bold; color: #1b5583;">
                        {{ \App\Helpers\IndianNumberHelper::numberToIndianWords(round($grandTotal)) ?? 'Rupees ' . number_format($grandTotal, 2) }}
                    </span>
                    <div style="margin-top: 4px; font-size: 7.5pt; color: #64748b;">
                        * Total Quantity: <strong>{{ $totalQty }} Units</strong> across <strong>{{ count($poDetails) }} Line Item(s)</strong>.
                    </div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="totals-table" style="width: 100%;">
                    <tr>
                        <td class="t-label">Gross Subtotal (Basic):</td>
                        <td class="t-val">₹{{ number_format($basicTotal, 2) }}</td>
                    </tr>
                    @if ($discountPct > 0 || $discountAmt > 0)
                        <tr>
                            <td class="t-label" style="color: #b91c1c;">Less: Discount ({{ $discountPct }}%):</td>
                            <td class="t-val" style="color: #b91c1c;">-₹{{ number_format($discountAmt, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="t-label">Net Taxable Value:</td>
                            <td class="t-val">₹{{ number_format($netTaxable, 2) }}</td>
                        </tr>
                    @endif
                    @if ($freightCharges > 0)
                        <tr>
                            <td class="t-label">Freight / Transportation Charges:</td>
                            <td class="t-val">₹{{ number_format($freightCharges, 2) }}</td>
                        </tr>
                    @endif
                    @if ($unloadingCharges > 0)
                        <tr>
                            <td class="t-label">Unloading Charges:</td>
                            <td class="t-val">₹{{ number_format($unloadingCharges, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="t-label">Applicable Tax (GST):</td>
                        <td class="t-val">₹{{ number_format($gstTotal, 2) }}</td>
                    </tr>
                    <tr class="grand-row">
                        <td>GRAND TOTAL (INCL. GST):</td>
                        <td class="t-val">₹{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Commercial & Operational Terms -->
    <div class="commercial-box">
        <div class="commercial-title">Commercial Terms &amp; Execution Conditions</div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>• Delivery / Lead Time:</strong> {{ $firstPo->lead_time ?: '4 Weeks from PO confirmation' }}<br>
                    <strong>• Payment Terms:</strong> {{ $firstPo->payment_terms ?: '50% Advance & 50% Against Proforma' }}
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <strong>• Warranty:</strong> {{ $firstPo->warranty_terms ?: '12 Months Standard Manufacturer Warranty' }}<br>
                    <strong>• Installation Scope:</strong> {{ $firstPo->installation_scope ?: 'In Supplier Scope' }}
                </td>
            </tr>
        </table>
        @if (!empty($firstPo->termsConditionDetails?->content))
            <div style="font-size: 7.5pt; color: #475569; border-top: 1px solid #e2e8f0; padding-top: 3px; margin-top: 3px;">
                <strong>Standard Terms:</strong> {{ $firstPo->termsConditionDetails->content }}
            </div>
        @endif
    </div>

    <!-- Highlighted Special Instructions (Requirement 4.1) -->
    <div class="special-instructions-card">
        <div class="special-instructions-title">
            MANDATORY SPECIAL INSTRUCTIONS &amp; DISPATCH REQUIREMENTS:
        </div>
        <div class="special-instructions-content">
            @if (!empty($firstPo->special_instructions))
                {!! nl2br(e($firstPo->special_instructions)) !!}
            @else
                1. Purchase Order No. (<strong>{{ $poNumber }}</strong>) and Date must be clearly mentioned on all Tax Invoices, Delivery Challans, and Test Certificates.<br>
                2. Goods must be securely packed to prevent any transit damage. Supplier must acknowledge receipt of this PO.
            @endif
        </div>
    </div>

    <!-- Signatory Block -->
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-title">Prepared By (Procurement)</div>
                <div class="sign-name">Supply Chain Executive</div>
            </td>
            <td>
                <div class="sign-title">Verified By (Operations)</div>
                <div class="sign-name">Commercial Manager</div>
            </td>
            <td>
                <div class="sign-title">Authorized By (Management)</div>
                <div class="sign-name">Mercuriis Authorized Signatory</div>
            </td>
        </tr>
    </table>

    <div class="footer-text">
        <strong>Mercuriis Storage &amp; Equipments Pvt Ltd</strong> | 89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu, India. | GSTIN: 33AATCM0824C1ZQ
    </div>

</body>
</html>
