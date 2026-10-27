<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Technical Specifications - {{ $firstPo->po_no }}</title>
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
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1b5583;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .ref-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        .item-section-title {
            background: #1b5583;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 0;
            text-transform: uppercase;
        }
        .item-info-table {
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
            background: #fdfefe;
        }
        .item-info-table td {
            padding: 4px 6px;
            font-size: 8pt;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            background: #f8fafc;
            width: 18%;
        }
        .info-val {
            font-weight: bold;
            color: #0f172a;
            width: 32%;
        }
        .specs-table {
            border: 1px solid #cbd5e1;
            margin-bottom: 14px;
        }
        .specs-table th {
            background: #e2e8f0;
            color: #1e293b;
            font-weight: bold;
            font-size: 8pt;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .specs-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 8pt;
            vertical-align: top;
        }
        .specs-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .no-specs-box {
            padding: 8px;
            font-size: 8pt;
            color: #64748b;
            font-style: italic;
            border: 1px dashed #cbd5e1;
            margin-bottom: 10px;
        }
        .footer-text {
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            margin-top: 15px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>

@php
    $poNumber = $firstPo->po_no;
    $poDate = $firstPo->created_at ? \Carbon\Carbon::parse($firstPo->created_at)->format('d-m-Y') : date('d-m-Y');
    $hideBrand = (bool)($firstPo->hide_brand_name);
@endphp

    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <h1 class="doc-title">TECHNICAL SPECIFICATIONS</h1>
                <div class="doc-subtitle">Detailed Engineering Specifications &amp; Supply Requirements</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="font-size: 9pt; font-weight: bold; color: #1b5583;">MERCURIIS STORAGE &amp; EQUIPMENTS</div>
                <div style="font-size: 7.5pt; color: #64748b;">Annexure to Purchase Order: {{ $poNumber }}</div>
            </td>
        </tr>
    </table>

    <div class="ref-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>Purchase Order Ref:</strong> <span style="color: #1b5583; font-weight: bold;">{{ $poNumber }}</span> (Date: {{ $poDate }})
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Total Line Items:</strong> {{ count($poDetails) }} Item(s)
                </td>
            </tr>
        </table>
    </div>

    @foreach ($poDetails as $idx => $item)
        @php
            $prod = $item->productDetails;
            $specs = ($prod && $prod->specifications) ? $prod->specifications : collect();
            if ($hideBrand) {
                $specs = $specs->filter(function($s) {
                    return !in_array(strtoupper(trim($s->description)), ['MAKE', 'BRAND', 'MANUFACTURER']);
                });
            }
        @endphp

        <div class="item-section-title">
            Line Item #{{ $idx + 1 }}: {{ $prod?->productname ?? 'N/A' }}
        </div>

        <table class="item-info-table">
            <tr>
                <td class="info-label">Part No / SKU:</td>
                <td class="info-val" style="color: #1b5583;">{{ $prod?->partno ?: ($prod?->sku ?: '-') }}</td>
                <td class="info-label">Category:</td>
                <td class="info-val">{{ $prod?->category?->category_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Order Quantity:</td>
                <td class="info-val">{{ $item->product_qty }} Units</td>
                <td class="info-label">HSN Code:</td>
                <td class="info-val">{{ $prod?->hsn ?: '-' }}</td>
            </tr>
            @if (!empty($item->product_description))
                <tr>
                    <td class="info-label">Description / Scope:</td>
                    <td class="info-val" colspan="3">{!! nl2br(e($item->product_description)) !!}</td>
                </tr>
            @endif
        </table>

        @if ($specs->isNotEmpty())
            <table class="specs-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">#</th>
                        <th style="width: 44%;">Parameter / Specification Attribute</th>
                        <th style="width: 35%;">Specified Value</th>
                        <th style="width: 15%; text-align: center;">Unit of Measure</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($specs as $sIdx => $spec)
                        <tr>
                            <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $sIdx + 1 }}</td>
                            <td style="font-weight: bold; color: #1e293b;">{{ $spec->description ?: 'Attribute' }}</td>
                            <td style="color: #0f172a; font-weight: 600;">{{ $spec->specification ?: '-' }}</td>
                            <td style="text-align: center; color: #475569;">{{ $spec->unit ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-specs-box">
                Standard manufacturer specification applies as per catalog model for this item.
            </div>
        @endif
    @endforeach

    <div class="footer-text">
        <strong>Mercuriis Storage &amp; Equipments Pvt Ltd</strong> | Approved Technical Specification Document | PO: {{ $poNumber }}
    </div>

</body>
</html>
