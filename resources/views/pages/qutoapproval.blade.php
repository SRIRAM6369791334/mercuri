@extends('layouts.app')
@section('title', 'Quotation Approval')
@section('main-content')

    <style>
        .card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            background: #fff;
        }
        .card-header {
            background: #f8fafc;
            font-weight: 700;
            font-size: 14px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 18px;
        }
        #qutap-table thead th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-weight: 600;
            font-size: 12.5px;
            white-space: nowrap;
            position: static !important;
            vertical-align: middle;
            text-align: center;
            border-bottom: 2px solid #cbd5e1 !important;
        }
        #qutap-table tbody td {
            font-size: 12.5px;
            vertical-align: middle;
        }
        .sticky-table-header {
            display: none !important;
        }
    </style>

    <section class="section leadsection container">
        <div class="form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Quotation Approval</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">All Quotation Approvals</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="qutap-table" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Quotation No</th>
                                <th>Customer Name</th>
                                <th>Category</th>
                                <th>Quotation</th>
                                <th>Terms Approval</th>
                                <th>Freight Approval</th>
                                <th>Charge Approval</th>
                                <th>Discount Approval</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $key => $quotation)
                                @php
                                    $pendingTermRequests = $quotation->termChangeRequests->where('status', 'Pending Approval');
                                    $hasFreightPending = ($quotation->freight_approval == 1 || optional($quotation->freight)->freight_approve == 1);
                                    $hasChargePending = ($quotation->charge_approval_status === 'Pending Approval');
                                    $hasDiscountPending = ($quotation->dis_app == 1);
                                @endphp
                                <tr>
                                    <td class="text-center text-muted">{{ $key + 1 }}</td>
                                    <td><strong>{{ $quotation->quota_id }}</strong></td>
                                    <td class="customer-col">{{ optional($quotation->lead)->LeadName ?? optional($quotation->lead)->CustomerName ?? '-' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ optional($quotation->lead?->categoryDetails)->category_name ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ url('/quotation/view/' . $quotation->id) }}" target="_blank"
                                            class="btn btn-primary btn-action-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                    
                                    {{-- Terms & Warranty Approval Column --}}
                                    <td class="text-center">
                                        @if($pendingTermRequests->count() > 0)
                                            @foreach($pendingTermRequests as $termReq)
                                                <div class="mb-1">
                                                    <span class="badge bg-info text-dark" style="font-size: 10px;">
                                                        {{ $termReq->type === 'payment_term' ? 'Payment Term' : 'Warranty' }}
                                                    </span>
                                                    <div class="mt-1">
                                                        <button type="button" class="btn btn-success btn-action-sm"
                                                            data-bs-toggle="modal" data-bs-target="#termApprovalModal{{ $termReq->id }}">
                                                            Review
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif(optional($quotation->freight?->termsCondition)->term_approve == 1 || optional($quotation->freight?->warrantyTerm)->term_approve == 1)
                                            <button id="terms_approval_btn" class="btn btn-success btn-action-sm"
                                                data-terms_id="{{ $quotation->freight?->termscondition }}"
                                                data-warrenty_id="{{ $quotation->freight?->warranty }}"
                                                data-term_content="{{ optional($quotation->freight?->termsCondition)->content }}"
                                                data-warranty_content="{{ optional($quotation->freight?->warrantyTerm)->content }}"
                                                data-bs-toggle="modal" data-bs-target="#terms_approval_modal">
                                                Approve
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Freight Approval Column --}}
                                    <td class="text-center">
                                        @if($hasFreightPending)
                                            <div class="small fw-semibold text-dark mb-1" style="font-size: 11.5px;">
                                                {{ optional($quotation->freight?->freightCategory)->categoty ?? 'Mercuriis Scope' }}
                                                @if(optional($quotation->freight?->freightSubCategory)->sub_category)
                                                    <span class="badge bg-secondary" style="font-size: 10px;">{{ $quotation->freight->freightSubCategory->sub_category }}</span>
                                                @endif
                                            </div>
                                            <button class="btn btn-success btn-action-sm approvefre-btn"
                                                data-freight_id="{{ optional($quotation->freight)->id }}"
                                                data-cat="{{ optional($quotation->freight?->freightCategory)->categoty ?? 'Mercuriis Scope' }}"
                                                data-subcat="{{ optional($quotation->freight?->freightSubCategory)->sub_category ?? '' }}"
                                                data-detail="{{ optional($quotation->freight?->freightDetail)->details ?? '' }}"
                                                data-bs-toggle="modal" data-bs-target="#freiapproval">
                                                Approve
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Charges Approval Column (Installation & Freight) --}}
                                    <td class="text-center">
                                        @if($hasChargePending)
                                            <div class="charge-box">
                                                <div><strong>Inst:</strong> ₹{{ number_format($quotation->installation_charge, 0) }}
                                                    @if($quotation->installation_charge != $quotation->default_installation_charge)
                                                        <span class="text-danger small">(Def: ₹{{ number_format($quotation->default_installation_charge, 0) }})</span>
                                                    @endif
                                                </div>
                                                <div><strong>Freight:</strong> ₹{{ number_format($quotation->freight_charge, 0) }}
                                                    @if($quotation->freight_charge != $quotation->default_freight_charge)
                                                        <span class="text-danger small">(Def: ₹{{ number_format($quotation->default_freight_charge, 0) }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-success btn-action-sm"
                                                    data-bs-toggle="modal" data-bs-target="#chargeApprovalModal{{ $quotation->id }}">
                                                    Review
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Discount Approval Column --}}
                                    <td class="text-center">
                                        @if($hasDiscountPending)
                                            @php
                                                $reqDiscountVal = (float) $quotation->effective_requested_discount;
                                                $displayReqDiscount = ($reqDiscountVal == (int)$reqDiscountVal) ? (int)$reqDiscountVal : number_format($reqDiscountVal, 2);
                                            @endphp
                                            <div class="small fw-bold text-danger mb-1" style="font-size: 11.5px;">
                                                Requested: {{ $displayReqDiscount }}%
                                            </div>
                                            <button class="btn btn-success btn-action-sm approvedis-btn"
                                                data-reqdisc="{{ $displayReqDiscount }}"
                                                data-id="{{ $quotation->id }}"
                                                data-bs-toggle="modal" data-bs-target="#discapproval">
                                                Approve
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Overall Status Column --}}
                                    <td class="text-center">
                                        @if($quotation->quto_app == 2)
                                            <span class="badge bg-success" style="font-size: 11px;">Approved</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 11px;">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">No quotations are currently awaiting approval.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== MODALS ==================== --}}

    {{-- 1. Discount Approval Modal --}}
    <div class="modal fade" id="discapproval" tabindex="-1" aria-labelledby="discapprovalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discapprovalLabel">Proceed with Discount Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="quot_pro_id">

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Requested Discount %</label>
                        <input type="text" class="form-control requested_percentage_input" readonly>
                    </div>
                    <div class="form-group mb-3" id="approved-discount-group">
                        <label for="revised_discount" class="form-label fw-bold">Approved / Updated Discount %</label>
                        <div class="input-group">
                            <input type="number" id="revised_discount" name="revisedDiscount" class="form-control" step="0.01" min="0" max="100" placeholder="Enter approved discount %">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">You can adjust or accept the requested discount percentage.</small>
                    </div>
                    <div class="mb-3">
                        <a href="#" target="_blank" id="view-discount-quotation" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Quotation
                        </a>
                    </div>

                    <div class="form-group mb-3">
                        <label for="disc_status" class="form-label fw-bold">Decision</label>
                        <select id="disc_status" class="form-select">
                            <option value="yes">Approve</option>
                            <option value="no">Not Approve (Reject)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="remarks-group" style="display: none;">
                        <label for="remarks" class="form-label fw-bold">Remarks <span class="text-danger">*</span></label>
                        <textarea id="remarks" name="disremark" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="disc-approval">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Freight Approval Modal --}}
    <div class="modal fade" id="freiapproval" tabindex="-1" aria-labelledby="freiapprovalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="freiapprovalLabel">Proceed with Freight Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="freight-lead-id">

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Freight / Transportation</label>
                        <input type="text" class="form-control freight" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Included or Extra</label>
                        <input type="text" class="form-control included" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Details</label>
                        <input type="text" class="form-control extra" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="fre_status" class="form-label fw-bold">Decision</label>
                        <select id="fre_status" class="form-select">
                            <option value="yes">Approve</option>
                            <option value="no">Not Approve (Reject)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="freremarks-group" style="display: none;">
                        <label for="freremarks" class="form-label fw-bold">Remarks <span class="text-danger">*</span></label>
                        <textarea id="freremarks" name="freremark" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="fre-approval">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Terms & Warranty Change Requests Modals (Per Request) --}}
    @foreach($quotations as $quotation)
        @foreach($quotation->termChangeRequests->where('status', 'Pending Approval') as $termReq)
            <div class="modal fade" id="termApprovalModal{{ $termReq->id }}" tabindex="-1" aria-labelledby="termApprovalModalLabel{{ $termReq->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="termApprovalModalLabel{{ $termReq->id }}">
                                Review {{ $termReq->type === 'payment_term' ? 'Payment Term' : 'Warranty' }} Request ({{ $quotation->quota_id }})
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Customer</label>
                                <div class="fw-semibold">{{ optional($quotation->lead)->LeadName ?? '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Old Term Value</label>
                                <div class="p-2 bg-light rounded small">{{ optional($termReq->oldTerm)->content ?? 'None / Standard' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Requested New Term Value</label>
                                <div class="p-2 border border-primary bg-light rounded small fw-bold text-dark">{{ optional($termReq->newTerm)->content ?? 'None' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Requested By</label>
                                <div>{{ optional($termReq->requester)->name ?? 'Sales User' }}</div>
                            </div>

                            <form id="termRejectForm{{ $termReq->id }}" method="post" action="{{ route('quotation.terms.decide', $termReq->id) }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="decision" value="Rejected">
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Rejection Remarks (Required only if rejecting)</label>
                                    <textarea class="form-control form-control-sm" name="remarks" rows="2" placeholder="Reason for rejection..."></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="termRejectForm{{ $termReq->id }}" class="btn btn-danger btn-sm">Reject</button>
                            <form method="post" action="{{ route('quotation.terms.decide', $termReq->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="decision" value="Approved">
                                <button type="submit" class="btn btn-success btn-sm">Approve Term</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    {{-- 4. Charge Approval Modals (Per Quotation) --}}
    @foreach($quotations as $quotation)
        @if($quotation->charge_approval_status === 'Pending Approval')
            <div class="modal fade" id="chargeApprovalModal{{ $quotation->id }}" tabindex="-1" aria-labelledby="chargeApprovalModalLabel{{ $quotation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chargeApprovalModalLabel{{ $quotation->id }}">
                                Review Charges ({{ $quotation->quota_id }})
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Customer</label>
                                <div class="fw-semibold">{{ optional($quotation->lead)->LeadName ?? '-' }}</div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="small text-muted">Installation Charge</div>
                                        <div class="fw-bold text-primary fs-6">₹{{ number_format($quotation->installation_charge, 2) }}</div>
                                        <div class="small text-muted">Default: ₹{{ number_format($quotation->default_installation_charge, 2) }}</div>
                                        @if($quotation->include_installation_in_basic)
                                            <span class="badge bg-info mt-1">Included in Basic</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="small text-muted">Freight Charge</div>
                                        <div class="fw-bold text-primary fs-6">₹{{ number_format($quotation->freight_charge, 2) }}</div>
                                        <div class="small text-muted">Default: ₹{{ number_format($quotation->default_freight_charge, 2) }}</div>
                                        @if($quotation->include_freight_in_basic)
                                            <span class="badge bg-info mt-1">Included in Basic</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <form id="chargeRejectForm{{ $quotation->id }}" method="post" action="{{ route('quotation.charges.decide', $quotation->id) }}">
                                @csrf
                                <input type="hidden" name="decision" value="Rejected">
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Correction Remarks (Required only if rejecting)</label>
                                    <textarea class="form-control form-control-sm" name="remarks" rows="2" placeholder="Enter correction remarks for sales team..."></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="chargeRejectForm{{ $quotation->id }}" class="btn btn-danger btn-sm">Reject & Return</button>
                            <form method="post" action="{{ route('quotation.charges.decide', $quotation->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="decision" value="Approved">
                                <button type="submit" class="btn btn-success btn-sm">Approve Charges</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- 5. Standard Term Modal --}}
    <div class="modal fade" id="terms_approval_modal" tabindex="-1" aria-labelledby="terms_approval_modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="terms_approval_modalLabel">Proceed with Terms Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="hidden_terms_id">
                    <input type="hidden" id="hidden_warrenty_id">

                    <label class="form-label mt-2">Payment Terms Content</label>
                    <textarea id="payment_terms" name="payment_terms" class="form-control" rows="3" readonly></textarea>

                    <label class="form-label mt-3">Warranty Terms</label>
                    <textarea id="warrenty_terms" name="warrenty_terms" class="form-control" rows="3" readonly></textarea>

                    <div class="form-group mt-3">
                        <label for="terms_status">Select Option</label>
                        <select id="terms_status" class="form-select">
                            <option value="yes">Approve</option>
                            <option value="no">Not Approve</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="terms_approval">Proceed</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")
    <script>
        $(document).ready(function () {
            if ($('#qutap-table').length) {
                $('#qutap-table').DataTable({
                    order: [[0, 'asc']]
                });
            }

            // Discount modal trigger
            $(document).on('click', '.approvedis-btn', function () {
                let quotProId = $(this).data('id');
                let reqdisc = $(this).data("reqdisc");
                let rawDisc = String(reqdisc || '').replace('%', '').trim();

                $("#quot_pro_id").val(quotProId);
                $(".requested_percentage_input").val(reqdisc + '%');
                $("#revised_discount").val(rawDisc);
                $("#view-discount-quotation").attr('href', `/quotation/view/${quotProId}`);
                
                $('#disc_status').val('yes').trigger('change');
                $('#discapproval').modal('show');
            });

            $('#disc_status').on('change', function () {
                if ($(this).val() === 'no') {
                    $('#remarks-group').show();
                    $('#remarks').attr('required', true);
                    $('#approved-discount-group').hide();
                } else {
                    $('#remarks-group').hide();
                    $('#remarks').removeAttr('required').val('');
                    $('#approved-discount-group').show();
                }
            });

            // Discount AJAX Submission
            $('#disc-approval').click(function () {
                let quotProId = $("#quot_pro_id").val();
                let qutoStatus = $('#disc_status').val();
                let remarks = $('#remarks').val();
                let revisedDiscount = $('#revised_discount').val();
                let submitButton = $(this);

                if (qutoStatus === 'no' && remarks.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remarks Required',
                        text: 'Please enter remarks before proceeding.'
                    });
                    return;
                }

                $.ajax({
                    url: `/approve-disc/${quotProId}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        disc_status: qutoStatus,
                        remarks: qutoStatus === 'no' ? remarks : '',
                        revisedDiscount: qutoStatus === 'yes' ? revisedDiscount : ''
                    },
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...
                        `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).html("Proceed");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Discount decision saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#discapproval').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        submitButton.prop('disabled', false).html("Proceed");
                        let errorMsg = 'Failed to process discount approval.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });

            // Freight modal trigger
            $(document).on('click', '.approvefre-btn', function () {
                let freightId = $(this).data('freight_id');
                let cat = $(this).data("cat");
                let subcat = $(this).data("subcat");
                let detail = $(this).data("detail");

                $('#freight-lead-id').val(freightId);
                $(".freight").val(cat);
                $(".included").val(subcat);
                $(".extra").val(detail);

                $('#freiapproval').modal('show');
            });

            $('#fre_status').on('change', function () {
                if ($(this).val() === 'no') {
                    $('#freremarks-group').show();
                    $('#freremarks').attr('required', true);
                } else {
                    $('#freremarks-group').hide();
                    $('#freremarks').removeAttr('required').val('');
                }
            });

            // Freight AJAX Submission
            $('#fre-approval').click(function () {
                let freightId = $('#freight-lead-id').val();
                let fre_status = $('#fre_status').val();
                let remarks = $('#freremarks').val();
                let freight = $(".freight").val();
                let included = $(".included").val();
                let extra = $(".extra").val();
                let submitButton = $(this);

                if (fre_status === 'no' && remarks.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remarks Required',
                        text: 'Please enter remarks before proceeding.'
                    });
                    return;
                }

                $.ajax({
                    url: `/approve-fre/${freightId}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        fre_status: fre_status,
                        remarks: fre_status === 'no' ? remarks : '',
                        freight: freight,
                        included: included,
                        extra: extra
                    },
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...
                        `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).html("Proceed");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Freight approval updated.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#freiapproval').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        submitButton.prop('disabled', false).html("Proceed");
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process freight approval.'
                        });
                    }
                });
            });

            // Standard Terms trigger
            $(document).on('click', '#terms_approval_btn', function () {
                let terms_id = $(this).data('terms_id');
                let warrenty_id = $(this).data("warrenty_id");
                let term_content = $(this).data("term_content");
                let warranty_content = $(this).data("warranty_content");

                $('#hidden_terms_id').val(terms_id);
                $("#hidden_warrenty_id").val(warrenty_id);
                $("#payment_terms").val(term_content);
                $("#warrenty_terms").val(warranty_content);

                $('#terms_approval_modal').modal('show');
            });

            $('#terms_approval').click(function () {
                let terms_id = $('#hidden_terms_id').val();
                let warrenty_id = $('#hidden_warrenty_id').val();
                let term_content = $('#payment_terms').val();
                let warranty_content = $("#warrenty_terms").val();
                let terms_status = $("#terms_status").val();
                let submitButton = $(this);

                $.ajax({
                    url: `/approve-terms-ajax`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        terms_id: terms_id,
                        warrenty_id: warrenty_id,
                        term_content: term_content,
                        warranty_content: warranty_content,
                        terms_status: terms_status
                    },
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...
                        `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).html("Proceed");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Terms approved.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#terms_approval_modal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        submitButton.prop('disabled', false).html("Proceed");
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to approve terms.'
                        });
                    }
                });
            });
        });
    </script>
@endsection

