@extends('layouts.app')
@section('title', 'Order PO Tracking')
@section('main-content')

    @can('purchase-order-display')

        <div class="container-fluid mt-4">
            <!-- Summary Stats Cards -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #1b5583; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Approved Orders</small>
                        <h3 class="mb-0 mt-2 font-weight-bold" style="color: #1b5583;">{{ $totalApproved }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #ffc107; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'pending']) }}';">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Pending PO</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #d39e00;">{{ $totalPending }}</h3>
                            <span class="badge bg-warning text-dark" style="font-size:10px;">Action Needed</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #28a745; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'released']) }}';">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">PO Released</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #28a745;">{{ $totalReleased }}</h3>
                            <span class="badge bg-success text-white" style="font-size:10px;">Done</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #6c757d; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Procurement Spend</small>
                        <h4 class="mb-0 mt-2 font-weight-bold text-dark">₹{{ number_format($totalProcurement, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #17a2b8; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Net Margin Tracked</small>
                        <h4 class="mb-0 mt-2 font-weight-bold" style="color: {{ $totalMargin >= 0 ? '#28a745' : '#dc3545' }};">
                            {{ $totalMargin >= 0 ? '+' : '' }}₹{{ number_format($totalMargin, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Tracking Card -->
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eee;">
                    <div>
                        <h5 class="mb-0 font-weight-bold" style="color:#2c3e50;">
                            <i class="fas fa-tasks text-primary me-2"></i>Customer Order to Supplier PO Tracking
                        </h5>
                        <small class="text-muted">Monitor whether approved customer orders have vendor POs created or pending</small>
                    </div>
                    <div class="btn-group" role="group" aria-label="Tracking Status Filter">
                        <a href="{{ route('order-po-tracking', ['status' => 'all']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            All Orders ({{ $totalApproved }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'pending']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-clock text-warning me-1"></i> Pending PO ({{ $totalPending }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'released']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'released' ? 'btn-success text-white font-weight-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-check-circle text-success me-1"></i> PO Released ({{ $totalReleased }})
                        </a>
                    </div>
                </div>

                <div class="card-body details-table">
                    <table id="order-po-tracking-table" class="display table table-hover" style="width:100%;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th>S.No</th>
                                <th>Customer Name</th>
                                <th>Order Ref (ORF)</th>
                                <th>Quotation No</th>
                                <th>Customer Quoted Amount</th>
                                <th>Supplier PO No</th>
                                <th>Supplier / Vendor</th>
                                <th>Supplier PO Amount</th>
                                <th>Price Margin / Variance</th>
                                <th>PO Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $order)
                                @php
                                    $isReleased = ($order->po_status === 'released' || !empty($order->supplier_po_no));
                                    $poList = $order->purchaseOrders;
                                    $supplierTotal = $poList->sum('total_amount');
                                    $varianceTotal = $poList->sum('price_variance');
                                    $vendorNames = $poList->map(fn($p) => $p->allVendorDetails?->company_name)->filter()->unique()->implode(', ');
                                @endphp
                                <tr style="text-align: center; vertical-align: middle;">
                                    <td>{{ $key + 1 }}</td>
                                    <td style="text-align: left; font-weight:600; color:#333;">
                                        {{ $order->lead?->LeadName ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ url('/orfdetail/view/' . $order->quoproid) }}" target="_blank" style="text-decoration:none; font-weight:600; color:#1b5583;">
                                            <span class="badge" style="background:#1b5583; color:#fff; font-size:11px;">
                                                <i class="fas fa-file-contract"></i> {{ $order->orfref ?? ('ORF #'.$order->id) }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->quono ?? '-' }}</small>
                                    </td>
                                    <td class="font-weight-bold" style="color:#1b5583;">
                                        ₹{{ number_format($order->customerTotal, 2) }}
                                    </td>
                                    <td>
                                        @if ($isReleased && $order->supplier_po_no)
                                            <a href="{{ url('/po-invoice/' . $order->supplier_po_no) }}" target="_blank" style="font-weight:600; color:#28a745; text-decoration:none;">
                                                <i class="fas fa-file-invoice"></i> {{ $order->supplier_po_no }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: left; max-width: 160px;">
                                        <span class="text-truncate d-inline-block" style="max-width:160px;" title="{{ $vendorNames ?: 'N/A' }}">
                                            {{ $vendorNames ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold" style="color:#333;">
                                        {{ $isReleased ? '₹' . number_format($supplierTotal, 2) : '-' }}
                                    </td>
                                    <td>
                                        @if ($isReleased && $poList->isNotEmpty())
                                            @if ($varianceTotal >= 0)
                                                <span style="color:#28a745; font-weight:bold;">+₹{{ number_format($varianceTotal, 2) }}</span>
                                            @else
                                                <span style="color:#dc3545; font-weight:bold;">-₹{{ number_format(abs($varianceTotal), 2) }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isReleased)
                                            <span class="badge" style="background:#28a745; color:white; padding:6px 10px; border-radius:4px; font-size:11px;">
                                                <i class="fas fa-check-circle"></i> PO Released
                                            </span>
                                        @else
                                            <span class="badge" style="background:#6c757d; color:#fff; padding:6px 10px; border-radius:4px; font-size:11px; font-weight:600;">
                                                <i class="fas fa-hourglass-half"></i> Awaiting PO
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isReleased)
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="{{ url('/po-invoice/' . $order->supplier_po_no) }}" target="_blank" class="btn btn-warning btn-sm" style="padding:3px 8px; font-size:11px;">
                                                    <i class="fas fa-eye"></i> View PO
                                                </a>
                                                <a href="{{ url('/po-download-merged/' . $order->supplier_po_no) }}" target="_blank" class="btn btn-outline-primary btn-sm" style="padding:3px 8px; font-size:11px;" title="Download Merged PO Package (PDF)">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted" style="border:1px solid #ddd; padding:4px 8px; font-size:11px;">
                                                <i class="fas fa-hourglass-half text-warning me-1"></i> Awaiting PO
                                            </span>
                                        @endif
                                    </td>
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
        let trackingTable = new DataTable('#order-po-tracking-table', {
            buttons: [
                'copy', 'excel', 'pdf'
            ],
            scrollX: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    </script>
@endsection
