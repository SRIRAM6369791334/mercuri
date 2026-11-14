<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quota_id ?? '' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            color: #111;
            background: #fff;
            font-family: Calibri, Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.35;
        }

        p {
            margin: 0 0 4mm;
        }

        h1,
        h2,
        h3 {
            margin: 0;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print {
            margin: 12px auto;
            text-align: center;
        }

        .no-print button {
            padding: 9px 16px;
            color: #fff;
            background: #0b5fa5;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
        }

        .no-print .btn-drawing-toolbar {
            display: inline-block;
            margin-left: 8px;
            padding: 8px 14px;
            color: #0b5fa5;
            background: #eef6fc;
            border: 1px solid #0b5fa5;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            vertical-align: middle;
        }

        .no-print .btn-drawing-toolbar:hover {
            background: #0b5fa5;
            color: #fff;
        }

        .drawing-reference-badge {
            margin: 3mm 0 4mm;
            padding: 2.5mm 4mm;
            background: #f4f8fb;
            border-left: 3px solid #0b5fa5;
            font-size: 9.5pt;
        }

        .document-page {
            position: relative;
            width: 210mm;
            height: 297mm;
            padding: 37mm 20.5mm 13mm;
            overflow: hidden;
            page-break-before: auto;
            page-break-after: always;
            break-after: page;
        }

        .document-page:last-of-type {
            page-break-after: auto;
            break-after: auto;
        }

        .profile-page {
            padding-top: 52mm;
        }

        .specification-page {
            padding-top: 50mm;
        }

        .commercial-page {
            padding-top: 50mm;
        }

        .commercial-page.quotation-table-page {
            padding-top: 36mm !important;
            padding-left: 9.5mm !important;
            padding-right: 9.5mm !important;
            padding-bottom: 12mm !important;
        }

        .watermark-overlay {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .watermark-text {
            position: absolute;
            top: 42%;
            left: -20%;
            width: 140%;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 85pt;
            font-weight: 900;
            letter-spacing: 16px;
            color: rgba(220, 50, 50, 0.05);
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            transform: rotate(-45deg);
            user-select: none;
            text-transform: uppercase;
        }

        .reference-page-frame {
            position: absolute;
            z-index: 0;
            top: 8.5mm;
            right: 8.5mm;
            bottom: 8.5mm;
            left: 8.5mm;
            border: 0.35mm solid #111;
            pointer-events: none;
        }

        .reference-page-logo {
            position: absolute;
            z-index: 10;
            top: 13mm;
            right: 11.5mm;
            width: 40mm;
            height: 23mm;
            background: #fff;
            object-fit: contain;
        }

        .reference-page-footer {
            position: absolute;
            z-index: 2;
            right: 20mm;
            bottom: 10mm;
            left: 20mm;
            background: transparent;
            color: #0070c0;
            font-family: "Times New Roman", Times, serif;
            font-size: 12.2pt;
            font-weight: 700;
            line-height: 1.15;
            text-align: center;
        }

        .reference-page-footer a,
        .reference-page-footer a:visited,
        .reference-page-footer a:hover,
        .reference-page-footer a:active {
            color: #000000 !important;
            text-decoration: underline;
        }

        .reference-page-number {
            position: absolute;
            z-index: 2;
            right: 25.5mm;
            bottom: 13.5mm;
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
        }

        .reference-row {
            display: table;
            width: 100%;
            margin-bottom: 6mm;
            font-weight: 700;
        }

        .reference-row>div {
            display: table-cell;
            width: 50%;
        }

        .reference-row .date {
            text-align: right;
        }

        .recipient {
            margin-bottom: 16mm;
        }

        .recipient p {
            margin: 0 0 1mm;
        }

        .proposal-title {
            color: #0d62aa;
            text-align: center;
            font-size: 13pt;
            font-weight: 700;
            text-decoration: underline;
        }

        .proposal-title {
            margin: 0 0 7mm;
            color: #111;
        }

        .letter-copy {
            text-align: justify;
            font-size: 12pt;
        }

        .offer-list {
            margin: 4mm 0 6mm;
        }

        .offer-list-row {
            margin-bottom: 1mm;
        }

        .signature {
            margin-top: 6mm;
        }

        .signature p {
            margin-bottom: 0.6mm;
        }

        .signature .contact-link,
        .commercial-signature .contact-link,
        a.contact-link,
        a.contact-link:visited,
        a.contact-link:hover,
        a.contact-link:active,
        .signature a,
        .commercial-signature a {
            color: #000000 !important;
            font-weight: 700;
            text-decoration: underline;
        }

        .section-heading {
            color: #0d62aa;
            text-align: center;
            font-size: 11.5pt;
            font-weight: 700;
            margin-bottom: 6mm;
        }

        .reference-highlight {
            display: table;
            margin: 0 auto 12mm;
            padding: 0 1px;
            background: #fff900;
            color: #0d62aa;
            text-align: center;
            font-size: 10.5pt;
            font-weight: 700;
            text-decoration: underline;
        }

        .profile-copy {
            text-align: justify;
            font-size: medium;
        }

        .profile-copy p {
            margin-bottom: 5mm;
        }

        .customer-heading {
            color: #0d62aa;
            text-align: center;
            font-weight: 700;
            margin: 6mm 0 5mm;
        }

        .customer-logos {
            display: block;
            width: 166mm;
            max-width: 100%;
            max-height: 83mm;
            object-fit: contain;
            margin: 0 auto;
        }

        .feature-image {
            display: block;
            width: 142mm;
            max-width: 100%;
            max-height: 145mm;
            object-fit: contain;
            margin: 13mm auto 0;
        }

        .group-name {
            text-align: center;
            color: #0d62aa;
            font-size: 10.5pt;
            font-weight: 700;
            margin-top: -5mm;
        }

        .specification-feature {
            display: block;
            width: 266mm;
            max-width: 100%;
            max-height: 109mm;
            object-fit: contain;
            margin: 14mm auto;
        }

        .specification-group-name {
            margin: 14mm 0 0;
            color: #28658f;
            font-size: 13pt;
            font-weight: 700;
            text-align: center;
        }

        .technical-title {
            background: #123f64;
            border: 0.35mm solid #123f64;
            padding: 3.2mm 2mm;
            color: #fff;
            text-align: center;
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .product-caption {
            padding: 2.2mm 2mm;
            border: 0.35mm solid #7890a3;
            border-top: 0;
            background: #eaf1f6;
            color: #123f64;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
        }

        .specification-table {
            width: 100%;
            table-layout: fixed;
        }

        .specification-sheet {
            width: 155mm;
            max-width: 100%;
            margin: -34px auto;
        }

        .specification-table th,
        .specification-table td {
            border: 0.25mm solid #9babb8;
            padding: 1.2mm 1.8mm;
            vertical-align: middle;
            word-break: break-word;
        }

        .specification-table th {
            background: #dce8f1;
            color: #123f64;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.25px;
        }

        .specification-table tbody tr:nth-child(even) td {
            background: #f7f9fb;
        }

        .specification-table th:nth-child(1),
        .specification-table td:nth-child(1) {
            width: 54%;
        }

        .specification-table th:nth-child(2),
        .specification-table td:nth-child(2) {
            width: 14%;
            text-align: center;
        }

        .specification-table th:nth-child(3),
        .specification-table td:nth-child(3) {
            width: 32%;
            text-align: center;
        }

        .excel-only-page {
            width: 100%;
            overflow: visible;
        }

        .excel-only-page table {
            width: 100% !important;
            table-layout: fixed;
            border-collapse: collapse;
            font-family: Calibri, Arial, sans-serif !important;
            font-size: 9.5pt !important;
        }

        .excel-only-page tr {
            page-break-inside: avoid;
        }

        .excel-only-page td {
            padding: 3px 5px !important;
            word-wrap: break-word;
        }

        .commercial-heading {
            margin-bottom: 1.8mm;
            color: #123f64;
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-align: center;
            text-transform: uppercase;
        }

        .commercial-meta {
            display: table;
            width: 100%;
            margin-bottom: 1.8mm;
            border: 0.3mm solid #7890a3;
            background: #f3f7fa;
            color: #263746;
            font-size: 8.8pt;
        }

        .commercial-meta>div {
            display: table-cell;
            width: 33.333%;
            padding: 1.2mm 2.5mm;
            border-right: 0.25mm solid #b6c2cb;
        }

        .commercial-meta>div:last-child {
            border-right: 0;
            text-align: right;
        }

        .commercial-meta .meta-center {
            text-align: center;
        }

        .commercial-meta strong {
            color: #123f64;
        }

        .products-table {
            width: 100%;
            table-layout: fixed;
            border: 0.45mm solid #123f64;
            font-size: 8.8pt;
        }

        .products-table th,
        .products-table td {
            border: 0.22mm solid #9babb8;
            padding: 1mm 0.6mm;
            vertical-align: middle;
            text-align: center;
            font-size: 9.8pt;
            line-height: 1.15;
        }

        .products-table thead {
            display: table-header-group;
        }

        .products-table tr {
            page-break-inside: avoid;
        }

        .products-table th {
            background: #123f64;
            border-color: #6f879a;
            color: #fff;
            font-weight: 700;
            line-height: 1.15;
            font-size: 9pt;
            padding: 1.2mm 0.6mm;
        }

        .products-table thead tr:nth-child(2) th {
            background: #28658f;
            font-size: 8.2pt;
            font-weight: 700;
            padding: 1mm 0.5mm;
        }

        .products-table tbody tr:nth-child(even):not(.total-row) td {
            background: #f4f7f9;
        }

        .products-table .description {
            padding-left: 1.5mm;
            text-align: left;
            font-size: 11.8pt;
            line-height: 1.2;
        }

        .products-table .terms-cell {
            padding: 1.2mm 1.5mm;
            text-align: left;
            font-size: 8.8pt;
            line-height: 1.2;
        }

        .products-table .amount {
            text-align: right;
            white-space: nowrap;
            padding-right: 1.2mm;
            font-size: 8.8pt;
        }

        .total-row td {
            border-top: 0.45mm solid #123f64;
            background: #dce8f1;
            color: #123f64;
            font-weight: 700;
        }

        .total-row .grand-total {
            background: #123f64;
            color: #fff;
            font-size: 10pt;
        }

        /*.commercial-copy {*/
        /*    margin-top: 2.5mm;*/
        /*    font-size: 8.1pt;*/
        /*    line-height: 1.22;*/
        /*}*/

        /*.commercial-copy p { margin-bottom: 0.8mm; }*/

        /*.commercial-details-grid,*/
        /*.terms-grid {*/
        /*    display: table;*/
        /*    width: 100%;*/
        /*    table-layout: fixed;*/
        /*}*/

        /*.commercial-details-column*/
        /* {*/
        /*    display: table-cell;*/
        /*    width: 50%;*/
        /*    vertical-align: top;*/
        /*    font-size: 11pt;*/
        /*}*/

        /*.commercial-details-column {*/
        /*    padding-right: 3mm;*/
        /*}*/

        /*.commercial-details-column + .commercial-details-column {*/
        /*    padding-right: 0;*/
        /*    padding-left: 3mm;*/
        /*    border-left: 0.25mm solid #c5d0d8;*/
        /*}*/
        .commercial-copy {
            margin-top: 2.5mm;
            font-size: 9.2pt;
            line-height: 1.24;
        }

        .commercial-copy p {
            margin: 0 0 1mm;
            font-size: 10.5pt;
        }

        .commercial-section {
            margin-bottom: 6mm;
        }

        .commercial-subheading {
            margin-bottom: 2mm;
            font-weight: 700;
            font-size: 12.8pt;
        }

        .commercial-subheading.payment {
            color: #111;
            text-decoration: underline;
        }

        .commercial-subheading.blue {
            color: #4472c4;
        }

        .commercial-subheading.bank {
            color: #0070c0;
            text-decoration: underline;
        }

        /*.commercial-subheading {*/
        /*    margin: 2.2mm 0 1mm;*/
        /*    font-weight: 700;*/
        /*}*/

        /*.commercial-subheading.payment { color: #111; text-decoration: underline; }*/
        /*.commercial-subheading.blue { color: #4472c4; }*/
        /*.commercial-subheading.bank { color: #0070c0; text-decoration: underline; }*/

        .payment-copy {
            margin-left: 4mm;
            font-weight: 700;
            white-space: pre-line;
            font-size: 9.4pt;
            line-height: 1.05;
        }

        .commercial-note-block {
            padding: 1.4mm 1.8mm;
            border: 0.25mm solid #b8c9d6;
            background: #f8fbfd;
        }

        .commercial-note-block .commercial-subheading {
            color: #123f64;
        }

        .commercial-note-line {
            display: block;
            margin-bottom: 0.6mm;
            line-height: 1.25;
        }

        .terms-intro {
            margin-top: 1mm;
            padding-top: 2mm;
            /* border-top: 0.35mm solid #7890a3; */
        }

        .delivery-period {
            font-size: 11.8pt;
            /* white-space: nowrap; */
        }

        .terms-title {
            display: table;
            margin: 0 auto 4.5mm;
            padding: 0;
            color: #0d62aa;
            text-align: center;
            font-size: 16pt;
            font-weight: 700;
            text-decoration: underline;
        }

        .terms-copy {
            font-size: 12.5pt;
            line-height: 1.23;
            text-align: justify;
        }

        /* .terms{
font-size:10.5pt;
} */
        .terms-copy p {
            margin-bottom: 3.9mm;
        }

        .terms-copy ol {
            margin: 0.7mm 0 1.5mm 5mm;
            padding: 0;
        }

        /* .terms-column { padding-right: 2.5mm; }
        .terms-column + .terms-column {
            padding-right: 0;
            padding-left: 2.5mm;
        } */

        .commercial-signature {
            margin-top: 2mm;
            padding-top: 1.5mm;
            border-top: 0.25mm solid #c5d0d8;
            text-align: left;
        }

        .muted {
            color: #666;
        }

        @if($pdfMode ?? false)
            .no-print {
                display: none !important;
            }

            .document-page {
                width: auto;
                height: 228mm;
                box-sizing: content-box;
            }

            .profile-page {
                height: 218mm;
            }

            .specification-page {
                height: 220mm;
            }

            .commercial-page {
                height: 216mm;
            }

        @endif
        @if(!($pdfMode ?? false))
            @media screen {
                body {
                    background: #e2e8f0;
                    padding: 15px 5px;
                }

                .no-print {
                    position: fixed;
                    top: 16px;
                    right: 18px;
                    z-index: 100;
                    margin: 0;
                }

                .document-page {
                    margin: 0 auto 25px auto;
                    background: #fff;
                    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
                    height: auto !important;
                    min-height: 297mm;
                    max-width: 210mm;
                    width: 100%;
                    overflow: visible !important;
                    padding-bottom: 35mm !important;
                    box-sizing: border-box;
                }

                .document-page.quotation-table-page {
                    padding-left: 9.5mm !important;
                    padding-right: 9.5mm !important;
                }

                @media (max-width: 768px) {
                    .document-page {
                        padding: 25mm 10mm 35mm !important;
                    }
                }
            }

        @endif @media print {
            .no-print {
                display: none !important;
            }

            .document-page {
                margin: 0;
                box-shadow: none;
            }

            .document-page.quotation-table-page {
                padding-left: 9.5mm !important;
                padding-right: 9.5mm !important;
            }
        }
    </style>
</head>

<body>
    @php
        $lead = $quotation->lead;
        $assignedUser = $lead?->assignedUser;
        $freight = $quotation->freight;
        $paymentTerm = $freight?->termsCondition;
        $warrantyTerm = $freight?->warrantyTerm;
        $dispatchTime = $freight?->dispatch_time
            ?? $quotationProducts->first()?->productDetails?->category?->dispatch_time
            ?? 'To be confirmed';
        $subjectGroups = $quotationProducts
            ->map(fn($item) => $productGroupImages->get($item->id)['name'] ?? null)
            ->filter()
            ->unique()
            ->values();
        $subject = $subjectGroups->isNotEmpty()
            ? $subjectGroups->implode(', ')
            : $quotationProducts->map(fn($item) => $item->productDetails?->productname)->filter()->unique()->implode(', ');
        $customerAddress = $quotation->billing_address ?: ($lead?->billaddress ?: 'Address not available');
        $commercialColumns = $isTamilNadu ? 15 : 13;
        $currency = $freight?->currency ?: 'INR';
        $documentAsset = fn(string $path) => ($pdfMode ?? false) ? public_path($path) : asset($path);
        $documentPageNumber = 1;

        $activeGa = $quotation->ga_number ?: ($lead?->ganumber ?: null);
        $activePit = $quotation->pit_drawing ?: ($lead?->pit_drawing ?: null);
    @endphp

    <div class="no-print">
        <button type="button" onclick="window.print()">Print Quotation</button>
        @if($activeGa)
            <a href="{{ asset($activeGa) }}" target="_blank" class="btn-drawing-toolbar">
                📄 View GA Drawing
            </a>
        @endif
        @if($activePit)
            <a href="{{ asset($activePit) }}" target="_blank" class="btn-drawing-toolbar">
                📐 View Pit Drawing
            </a>
        @endif
    </div>

    <section class="document-page first-page">
        @include('quotations.partials.reference-page-chrome', [
            'pageNumber' => $documentPageNumber,
            'decorated' => true,
            'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
        ])

        <div class="reference-row">
            <div>REF: {{ $quotation->quota_id ?? 'N/A' }}</div>
            <div class="date">DATE: {{ optional($quotation->created_at)->format('d.m.Y') }}</div>
        </div>

        <div class="recipient">
            <p>To,</p>
            <p><strong>M/S: {{ strtoupper($lead?->LeadName ?? 'N/A') }},</strong></p>
            <p>{{ strtoupper($billingCityName ?: $customerAddress) }}.</p>
        </div>

        <div class="proposal-title">Techno Quotation for {{ $subject ?: 'the requested products' }}.</div>

        <div class="letter-copy">
            <p>Dear Sir/Madam,</p>
            <p>We sincerely thank you for your valued enquiry. Based on a detailed understanding of your requirements,
                we are pleased to submit our final techno-Quotationproposal.</p>
            <p>The contents of our offer are structured as follows:</p>
            <div class="offer-list">
                <div class="offer-list-row">Company Profile</div>
                <div class="offer-list-row">Techno Quotation offer</div>
                <div class="offer-list-row">Quotation terms &amp; conditions</div>
            </div>
            <p>We believe this proposal reflects a technically robust and commercially competitive solution that aligns
                with your expectations. This offer is based on the Mercuriis team's understanding of your requirements.
            </p>
            <p>We kindly request your team to thoroughly review the drawing &amp; offer to ensure it fully meets your
                requirements.</p>
            @if($activeGa || $activePit)
                <div class="drawing-reference-badge">
                    <strong style="color: #0b5fa5;">Attached Engineering Drawings:</strong>
                    <div style="margin-top: 1.5mm; display: flex; flex-wrap: wrap; gap: 15px;">
                        @if($activeGa)
                            <span>📄 <strong>GA Drawing:</strong> {{ basename($activeGa) }}</span>
                        @endif
                        @if($activePit)
                            <span>📐 <strong>Civil Pit Drawing:</strong> {{ basename($activePit) }}</span>
                        @endif
                    </div>
                </div>
            @endif
            <p>We look forward to the opportunity of receiving your esteemed order at the earliest.</p>
            <p>Thank you for your consideration.</p>
            <p>We remain committed to delivering exceptional service and reliable solutions at all times.</p>
        </div>

        <div class="signature">
            <p>Warm Regards,</p>
            <p><strong>For Mercuriis Storage &amp; Equipments Pvt Ltd,</strong></p>
            @if($assignedUser)
                <p style="margin-top: 14px;"><strong>{{ $assignedUser->name }}</strong></p>
                @if($assignedUser->mobilenumber)
                <p><strong>HP {{ $assignedUser->mobilenumber }}</strong></p>@endif
                @if($assignedUser->email)
                <p><a class="contact-link" href="mailto:{{ $assignedUser->email }}" style="color: #000000;">{{ $assignedUser->email }}</a></p>@endif
                <p><a class="contact-link" href="https://www.mercuriis.com" style="color: #000000;">www.mercuriis.com</a></p>
            @endif
        </div>

    </section>

    @php $documentPageNumber++; @endphp
    <section class="document-page profile-page">
        @include('quotations.partials.reference-page-chrome', [
            'pageNumber' => $documentPageNumber,
            'decorated' => $documentPageNumber % 2 === 1,
            'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
        ])

        <div class="section-heading">Company Profile: Mercuriis Storage &amp; Equipments Pvt Ltd</div>

        <div class="profile-copy">
            <p>Mercuriis is founded by 3 young experienced professional entrepreneurs with comprehensive expertise in
                the industrial and warehousing solutions sector. Our strength lies in our ability to offer strategic
                design, robust manufacturing, and precise project execution.</p>
            <p>Mercuriis promoters' experience goes beyond operational aspects of Design, Manufacturing and Project
                Management.</p>
            <p>Mercuriis thrives on Strategic Knowledge &amp; Implementation Capabilities, by applying its experience in
                Research, Development &amp; Best Practices.</p>
            <p><strong>PURPOSE</strong> is delivering <strong>Thought Leadership</strong> through effective solutions to
                our customers, thereby adding value to their time and space and helping them achieve better ROI.</p>
        </div>

        <div class="customer-heading">Mercuriis Key Customer</div>
        <img class="customer-logos" src="{{ $documentAsset('assets/images/quotation-groups/customer-logos.png') }}"
            alt="Mercuriis key customers">

    </section>

    @if($isSpecial)
        @php $documentPageNumber++; @endphp
        <section class="document-page">
            @include('quotations.partials.reference-page-chrome', [
                'pageNumber' => $documentPageNumber,
                'decorated' => $documentPageNumber % 2 === 1,
                'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
            ])

            <div class="reference-highlight">TECHNICAL SPECIFICATION</div>
            @php
                $excelHtml = null;
                $designExtension = $design?->quotation ? strtolower(pathinfo($design->quotation, PATHINFO_EXTENSION)) : null;
                if ($design?->quotation && in_array($designExtension, ['xls', 'xlsx', 'csv'], true)) {
                    $excelPath = storage_path('app/public/' . $design->quotation);
                    if (file_exists($excelPath)) {
                        $excelHtml = \App\Helpers\QuotationHelper::renderExcelAsHtml($excelPath, $quotation);
                    }
                }
            @endphp

            @if($excelHtml)
                <div class="excel-only-page">{!! $excelHtml !!}</div>
            @elseif($design?->quotation)
                <p class="muted">The approved technical quotation is available at {{ asset('storage/' . $design->quotation) }}.
                </p>
            @else
                <p class="muted">No technical quotation file is attached.</p>
            @endif
        </section>
    @else
        @php $shownGroupImages = []; @endphp
        @foreach($quotationProducts as $item)
            @php
                $product = $item->productDetails;
                $groupImage = $productGroupImages->get($item->id);
                $groupImagePath = $groupImage['path'] ?? null;
                $specifications = $product?->specifications ?? collect();
            @endphp

            @if($product && $specifications->isNotEmpty())
                @if($groupImagePath && !in_array($groupImagePath, $shownGroupImages, true))
                    @php $shownGroupImages[] = $groupImagePath; @endphp
                    @php $documentPageNumber++; @endphp
                    <section class="document-page specification-page">
                        @include('quotations.partials.reference-page-chrome', [
                            'pageNumber' => $documentPageNumber,
                            'decorated' => $documentPageNumber % 2 === 1,
                            'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
                        ])

                        <div class="specification-sheet">
                            <div class="technical-title">TECHNICAL SPECIFICATION</div>
                            <!-- <div class="product-caption">{{ $item->part_no ?: ($product->partno ?: '-') }} - {{ $product->productname }}</div> -->
                            <div class="specification-group-name">{{ $groupImage['name'] }}</div>
                            <img class="specification-feature" src="{{ $documentAsset($groupImagePath) }}"
                                alt="{{ $groupImage['name'] }} features">
                        </div>
                    </section>
                @endif

                @php $documentPageNumber++; @endphp
                <section class="document-page specification-page">
                    @include('quotations.partials.reference-page-chrome', [
                        'pageNumber' => $documentPageNumber,
                        'decorated' => $documentPageNumber % 2 === 1,
                        'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
                    ])

                    <div class="specification-sheet">
                        <div class="technical-title">TECHNICAL SPECIFICATION</div>
                        <div class="product-caption">{{ $item->part_no ?: ($product->partno ?: '-') }} -
                            {{ \App\Helpers\QuotationPresentationHelper::productGroupName($product) }}</div>
                        <table class="specification-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Unit</th>
                                    <th>Specification</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($specifications as $specification)
                                    <tr>
                                        <td>{{ $specification->description }}</td>
                                        <td>{{ $specification->unit }}</td>
                                        <td>{{ $specification->specification }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

    @php $documentPageNumber++; @endphp
    <section class="document-page commercial-page quotation-table-page">
        @include('quotations.partials.reference-page-chrome', [
            'pageNumber' => $documentPageNumber,
            'decorated' => $documentPageNumber % 2 === 1,
            'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
        ])

        @php
            $freightCharge = (float) ($freight?->freight ?? 0);
            $freightExtraCharge = (float) ($freight?->freightextra ?? 0);
            $installationCharge = (float) ($freight?->installation ?? 0);
            $commercialChargeLines = collect([
                ['label' => 'Freight Charges', 'value' => $freightCharge],
                ['label' => 'Freight Extra Charges', 'value' => $freightExtraCharge],
                ['label' => 'Installation Charges', 'value' => $installationCharge],
            ])->filter(fn($item) => (float) $item['value'] > 0);
        @endphp

        <div class="commercial-heading">Commercial</div>

        <div class="commercial-meta">
            <div><strong>Q.No:</strong> {{ $quotation->quota_id ?? 'N/A' }}</div>
            <div class="meta-center"><strong>Date:</strong> {{ optional($quotation->created_at)->format('d M Y') }}
            </div>
            <div><strong>Currency:</strong> {{ $currency }}</div>
        </div>

        <table class="products-table">
            <colgroup>
                <col style="width: 4.5%"> <!-- Sl. No -->
                <col style="width: 11.5%"> <!-- Part No -->
                <col style="width: 30.0%"> <!-- Description of Goods -->
                <col style="width: 9.0%"> <!-- HSN/SAC -->
                <col style="width: 5.0%"> <!-- Unit -->
                <col style="width: 5.0%"> <!-- Qty -->
                <col style="width: 11.5%"> <!-- List Price -->
                <col style="width: 11.5%"> <!-- Unit Price -->
                <col style="width: 12.0%"> <!-- Total Price -->
            </colgroup>
            <thead>
                <tr>
                    <th>Sl. No</th>
                    <th>Part No</th>
                    <th class="description">Description of Goods</th>
                    <th>HSN/SAC</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>List Price</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sNo = 1;
                    $basicTotal = 0;
                    $totalTaxAmount = 0;
                    $gstPercent = 18;
                @endphp
                @foreach($quotationProducts as $index => $item)
                    @php
                        $product = $item->productDetails;
                        $pRate = (float) $item->ratee;
                        $pQty = (float) $item->quanty;
                        $grossPrice = $pRate * $pQty;
                        $discPercent = (float) ($item->revised_discount ?? $item->requestdis ?? 0);
                        $discAmt = (float) ($item->revised_disamt ?? $item->requestdis_amt ?? (($grossPrice * $discPercent) / 100));
                        $pTotal = (float) ($item->lbt ?? ($grossPrice - $discAmt));
                        $basicTotal += $pTotal;

                        $listPrice = (float) ($product?->rate ?? $pRate);
                        $gstPercent = (float) ($item->tax ?? $product?->gst ?? 18);
                        $gstAmt = (float) ($item->taxamt ?? ($pTotal * $gstPercent / 100));
                        $totalTaxAmount += $gstAmt;
                        $lineGrandTotal = (float) ($item->grandtotal ?? ($pTotal + $gstAmt));
                    @endphp
                    <tr>
                        <td>{{ $sNo++ }}</td>
                        <td style="white-space: nowrap;">{{ $item->part_no ?: ($product?->partno ?: '-') }}</td>
                        <td class="description">{{ \App\Helpers\QuotationPresentationHelper::productGroupName($product) }}
                        </td>
                        <td style="white-space: nowrap;">{{ $product?->hsn ?? 'N/A' }}</td>
                        <td>{{ $product?->uomData?->name ?? 'Nos' }}</td>
                        <td>{{ $pQty }}</td>
                        <td class="amount">{{ number_format($listPrice, 2) }}</td>
                        <td class="amount">{{ number_format($pRate, 2) }}</td>
                        <td class="amount">{{ number_format($pTotal, 2) }}</td>
                    </tr>

                    @if($item->accessories && $item->accessories->count() > 0)
                        @foreach($item->accessories as $acc)
                            <tr>
                                <td>{{ $sNo++ }}</td>
                                <td>-</td>
                                <td class="description">{{ $acc->name }}</td>
                                <td>-</td>
                                <td>Nos</td>
                                <td>{{ $acc->quantity > 0 ? $acc->quantity : 1 }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                @php
                    $rawInstCharge = (float) ($quotation->installation_charge ?? 0);
                    $rawFreightCharge = (float) ($quotation->freight_charge ?? 0);
                    $rawWarrantyCharge = (float) ($quotation->additional_warranty_charge ?? 0);

                    $includeInstInBasic = ((string) ($quotation->include_installation_in_basic ?? '0') === '1');
                    $includeFreightInBasic = ((string) ($quotation->include_freight_in_basic ?? '0') === '1');
                    $includeWarrantyInBasic = ((string) ($quotation->include_warranty_in_basic ?? '0') === '1');

                    $instAddedToBasic = $includeInstInBasic ? $rawInstCharge : 0;
                    $freightAddedToBasic = $includeFreightInBasic ? $rawFreightCharge : 0;
                    $warrantyAddedToBasic = $includeWarrantyInBasic ? $rawWarrantyCharge : 0;

                    $displayedBasicTotal = $basicTotal + $instAddedToBasic + $freightAddedToBasic + $warrantyAddedToBasic;
                    $subtotalWithCharges = $basicTotal + $rawInstCharge + $rawFreightCharge + $rawWarrantyCharge;
                    $gstAmountCalc = $totalTaxAmount > 0 ? ($totalTaxAmount + (($rawInstCharge + $rawFreightCharge + $rawWarrantyCharge) * 0.18)) : ($subtotalWithCharges * 0.18);
                    $rawGrandTotalFinal = $subtotalWithCharges + $gstAmountCalc;
                    $grandTotalFinal = round($rawGrandTotalFinal);
                    $roundOffAmount = $grandTotalFinal - $rawGrandTotalFinal;
                    $hasRoundOff = abs($roundOffAmount) >= 0.005;

                    $cgstAmountCalc = $gstAmountCalc / 2;
                    $sgstAmountCalc = $gstAmountCalc / 2;

                    $leftColspan = 5;
                    $rightLabelColspan = 2;
                    $rightValueColspan = 2;
                    $rightRowCount = $isTamilNadu ? ($hasRoundOff ? 8 : 7) : ($hasRoundOff ? 7 : 6);
                    $leftRowspan = $rightRowCount;
                @endphp

                <tr>
                    <td colspan="{{ $leftColspan }}" rowspan="{{ $leftRowspan }}"
                        style="border: 0.22mm solid #9babb8; vertical-align: middle; text-align: left; padding: 2mm 2.5mm; font-size: 8.8pt; background: #fff; line-height: 1.35;">
                        <div><strong>Total Quotation Value (In Words):</strong>
                            {{ \App\Helpers\IndianNumberHelper::numberToIndianWords((float) $grandTotalFinal) }}</div>
                    </td>
                    <td colspan="{{ $rightLabelColspan }}"
                        style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 8.8pt;">
                        BASIC TOTAL</td>
                    <td colspan="{{ $rightValueColspan }}" class="amount"
                        style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 8.8pt; white-space: nowrap;">
                        {{ number_format($displayedBasicTotal, 2) }}</td>
                </tr>
                @if($isTamilNadu)
                    <tr>
                        <td colspan="{{ $rightLabelColspan }}"
                            style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                            CGST ({{ (float) ($gstPercent / 2) }}%)</td>
                        <td colspan="{{ $rightValueColspan }}" class="amount"
                            style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                            {{ number_format($cgstAmountCalc, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="{{ $rightLabelColspan }}"
                            style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                            SGST ({{ (float) ($gstPercent / 2) }}%)</td>
                        <td colspan="{{ $rightValueColspan }}" class="amount"
                            style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                            {{ number_format($sgstAmountCalc, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="{{ $rightLabelColspan }}"
                            style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                            IGST ({{ (float) $gstPercent }}%)</td>
                        <td colspan="{{ $rightValueColspan }}" class="amount"
                            style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                            {{ number_format($gstAmountCalc, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="{{ $rightLabelColspan }}"
                        style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                        Installation charges</td>
                    <td colspan="{{ $rightValueColspan }}" class="amount"
                        style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                        {{ (!$includeInstInBasic && $rawInstCharge > 0) ? number_format($rawInstCharge, 2) : '-' }}</td>
                </tr>
                <tr>
                    <td colspan="{{ $rightLabelColspan }}"
                        style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                        Freight Charges</td>
                    <td colspan="{{ $rightValueColspan }}" class="amount"
                        style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                        {{ (!$includeFreightInBasic && $rawFreightCharge > 0) ? number_format($rawFreightCharge, 2) : '-' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ $rightLabelColspan }}"
                        style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                        Additional Warranty Charges</td>
                    <td colspan="{{ $rightValueColspan }}" class="amount"
                        style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                        {{ (!$includeWarrantyInBasic && $rawWarrantyCharge > 0) ? number_format($rawWarrantyCharge, 2) : '-' }}
                    </td>
                </tr>
                @if($hasRoundOff)
                    <tr>
                        <td colspan="{{ $rightLabelColspan }}"
                            style="font-weight: bold; text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt;">
                            Round Off</td>
                        <td colspan="{{ $rightValueColspan }}" class="amount"
                            style="text-align: right; padding: 1mm 2mm; background: #fff; font-size: 9.8pt; white-space: nowrap;">
                            {{ ($roundOffAmount > 0 ? '+' : '') . number_format($roundOffAmount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="{{ $rightLabelColspan }}" class="grand-total"
                        style="font-weight: bold; text-align: right; padding: 1.5mm 2mm; font-size: 10pt;">GRAND TOTAL
                    </td>
                    <td colspan="{{ $rightValueColspan }}" class="amount grand-total"
                        style="font-weight: bold; text-align: right; padding: 1.5mm 2mm; font-size: 10pt; white-space: nowrap;">
                        INR {{ number_format($grandTotalFinal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="commercial-copy" style="margin-top: 2mm;">
            <div class="commercial-subheading" style="font-size: 11.5pt; font-weight: bold; margin: 2.8mm;">
                Notes:
            </div>
            <div class="payment-copy" style="margin: 15px; font-size: 11.2pt; line-height: 1.22;">
                @forelse($freightNoteLines ?? [] as $freightLine)
                    @continue(str_contains(strtolower($freightLine), 'none') || str_contains(strtolower($freightLine), 'nil'))
                    <div class="commercial-note-line">{{ $freightLine }}</div>
                @empty
                    <div class="commercial-note-line">Freight and installation charges are included in the Quotation
                        calculation above.</div>
                @endforelse
                <div class="commercial-note-line"><strong>Payment Terms:</strong>
                    {{ $paymentTerm?->content ?: '100% advance' }}</div>
            </div>
        </div>

        <!-- Order / Bank Details Grid -->
        <div
            style="display: table; width: 100%; margin-top: 2.5mm; border-top: 0.25mm solid #cbd5e1; padding-top: 2mm;">
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 3mm;">
                <div class="commercial-subheading blue"
                    style="font-size: 11.5pt; font-weight: bold; color: #4472c4; margin-bottom: 1mm;">Order to be
                    released to:</div>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;"><strong>Mercuriis Storage &amp;
                        Equipments Pvt Ltd,</strong></p>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;"><strong>89, Balaji Nagar,
                        Athanancheri, Padappai, Chennai - 601301, TN, India.</strong></p>
                <p style="margin: 0; font-size: 11.2pt; line-height: 1.2;"><strong>GST NO:</strong> 33AATCM0824C1ZQ</p>
            </div>

            <div
                style="display: table-cell; width: 50%; vertical-align: top; padding-left: 3mm; border-left: 0.25mm solid #cbd5e1;">
                <div class="commercial-subheading bank"
                    style="font-size: 11.5pt; font-weight: bold; color: #0070c0; text-decoration: underline; margin-bottom: 1mm;">
                    Bank Details</div>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;">Account Number : 737005000651</p>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;">Account Holder's Name : MERCURIIS
                    STORAGE AND EQUIPMENTS PRIVATE LIMITED</p>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;">Bank Name : ICICI BANK LTD</p>
                <p style="margin: 0 0 0.8mm; font-size: 11.2pt; line-height: 1.2;">IFSC Code : ICIC0007370</p>
                <p style="margin: 0; font-size: 11.2pt; line-height: 1.2;">PAN Number : AATCM0824C</p>
            </div>
        </div>

        </div>

        <!--    <div class="commercial-copy">-->
        <!--     <div class="commercial-details-grid">-->
        <!--            <div class="commercial-details-column">-->
        <!--                <div class="commercial-subheading payment">Payment Terms:</div>-->
        <!--                <p class="payment-copy">{{ $paymentTerm?->content ?: 'To be mutually agreed.' }}</p>-->

        <!--                <div class="commercial-subheading blue">Order to be released to:</div>-->
        <!--                <p><strong>Mercuriis Storage &amp; Equipments Pvt Ltd,</strong></p>-->
        <!--                <p><strong>89, Balaji Nagar, Athanancheri, Padappai, Chennai - 601301, TN, India.</strong></p>-->
        <!--                <p><strong>GST NO:</strong> 33AATCM0824C1ZQ</p>-->
        <!--            </div>-->

        <!--            <div class="commercial-details-column">-->
        <!--                <div class="commercial-subheading bank">BANK Details</div>-->
        <!--                <p>Account Number : 737005000651</p>-->
        <!--                <p>Account Holder's name : MERCURIIS STORAGE AND EQUIPMENTS PRIVATE LIMITED</p>-->
        <!--                <p>Bank Name : ICICI BANK LTD</p>-->
        <!--                <p>IFSC Code - ICIC0007370</p>-->
        <!--                <p>PAN number - AATCM0824C</p>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--</div>-->
    </section>

    @php $documentPageNumber++; @endphp
    <section class="document-page commercial-page">
        @include('quotations.partials.reference-page-chrome', [
            'pageNumber' => $documentPageNumber,
            'decorated' => $documentPageNumber % 2 === 1,
            'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
        ])

        <!-- <div class="commercial-copy"> -->


        <div class="terms-intro">
            <div class="terms-title">Terms &amp; Conditions</div>
            <p class="delivery-period"><strong><u>Lead Time:</u></strong>&nbsp;&nbsp;{{ $dispatchTime }}</p>
        </div>

        <div class="terms-copy terms-grid">
            <div class="terms">
                <p><strong>Change / cancellation:</strong> Once an order is placed on Mercuriis Storage &amp; Equipments
                    Pvt Ltd, we are not agreeable to cancellation or quantity reduction. If cancellation or quantity
                    reduction is informed before manufacturing starts, Mercuriis reserves the right to charge 10% of the
                    contract value or forfeit the advance payment. If parts or components have already been
                    manufactured, the full cost of materials will be charged.</p>

                <p><strong>Force Majeure Clause:</strong> Mercuriis shall not be responsible for delay caused by acts of
                    God, war, revolution, embargo, riots, civil commotion, political disturbances, strikes, lockouts,
                    stoppage of labour, failure in supplies, non-availability of essential raw materials such as steel,
                    or any other cause beyond our control.</p>

                <p><strong>Inspection:</strong> If required, inspection of parts may be carried out by your authorised
                    representative at your cost at our manufacturing plant when the parts or components are ready. Prior
                    notice is required so that the inspection can be arranged.</p>
            </div>

            <div class="terms">
                <p><strong>Price &amp; Site Scope:</strong> Price is ex works Mercuriis Storage &amp; Equipments Private
                    Limited. Unloading scope: {{ $unloadingScope ?: 'As agreed in the quotation.' }}</p>

                <p><strong>Performance Warranty:</strong>
                    {{ $warrantyTerm?->content ?: 'We offer a 12-month warranty from the date of supply for manufacturing and workmanship defects' }},
                    provided:</p>
                <ol>
                    <li>The equipment is used within the specified load and operational limits.</li>
                    <li>Damage due to mishandling, negligence, improper use, normal wear and tear, or unauthorised
                        modification is not covered.</li>
                </ol>

                <p>We trust this proposal is aligned with your expectations and requirements and look forward to
                    receiving your valuable order at the earliest.</p>
                <p>Thanking you and assuring you of our best services at all times.</p>

                <div class="commercial-signature">
                    <p><strong>For Mercuriis Storage &amp; Equipments Pvt Ltd,</strong></p>
                    @if($assignedUser)
                        <p><strong>{{ $assignedUser->name }}</strong></p>
                        @if($assignedUser->mobilenumber)
                        <p><strong>HP {{ $assignedUser->mobilenumber }}</strong></p>@endif
                        @if($assignedUser->email)
                            <p><a class="contact-link" href="mailto:{{ $assignedUser->email }}" style="color: #000000;">{{ $assignedUser->email }}</a>
                        </p>@endif
                        <p><a class="contact-link" href="https://www.mercuriis.com" style="color: #000000;">www.mercuriis.com</a></p>
                    @endif
                </div>
            </div>
        </div>
        <!-- </div> -->
    </section>

    @if($activeGa && preg_match('/\.(jpg|jpeg|png)$/i', $activeGa))
        @php $documentPageNumber++; @endphp
        <section class="document-page annexure-page">
            @include('quotations.partials.reference-page-chrome', [
                'pageNumber' => $documentPageNumber,
                'decorated' => true,
                'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
            ])
            <div class="page-title" style="margin-bottom: 4mm; font-size: 14pt; color: #0b5fa5; font-weight: bold; border-bottom: 2px solid #0b5fa5; padding-bottom: 2mm;">
                Annexure &mdash; General Arrangement (GA) Drawing
            </div>
            <div style="text-align: center; height: 215mm; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; padding: 4mm; background: #fafafa;">
                <img src="{{ $documentAsset($activeGa) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="GA Drawing">
            </div>
        </section>
    @endif

    @if($activePit && preg_match('/\.(jpg|jpeg|png)$/i', $activePit))
        @php $documentPageNumber++; @endphp
        <section class="document-page annexure-page">
            @include('quotations.partials.reference-page-chrome', [
                'pageNumber' => $documentPageNumber,
                'decorated' => true,
                'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
            ])
            <div class="page-title" style="margin-bottom: 4mm; font-size: 14pt; color: #0b5fa5; font-weight: bold; border-bottom: 2px solid #0b5fa5; padding-bottom: 2mm;">
                Annexure &mdash; Civil Pit Drawing
            </div>
            <div style="text-align: center; height: 215mm; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; padding: 4mm; background: #fafafa;">
                <img src="{{ $documentAsset($activePit) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Pit Drawing">
            </div>
        </section>
    @endif

    @if(($activeGa && !preg_match('/\.(jpg|jpeg|png)$/i', $activeGa)) || ($activePit && !preg_match('/\.(jpg|jpeg|png)$/i', $activePit)))
        @php $documentPageNumber++; @endphp
        <section class="document-page annexure-page">
            @include('quotations.partials.reference-page-chrome', [
                'pageNumber' => $documentPageNumber,
                'decorated' => true,
                'logoSrc' => $documentAsset('assets/images/mercuri.jpeg'),
            ])
            <div class="page-title" style="margin-bottom: 6mm; font-size: 14pt; color: #0b5fa5; font-weight: bold; border-bottom: 2px solid #0b5fa5; padding-bottom: 2mm;">
                Annexure &mdash; Engineering Drawing Documents
            </div>
            <div style="border: 1px solid #ccd; border-radius: 4px; padding: 6mm; background: #fcfcfc;">
                <p>The following technical drawings accompany this proposal and are available for review/download:</p>
                <table style="width: 100%; border-collapse: collapse; margin-top: 4mm;">
                    <thead>
                        <tr style="background: #0b5fa5; color: #fff;">
                            <th style="padding: 3mm; text-align: left;">Drawing Type</th>
                            <th style="padding: 3mm; text-align: left;">File Name</th>
                            <th style="padding: 3mm; text-align: center;">Format</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($activeGa)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 3mm;">General Arrangement (GA) Drawing</td>
                            <td style="padding: 3mm;"><strong>{{ basename($activeGa) }}</strong></td>
                            <td style="padding: 3mm; text-align: center;">{{ strtoupper(pathinfo($activeGa, PATHINFO_EXTENSION)) }}</td>
                        </tr>
                        @endif
                        @if($activePit)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 3mm;">Civil Pit Layout Drawing</td>
                            <td style="padding: 3mm;"><strong>{{ basename($activePit) }}</strong></td>
                            <td style="padding: 3mm; text-align: center;">{{ strtoupper(pathinfo($activePit, PATHINFO_EXTENSION)) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <p style="margin-top: 6mm; font-size: 9.5pt; color: #555;">Note: Digital copies may also be accessed directly via the Mercuriis CRM portal or through the provided links.</p>
            </div>
        </section>
    @endif
</body>

</html>