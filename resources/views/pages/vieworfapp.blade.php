@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    <section class="section container-fluid">

        {{-- @php

            $orfApprovedCount = App\Models\Orf::where('approval_status', 1)->where('cs_status', 1)->count();

            $orfApprovedAmountTotal = App\Models\Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
                ->where('orf.approval_status', 1)
                ->where('orf.cs_status', 1)
                ->sum('quotationproduct.grandtotal');

            $orfSummary['orfApprovedCount'] = $orfApprovedCount;
            $orfSummary['orfApprovedAmountTotal'] = $orfApprovedAmountTotal;
        @endphp --}}
        @php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$role = $user->role;

/* -------------------------------------------------
 | BASE QUERY
 ------------------------------------------------- */
$orfBaseQuery = App\Models\Orf::query()
    ->where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)
    ->join('leads', 'orf.leano', '=', 'leads.id')
    ->join('product_categories_tb as cate', 'cate.id', '=', 'leads.category');

/* -------------------------------------------------
 | ROLE BASED FILTER
 ------------------------------------------------- */
// if ($role === 'Design Engineer') {
//     $orfBaseQuery->where('cate.sub_category', 'special');
// }
if ($role === 'Design Engineer') {
    $orfBaseQuery->where('cate.sub_category', 'special');
}

// 🔹 Sales Manager → Only their own leads
elseif ($role === 'Sales Manager') {
    $orfBaseQuery->where('leads.assigned_to', $user->id);
}
/* -------------------------------------------------
 | COUNT & AMOUNTS
 ------------------------------------------------- */
$orfApprovedCount = (clone $orfBaseQuery)->count();
$orfPendingPoCount = (clone $orfBaseQuery)->where(function($q) {
    $q->whereNull('orf.po_status')->orWhere('orf.po_status', '!=', 'released');
})->count();
$orfReleasedPoCount = (clone $orfBaseQuery)->where('orf.po_status', 'released')->count();

/* -------------------------------------------------
 | TOTAL AMOUNT
 ------------------------------------------------- */
$orfApprovedAmountTotal = (clone $orfBaseQuery)
    ->join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->sum('quotationproduct.grandtotal');

/* -------------------------------------------------
 | SUMMARY ARRAY
 ------------------------------------------------- */
$orfSummary = [
    'orfApprovedCount' => $orfApprovedCount,
    'orfPendingPoCount' => $orfPendingPoCount,
    'orfReleasedPoCount' => $orfReleasedPoCount,
    'orfApprovedAmountTotal' => $orfApprovedAmountTotal,
];
@endphp


        <div class="container-fluid mt-4">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #1b5583; border-radius: 8px;">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Total Approved Orders</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #1b5583;">{{ $orfSummary['orfApprovedCount'] }}</h3>
                            <span class="badge" style="background:#1b5583; color:#fff; font-size:12px;">₹{{ number_format($orfSummary['orfApprovedAmountTotal']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #ffc107; border-radius: 8px; cursor:pointer;" onclick="$('#btn-filter-pending').trigger('click');">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Pending Supplier PO</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #d39e00;">{{ $orfSummary['orfPendingPoCount'] }}</h3>
                            <span class="badge" style="background:#fff3cd; color:#856404; font-size:12px; border:1px solid #ffeeba;">
                                <i class="fas fa-clock"></i> Action Required
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm p-3" style="border-left: 4px solid #28a745; border-radius: 8px; cursor:pointer;" onclick="$('#btn-filter-released').trigger('click');">
                        <small class="text-muted text-uppercase" style="font-size:11px; font-weight:600;">Supplier PO Released</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 font-weight-bold" style="color: #28a745;">{{ $orfSummary['orfReleasedPoCount'] }}</h3>
                            <span class="badge" style="background:#d4edda; color:#155724; font-size:12px; border:1px solid #c3e6cb;">
                                <i class="fas fa-check-circle"></i> PO Released
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <a href="{{ url('/purchase-order-details') }}" class="btn" style="background:#1b5583; color:white; border-radius:6px; font-weight:600;">
                        <i class="fas fa-boxes me-1"></i> Supplier PO Details
                    </a>
                </div>
            </div>

            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eee;">
                    <div>
                        <h5 class="mb-0 font-weight-bold" style="color:#2c3e50;">
                            <i class="fas fa-file-contract text-primary me-2"></i>Order Review Form (ORF) & Supplier PO Status
                        </h5>
                        <small class="text-muted">Track approved customer orders and their corresponding vendor purchase orders</small>
                    </div>
                    <div class="btn-group" role="group" aria-label="PO Filter">
                        <button type="button" class="btn btn-sm btn-primary po-filter-btn active" id="btn-filter-all" data-filter="all">
                            All Orders ({{ $orfSummary['orfApprovedCount'] }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary po-filter-btn" id="btn-filter-pending" data-filter="pending">
                            <i class="fas fa-clock text-warning me-1"></i> Pending PO ({{ $orfSummary['orfPendingPoCount'] }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary po-filter-btn" id="btn-filter-released" data-filter="released">
                            <i class="fas fa-check-circle text-success me-1"></i> PO Released ({{ $orfSummary['orfReleasedPoCount'] }})
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>

<div class="modal fade" id="attachmentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Attachments</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>File</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="attachmentsTable">
                        <tr>
                            <td colspan="3" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $(document).on('click', '.po-filter-btn', function () {
                    $('.po-filter-btn').removeClass('active btn-primary btn-warning btn-success')
                        .addClass('btn-outline-secondary');
                    $(this).addClass('active').removeClass('btn-outline-secondary');

                    var filter = $(this).data('filter');
                    var dt = window.LaravelDataTables ? window.LaravelDataTables['orf'] : null;
                    if (!dt) return;

                    if (filter === 'pending') {
                        $(this).addClass('btn-warning text-dark font-weight-bold');
                        dt.column(6).search('Pending').draw();
                    } else if (filter === 'released') {
                        $(this).addClass('btn-success text-white font-weight-bold');
                        dt.column(6).search('Released').draw();
                    } else {
                        $(this).addClass('btn-primary text-white font-weight-bold');
                        dt.column(6).search('').draw();
                    }
                });
            });
        </script>
    @endpush
