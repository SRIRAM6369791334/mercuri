@extends('layouts.app')
@section('title', 'Vendors Details')
@section('main-content')

    @can('purchase-order-display')
        @php
            $totalPos = count($poDetails);
            $orderLinkedPos = $poDetails->whereNotNull('orf_id')->count();
            $directPos = $totalPos - $orderLinkedPos;
            $totalAmount = $poDetails->sum('total_amount');
            $totalVariance = $poDetails->whereNotNull('orf_id')->sum('price_variance');
        @endphp

        <div class="container-fluid mt-4">
            <!-- Summary Stats Cards -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #1b5583; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Total Purchase Orders</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #1b5583;">{{ $totalPos }}</h3>
                            <span class="badge" style="background:#1b5583; color:#fff; font-size:12px;">₹{{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #28a745; border-radius: 8px; cursor:pointer;" onclick="$('#btn-filter-order-pos').trigger('click');">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Order-Linked POs</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #28a745;">{{ $orderLinkedPos }}</h3>
                            <span class="badge" style="background:#d4edda; color:#155724; font-size:12px;">Customer Orders</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #17a2b8; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Active Suppliers</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #17a2b8;">{{ $poDetails->pluck('vendor_id')->unique()->count() }}</h3>
                            <span class="badge" style="background:#d1ecf1; color:#0c5460; font-size:12px;">Vendors</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ url('/vieworfapp') }}" class="btn btn-outline-primary" style="font-weight:600; border-radius:6px;">
                        <i class="fas fa-file-contract me-1"></i> View ORF Orders
                    </a>
                    @can('purchase-order-create')
                        <a href="/create-po" class="btn" style="background:#1b5583; color:#fff; font-weight:600; border-radius:6px;">
                            <i class="fas fa-plus me-1"></i> Create PO
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eee;">
                    <div>
                        <h5 class="mb-0 font-weight-bold" style="color:#2c3e50;">
                            <i class="fas fa-shopping-cart text-primary me-2"></i>Supplier Purchase Orders
                        </h5>
                        <small class="text-muted">Track vendor purchase orders against customer orders and monitor procurement</small>
                    </div>
                    <div class="btn-group" role="group" aria-label="PO Filter">
                        <button type="button" class="btn btn-sm btn-primary po-type-filter active" id="btn-filter-all-pos" data-filter="all">
                            All POs ({{ $totalPos }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary po-type-filter" id="btn-filter-order-pos" data-filter="order">
                            <i class="fas fa-link text-success me-1"></i> Order Linked ({{ $orderLinkedPos }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary po-type-filter" id="btn-filter-direct-pos" data-filter="direct">
                            <i class="fas fa-box text-secondary me-1"></i> Direct POs ({{ $directPos }})
                        </button>
                    </div>
                </div>
                <div class="card-body details-table">
                    <table id="purchase-order-details-table" class="display table table-hover" style="width:100%;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th>S.No</th>
                                <th>PO Number</th>
                                <th>Customer Order</th>
                                <th>Vendor</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Supplier Rate</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Invoice</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($poDetails as $key => $data)
                                <tr style="text-align: center; vertical-align: middle;" data-is-order="{{ $data->orf_id ? 'yes' : 'no' }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="font-weight-bold" style="color:#1b5583;">{{ $data->po_no }}</td>
                                    <td>
                                        @if ($data->orf)
                                            <div>
                                                <span class="badge" style="background:#1b5583;color:#fff;padding:4px 8px;border-radius:4px;font-size:11px;">
                                                    <i class="fas fa-file-contract"></i> {{ $data->orf->orfref ?? ('ORF #'.$data->orf_id) }}
                                                </span>
                                                @if ($data->orf->lead)
                                                    <div style="font-size:11px;color:#555;font-weight:600;margin-top:3px;">
                                                        {{ $data->orf->lead->LeadName }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted" style="border:1px solid #ddd;font-size:11px;">Direct PO</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->allVendorDetails?->company_name ?? "N/A" }}</td>
                                    <td style="text-align: left; max-width: 180px;">
                                        <div class="text-truncate" title="{{ $data->productDetails?->productname ?? 'N/A' }}">
                                            {{ $data->productDetails?->productname ?? "N/A" }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary" style="font-size:12px;">{{ $data->product_qty }}</span>
                                    </td>
                                    <td>
                                        <span style="font-weight:600;color:#333;">
                                            ₹{{ number_format($data->unit_price ?: ($data->productDetails?->rate ?? 0), 2) }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold" style="color:#28a745;">₹{{ number_format($data->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge" style="background:#28a745;color:white;padding:5px 8px;border-radius:4px;font-size:11px;">
                                            <i class="fas fa-check-circle"></i> Released
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="/po-invoice/{{ $data->po_no }}" target="_blank">
                                                <button class="btn btn-warning btn-sm" style="padding:3px 8px;font-size:12px;">
                                                    <i class="fas fa-eye"></i> View PO
                                                </button>
                                            </a>
                                            <a href="/po-download-merged/{{ $data->po_no }}" target="_blank" class="btn btn-outline-primary btn-sm" style="padding:3px 8px;font-size:12px;" title="Download Merged PO Package (PDF)">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                    @canany(['purchase-order-edit', 'purchase-order-delete'])
                                        <td>
                                            <div class="vendor-action d-flex justify-content-center">
                                                @can('purchase-order-edit')
                                                    <a href="/edit-po-data/{{  $data->po_no }}">
                                                        <button class="btn btn-info btn-sm me-1" style="padding:3px 8px;font-size:12px;">Edit</button>
                                                    </a>
                                                @endcan
                                                @can('purchase-order-delete')
                                                    <button class="btn btn-danger btn-sm common-delete-btn" style="padding:3px 8px;font-size:12px;"
                                                        data-url="/po-delete/{{ $data->id }}">Delete</button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endcan

@endsection

@section("scripts")
    <script>
        let poTable = new DataTable('#purchase-order-details-table', {
            buttons: [
                'copy', 'excel', 'pdf'
            ],
            scrollX: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });

        $(document).on('click', '.po-type-filter', function () {
            $('.po-type-filter').removeClass('active btn-primary btn-success btn-secondary')
                .addClass('btn-outline-secondary');
            $(this).addClass('active').removeClass('btn-outline-secondary');

            let filter = $(this).data('filter');
            if (filter === 'order') {
                $(this).addClass('btn-success text-white font-weight-bold');
                poTable.column(2).search('ORF').draw();
            } else if (filter === 'direct') {
                $(this).addClass('btn-secondary text-white font-weight-bold');
                poTable.column(2).search('Direct PO').draw();
            } else {
                $(this).addClass('btn-primary text-white font-weight-bold');
                poTable.column(2).search('').draw();
            }
        });
    </script>
@endsection