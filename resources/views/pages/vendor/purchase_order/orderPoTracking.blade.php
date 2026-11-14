@extends('layouts.app')
@section('title', 'Customer Order to Supplier PO & Advance Tracking')
@section('main-content')

    @can('purchase-order-display')

        <div class="container-fluid mt-3">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1 fw-bold" style="color: #1e293b;">
                        <i class="fas fa-shipping-fast text-primary me-2"></i>Order PO &amp; Supplier Tracking
                    </h4>
                    <p class="text-muted small mb-0">Track Customer Orders (ORF), Supplier POs, 2-Step Advance Approvals (Sir &rarr; Finance UTR), Due Dates, and Production Milestones.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchase-order-details-page') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> All Purchase Orders
                    </a>
                </div>
            </div>

            <!-- Executive KPI Cards -->
            <div class="row g-2 mb-3">
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #1b5583; border-radius: 8px;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">Tracked Orders</small>
                        <h3 class="mb-0 mt-1 fw-bold" style="color: #1b5583;">{{ $totalApproved }}</h3>
                        <small class="text-muted" style="font-size:11px;">ORF Confirmed + Direct POs</small>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #f59e0b; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'pending']) }}';">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">Pending PO</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h3 class="mb-0 fw-bold text-warning">{{ $totalPending }}</h3>
                            <span class="badge bg-warning text-dark" style="font-size:10px;">Action Needed</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Awaiting Supplier PO</small>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #10b981; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'released']) }}';">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">PO Released</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h3 class="mb-0 fw-bold text-success">{{ $totalReleased }}</h3>
                            <span class="badge bg-success text-white" style="font-size:10px;">Active</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Supplier POs Issued</small>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #e11d48; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'pending_sir']) }}';">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">Step 1: Sir Approval</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h3 class="mb-0 fw-bold text-danger">{{ $pendingSirCount }}</h3>
                            <span class="badge bg-danger text-white" style="font-size:10px;">Approval Req</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Advance Payment Approval</small>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #6366f1; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'pending_finance']) }}';">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">Step 2: Finance UTR</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h3 class="mb-0 fw-bold" style="color: #6366f1;">{{ $pendingFinanceCount }}</h3>
                            <span class="badge bg-primary text-white" style="font-size:10px;">Pay UTR</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Ready for UTR Release</small>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card shadow-sm p-3 h-100" style="border-left: 4px solid #059669; border-radius: 8px; cursor:pointer;" onclick="window.location='{{ route('order-po-tracking', ['status' => 'advance_released']) }}';">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:10px;">Advance Released</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h3 class="mb-0 fw-bold text-success">{{ $advanceReleasedCount }}</h3>
                            <span class="badge bg-success text-white" style="font-size:10px;"><i class="fas fa-check"></i></span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">UTR Documented</small>
                    </div>
                </div>
            </div>

            <!-- Financial Margin Strip -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="card shadow-sm px-3 py-2 bg-light border-0 d-flex flex-row justify-content-between align-items-center">
                        <span class="text-muted fw-semibold small"><i class="fas fa-coins text-warning me-1"></i> Total Procurement Spend:</span>
                        <span class="fw-bold fs-6 text-dark">₹{{ number_format($totalProcurement, 2) }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm px-3 py-2 bg-light border-0 d-flex flex-row justify-content-between align-items-center">
                        <span class="text-muted fw-semibold small"><i class="fas fa-chart-line text-success me-1"></i> Net Tracked Margin / Variance:</span>
                        <span class="fw-bold fs-6" style="color: {{ $totalMargin >= 0 ? '#10b981' : '#ef4444' }};">
                            {{ $totalMargin >= 0 ? '+' : '' }}₹{{ number_format($totalMargin, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Tracking Card -->
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom: 1px solid #e2e8f0;">
                    <div>
                        <h5 class="mb-0 fw-bold" style="color:#0f172a;">
                            <i class="fas fa-stream text-primary me-2"></i>Order Fulfillment &amp; Supplier Production Tracking
                        </h5>
                        <small class="text-muted">Live dashboard covering PO generation, Advance Workflow, Due Dates &amp; Milestone statuses</small>
                    </div>
                    <div class="btn-group flex-wrap" role="group" aria-label="Status Filters">
                        <a href="{{ route('order-po-tracking', ['status' => 'all']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            All Tracked Orders ({{ $totalApproved }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'pending']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-clock text-warning me-1"></i> Pending PO ({{ $totalPending }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'released']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'released' ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-check-circle me-1"></i> PO Released ({{ $totalReleased }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'pending_sir']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'pending_sir' ? 'btn-danger text-white fw-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-user-check me-1"></i> Sir Approval ({{ $pendingSirCount }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'pending_finance']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'pending_finance' ? 'btn-info text-dark fw-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-money-check-alt me-1"></i> Finance UTR ({{ $pendingFinanceCount }})
                        </a>
                        <a href="{{ route('order-po-tracking', ['status' => 'advance_released']) }}" 
                           class="btn btn-sm {{ $statusFilter == 'advance_released' ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-receipt me-1"></i> Advance Done ({{ $advanceReleasedCount }})
                        </a>
                    </div>
                </div>

                <div class="card-body details-table p-2">
                    <table id="order-po-tracking-table" class="display table table-hover table-bordered align-middle" style="width:100%; font-size: 12.5px;">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th style="width: 35px;">#</th>
                                <th style="min-width: 130px;">Customer &amp; ORF</th>
                                <th style="min-width: 120px;">Quotation &amp; Value</th>
                                <th style="min-width: 140px;">Supplier PO &amp; Branch</th>
                                <th style="min-width: 110px;">Supplier PO Amt</th>
                                <th style="min-width: 140px;">Advance Tracking</th>
                                <th style="min-width: 120px;">Committed Due Date</th>
                                <th style="min-width: 120px;">Supplier WO / Dispatch</th>
                                <th style="min-width: 110px;">Production Milestone</th>
                                <th style="min-width: 140px;">Action / Workflow</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $order)
                                @php
                                    $poList = $order->purchaseOrders;
                                    $primaryPo = $poList->first();
                                    $isCancelled = $primaryPo?->lifecycle_status === 'cancelled' || $order->po_status === 'cancelled';
                                    $isReleased = ($order->po_status === 'released' || !empty($order->supplier_po_no) || $poList->isNotEmpty());
                                    $supplierTotal = $poList->sum('total_amount')
                                        + (float) ($primaryPo?->freight_charges ?? 0)
                                        + (float) ($primaryPo?->unloading_charges ?? 0)
                                        + (float) ($primaryPo?->roundoff ?? 0);
                                    $varianceTotal = $poList->sum('price_variance');
                                    $vendorNames = $poList->map(fn($p) => $p->allVendorDetails?->company_name)->filter()->unique()->implode(', ');
                                    
                                    // Tracking details from primary PO
                                    $poNo = $order->supplier_po_no ?: ($primaryPo?->po_no ?? null);
                                    $mercuriisBranch = $primaryPo?->mercuriis_branch ?? 'Chennai';
                                    $advPercent = (float)($primaryPo?->advance_percentage ?? 0);
                                    $advAmt = (float)($primaryPo?->advance_amount ?? 0);
                                    if ($advAmt == 0 && $advPercent > 0 && $supplierTotal > 0) {
                                        $advAmt = ($supplierTotal * $advPercent) / 100;
                                    }
                                    $advStatus = $primaryPo?->advance_status ?? null;
                                    $utrNo = $primaryPo?->finance_utr_no ?? null;
                                    $dueDate = $primaryPo?->calculated_due_date ?? null;
                                    $leadTime = $primaryPo?->lead_time ?? '4 Weeks';
                                    $woNo = $primaryPo?->supplier_work_order_no ?? null;
                                    $dispDate = $primaryPo?->committed_dispatch_date ?: ($primaryPo?->supplier_dispatch_date ?? null);
                                    $actualDispatchDate = $primaryPo?->actual_dispatch_date ?? null;
                                    $milestone = $primaryPo?->production_milestone ?? ($isReleased ? 'po_issued' : 'awaiting_po');

                                    // Overdue calculation
                                    $isOverdue = false;
                                    $daysRemaining = null;
                                    if ($dueDate && $milestone !== 'delivered' && $milestone !== 'dispatched') {
                                        $dueCarbon = \Carbon\Carbon::parse($dueDate)->startOfDay();
                                        $todayCarbon = \Carbon\Carbon::now()->startOfDay();
                                        $diff = $todayCarbon->diffInDays($dueCarbon, false);
                                        $daysRemaining = (int)$diff;
                                        if ($diff < 0) {
                                            $isOverdue = true;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <!-- S.No -->
                                    <td class="text-center fw-bold">{{ $key + 1 }}</td>

                                    <!-- Customer & ORF -->
                                    <td>
                                        <div class="fw-bold text-dark">{{ !empty($order->is_direct_po) ? 'Direct Procurement' : ($order->lead?->LeadName ?? 'N/A') }}</div>
                                        @if (!empty($order->is_direct_po))
                                            <span class="badge" style="background:#64748b; color:#fff; font-size:10.5px;"><i class="fas fa-boxes"></i> Direct PO</span>
                                        @else
                                            <a href="{{ url('/orfdetail/view/' . $order->quoproid) }}" target="_blank" class="text-decoration-none">
                                                <span class="badge" style="background:#1b5583; color:#fff; font-size:10.5px;">
                                                    <i class="fas fa-file-contract"></i> {{ $order->orfref ?? ('ORF #'.$order->id) }}
                                                </span>
                                            </a>
                                        @endif
                                        @if ($order->created_at)
                                            <div class="text-muted" style="font-size:10.5px;">{{ $order->created_at->format('d-M-Y') }}</div>
                                        @endif
                                    </td>

                                    <!-- Quotation & Value -->
                                    <td>
                                        <div class="text-muted fw-semibold" style="font-size:11px;">{{ !empty($order->is_direct_po) ? 'Direct procurement' : ($order->quono ?? '-') }}</div>
                                        <div class="fw-bold text-primary" style="font-size:13px;">
                                            ₹{{ number_format($order->customerTotal, 2) }}
                                        </div>
                                    </td>

                                    <!-- Supplier PO & Branch -->
                                    <td>
                                        @if ($isCancelled)
                                            <span class="badge bg-danger text-white">Cancelled</span>
                                            @if ($poNo)
                                                <div><a href="{{ url('/po-invoice/' . $poNo) }}" target="_blank" class="fw-bold text-muted text-decoration-none">{{ $poNo }}</a></div>
                                            @endif
                                            <div class="text-truncate" style="max-width: 140px; font-size:11px; color:#475569;" title="{{ $vendorNames ?: 'N/A' }}">
                                                {{ $vendorNames ?: '-' }}
                                            </div>
                                        @elseif ($isReleased && $poNo)
                                            <a href="{{ url('/po-invoice/' . $poNo) }}" target="_blank" class="fw-bold text-success text-decoration-none">
                                                <i class="fas fa-file-invoice"></i> {{ $poNo }}
                                            </a>
                                            <div class="text-truncate" style="max-width: 140px; font-size:11px; color:#475569;" title="{{ $vendorNames ?: 'N/A' }}">
                                                {{ $vendorNames ?: '-' }}
                                            </div>
                                            <span class="badge bg-light text-dark border" style="font-size:9.5px;">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $mercuriisBranch }}
                                            </span>
                                        @else
                                            @php
                                                $orderCreatedAt = $order->created_at ?? $order->date;
                                                $pendingDays = $orderCreatedAt ? (int) \Carbon\Carbon::parse($orderCreatedAt)->startOfDay()->diffInDays(now()->startOfDay()) : 0;
                                                if ($pendingDays == 0) {
                                                    $pendingBadge = 'bg-info text-dark';
                                                    $pendingLabel = 'Pending: Today';
                                                } elseif ($pendingDays <= 3) {
                                                    $pendingBadge = 'bg-primary text-white';
                                                    $pendingLabel = "Pending: {$pendingDays}d";
                                                } elseif ($pendingDays <= 7) {
                                                    $pendingBadge = 'bg-warning text-dark';
                                                    $pendingLabel = "Pending: {$pendingDays}d";
                                                } else {
                                                    $pendingBadge = 'bg-danger text-white';
                                                    $pendingLabel = "Pending: {$pendingDays}d (Delayed)";
                                                }
                                            @endphp
                                            <span class="badge bg-secondary text-white" style="font-size:10.5px;">
                                                <i class="fas fa-hourglass-half"></i> Awaiting PO
                                            </span>
                                            <div class="mt-1">
                                                <span class="badge {{ $pendingBadge }}" style="font-size:9.5px; font-weight:600;" title="Order received {{ $pendingDays }} days ago">
                                                    <i class="fas fa-clock me-1"></i>{{ $pendingLabel }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Supplier PO Amount & Margin -->
                                    <td>
                                        @if ($isReleased)
                                            <div class="fw-bold text-dark">₹{{ number_format($supplierTotal, 2) }}</div>
                                            @if ($varianceTotal >= 0)
                                                <small class="text-success fw-bold" style="font-size:10.5px;">+₹{{ number_format($varianceTotal, 2) }}</small>
                                            @else
                                                <small class="text-danger fw-bold" style="font-size:10.5px;">-₹{{ number_format(abs($varianceTotal), 2) }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Advance Tracking (Srinath Workflow) -->
                                    <td>
                                        @if ($isReleased)
                                            @if ($advPercent > 0 && !$isCancelled)
                                                <div class="fw-semibold text-dark" style="font-size:11px;">
                                                    {{ $advPercent }}% : <span class="fw-bold text-primary">₹{{ number_format($advAmt, 2) }}</span>
                                                </div>
                                            @endif

                                            @if ($advStatus === 'pending_sir_approval')
                                                <span class="badge bg-warning text-dark mt-1" style="font-size:10px;">
                                                    <i class="fas fa-clock"></i> Pending Sir Approval
                                                </span>
                                            @elseif ($advStatus === 'approved_by_sir')
                                                <span class="badge bg-info text-dark mt-1" style="font-size:10px;">
                                                    <i class="fas fa-thumbs-up"></i> Sir Approved &rarr; Finance
                                                </span>
                                            @elseif ($advStatus === 'rejected_by_sir')
                                                <span class="badge bg-danger text-white mt-1" style="font-size:10px;">
                                                    <i class="fas fa-times-circle"></i> Rejected by Sir
                                                </span>
                                                @if ($primaryPo?->sir_remark)
                                                    <div class="text-danger fw-semibold mt-1" style="font-size:10px;" title="Sir Remarks: {{ $primaryPo->sir_remark }}">
                                                        <i class="fas fa-comment-dots me-1"></i>{{ Str::limit($primaryPo->sir_remark, 24) }}
                                                    </div>
                                                @endif
                                            @elseif ($advStatus === 'advance_released')
                                                <span class="badge bg-success text-white mt-1" style="font-size:10px;">
                                                    <i class="fas fa-check-double"></i> UTR Released
                                                </span>
                                                @if ($utrNo)
                                                    <div class="fw-bold text-muted" style="font-size:10px;" title="UTR / Ref: {{ $utrNo }}">
                                                        UTR: {{ Str::limit($utrNo, 14) }}
                                                    </div>
                                                @endif
                                            @else
                                                @if ($advPercent > 0)
                                                    <span class="badge bg-light text-muted border mt-1" style="font-size:10px;">
                                                        Awaiting Advance Req
                                                    </span>
                                                @else
                                                    <span class="text-muted small">No Advance</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Committed Due Date & Timeline -->
                                    <td>
                                        @if ($isReleased)
                                            <div class="fw-semibold text-dark" style="font-size:11px;">
                                                <i class="fas fa-stopwatch text-muted me-1"></i>{{ $leadTime }}
                                            </div>
                                            @if ($dueDate)
                                                <div class="small fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                                    {{ \Carbon\Carbon::parse($dueDate)->format('d-M-Y') }}
                                                </div>
                                                @if ($isOverdue)
                                                    <span class="badge bg-danger text-white" style="font-size:9.5px;">
                                                        <i class="fas fa-exclamation-triangle"></i> Overdue ({{ abs($daysRemaining) }}d)
                                                    </span>
                                                @elseif ($daysRemaining !== null)
                                                    <span class="badge bg-light text-primary border" style="font-size:9.5px;">
                                                        {{ $daysRemaining }} days left
                                                    </span>
                                                @endif
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Supplier Work Order & Dispatch Date -->
                                    <td>
                                        @if ($isReleased)
                                            @if ($woNo)
                                                <div class="fw-bold text-dark" style="font-size:11px;">
                                                    <i class="fas fa-receipt text-secondary me-1"></i>{{ $woNo }}
                                                </div>
                                            @else
                                                <span class="text-muted small">WO: Not Updated</span>
                                            @endif

                                            @if ($dispDate)
                                                @php
                                                    $dispCarbon = \Carbon\Carbon::parse($dispDate)->startOfDay();
                                                    $dispDiff = \Carbon\Carbon::now()->startOfDay()->diffInDays($dispCarbon, false);
                                                @endphp
                                                <div class="fw-bold mt-1 {{ $dispDiff < 0 ? 'text-danger' : ($dispDiff <= 7 ? 'text-warning' : 'text-info') }}" style="font-size:11px;">
                                                    <i class="fas fa-shipping-fast me-1"></i>{{ $dispCarbon->format('d-M-Y') }}
                                                </div>
                                                @if ($milestone !== 'dispatched' && $milestone !== 'delivered')
                                                    @if ($dispDiff < 0)
                                                        <span class="badge bg-danger text-white" style="font-size:9px;">
                                                            <i class="fas fa-exclamation-triangle"></i> Overdue ({{ abs((int)$dispDiff) }}d)
                                                        </span>
                                                    @elseif ($dispDiff <= 7)
                                                        <span class="badge bg-warning text-dark fw-bold" style="font-size:9px;">
                                                            <i class="fas fa-bell"></i> 1-Wk Alert ({{ (int)$dispDiff }}d left)
                                                        </span>
                                                    @endif
                                                    @if ($actualDispatchDate)
                                                        <div class="text-success fw-semibold mt-1" style="font-size:10.5px;">Actual: {{ \Carbon\Carbon::parse($actualDispatchDate)->format('d-M-Y') }}</div>
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Production Milestone -->
                                    <td class="text-center">
                                        @if ($isReleased)
                                            @php
                                                $milestoneBadges = [
                                                    'po_issued' => ['bg' => '#64748b', 'label' => 'PO Issued', 'icon' => 'fa-file-alt'],
                                                    'advance_pending' => ['bg' => '#d97706', 'label' => 'Adv Pending', 'icon' => 'fa-clock'],
                                                    'in_fabrication' => ['bg' => '#2563eb', 'label' => 'In Fabrication', 'icon' => 'fa-cogs'],
                                                    'ready_for_dispatch' => ['bg' => '#7c3aed', 'label' => 'Ready Dispatch', 'icon' => 'fa-box-check'],
                                                    'dispatched' => ['bg' => '#0891b2', 'label' => 'Dispatched', 'icon' => 'fa-truck'],
                                                    'delivered' => ['bg' => '#059669', 'label' => 'Delivered', 'icon' => 'fa-check-circle']
                                                ];
                                                $mInfo = $milestoneBadges[$milestone] ?? ['bg' => '#64748b', 'label' => ucfirst(str_replace('_', ' ', $milestone)), 'icon' => 'fa-circle'];
                                            @endphp
                                            <span class="badge text-white" style="background: {{ $mInfo['bg'] }}; font-size:10.5px; padding: 5px 8px;">
                                                <i class="fas {{ $mInfo['icon'] }} me-1"></i> {{ $mInfo['label'] }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white" style="font-size:10.5px;">Awaiting PO</span>
                                        @endif
                                    </td>

                                    <!-- Action / Workflow -->
                                    <td class="text-center">
                                        @if ($isReleased && $poNo)
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                
                                                <!-- Workflow Buttons based on Advance Status -->
                                                @if ($advPercent > 0)
                                                    @if (empty($advStatus) || $advStatus === 'pending_request')
                                                        <button type="button" class="btn btn-xs btn-outline-warning w-100 py-1" style="font-size:11px;"
                                                                onclick="requestAdvanceAction('{{ $poNo }}')">
                                                            <i class="fas fa-paper-plane me-1"></i> Request Adv
                                                        </button>
                                                    @elseif ($advStatus === 'rejected_by_sir')
                                                        <button type="button" class="btn btn-xs btn-outline-warning w-100 py-1" style="font-size:11px;"
                                                                onclick="requestAdvanceAction('{{ $poNo }}')">
                                                            <i class="fas fa-redo me-1"></i> Re-request Adv
                                                        </button>
                                                    @elseif ($advStatus === 'pending_sir_approval')
                                                        <button type="button" class="btn btn-xs btn-outline-danger w-100 py-1 fw-bold" style="font-size:11px;"
                                                                onclick="openSirModal('{{ $poNo }}', '{{ number_format($advAmt, 2) }}', '{{ addslashes($vendorNames) }}')">
                                                            <i class="fas fa-user-check me-1"></i> Sir Approve
                                                        </button>
                                                    @elseif ($advStatus === 'approved_by_sir')
                                                        <button type="button" class="btn btn-xs btn-outline-primary w-100 py-1 fw-bold" style="font-size:11px;"
                                                                onclick="openFinanceModal('{{ $poNo }}', '{{ number_format($advAmt, 2) }}', '{{ addslashes($vendorNames) }}')">
                                                            <i class="fas fa-rupee-sign me-1"></i> Finance UTR
                                                        </button>
                                                    @endif
                                                @endif

                                                @unless ($isCancelled)
                                                    <!-- Milestone Update Trigger -->
                                                    <button type="button" class="btn btn-xs btn-outline-secondary w-100 py-1" style="font-size:11px;"
                                                            onclick="openMilestoneModal('{{ $poNo }}', '{{ $woNo }}', '{{ $dispDate }}', '{{ $milestone }}', '{{ $actualDispatchDate }}')">
                                                        <i class="fas fa-edit me-1"></i> Update Milestone
                                                    </button>
                                                @endunless

                                                <!-- View / Download Actions -->
                                                <div class="d-flex gap-1 w-100 mt-1">
                                                    <a href="{{ url('/po-invoice/' . $poNo) }}" target="_blank" class="btn btn-xs btn-outline-dark flex-fill" style="font-size:10.5px;" title="View Purchase Order">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ url('/po-download-merged/' . $poNo) }}" target="_blank" class="btn btn-xs btn-outline-danger flex-fill" style="font-size:10.5px;" title="Download PDF Package">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </a>
                                                </div>

                                            </div>
                                        @else
                                            <a href="{{ url('/create-po?orf_id=' . $order->id) }}" class="btn btn-sm btn-primary shadow-sm" style="font-size:11.5px; padding: 4px 10px;">
                                                <i class="fas fa-plus me-1"></i> Create PO
                                            </a>
                                            <div class="text-muted mt-1" style="font-size:10px;">
                                                <i class="fas fa-history me-1"></i>{{ ($pendingDays ?? 0) == 0 ? 'Today' : ($pendingDays ?? 0) . ' ' . \Illuminate\Support\Str::plural('day', ($pendingDays ?? 0)) . ' pending' }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 1: SIR / MANAGEMENT ADVANCE APPROVAL (STEP 1)                       -->
        <!-- ========================================================================= -->
        <div class="modal fade" id="sirApprovalModal" tabindex="-1" aria-labelledby="sirApprovalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form id="sirApprovalForm">
                        @csrf
                        <input type="hidden" name="po_no" id="sir_po_no">
                        <div class="modal-header bg-danger text-white py-2">
                            <h6 class="modal-title fw-bold" id="sirApprovalModalLabel">
                                <i class="fas fa-user-shield me-2"></i>Management / Sir Advance Approval (Step 1)
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="alert alert-light border mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">PO Number</small>
                                        <span class="fw-bold text-dark fs-6" id="sir_modal_po_display">-</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted d-block">Advance Amount</small>
                                        <span class="fw-bold text-primary fs-6" id="sir_modal_adv_display">₹0.00</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <strong>Vendor:</strong> <span id="sir_modal_vendor_display">-</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Decision <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="sir_action_approve" value="approve" checked>
                                        <label class="form-check-label fw-bold text-success" for="sir_action_approve">
                                            <i class="fas fa-check-circle me-1"></i> Approve Advance &rarr; Send to Finance
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="sir_action_reject" value="reject">
                                        <label class="form-check-label fw-bold text-danger" for="sir_action_reject">
                                            <i class="fas fa-times-circle me-1"></i> Reject Request
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small">Approval Remarks / Instructions</label>
                                <textarea name="remark" id="sir_remark" class="form-control form-control-sm" rows="3" placeholder="Add approval remarks or instructions for Finance..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer py-2 bg-light">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger fw-bold" id="btnSubmitSirApproval">
                                <i class="fas fa-check me-1"></i> Confirm Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: FINANCE ADVANCE RELEASE & UTR DOCUMENTATION (STEP 2)             -->
        <!-- ========================================================================= -->
        <div class="modal fade" id="financeReleaseModal" tabindex="-1" aria-labelledby="financeReleaseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form id="financeReleaseForm">
                        @csrf
                        <input type="hidden" name="po_no" id="finance_po_no">
                        <div class="modal-header bg-primary text-white py-2">
                            <h6 class="modal-title fw-bold" id="financeReleaseModalLabel">
                                <i class="fas fa-money-check-alt me-2"></i>Finance Team: Release Advance &amp; Record UTR (Step 2)
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="alert alert-light border mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">PO Number</small>
                                        <span class="fw-bold text-dark fs-6" id="finance_modal_po_display">-</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted d-block">Approved Advance</small>
                                        <span class="fw-bold text-success fs-6" id="finance_modal_adv_display">₹0.00</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <strong>Vendor:</strong> <span id="finance_modal_vendor_display">-</span>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold small">Bank UTR / Transaction No <span class="text-danger">*</span></label>
                                    <input type="text" name="finance_utr_no" id="finance_utr_no" class="form-control form-control-sm text-uppercase fw-bold" placeholder="e.g. UTR1238947261" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold small">Payment Mode <span class="text-danger">*</span></label>
                                    <select name="payment_mode" id="finance_payment_mode" class="form-select form-select-sm" required>
                                        <option value="NEFT" selected>NEFT</option>
                                        <option value="RTGS">RTGS</option>
                                        <option value="IMPS">IMPS</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="UPI">UPI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="finance_payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small">Finance Remarks</label>
                                <textarea name="remark" id="finance_remark" class="form-control form-control-sm" rows="2" placeholder="Payment reference or bank notes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer py-2 bg-light">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSubmitFinanceRelease">
                                <i class="fas fa-check-double me-1"></i> Record Payment &amp; Release
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 3: SUPPLIER WORK ORDER, DISPATCH DATE & PRODUCTION MILESTONE        -->
        <!-- ========================================================================= -->
        <div class="modal fade" id="milestoneModal" tabindex="-1" aria-labelledby="milestoneModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form id="milestoneForm">
                        @csrf
                        <input type="hidden" name="po_no" id="milestone_po_no">
                        <div class="modal-header bg-dark text-white py-2">
                            <h6 class="modal-title fw-bold" id="milestoneModalLabel">
                                <i class="fas fa-truck-loading me-2"></i>Update Supplier Production &amp; Dispatch
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">PO Number</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="milestone_modal_po_display" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Supplier Work Order No (WO / SO)</label>
                                <input type="text" name="supplier_work_order_no" id="milestone_wo_no" class="form-control form-control-sm text-uppercase" placeholder="e.g. WO-98242">
                                <small class="text-muted" style="font-size:10.5px;">Supplier's internal acknowledgment or production order number</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Committed Dispatch / Delivery Date</label>
                                <input type="date" name="committed_dispatch_date" id="milestone_dispatch_date" class="form-control form-control-sm">
                                <label class="form-label fw-bold small mt-2">Actual Dispatch Date</label>
                                <input type="date" name="actual_dispatch_date" id="milestone_actual_dispatch_date" class="form-control form-control-sm">
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small">Production Stage / Milestone <span class="text-danger">*</span></label>
                                <select name="production_milestone" id="milestone_status" class="form-select form-select-sm" required>
                                    <option value="po_issued">PO Issued to Supplier</option>
                                    <option value="advance_pending">Awaiting Advance Release</option>
                                    <option value="in_fabrication">In Fabrication / Production</option>
                                    <option value="ready_for_dispatch">Ready for Dispatch / Inspection</option>
                                    <option value="dispatched">Dispatched by Supplier</option>
                                    <option value="delivered">Delivered &amp; Received at Site / Warehouse</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer py-2 bg-light">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success fw-bold" id="btnSubmitMilestone">
                                <i class="fas fa-save me-1"></i> Save Milestones
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endcan

@endsection

@section("scripts")
    <!-- DataTables Buttons Dependencies for Excel & PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with Excel & PDF Export
            let trackingTable = $('#order-po-tracking-table').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel text-success me-1"></i> Export to Excel',
                        className: 'btn btn-sm btn-outline-success',
                        title: 'Mercuriis_Order_PO_Supplier_Tracking_' + new Date().toISOString().slice(0, 10),
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf text-danger me-1"></i> PDF',
                        className: 'btn btn-sm btn-outline-danger',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i> Print',
                        className: 'btn btn-sm btn-outline-secondary'
                    }
                ],
                scrollX: true,
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search orders, PO, vendor..."
                }
            });

            // 1. Advance Request Action
            window.requestAdvanceAction = function(poNo) {
                if (!confirm("Are you sure you want to request Advance Approval from Sir / Management for PO #" + poNo + "?")) {
                    return;
                }
                $.ajax({
                    url: "{{ route('po.request-advance') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        po_no: poNo
                    },
                    success: function(res) {
                        alert(res.message || "Advance approval request submitted successfully.");
                        window.location.reload();
                    },
                    error: function(err) {
                        alert(err.responseJSON?.message || "Failed to submit advance request.");
                    }
                });
            };

            // 2. Open Sir Approval Modal
            window.openSirModal = function(poNo, advAmt, vendorName) {
                $('#sir_po_no').val(poNo);
                $('#sir_modal_po_display').text(poNo);
                $('#sir_modal_adv_display').text('₹' + advAmt);
                $('#sir_modal_vendor_display').text(vendorName || 'N/A');
                $('#sir_remark').val('');
                let modal = new bootstrap.Modal(document.getElementById('sirApprovalModal'));
                modal.show();
            };

            // Submit Sir Approval
            $('#sirApprovalForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSubmitSirApproval');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

                $.ajax({
                    url: "{{ route('po.sir-approval') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message || "Sir decision recorded successfully.");
                        window.location.reload();
                    },
                    error: function(err) {
                        alert(err.responseJSON?.message || "Failed to record approval.");
                        $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Confirm Decision');
                    }
                });
            });

            // 3. Open Finance Release Modal
            window.openFinanceModal = function(poNo, advAmt, vendorName) {
                $('#finance_po_no').val(poNo);
                $('#finance_modal_po_display').text(poNo);
                $('#finance_modal_adv_display').text('₹' + advAmt);
                $('#finance_modal_vendor_display').text(vendorName || 'N/A');
                $('#finance_utr_no').val('');
                $('#finance_remark').val('');
                let modal = new bootstrap.Modal(document.getElementById('financeReleaseModal'));
                modal.show();
            };

            // Submit Finance Release
            $('#financeReleaseForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSubmitFinanceRelease');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Recording...');

                $.ajax({
                    url: "{{ route('po.finance-release') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message || "Advance payment released with UTR reference.");
                        window.location.reload();
                    },
                    error: function(err) {
                        alert(err.responseJSON?.message || "Failed to record payment release.");
                        $btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Record Payment & Release');
                    }
                });
            });

            // 4. Open Milestone Modal
            window.openMilestoneModal = function(poNo, woNo, dispDate, milestone, actualDispatchDate) {
                $('#milestone_po_no').val(poNo);
                $('#milestone_modal_po_display').val(poNo);
                $('#milestone_wo_no').val(woNo || '');
                $('#milestone_dispatch_date').val(dispDate || '');
                $('#milestone_actual_dispatch_date').val(actualDispatchDate || '');
                if (milestone) {
                    $('#milestone_status').val(milestone === 'awaiting_advance' ? 'advance_pending' : milestone);
                }
                let modal = new bootstrap.Modal(document.getElementById('milestoneModal'));
                modal.show();
            };

            // Submit Milestone Update
            $('#milestoneForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSubmitMilestone');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: "{{ route('po.update-milestone') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message || "Production milestones updated successfully.");
                        window.location.reload();
                    },
                    error: function(err) {
                        alert(err.responseJSON?.message || "Failed to update milestone.");
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Milestones');
                    }
                });
            });

        });
    </script>
@endsection