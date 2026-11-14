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
    $poDateSource = $firstPo->po_date ?: $firstPo->created_at;
    $poDate = $poDateSource ? \Carbon\Carbon::parse($poDateSource)->format('d-m-Y') : date('d-m-Y');
    $poNumber = $firstPo->po_no;

    $totalQty = 0;
    $basicTotal = 0;
    $lineDiscountTotal = 0;
    $overallDiscountTotal = 0;
    $netTaxable = 0;
    $gstTotal = 0;
    foreach ($poDetails as $item) {
        $totalQty += (int) $item->product_qty;
        $lineBasic = (float) ($item->product_total_price ?: ($item->product_qty * $item->unit_price));
        $lineDiscountPct = (float) ($item->discount_percentage ?? 0);
        $lineDiscount = $item->discount_amount !== null
            ? (float) $item->discount_amount
            : round($lineBasic * $lineDiscountPct / 100, 2);
        $overallDiscountPct = (float) ($item->overall_discount_percentage ?? 0);
        $afterLineDiscount = max(0, $lineBasic - $lineDiscount);
        $overallDiscount = $item->overall_discount_amount !== null
            ? (float) $item->overall_discount_amount
            : round($afterLineDiscount * $overallDiscountPct / 100, 2);
        $lineTaxable = $item->taxable_amount !== null
            ? (float) $item->taxable_amount
            : ($item->total_amount !== null && $item->gst_amount !== null
                ? max(0, (float) $item->total_amount - (float) $item->gst_amount)
                : max(0, $afterLineDiscount - $overallDiscount));
        $lineGst = $item->gst_amount !== null
            ? (float) $item->gst_amount
            : round($lineTaxable * (float) ($item->gst_percentage ?? $item->productDetails?->gst ?? 18) / 100, 2);

        $basicTotal += $lineBasic;
        $lineDiscountTotal += $lineDiscount;
        $overallDiscountTotal += $overallDiscount;
        $netTaxable += $lineTaxable;
        $gstTotal += $lineGst;
    }

    $overallDiscountPct = (float) ($firstPo->overall_discount_percentage ?? 0);
    $freightCharges = (float)($firstPo->freight_charges ?? 0);
    $unloadingCharges = (float)($firstPo->unloading_charges ?? 0);
    $rawGrand = $netTaxable + $freightCharges + $unloadingCharges + $gstTotal;
    $roundDiff = $firstPo->roundoff !== null
        ? (float) $firstPo->roundoff
        : round(round($rawGrand) - $rawGrand, 2);
    $grandTotal = round($rawGrand + $roundDiff, 2);

    $branchChoice = $firstPo->mercuriis_branch ?? 'Chennai';
    $branchConfig = config('mercuriis_branches.branches.' . $branchChoice)
        ?? config('mercuriis_branches.branches.' . config('mercuriis_branches.default', 'Chennai'));
    $companyBillingAddress = $branchConfig['full_address'] ?? '';
    $branchGstin = $branchConfig['gstin'] ?? '';

    // Shipping address determination
    $shippingAddress = $firstPo->shipping_address ?: $companyBillingAddress;
    $shippingGst = $firstPo->shipping_gst ?: $branchGstin;

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
                {!! nl2br(e($companyBillingAddress)) !!}
            </td>

            <!-- 3. SHIP-TO / DELIVERY DESTINATION -->
            <td style="background: #fdfefe;">
                <div class="address-title" style="color: #0369a1;">Ship To / Delivery Destination</div>
                @if (($firstPo->shipping_type ?? 'warehouse') === 'warehouse')
                    <div style="font-weight: bold; color: #1b5583; margin-bottom: 2px;">
                        MERCURIIS WAREHOUSE ({{ strtoupper($branchChoice) }})
                    </div>
                @else
                    <div style="font-weight: bold; color: #0284c7; margin-bottom: 2px;">
                        DIRECT CUSTOMER SITE DELIVERY
                    </div>
                @endif
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
                        {{ $item->part_no ?: ($prod?->partno ?: ($prod?->sku ?: '-')) }}
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
                    <td style="text-align: center;">{{ $item->hsn_code ?: ($prod?->hsncode ?: ($prod?->hsn ?: '8428')) }}</td>
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
                    @if ($lineDiscountTotal > 0)
                        <tr>
                            <td class="t-label" style="color: #b91c1c;">Less: Line Discounts:</td>
                            <td class="t-val" style="color: #b91c1c;">-₹{{ number_format($lineDiscountTotal, 2) }}</td>
                        </tr>
                    @endif
                    @if ($overallDiscountTotal > 0)
                        <tr>
                            <td class="t-label" style="color: #b91c1c;">Less: Overall Discount ({{ $overallDiscountPct }}%):</td>
                            <td class="t-val" style="color: #b91c1c;">-₹{{ number_format($overallDiscountTotal, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="t-label">Net Taxable Value:</td>
                        <td class="t-val">₹{{ number_format($netTaxable, 2) }}</td>
                    </tr>
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
                    @if (abs($roundDiff) >= 0.005)
                        <tr>
                            <td class="t-label">Round Off:</td>
                            <td class="t-val">₹{{ ($roundDiff >= 0 ? '+' : '') . number_format($roundDiff, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-row">
                        <td>NET PO VALUE (GRAND TOTAL):</td>
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
                    @if (!empty($firstPo->advance_percentage) && $firstPo->advance_percentage > 0)
                        <br><strong style="color: #0369a1;">• Advance Required:</strong> {{ $firstPo->advance_percentage }}% (₹{{ number_format($firstPo->advance_amount, 2) }})
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <strong>• Warranty:</strong> {{ $firstPo->warranty_terms ?: '12 Months Standard Manufacturer Warranty' }}<br>
                    <strong>• Installation Scope:</strong> {{ $firstPo->installation_scope ?: 'In Supplier Scope' }}
                </td>
            </tr>
        </table>
        @php
            $standardTermsContent = $firstPo->termsConditionDetails?->content;
            if (empty($standardTermsContent)) {
                $standardTermsContent = \App\Models\Term::where("whichterm", 2)->value('content') 
                    ?? "1. Payment shall be made as per agreed payment terms against tax invoice.\n2. Materials must strictly conform to approved specifications and quality standards.\n3. Safe and damage-free delivery to destination is the responsibility of the supplier.";
            }
        @endphp
        <div style="font-size: 7.5pt; color: #475569; border-top: 1px solid #e2e8f0; padding-top: 3px; margin-top: 3px;">
            <strong>Standard Terms:</strong> {!! nl2br(e($standardTermsContent)) !!}
        </div>
    </div>

    @if (!empty($firstPo->special_instructions))
        <!-- Highlighted Special Instructions (Requirement 4.1) -->
        <div class="special-instructions-card">
            <div class="special-instructions-title">
                MANDATORY SPECIAL INSTRUCTIONS &amp; DISPATCH REQUIREMENTS:
            </div>
            <div class="special-instructions-content">
                {!! nl2br(e($firstPo->special_instructions)) !!}
            </div>
        </div>
    @endif

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
        {{ str_replace("\n", ' | ', $companyBillingAddress) }}
    </div>

</body>
</html>
