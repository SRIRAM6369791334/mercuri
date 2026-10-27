@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    {{-- @include('sweetalert::alert') --}}
    {{-- <script>
    window.onload = function () {
        window.location.assign("{{ request('next') }}");
    };
</script> --}}
<style>
    .lead-section b {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.lead-section .form-select {
    font-size: 12.5px;
    padding: 6px 10px;
    border-radius: 6px;
}
.mt-4 { margin-top: 14px !important; }
.mt-5 { margin-top: 20px !important; }
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
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 700;
}
.quota-card {
    font-size: 12.5px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #eef2ff;
    color: #1e3a8a;
    text-align: center;
    display: inline-block;
}

.attachment-box {
    border: 1px dashed #ccc;
    padding: 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}


.attachment-list {
    border: 1px dashed #d0d5dd;
    padding: 10px;
    border-radius: 6px;
    background: #fafafa;
}

.attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 6px;
    background: #fff;
}

.attachment-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.attachment-left img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.attachment-name {
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attachment-actions {
    display: flex;
    gap: 6px;
}



    </style>


{{-- @php
    $designCount = App\Models\Design::whereNotIn('idlead', function ($query) {
        $query->select('id_lead')->from('quotations');
    })->count();
@endphp --}}
@php
use App\Models\Design;
use Illuminate\Support\Facades\Auth;

$userId = Auth::id();
$role   = Auth::user()->role;

$designCountQuery = Design::query()
    ->whereHas('lead', function ($q) {
        $q->whereNotIn('quota_proceed', [0, 1])
          ->orWhereNull('quota_proceed');
    })
    ->whereNotIn('idlead', function ($q) {
        $q->select('id_lead')->from('quotations');
    });

/* ---------------- ROLE BASED COUNT ---------------- */

// 1️⃣ Super Admin → all
if ($role === 'Super Admin') {
    // no extra condition
}

// 2️⃣ Design Engineer
elseif ($role === 'Design Engineer') {

    $designCountQuery->where(function ($q) use ($userId) {

        // TL pending designs
        $q->where(function ($sub) use ($userId) {
            $sub->where('assigndesign', $userId)
                ->whereNull('design_status')
                ->whereNull('design_user');
        })

        // OR designs assigned to me
        ->orWhere(function ($sub) use ($userId) {
            $sub->where('design_user', $userId)
                ->whereNull('design_status');
        });
    });
}

// 3️⃣ Sales Manager
elseif ($role === 'Sales Manager') {

    $designCountQuery->where('design_status', 1)
                     ->whereNotNull('assigndesign')
                     ->whereHas('lead', function ($q) use ($userId) {
                         $q->where('assigned_to', $userId);
                     });
}

// 4️⃣ Other roles → no data
else {
    $designCountQuery->whereRaw('1 = 0');
}

$designCount = $designCountQuery->count();


// $designCount = Design::whereHas('lead', function ($q) {
//         $q->whereNotIn('quota_proceed', [0, 1])
//           ->orWhereNull('quota_proceed');
//     })
//     ->whereNotIn('idlead', function ($query) {
//         $query->select('id_lead')->from('quotations');
//     })
//     ->count();
    $tlIds = Design::whereHas('lead', function ($q) {
        $q->whereNotIn('quota_proceed', [0, 1])
          ->orWhereNull('quota_proceed');
    })
    ->whereNotIn('idlead', function ($query) {
        $query->select('id_lead')->from('quotations');
    })
    ->pluck('assigndesign')
    ->unique()
    ->filter();

$designers = App\Models\User::whereIn('tl_id', $tlIds)
    ->where('role', 'Design Engineer')
    ->orderBy('name')
    ->get();

@endphp




    <section class="section lead-section container-fluid">
        <div class=" form-card">

                <div class="row mt-4">
                    {{-- <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Product Segment: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="dlead_cat_filter" name="catego" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\ProductCategory::get() as $data)
                                        <option value="{{ $data->id }}" data-cat="{{ $data->category_name }}">
                                            {{ $data->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-lg-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <b>Product Group: </b>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select" id="dquotation_pro_group_filter" name="produ" required>
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
                                <select class="form-select" id="dlead_pro_filter" name="produ" required>
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
                    {{-- <div class="col-lg-3">
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
                    </div> --}}
                </div>

            <div class=" mt-5">
                <div class="card">
                    <div class="card-header">
                         <div class="row align-items-center">
                                <div class="col-lg-9">
                                    Design List
                                </div>
                                <div class="col-lg-3">
                        <div class="quota-card"><strong>Design Count -</strong> <b
                                style="color:#225885">{{ $designCount }}</b></div>
                    </div>
                            </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-4">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="assignDesignerModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="assignDesignerForm">
        @csrf
        <input type="hidden" id="design_id" name="design_id">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Designer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <select class="form-select" name="design_user" required>
                    <option value="">Select Designer</option>
                    @foreach($designers as $designer)
                        <option value="{{ $designer->id }}">{{ $designer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Assign</button>
            </div>
        </div>
    </form>
  </div>
</div>

<div class="modal fade" id="designSpecModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Design Specifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="designInputPreview" class="mb-3 text-center"></div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Specification</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody id="designSpecTable">
                        <tr>
                            <td colspan="2" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                  <div class="mt-4">
    <h6 class="fw-bold">Remarks</h6>
    <div id="designLeadRemark" class="border rounded p-2 text-muted">
        --
    </div>
</div>
            </div>



        </div>
    </div>
</div>


<div class="modal fade" id="uploadDesignModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="uploadDesignForm" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="design_id" id="design_iid">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Design Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- <div class="mb-3">
                        <label class="form-label">Quotation (PDF / Image)</label>
                        <input type="file" name="quotation" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Drawing (PDF / Image)</label>
                        <input type="file" name="drawing" class="form-control" required>
                    </div> --}}
                    <!-- QUOTATION -->
<div class="mb-3">
    <label class="form-label fw-bold">Quotation</label>

    <div id="quotationPreview" class="attachment-list"><span class="text-muted">No file selected</span></div>

    <button type="button"
        class="btn btn-sm btn-outline-primary select-file"
        data-input="quotationInput">
        Attach
    </button>

    <input type="file"
           id="quotationInput"
           name="quotation"
           class="d-none"
           accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx"
           required>
</div>


<!-- DRAWINGS -->
<!-- EXISTING DRAWINGS (READ ONLY) -->
<div class="mb-3">
    <label class="form-label fw-bold">Drawings</label>
    <div id="existingDrawings" class="attachment-list">
        <span class="text-muted">Loading drawings...</span>
    </div>
</div>





                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="moveQuotationModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Move to Quotation</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                {{-- <a href="#" target="_blank" id="viewQuotationBtn"
                   class="btn btn-outline-primary me-2">
                    View Quotation
                </a>

                <a href="#" target="_blank" id="viewDrawingBtn"
                   class="btn btn-outline-secondary">
                    View Drawing
                </a> --}}

                {{-- <hr> --}}

                <button class="btn btn-success mt-2" id="confirmMoveQuotation">
                    Yes, Move to Quotation
                </button>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editDesignModal">
  <div class="modal-dialog modal-lg">
    <form id="editDesignForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="design_id" id="edit_design_id">

        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Design</h5>
            </div>

            <div class="modal-body">

                {{-- <div class="mb-3">
                    <label>Design Input</label>
                    <div id="existingDesignInput" class="mb-2"></div>
                    <input type="file" class="form-control" name="design_input">
                </div> --}}
                <div class="mb-3" id="returnRemarksWrapper" style="display:none;">
    <label class="fw-bold text-danger">Return Remarks</label>
    <textarea class="form-control bg-light"
              id="returnRemarksText"
              rows="3"
              readonly></textarea>
</div>

                <div class="mb-3">
    <label class="fw-bold">Design Inputs</label>

    <!-- EXISTING FILES -->
    <div id="existingDesignInput" class="mb-3"></div>

    <!-- ADD NEW FILES -->
    <div id="newDesignInputWrapper">
        <div class="d-flex gap-2 mb-2">
            <input type="file"
                   name="new_design_input[]"
                   class="form-control"
                   accept=".pdf,.docx,.jpg,.png,.xls,.xlsx">
            <button type="button"
                    class="btn btn-success addNewDesignInput">+</button>
        </div>
    </div>
</div>


                <table class="table">
                    <thead>
                        <tr>
                            <th>Specification</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody id="editSpecTable"></tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    Update & Send to Design
                </button>
            </div>
        </div>
    </form>
  </div>
</div>



<div class="modal fade" id="uploadDrawingModal">
  <div class="modal-dialog modal-lg">
    <form id="uploadDrawingForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="design_id" id="drawing_design_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Drawings</h5>
        </div>

        <div class="modal-body">

         <!-- ADD NEW -->
<div class="mt-3">
    <span class="text-primary fw-bold attach-drawing-btn" style="cursor:pointer">
        + Attach
    </span>

    <input type="file"
           id="newDrawingInput"
           name="drawing[]"
           multiple
           hidden
           accept=".pdf,.jpg,.png,.jpeg,.dwg">
</div>

<!-- PREVIEW NEW FILES -->
<div id="newDrawingPreview" class="mt-3"></div>


        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="approvedDrawingModal">
  <div class="modal-dialog modal-lg">
    <form id="approvedDrawingForm" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="design_id" id="approved_design_id">

      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Approved Drawing Upload</h5>
        </div>

        <div class="modal-body">

          <!-- QUOTATION FILE -->
          <div class="mb-3">
            <label class="fw-bold">Attach Quotation</label>
            <input type="file"
                   name="quotation_file"
                   class="form-control"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.csv"
                   required>
          </div>

          <!-- DRAWINGS -->
         <div class="mb-3">
    <span class="text-primary fw-bold approved-attach-btn" style="cursor:pointer">
        + Attach Drawings
    </span>

    <input type="file"
           id="approvedDrawingInput"
           name="drawing[]"
           multiple
           hidden
           accept=".pdf,.jpg,.png,.jpeg,.dwg,.xls,.xlsx,.csv">
</div>

<div id="approvedDrawingPreview" class="mt-3"></div>


          <!-- REMARKS -->
          <div class="mb-3">
            <label class="fw-bold">Drawing Remarks</label>
            <textarea name="drawing_remarks"
                      class="form-control"
                      rows="3"
                      placeholder="Enter remarks..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Submit</button>
        </div>

      </div>
    </form>
  </div>
</div>




<div class="modal fade" id="drawingApprovalModal">
  <div class="modal-dialog modal-lg">
    <form id="drawingApprovalForm">
      @csrf
      <input type="hidden" name="design_id" id="approval_design_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Drawing Approval</h5>
        </div>

        <div class="modal-body" id="approvalDrawingList">
            Loading...
        </div>

        <div class="modal-footer">

          <button type="submit" class="btn btn-success" id="approvalSubmitBtn">
            Submit
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="drawingRejectedModal">
  <div class="modal-dialog modal-lg">
    <form id="drawingRejectedForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="design_id" id="rejected_design_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Drawings Status</h5>
        </div>

        <div class="modal-body" id="rejectedDrawingList">
            Loading...
        </div>

        <div class="modal-footer">
          <button type="submit"
                  class="btn btn-success"
                  id="rejectedUpdateBtn">
            Update Drawings
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="drawingActionModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Drawing Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <p class="mb-3">What do you want to do?</p>

        <button class="btn btn-success w-100 mb-2" id="sendForApprovalBtn">
          Send Drawing to Approval
        </button>

        <button class="btn btn-primary w-100" id="alreadyApprovedBtn">
          Already Approved
        </button>
      </div>
    </div>
  </div>
</div>

        @endsection

        @push('scripts')
            {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @endpush
