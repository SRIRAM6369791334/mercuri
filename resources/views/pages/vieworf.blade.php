@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    @can('orf-approval-display')

        {{-- @include('sweetalert::alert') --}}
        <style>


            </style>

        <section class="section container-fluid">
            <div class="mt-2">
@php


$user = Auth::user();
$isSalesManager = $user->role === 'Sales Manager';

/* -------------------------------------------------
 | BASE QUERY (do not change conditions)
 ------------------------------------------------- */
$orfBaseQuery = App\Models\Orf::query();

if ($isSalesManager) {
    $orfBaseQuery
        ->join('leads', 'leads.id', '=', 'orf.leano')
        ->where('leads.assigned_to', $user->id);
}

/* -------------------------------------------------
 | COUNTS
 ------------------------------------------------- */
$orfCount = (clone $orfBaseQuery)->count();

$orfApprovedCount = (clone $orfBaseQuery)
    ->where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)
    ->count();

$orfPendingCount = (clone $orfBaseQuery)
    ->where(function ($query) {
        $query->where(function ($q) {
            $q->where('orf.approval_status', 0)
              ->where('orf.cs_status', 0);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 0)
              ->where('orf.cs_status', 1);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 1)
              ->where('orf.cs_status', 0);
        });
    })
    ->count();

/* -------------------------------------------------
 | AMOUNTS
 ------------------------------------------------- */
$orfAmountTotal = (clone $orfBaseQuery)
    ->join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->sum('quotationproduct.grandtotal');

$orfApprovedAmountTotal = (clone $orfBaseQuery)
    ->join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)
    ->sum('quotationproduct.grandtotal');

$orfPendingTotal = (clone $orfBaseQuery)
    ->join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->where(function ($query) {
        $query->orWhere(function ($q) {
            $q->where('orf.approval_status', 0)
              ->where('orf.cs_status', 0);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 0)
              ->where('orf.cs_status', 1);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 1)
              ->where('orf.cs_status', 0);
        });
    })
    ->sum('quotationproduct.grandtotal');

/* -------------------------------------------------
 | SUMMARY
 ------------------------------------------------- */
$orfSummary = [
    'orfCount' => $orfCount,
    'orfApprovedCount' => $orfApprovedCount,
    'orfPendingCount' => $orfPendingCount,
    'orfAmountTotal' => $orfAmountTotal,
    'orfApprovedAmountTotal' => $orfApprovedAmountTotal,
    'orfPendingTotal' => $orfPendingTotal,
];
@endphp

                {{-- @php

                    $orfCount = App\Models\Orf::count();
                    $orfApprovedCount = App\Models\Orf::where('approval_status', 1)->where('cs_status', 1)->count();
                    $orfPendingCount = App\Models\Orf::where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('approval_status', 0)->where('cs_status', 0);
                        })->orWhere(function ($q) {
                            $q->where('approval_status', 0)->where('cs_status', 1);
                        })->orWhere(function ($q) {
                            $q->where('approval_status', 1)->where('cs_status', 0);
                        });
                    })->count();


                    $orfAmountTotal = App\Models\Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
                        ->sum('quotationproduct.grandtotal');

                    $orfApprovedAmountTotal = App\Models\Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
                        ->where('orf.approval_status', 1)
                        ->where('orf.cs_status', 1)
                        ->sum('quotationproduct.grandtotal');

                    $orfPendingTotal = App\Models\Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
                        ->where(function ($query) {
                            $query->orWhere(function ($q) {
                                $q->where('orf.approval_status', 0)->where('orf.cs_status', 0);
                            });
                            $query->orWhere(function ($q) {
                                $q->where('orf.approval_status', 0)->where('orf.cs_status', 1);
                            });
                            $query->orWhere(function ($q) {
                                $q->where('orf.approval_status', 1)->where('orf.cs_status', 0);
                            });
                        })
                        ->sum('quotationproduct.grandtotal');

                    $orfSummary['orfCount'] = $orfCount;
                    $orfSummary['orfApprovedCount'] = $orfApprovedCount;
                    $orfSummary['orfPendingCount'] = $orfPendingCount;
                    $orfSummary['orfAmountTotal'] = $orfAmountTotal;
                    $orfSummary['orfApprovedAmountTotal'] = $orfApprovedAmountTotal;
                    $orfSummary['orfPendingTotal'] = $orfPendingTotal;
                @endphp --}}

                <div class="mt-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    Order Review Form (ORF)Sales Approval
                                </div>
                                <div class="col-lg-6">
                                    <div class="row align-items-center">
                                        <div class="col-5 text-center">
                                            <span class=" d-block text-truncate" style="color:#000;">Pending
                                                count</span>
                                        </div>
                                        <div class="col-2">
                                            <h4 class="m-0">
                                                <span style="color:#000;">{{ $orfSummary['orfPendingCount'] }}</span>
                                            </h4>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="m-0">
                                                <span
                                                    style="color:#dc3545;">₹{{ number_format($orfSummary['orfPendingTotal']) }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="statusModalLabel">Change ORF Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="orfstatus">
                                    @csrf
                                    <input type="hidden" id="orfid" name="orfid">
                                    <input type="hidden" id="cssta" name="cssta">
                                    <input type="hidden" id="sales_check_drawing_value" name="check_drawing" value="0">

                                    <div class="form-group mt-4 mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Approval Proceed <span
                                                style="color:red">*</span></label>
                                        <select class="form-select " name="approval_status" id="approval_status" required>

                                            <option value="1"> Yes </option>
                                            <option value="2"> No </option>

                                        </select>
                                    </div>

                                    <div class="form-group mt-3 mb-3" id="reject_remark_field" style="display: none;">
                                        <label for="reject_remark" class="form-label">Remarks</label>
                                        <textarea class="form-control" name="reject_remark" id="reject_remark"
                                            rows="3"></textarea>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label fw-bold">Verification Checklist</label>
                                        <div class="form-check">
                                            <input class="form-check-input approval-check" type="checkbox" id="sales_check_po" name="check_po" value="1">
                                            <label class="form-check-label" for="sales_check_po">PO verified</label>
                                        </div>
                                        <div class="form-check" id="sales_drawing_check_container">
                                            <input class="form-check-input approval-check" type="checkbox" id="sales_check_drawing">
                                            <label class="form-check-label" for="sales_check_drawing" id="sales_drawing_label">Drawing verified</label>
                                            <span class="badge bg-secondary ms-2" id="drawing_not_required_badge" style="display:none;">Drawing Not Required (Standard Product)</span>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input approval-check" type="checkbox" id="sales_check_commercial" name="check_commercial" value="1">
                                            <label class="form-check-label" for="sales_check_commercial">Commercial details verified</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="statusModalcs" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="statusModalLabel">Change ORF CS Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="orfcsstatus">
                                    @csrf
                                    <input type="hidden" id="orfidcs" name="orfid">
                                    <input type="hidden" id="appsta" name="appsta">

                                    <div class="form-group mt-4 ">
                                        <label for="exampleFormControlInput1" class="form-label">Approval Proceed <span
                                                style="color:red">*</span></label>
                                        <select class="form-select " name="cs_status" id="cs_status" required>

                                            <option value="1"> Yes </option>
                                            <option value="2"> No </option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-4 mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Dispatch Date<span
                                                style="color:red">*</span></label>
                                        <input type="date" name="dispatchdate" class="form-control" style="" placeholder="">
                                    </div>
                                    <div class="form-group mt-3 mb-3" id="csreject_remark_field" style="display: none;">
                                        <label for="csreject_remark" class="form-label">Remarks</label>
                                        <textarea class="form-control" name="csreject_remark" id="csreject_remark"
                                            rows="3"></textarea>
                                    </div>

                                    <div class="form-group mt-3 mb-3">
                                        <label class="form-label fw-bold">Verification Checklist</label>
                                        <div class="form-check d-flex align-items-center mb-2">
                                            <input class="form-check-input cs-approval-check me-2" type="checkbox" name="check_quotation" id="cs_check_quotation" value="1">
                                            <label class="form-check-label mb-0" for="cs_check_quotation">Quotation verified</label>
                                            <a href="#" id="cs_view_quotation" target="_blank" class="badge bg-primary text-white text-decoration-none ms-2" style="display:none;" title="View Quotation">View</a>
                                        </div>
                                        <div class="form-check d-flex align-items-center mb-2">
                                            <input class="form-check-input cs-approval-check me-2" type="checkbox" name="check_orf" id="cs_check_orf" value="1">
                                            <label class="form-check-label mb-0" for="cs_check_orf">ORF verified</label>
                                            <a href="#" id="cs_view_orf" target="_blank" class="badge bg-warning text-dark text-decoration-none ms-2" style="display:none;" title="View ORF Detail">View</a>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

    @endcan

@endsection

        @push('scripts')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

            <script>
                // Ensure jQuery is properly referenced
                (function ($) {
                    $(document).ready(function () {
                        // Initialize dropdowns
                        $('body').on('click', '.dropdown-toggle', function () {
                            $(this).next('.dropdown-menu').toggleClass('show');
                        });

                        // Close dropdown when clicking outside
                        $(document).on('click', function (e) {
                            if (!$(e.target).closest('.dropdown').length) {
                                $('.dropdown-menu').removeClass('show');
                            }
                        });

                        // Dynamic Drawing Checklist for Sales Status Modal
                        $('body').on('click', '.orf-status-btn, [data-bs-target="#statusModal"]', function () {
                            var orfId = $(this).data('id');
                            var csSta = $(this).data('cssta');
                            var sigDraw = $(this).data('sigdraw');

                            $('#orfid').val(orfId);
                            $('#cssta').val(csSta);

                            if (!sigDraw || sigDraw === '' || sigDraw === 'null' || sigDraw === undefined) {
                                $('#sales_check_drawing').prop('checked', true).prop('disabled', true);
                                $('#sales_check_drawing_value').val('1');
                                $('#drawing_not_required_badge').show();
                                $('#sales_drawing_label').hide();
                            } else {
                                $('#sales_check_drawing').prop('checked', false).prop('disabled', false);
                                $('#sales_check_drawing_value').val('0');
                                $('#drawing_not_required_badge').hide();
                                $('#sales_drawing_label').show();
                            }

                            $('#sales_check_drawing').off('change.checklist').on('change.checklist', function () {
                                $('#sales_check_drawing_value').val(this.checked ? '1' : '0');
                            });
                        });

                        // CS Status Modal Population
                        $('body').on('click', '.orf-csstatus-btn, [data-bs-target="#statusModalcs"]', function () {
                            var orfId = $(this).data('rid');
                            var appSta = $(this).data('appsta');
                            var checkQuo = $(this).data('checkquotation');
                            var checkOrf = $(this).data('checkorf');
                            var quoproid = $(this).data('quoproid');

                            $('#orfidcs').val(orfId);
                            $('#appsta').val(appSta);
                            $('#cs_check_quotation').prop('checked', checkQuo == 1);
                            $('#cs_check_orf').prop('checked', checkOrf == 1);

                            if (quoproid) {
                                $('#cs_view_quotation').attr('href', '/quotation/view/' + quoproid).show();
                                $('#cs_view_orf').attr('href', '/orfdetail/view/' + quoproid).show();
                            }
                        });
                    });
                })(window.jQuery);
            </script>

        @endpush
