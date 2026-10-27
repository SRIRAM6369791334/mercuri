<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $poDetails[0]->po_no ?? 'PO' }}</title>
    <style>
        * { box-sizing: border-box; }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            color: #1e293b;
            background: #fff;
            font-family: Calibri, 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print {
            margin: 14px auto;
            text-align: center;
        }

        .no-print button {
            padding: 9px 22px;
            color: #fff;
            background: #1b5583;
            border: 0;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(27, 85, 131, 0.35);
        }
        .no-print button:hover {
            background: #134267;
        }

        .document-page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            padding: 16mm 16mm 20mm 16mm;
            margin: 0 auto;
            background: #fff;
        }

        /* Top Header */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #1b5583;
            padding-bottom: 8px;
        }
        .doc-header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .company-logo img {
            max-width: 110px;
            max-height: 48px;
            object-fit: contain;
        }
        .doc-title-block {
            text-align: right;
        }
        .doc-title {
            font-size: 18pt;
            font-weight: 800;
            color: #1b5583;
            letter-spacing: 1px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 8.5pt;
            color: #64748b;
            font-weight: 600;
        }

        /* Meta Information Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .meta-table td {
            padding: 5px 8px;
            font-size: 8.5pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: 700;
            color: #475569;
            width: 18%;
            background: #f1f5f9;
        }
        .meta-value {
            font-weight: 700;
            color: #0f172a;
            width: 32%;
        }

        /* Section Headings */
        .section-heading {
            font-size: 9pt;
            font-weight: 800;
            color: #fff;
            background: #1b5583;
            padding: 4px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
            margin-bottom: 0;
            border-radius: 3px 3px 0 0;
        }

        /* Address Blocks Table */
        .address-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .address-table td {
            width: 33.33%;
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: top;
            font-size: 8.5pt;
            line-height: 1.35;
            background: #fff;
        }
        .address-box-title {
            font-weight: 800;
            color: #1b5583;
            font-size: 8.5pt;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            text-transform: uppercase;
        }

        /* Item Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .items-table th {
            background: #1b5583;
            color: #fff;
            font-weight: 700;
            font-size: 8.5pt;
            padding: 6px 6px;
            border: 1px solid #0f395c;
            text-align: center;
        }
        .items-table td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Totals Block */
        .totals-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }
        .amount-words-box {
            flex: 1.1;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px;
            background: #f8fafc;
            font-size: 8pt;
        }
        .totals-table {
            flex: 0.9;
            border-collapse: collapse;
            width: 100%;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 8.5pt;
            border: 1px solid #cbd5e1;
        }
        .totals-table .label {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
        }
        .totals-table .grand-row td {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 800;
            font-size: 9.5pt;
            border-top: 2px solid #0284c7;
        }

        /* Terms & Conditions Grid */
        .commercial-terms-box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            background: #fafafa;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .commercial-terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 16px;
            margin-bottom: 6px;
        }
        .term-item {
            color: #334155;
        }
        .term-item strong {
            color: #0f172a;
        }

        /* Highlighted Special Instructions Box (Requirement 11) */
        .special-instructions-card {
            background: #fffbeb;
            border: 2px dashed #f59e0b;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .special-instructions-title {
            color: #b45309;
            font-weight: 800;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .special-instructions-content {
            font-size: 8.5pt;
            color: #92400e;
            font-weight: 600;
            line-height: 1.35;
        }

        /* Attachments Bar */
        .attachments-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 8pt;
            color: #334155;
        }

        /* Signatures */
        .sign-grid {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
        }
        .sign-box {
            flex: 1;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 10px 8px;
            text-align: center;
            background: #fafafa;
        }
        .sign-box-title {
            font-size: 7.5pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .sign-box-name {
            font-size: 8.5pt;
            font-weight: 700;
            color: #0f172a;
        }

        /* Document Footer */
        .doc-footer {
            margin-top: 12px;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
            line-height: 1.4;
        }

        @media print {
            .no-print { display: none !important; }
            .document-page {
                padding: 12mm 12mm 12mm 12mm;
                min-height: auto;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="display: flex; justify-content: center; gap: 12px; margin: 16px auto;">
    <button onclick="window.print();" style="background:#1b5583;">
        <i class="fas fa-print"></i> Print / Save Purchase Order PDF
    </button>
    <a href="/po-download-merged/{{ $poDetails[0]->po_no }}" target="_blank" style="text-decoration:none;">
        <button type="button" style="background:#0d9488;">
            <i class="fas fa-file-pdf"></i> Download Complete Merged Package (PO + Tech Specs + Approved Drawing + Quote)
        </button>
    </a>
</div>

@php
    $firstPo = $poDetails[0];
    $vendor = $firstPo->allVendorDetails;
    $city = $vendor ? App\Models\Citylist::where('id', $vendor->city_id)->first() : null;
    $state = $vendor ? App\Models\Statelist::where('id', $vendor->state_id)->first() : null;

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

    // Default company address
    $companyName = "MERCURIIS STORAGE & EQUIPMENTS PVT LTD";
    $companyBillingAddress = "89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu, India.\nGSTIN: 33AATCM0824C1ZQ | Phone: +91 99620 55076 | Email: info@mercuriis.com";

    // Shipping address determination
    $shippingAddress = $firstPo->shipping_address ?: $companyBillingAddress;
    $shippingGst = $firstPo->shipping_gst ?: "33AATCM0824C1ZQ";

    $hideBrand = (bool)($firstPo->hide_brand_name);

    // Decoded quote attachments
    $quoteFiles = [];
    if (!empty($firstPo->quote_attachments)) {
        $decoded = json_decode($firstPo->quote_attachments, true);
        if (is_array($decoded)) {
            $quoteFiles = $decoded;
        }
    }
@endphp

<div class="document-page">

    <!-- Header Table -->
    <table class="doc-header-table">
        <tr>
            <td class="company-logo" style="width: 45%;">
                <img src="/assets/images/logom.jpeg" alt="Mercuriis Logo" onerror="this.style.display='none'">
                <div style="font-weight: 800; font-size: 11pt; color: #1b5583; margin-top: 3px;">
                    MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD
                </div>
            </td>
            <td class="doc-title-block" style="width: 55%;">
                <h1 class="doc-title">PURCHASE ORDER</h1>
                <div class="doc-subtitle">Official Procurement Order &amp; Delivery Contract</div>
            </td>
        </tr>
    </table>

    <!-- Metadata Table -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">P.O. Number:</td>
            <td class="meta-value" style="color: #1b5583; font-size: 10pt;">{{ $poNumber }}</td>
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

    <!-- Addresses Block (Supplier, Billing / Purchaser, Shipping / Delivery) -->
    <table class="address-table">
        <tr>
            <!-- 1. VENDOR / SUPPLIER (WITH BRANCH & GST) -->
            <td>
                <div class="address-box-title"><i class="fas fa-building"></i> Supplier / Vendor</div>
                <strong>{{ $vendor?->company_name ?? 'N/A' }}</strong><br>
                @if (!empty($firstPo->supplier_billing_address))
                    {!! nl2br(e($firstPo->supplier_billing_address)) !!}<br>
                @elseif (!empty($vendor?->address))
                    {!! nl2br(e($vendor->address)) !!}<br>
                    {{ $city?->city_name ?? '' }} {{ $state?->state ? ', ' . $state->state : '' }} {{ $vendor?->postal_code ? '- ' . $vendor->postal_code : '' }}<br>
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
                <div class="address-box-title"><i class="fas fa-file-invoice"></i> Purchaser / Billing Address</div>
                <strong>MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD</strong><br>
                89, Balaji Nagar, Athanancheri,<br>
                Padappai, Chennai - 601301,<br>
                Tamil Nadu, India.<br>
                <strong>GSTIN:</strong> 33AATCM0824C1ZQ<br>
                <strong>Email:</strong> info@mercuriis.com
            </td>

            <!-- 3. SHIPPING / DELIVERY DESTINATION (MERCURIIS DISPLAYED) -->
            <td style="background: #fdfefe;">
                <div class="address-box-title" style="color: #0369a1;"><i class="fas fa-truck"></i> Ship To / Delivery Destination</div>
                <div style="font-weight: 800; font-size: 9pt; color: #1b5583; margin-bottom: 3px;">
                    MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD
                </div>
                <div style="font-weight: 600; color: #0f172a;">
                    {!! nl2br(e($shippingAddress)) !!}
                </div>
                <div style="margin-top: 4px;">
                    <strong>Destination GSTIN:</strong> {{ $shippingGst }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Scope of Supply & Bill of Materials Table -->
    <div class="section-heading">Scope of Supply &amp; Bill of Materials</div>
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

                    $specs = ($prod && $prod->specifications) ? $prod->specifications : collect();
                    if ($hideBrand) {
                        $specs = $specs->filter(function($s) {
                            return !in_array(strtoupper(trim($s->description)), ['MAKE', 'BRAND', 'MANUFACTURER']);
                        });
                    }
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-weight: 700; color: #1b5583;">
                        {{ $prod?->partno ?: ($prod?->sku ?: '-') }}
                    </td>
                    <td>
                        <strong style="color: #0f172a; font-size: 9pt;">{{ $prod?->productname ?? 'N/A' }}</strong>
                        @if (!empty($item->product_description))
                            <div style="font-size: 8pt; color: #475569; margin-top: 2px; white-space: pre-line;">{{ $item->product_description }}</div>
                        @elseif (!empty($prod?->productdes))
                            <div style="font-size: 8pt; color: #475569; margin-top: 2px;">{{ $prod->productdes }}</div>
                        @endif
                        @if ($specs->isNotEmpty())
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">
                                <em>Specs:</em>
                                @foreach ($specs as $spec)
                                    <span>{{ $spec->description ? $spec->description . ': ' : '' }}{{ $spec->specification }}{{ $spec->unit ? ' ' . $spec->unit : '' }}</span>{{ !$loop->last ? ' | ' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $prod?->hsn ?: '-' }}</td>
                    <td style="text-align: center; font-weight: 800; font-size: 9pt;">{{ $itemQty }}</td>
                    <td style="text-align: right;">{{ number_format($unitPrice, 2) }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($lineBasic, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Section -->
    <div class="totals-wrapper">
        <div class="amount-words-box">
            <strong>Amount Chargeable in Words:</strong><br>
            <span style="font-weight: 700; color: #1b5583;">
                {{ \App\Helpers\IndianNumberHelper::numberToIndianWords(round($grandTotal)) ?? 'Rupees ' . number_format($grandTotal, 2) }}
            </span>
            <div style="margin-top: 6px; font-size: 7.5pt; color: #64748b;">
                * Total Quantity Ordered: <strong>{{ $totalQty }} Units</strong> across <strong>{{ count($poDetails) }} Line Item(s)</strong>.
            </div>
        </div>

        <table class="totals-table">
            <tr>
                <td class="label">Gross Subtotal (Basic):</td>
                <td style="text-align: right; font-weight: 700;">₹{{ number_format($basicTotal, 2) }}</td>
            </tr>
            @if ($discountPct > 0 || $discountAmt > 0)
                <tr>
                    <td class="label" style="color: #b91c1c;">Less: Discount ({{ $discountPct }}%):</td>
                    <td style="text-align: right; font-weight: 700; color: #b91c1c;">-₹{{ number_format($discountAmt, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Net Taxable Value:</td>
                    <td style="text-align: right; font-weight: 700;">₹{{ number_format($netTaxable, 2) }}</td>
                </tr>
            @endif
            @if ($freightCharges > 0)
                <tr>
                    <td class="label">Freight / Transportation Charges:</td>
                    <td style="text-align: right; font-weight: 700;">₹{{ number_format($freightCharges, 2) }}</td>
                </tr>
            @endif
            @if ($unloadingCharges > 0)
                <tr>
                    <td class="label">Unloading Charges:</td>
                    <td style="text-align: right; font-weight: 700;">₹{{ number_format($unloadingCharges, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Total Applicable Tax (GST):</td>
                <td style="text-align: right; font-weight: 700;">₹{{ number_format($gstTotal, 2) }}</td>
            </tr>
            <tr class="grand-row">
                <td>GRAND TOTAL (INCL. GST):</td>
                <td style="text-align: right;">₹{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Commercial & Operational Terms -->
    <div class="commercial-terms-box">
        <div style="font-weight: 800; color: #1b5583; font-size: 8.5pt; margin-bottom: 4px; text-transform: uppercase;">
            Commercial Terms &amp; Conditions
        </div>
        <div class="commercial-terms-grid">
            <div class="term-item">
                <strong>• Delivery / Lead Time:</strong> {{ $firstPo->lead_time ?: '4 Weeks from PO confirmation' }}
            </div>
            <div class="term-item">
                <strong>• Payment Terms:</strong> {{ $firstPo->payment_terms ?: '50% Advance & Balance against Proforma' }}
            </div>
            <div class="term-item">
                <strong>• Warranty Terms:</strong> {{ $firstPo->warranty_terms ?: '12 Months Standard Manufacturer Warranty' }}
            </div>
            <div class="term-item">
                <strong>• Installation Scope:</strong> {{ $firstPo->installation_scope ?: 'In Supplier Scope' }}
            </div>
            <div class="term-item">
                <strong>• Transit &amp; Delivery:</strong> Direct to Destination Address as specified above
            </div>
        </div>
        @if (!empty($firstPo->termsConditionDetails?->content))
            <div style="font-size: 8pt; color: #475569; border-top: 1px solid #e2e8f0; padding-top: 4px; margin-top: 4px;">
                <strong>Standard Terms:</strong> {{ $firstPo->termsConditionDetails->content }}
            </div>
        @endif
    </div>

    <!-- Highlighted Special Instructions (Requirement 4.1) -->
    <div class="special-instructions-card">
        <div class="special-instructions-title">
            <i class="fas fa-exclamation-triangle"></i> Special Instructions &amp; Mandatory Dispatch Requirements:
        </div>
        <div class="special-instructions-content">
            @if (!empty($firstPo->special_instructions))
                {!! nl2br(e($firstPo->special_instructions)) !!}
            @else
                1. Purchase Order No. (<strong>{{ $poNumber }}</strong>) and Date must be clearly mentioned on all Tax Invoices, Delivery Challans, and Test Certificates.<br>
                2. Goods must be securely packed to prevent any in-transit damage. Supplier must acknowledge receipt of this Purchase Order.
            @endif
        </div>
    </div>

    <!-- Attachments Reference Bar (Requirement 4.2) -->
    @if (!empty($firstPo->drawing_attachment) || !empty($quoteFiles) || !empty($firstPo->files))
        <div class="attachments-bar">
            <strong><i class="fas fa-paperclip"></i> Associated Procurement Attachments:</strong>
            @if (!empty($firstPo->drawing_attachment))
                <span class="badge bg-light text-dark border ms-1">
                    <i class="fas fa-drafting-compass text-info"></i> Approved Drawing:
                    <a href="/uploads/po_files/{{ $firstPo->drawing_attachment }}" target="_blank" style="color:#1b5583; font-weight:600;">{{ $firstPo->drawing_attachment }}</a>
                </span>
            @endif
            @if (!empty($quoteFiles))
                @foreach ($quoteFiles as $qFile)
                    <span class="badge bg-light text-dark border ms-1">
                        <i class="fas fa-file-excel text-success"></i> Quote Ref:
                        <a href="/uploads/po_files/{{ $qFile }}" target="_blank" style="color:#1b5583; font-weight:600;">{{ $qFile }}</a>
                    </span>
                @endforeach
            @endif
            @if (!empty($firstPo->files) && empty($quoteFiles))
                <span class="badge bg-light text-dark border ms-1">
                    <i class="fas fa-file-alt text-primary"></i> Attached Doc:
                    <a href="/uploads/po_files/{{ $firstPo->files }}" target="_blank" style="color:#1b5583; font-weight:600;">{{ $firstPo->files }}</a>
                </span>
            @endif
        </div>
    @endif

    <!-- Authorized Signatory Block -->
    <div class="sign-grid">
        <div class="sign-box">
            <div class="sign-box-title">Prepared By (Procurement)</div>
            <div class="sign-box-name">Supply Chain Executive</div>
        </div>
        <div class="sign-box">
            <div class="sign-box-title">Verified By (Operations)</div>
            <div class="sign-box-name">Commercial Manager</div>
        </div>
        <div class="sign-box">
            <div class="sign-box-title">Authorized By (Management)</div>
            <div class="sign-box-name">Mercuriis Authorized Signatory</div>
        </div>
    </div>

    <!-- Document Footer -->
    <div class="doc-footer">
        <strong>Mercuriis Storage &amp; Equipments Pvt Ltd</strong> | 89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, Tamil Nadu, India.<br>
        GSTIN: 33AATCM0824C1ZQ | Corporate Website: www.mercuriis.com | Support: info@mercuriis.com
    </div>

</div>

</body>
</html>