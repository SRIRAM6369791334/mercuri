@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
    <style>
        .quota-card {
            padding: 5px 10px;
            font-size: 16px;
        }
        .quotation-section label,
.quotation-section b {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.quotation-section .form-select {
    font-size: 12.5px;
    padding: 6px 10px;
    border-radius: 6px;
}
.form-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 16px;
}

.card {
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

.card-header {
    background: #f9fafb;
    font-size: 13px;
    font-weight: 700;
    padding: 12px 16px;
}
.quotation-section .row.align-items-center > .col-lg-4 {
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 10px;
    margin-bottom: 10px;
}

.quotation-section h4 {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
}

.quotation-section span {
    font-size: 12px;
}

    </style>
    @if(request('next') && request('final'))
        <script>
            window.onload = function () {

                window.location.assign("{{ request('next') }}?final={{ urlencode(request('final')) }}");
            };
        </script>

    @endif


    @can('quotation-display')

        <section class="section quotation-section container-fluid">
            <div class="form-card">

                <div class="row mt-4">
                    <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Product Segment: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="lead_cat_filter" name="catego" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\ProductCategory::get() as $data)
                                        <option value="{{ $data->id }}" data-cat="{{ $data->category_name }}">
                                            {{ $data->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Product Group: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="quotation_pro_group_filter" name="produ" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\ProductSubCategory::get() as $data)
                                        <option value="{{ $data->id }}" data-pro="{{ $data->subcategory_name }}">
                                            {{ $data->subcategory_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Product Model: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="lead_pro_filter" name="produ" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\Product::get() as $data)
                                        <option value="{{ $data->id }}" data-pro="{{ $data->productname }}">
                                            {{ $data->productname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Status: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="lead_status_filter" name="statu" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\QuotationStatus::get() as $quto)
                                        <option value="{{ $quto->id }}" data-quto="{{ $quto->name }}">{{ $quto->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-3">
                                    Quotation List
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mt-3 justify-content-center">
                                <div class="col-lg-12 mb-4">
                                    <div class="row align-items-center" style="width: auto;">
                                        @php
                                            $user = auth()->user();
                                            $isSuperAdmin = $user->role === 'Super Admin';

                                            $latestQuotation = DB::table('quotations as q1')
                                                ->select('q1.*')
                                                ->whereRaw('q1.id = (
                                                    SELECT MAX(q2.id)
                                                    FROM quotations as q2
                                                    WHERE q2.id_lead = q1.id_lead
                                                )');

                                            $baseLeadQuery = \App\Models\Lead::when(!$isSuperAdmin, function ($q) use ($user) {
                                                    $q->where('leads.assigned_to', $user->id);
                                                })
                                                ->where('leads.quota_proceed', 1)
                                                ->where(function ($q) {
                                                    $q->whereNotIn('leads.quto_status', [3, 4, 5])
                                                      ->orWhereNull('leads.quto_status');
                                                })
                                                ->whereNotExists(function ($q) {
                                                    $q->from('orf')
                                                      ->whereColumn('orf.leano', 'leads.id');
                                                });

                                            $quotationTotalCount = (clone $baseLeadQuery)->count();

                                            $quotationAmountTotal = (clone $baseLeadQuery)
                                                ->leftJoinSub($latestQuotation, 'quotations', function ($join) {
                                                    $join->on('quotations.id_lead', '=', 'leads.id');
                                                })
                                                ->sum('quotations.grand_total');

                                            $quotationApprovedCount = (clone $baseLeadQuery)
                                                ->joinSub($latestQuotation, 'quotations', function ($join) {
                                                    $join->on('quotations.id_lead', '=', 'leads.id')
                                                         ->where('quotations.quto_app', 2);
                                                })
                                                ->count();

                                            $quotationApprovedTotal = (clone $baseLeadQuery)
                                                ->joinSub($latestQuotation, 'quotations', function ($join) {
                                                    $join->on('quotations.id_lead', '=', 'leads.id')
                                                         ->where('quotations.quto_app', 2);
                                                })
                                                ->sum('quotations.grand_total');

                                            $quotationPendingCount = (clone $baseLeadQuery)
                                                ->joinSub($latestQuotation, 'quotations', function ($join) {
                                                    $join->on('quotations.id_lead', '=', 'leads.id')
                                                         ->where('quotations.quto_app', '!=', 2);
                                                })
                                                ->count();

                                            $quotationPendingTotal = (clone $baseLeadQuery)
                                                ->joinSub($latestQuotation, 'quotations', function ($join) {
                                                    $join->on('quotations.id_lead', '=', 'leads.id')
                                                         ->where('quotations.quto_app', '!=', 2);
                                                })
                                                ->sum('quotations.grand_total');

                                            $quotationSummary = [
                                                'quotationTotalCount' => $quotationTotalCount,
                                                'quotationApprovedCount' => $quotationApprovedCount,
                                                'quotationPendingCount' => $quotationPendingCount,
                                                'quotationAmountTotal' => $quotationAmountTotal,
                                                'quotationApprovedTotal' => $quotationApprovedTotal,
                                                'quotationPendingTotal' => $quotationPendingTotal,
                                            ];
                                        @endphp

                                        <div class="col-lg-4">
                                            <div class="row align-items-center">
                                                <div class="col-5 text-center">
                                                    <span class="d-block text-truncate" style="color:#000;">Total count</span>
                                                </div>
                                                <div class="col-2">
                                                    <h4>
                                                        <span style="color:#000;">{{ $quotationSummary['quotationTotalCount'] }}</span>
                                                    </h4>
                                                </div>
                                                <div class="col-5 text-end pe-3">
                                                    <h4>
                                                        <span style="color:#000;">₹{{ number_format($quotationSummary['quotationAmountTotal']) }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="row align-items-center">
                                                <div class="col-5 text-center">
                                                    <span class="d-block text-truncate" style="color:#000;">Approved count</span>
                                                </div>
                                                <div class="col-2">
                                                    <h4>
                                                        <span style="color:#000;">{{ $quotationSummary['quotationApprovedCount'] }}</span>
                                                    </h4>
                                                </div>
                                                <div class="col-5 text-end pe-3">
                                                    <h4>
                                                        <span style="color:#28a745;">₹{{ number_format($quotationSummary['quotationApprovedTotal']) }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="row align-items-center">
                                                <div class="col-5 text-center">
                                                    <span class="d-block text-truncate" style="color:#000;">Pending count</span>
                                                </div>
                                                <div class="col-2">
                                                    <h4>
                                                        <span style="color:#000;">{{ $quotationSummary['quotationPendingCount'] }}</span>
                                                    </h4>
                                                </div>
                                                <div class="col-5 text-end pe-3">
                                                    <h4>
                                                        <span style="color:#dc3545;">₹{{ number_format($quotationSummary['quotationPendingTotal']) }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @endcan

    <!-- Change Status Modal -->
    <div class="modal fade" id="qutostatusModal" tabindex="-1" aria-labelledby="qutostatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qutostatusModalLabel">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="change-qutostatus-form">
                        <input type="hidden" id="quotation_id" name="quotation_id">
                        <div class="mb-3">
                            <label for="status-select" class="form-label">Select Status</label>
                            <select class="form-select" id="quot-status-select">
                                <option value="">-- Choose Status --</option>
                                @php
                                    $quotationStatuses = \App\Models\QuotationStatus::all();
                                @endphp

                                @foreach($quotationStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="quto-remarks-div">
        <label class="form-label">
            Remarks <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="quto-remarks" name="quotastatus_remarks"
            rows="3" placeholder="Enter remarks"></textarea>
    </div>
    <!-- Lead Remarks (Readonly) -->
<div class="mb-3">
    <label class="form-label">Lead Remarks</label>
    <textarea class="form-control"
              id="lead-remarks"
              rows="3"
              readonly
              style="background-color:#f5f5f5;"></textarea>
</div>

<!-- GA Preview -->
<div class="mb-3 d-none" id="ga-preview-wrapper">
    <label class="form-label">GA Preview</label>
    <div class="border p-2 text-center">
        <img id="ga-preview-image"
             src=""
             alt="GA Preview"
             style="max-width:100%; max-height:250px;">
    </div>
</div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="closureDateModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="closure-date-form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Closure Date</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="closure_lead_id">

          <div class="mb-3">
            <label class="form-label">Closure Date</label>
            <input type="date" class="form-control" id="closure_date" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>


    <!-- Check Status Modal -->
    <div class="modal fade" id="checkStatusModal" tabindex="-1" aria-labelledby="checkStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkStatusModalLabel">Check Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Status:</strong> <span id="show_quto_status"></span></p>
                    <p><strong>Status Date:</strong> <span id="show_qutostatus_date"></span></p>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
