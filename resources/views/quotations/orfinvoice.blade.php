<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORF - {{ $orf->orfref ?? ('ORF/' . ($quotation->quota_id ?? '')) }}</title>
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
            padding: 9px 20px;
            color: #fff;
            background: #fe6502;
            border: 0;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(254, 101, 2, 0.35);
        }
        .no-print button:hover {
            background: #e05700;
        }

        .document-page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            padding: 34mm 16mm 22mm 16mm;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
            background: #fff;
        }

        .document-page:last-of-type {
            page-break-after: auto;
            break-after: auto;
        }

        /* Screen Preview Card styling */
        @if(!($pdfMode ?? false))
        @media screen {
            body { 
                background: #e2e8f0;
                padding: 20px 10px;
            }

            .no-print {
                position: fixed;
                top: 16px;
                right: 20px;
                z-index: 100;
                margin: 0;
            }

            .document-page {
                margin: 0 auto 30px auto;
                background: #fff;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
                border-radius: 4px;
            }
        }
        @endif

        @media print {
            .no-print { display: none !important; }
            .document-page {
                margin: 0;
                padding: 32mm 15mm 20mm 15mm;
                box-shadow: none;
            }
        }

        /* Header Branding */
        .orf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #fe6502;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .orf-header-logo img {
            height: 52px;
            width: auto;
        }
        .orf-header-title {
            text-align: right;
        }
        .orf-header-title h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: 800;
            color: #fe6502;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .orf-header-title .sub-title {
            font-size: 8.5pt;
            color: #64748b;
            font-weight: 600;
        }

        /* Section Headings */
        .section-title {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 9.5pt;
            font-weight: 700;
            padding: 4px 8px;
            border-left: 3.5px solid #fe6502;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Info Grid Tables */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .info-table td, .info-table th {
            padding: 4.5px 7px;
            font-size: 8.5pt;
            vertical-align: top;
            border: 1px solid #cbd5e1;
        }
        .info-table .label {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            width: 22%;
        }
        .info-table .value {
            color: #0f172a;
            width: 28%;
        }

        /* Product & Commercial Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .items-table th {
            background: #1e293b;
            color: #fff;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #1e293b;
            text-align: center;
        }
        .items-table td {
            padding: 5px 6px;
            font-size: 8.5pt;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }

        /* Summary Box */
        .summary-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 4px;
            gap: 15px;
        }
        .terms-summary {
            flex: 1.2;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px;
            background: #fcfcfd;
        }
        .totals-table {
            flex: 0.8;
            border-collapse: collapse;
            width: 100%;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 8.5pt;
            border: 1px solid #cbd5e1;
        }
        .totals-table .label {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }
        .totals-table .grand-row td {
            background: #fff7ed;
            color: #c2410c;
            font-weight: 800;
            font-size: 9.5pt;
            border-top: 2px solid #fe6502;
        }

        /* Sign-off Block */
        .sign-block {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .sign-col {
            flex: 1;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 10px 8px;
            text-align: center;
            background: #fafafa;
        }
        .sign-title {
            font-size: 8pt;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .sign-name {
            font-size: 8.5pt;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }

        /* Reference Page Footer */
        .reference-page-footer {
            position: absolute;
            left: 16mm;
            right: 16mm;
            bottom: 8mm;
            text-align: center;
            font-size: 7.5pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            line-height: 1.3;
        }
        .reference-page-number {
            position: absolute;
            right: 16mm;
            bottom: 8mm;
            font-size: 8pt;
            font-weight: 700;
            color: #475569;
        }
    </style>
</head>
<body>
@php
    $lead = $quotation->lead;
    $assignedUser = $lead?->assignedUser;
    $freight = $quotation->freight;
    $currency = $freight?->currency ?: 'INR';
    $documentAsset = fn (string $path) => ($pdfMode ?? false) ? public_path($path) : asset($path);
    $logoSrc = $documentAsset('assets/images/logom.jpeg');

    $orfRefNo = $orf?->orfref ?: ('ORF/' . $quotation->quota_id);
    $orfDateStr = $orf?->orfdate ? \Carbon\Carbon::parse($orf->orfdate)->format('d-m-Y') : ($quotation->created_at ? $quotation->created_at->format('d-m-Y') : date('d-m-Y'));
    $poDateStr = $orf?->podate ? \Carbon\Carbon::parse($orf->podate)->format('d-m-Y') : '-';
    $crdDateStr = $orf?->crddate ? \Carbon\Carbon::parse($orf->crddate)->format('d-m-Y') : '-';
    $specialIns = $orf?->specialins ?: 'Standard Packing & Delivery as per PO terms.';
    $customerPoRef = $orf?->attcuspo ? basename($orf->attcuspo) : ($lead?->po_number ?? 'Confirmed as per PO');
@endphp

<div class="no-print">
    <button type="button" onclick="window.print()">
        Print ORF Document
    </button>
</div>

{{-- ==================== PAGE 1: ORF MAIN DETAILS & COMMERCIALS ==================== --}}
<section class="document-page">
    <div class="orf-header">
        <div class="orf-header-logo">
            <img src="{{ $logoSrc }}" alt="Mercuriis Logo">
        </div>
        <div class="orf-header-title">
            <h1>Order Release Form (ORF)</h1>
            <div class="sub-title">MERCURIIS STORAGE &amp; EQUIPMENTS PVT LTD</div>
        </div>
    </div>

    {{-- 1. ORDER & CUSTOMER META TABLE --}}
    <div class="section-title">1. Order &amp; Reference Information</div>
    <table class="info-table">
        <tr>
            <td class="label">ORF Reference No:</td>
            <td class="value fw-bold" style="color: #fe6502;">{{ $orfRefNo }}</td>
            <td class="label">ORF Date:</td>
            <td class="value fw-bold">{{ $orfDateStr }}</td>
        </tr>
        <tr>
            <td class="label">Quotation No:</td>
            <td class="value">{{ $quotation->quota_id ?? 'N/A' }}</td>
            <td class="label">Quotation Date:</td>
            <td class="value">{{ optional($quotation->created_at)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Customer PO Date:</td>
            <td class="value">{{ $poDateStr }}</td>
            <td class="label">Target Delivery (CRD):</td>
            <td class="value fw-bold" style="color: #047857;">{{ $crdDateStr }}</td>
        </tr>
        <tr>
            <td class="label">Sales Engineer:</td>
            <td class="value">{{ $assignedUser?->name ?? 'Sales Team' }}</td>
            <td class="label">Product Segment:</td>
            <td class="value">{{ optional($leadCategory)->category_name ?? ($isSpecial ? 'Special' : 'Standard') }}</td>
        </tr>
    </table>

    {{-- 2. ADDRESS INFORMATION --}}
    <div class="section-title">2. Customer, Billing &amp; Delivery Information</div>
    <table class="info-table">
        <tr>
            <th class="label text-start" colspan="2" style="width: 50%;">Customer &amp; Billing Details</th>
            <th class="label text-start" colspan="2" style="width: 50%;">Shipping &amp; Delivery Address</th>
        </tr>
        <tr>
            <td colspan="2" class="value">
                <strong>{{ $lead?->LeadName ?? 'N/A' }}</strong><br>
                {{ $quotation->billing_address ?: ($lead?->billaddress ?: 'N/A') }}<br>
                <strong>City:</strong> {{ $billingCityName ?: '-' }} | <strong>State:</strong> {{ optional($shippingState)->state ?? 'Tamil Nadu' }}<br>
                <strong>GSTIN:</strong> {{ $quotation->billing_gst ?: ($lead?->gst ?: 'N/A') }} | <strong>Pincode:</strong> {{ $quotation->billing_pincode ?: ($lead?->postalcode ?: '-') }}
            </td>
            <td colspan="2" class="value">
                <strong>Place of Supply / Site:</strong><br>
                {{ $quotation->shipping_address ?: ($quotation->billing_address ?: ($lead?->billaddress ?: 'Same as billing')) }}<br>
                <strong>City:</strong> {{ optional(\App\Models\Citylist::find($quotation->shipping_city))->city_name ?? ($billingCityName ?: '-') }} | 
                <strong>Pincode:</strong> {{ $quotation->shipping_pincode ?: ($quotation->billing_pincode ?: '-') }}<br>
                <strong>Shipping GSTIN:</strong> {{ $quotation->shipping_gst ?: ($quotation->billing_gst ?: '-') }}
            </td>
        </tr>
    </table>

    {{-- 3. PRODUCTS & SCOPE OF SUPPLY TABLE --}}
    <div class="section-title">3. Scope of Supply &amp; Bill of Materials</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 12%;">Part No</th>
                <th style="width: 32%;">Description of Goods</th>
                <th style="width: 8%;">HSN</th>
                <th style="width: 6%;">Qty</th>
                <th style="width: 12%;">Unit Rate (₹)</th>
                <th style="width: 12%;">Basic (₹)</th>
                <th style="width: 14%;">Total + Tax (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sNo = 1;
                $calcBasicTotal = 0;
                $calcTaxTotal = 0;
            @endphp
            @foreach($quotationProducts as $item)
                @php
                    $prod = $item->productDetails;
                    $rate = (float) $item->ratee;
                    $qty = (float) ($item->quanty ?: 1);
                    $lbt = (float) ($item->lbt ?? ($rate * $qty));
                    $taxRate = (float) ($item->tax ?? ($prod?->gst ?? 18));
                    $taxAmt = (float) ($item->taxamt ?? ($lbt * $taxRate / 100));
                    $lineTotal = (float) ($item->grandtotal ?? ($lbt + $taxAmt));
                    $calcBasicTotal += $lbt;
                    $calcTaxTotal += $taxAmt;
                @endphp
                <tr>
                    <td class="text-center">{{ $sNo++ }}</td>
                    <td class="text-center fw-bold">{{ $item->part_no ?: ($prod?->partno ?: '-') }}</td>
                    <td>
                        <strong>{{ \App\Helpers\QuotationPresentationHelper::productDescription($prod) }}</strong>
                        @if($item->accessories && $item->accessories->count() > 0)
                            <div style="font-size: 7.5pt; color: #475569; margin-top: 2px;">
                                <em>Accessories:</em>
                                @foreach($item->accessories as $acc)
                                    <span>+ {{ $acc->name }} (Qty: {{ $acc->quantity }})</span>{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $prod?->hsn ?: '-' }}</td>
                    <td class="text-center fw-bold">{{ $qty }}</td>
                    <td class="text-end">{{ number_format($rate, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($lbt, 2) }}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. COMMERCIAL SUMMARY & EXECUTION TERMS --}}
    <div class="summary-wrapper">
        <div class="terms-summary">
            <div style="font-weight: 700; font-size: 8.5pt; color: #fe6502; margin-bottom: 4px; text-transform: uppercase;">
                Scope &amp; Commercial Terms:
            </div>
            <div style="font-size: 8pt; line-height: 1.4; color: #334155;">
                <div><strong>• Payment Terms:</strong> {{ optional($quotation->freight?->termsCondition)->content ?? 'As agreed in PO' }}</div>
                <div><strong>• Warranty Terms:</strong> {{ optional($quotation->freight?->warrantyTerm)->content ?? 'Standard 12 Months' }}</div>
                <div><strong>• Freight / Transportation:</strong> {{ $freightCategoryName }} ({{ $unloadingScope ?? 'Standard Unloading' }})</div>
                <div><strong>• Installation Scope:</strong> {{ optional($quotation->freight?->installationScope)->name ?? 'Customer Scope' }}</div>
                <div style="margin-top: 4px; padding: 4px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 3px;">
                    <strong style="color: #c2410c;">• Special Production / Dispatch Instructions:</strong><br>
                    {{ $specialIns }}
                </div>
            </div>
        </div>

        <table class="totals-table">
            <tr>
                <td class="label">Basic Subtotal:</td>
                <td class="value text-end fw-bold">₹{{ number_format($quotation->basic_total ?: $calcBasicTotal, 2) }}</td>
            </tr>
            @if((float)$quotation->installation_charge > 0)
                <tr>
                    <td class="label">Installation Charge:</td>
                    <td class="value text-end">₹{{ number_format($quotation->installation_charge, 2) }}</td>
                </tr>
            @endif
            @if((float)$quotation->freight_charge > 0)
                <tr>
                    <td class="label">Freight Charge:</td>
                    <td class="value text-end">₹{{ number_format($quotation->freight_charge, 2) }}</td>
                </tr>
            @endif
            @if((float)$quotation->additional_warranty_charge > 0)
                <tr>
                    <td class="label">Additional Warranty:</td>
                    <td class="value text-end">₹{{ number_format($quotation->additional_warranty_charge, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Total Tax (GST):</td>
                <td class="value text-end">₹{{ number_format($quotation->tax_total ?: $calcTaxTotal, 2) }}</td>
            </tr>
            <tr class="grand-row">
                <td class="label">Grand Total ({{ $currency }}):</td>
                <td class="value text-end">₹{{ number_format($quotation->grand_total ?: ($calcBasicTotal + $calcTaxTotal), 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- 5. VERIFICATION & APPROVAL SIGNATURES --}}
    <div class="section-title" style="margin-top: 12px;">4. Order Release Authorization &amp; Sign-off</div>
    <div class="sign-block">
        <div class="sign-col">
            <div class="sign-title">Prepared By (Sales)</div>
            <div class="sign-name">{{ $assignedUser?->name ?? 'Sales Engineer' }}</div>
        </div>
        <div class="sign-col">
            <div class="sign-title">Verified By (Customer Support)</div>
            <div class="sign-name">{{ Auth::user()->name ?? 'CS Coordinator' }}</div>
        </div>
        <div class="sign-col">
            <div class="sign-title">Authorized By (Operations)</div>
            <div class="sign-name">Management / Factory Head</div>
        </div>
    </div>

    {{-- Chrome Footer --}}
    <div class="reference-page-footer">
        <div><strong>Mercuriis Storage &amp; Equipments Pvt Ltd</strong> | 89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, TN, India.</div>
        <div>GST No: 33AATCM0824C1ZQ | Website: www.mercuriis.com</div>
    </div>
    <div class="reference-page-number">Page 1 of 1</div>
</section>

</body>
</html>
