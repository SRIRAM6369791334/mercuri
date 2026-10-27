@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
@can('leads-display')
    <section class="section leadsection container" >
        <div class=" form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Lead Enquiries</h5>
                        </div>
                        <div class="col-lg-2">
                        </div>
                        @can('leads-create')
                            <div class="col-lg-4 text-end">
                                <button type="button" class="btn btn-outline-primary waves-effect me-2" data-bs-toggle="modal" data-bs-target="#bulkUploadGeneralModal">
                                    <i class="mdi mdi-upload"></i> Bulk Upload
                                </button>
                                <a href="/addleads" class="btn btn-primary add-employee-btn">Add Lead Enquiry</a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container-fluid mt-5">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        All Lead Enquiries
                    </div>
                    <div class="col-lg-4">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-end">
                                <b>Lead Status: </b>
                            </div>
                            <div class="col-lg-6">
                                <select class="form-select" id="lead_status_filter" name="status" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
    @if($data->dropdowndata !== 'WON')
        <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">
            {{ $data->dropdowndata }}
        </option>
    @endif
@endforeach
                                    {{-- @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
                                        <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">{{ $data->dropdowndata }}</option>
                                    @endforeach --}}
                                </select>
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
@php
    // use App\Models\AssignSetting;

    $assignSetting = App\Models\AssignSetting::first();
@endphp

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Move to Enquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changeStatusForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="lead_id" name="lead_id">

                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status <span
                            style="color:red">*</span></label>
                            <select class="form-control" id="leadstatus" name="status" required>
    <option value="">-- Choose Status --</option>

    @foreach (
        App\Models\Dropdowndata::where('formid', 6)
            ->where('dropdowndata', 'WON')
            ->orderBy('orderno', 'asc')
            ->get() as $data
    )
        <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">
            {{ $data->dropdowndata }}
        </option>
    @endforeach
</select>
                        {{-- <select class="form-control" id="leadstatus" name="status" required>

                            <option value="">-- Choose Status --</option>
                            @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
                                <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">{{ $data->dropdowndata }}</option>
                            @endforeach
                        </select> --}}
                    </div>
                    @php
                        $todayDate = date('Y-m-d', strtotime('today'));
                    @endphp
                    <div class="mb-3" id="convertedOptionsDiv" style="display: none;">
                        <div class="form-group mt-4">
                            <label for="converteddate" class="form-label">Converted Date <span style="color:red">*</span></label>
                            <input type="date" class="form-control" name="converteddate" id="converteddate" value="{{ $todayDate }}" max="{{$todayDate}}" placeholder="">
                        </div>
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1"
                                class="form-label">Product Segment <span
                                style="color:red">*</span></label>
                                <select class="form-select " name="category" id="catepro" >
                                    <option value=""> Choose Category </option>
                                    @if ($status = App\Models\ProductCategory::get())
                                    @foreach ($status as $data)
                                        <option value="{{ $data->id }}" data-sub="{{ $data->sub_category }}" data-type="{{ $data->type }}"
                                            >
                                            {{ $data->category_name }}</option>
                                    @endforeach
                                @endif

                                </select>
                        </div>
                        <div class="form-group mt-4" id="productGroupDiv">
                            <label for="lead_sub_category_id"
                                class="form-label">Product Group <span
                                style="color:red">*</span></label>
                                <select class="form-select" name="sub_category_id" id="lead_sub_category_id" required>
                                    <option value="">-- Choose Product Group --</option>
                                </select>
                        </div>
                        <div class="form-group mt-4" id="productsDiv">
                            <label for="pprod" class="form-label">Products <span style="color:red">*</span></label>
                            <select class="form-select select2" name="products[]" id="pprod" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>
                        <div class="form-group mt-4" id="designSpecField" style="display:none;">
    <label class="form-label">
        Design Specifications <span style="color:red">*</span>
    </label>

    <div id="specificationWrapper"></div>
</div>




                        <div class="form-group mt-4" id="ganumberfield" >
                            <label for="ga_number" class="form-label">GA Number </label>
                            <input type="file" class="form-control" name="ga_number" id="ga_number" accept=".pdf, .docx, .jpg, .png" >
                        </div>
                        {{-- <div class="form-group mt-4" id="designinputField" style="display:none;">
                            <label for="Design" class="form-label"> Design Input</label>
                            <input type="file" class="form-control" name="design_input" id="design_input" accept=".pdf, .docx, .jpg, .png" >
                        </div> --}}
                        <div class="form-group mt-4" id="designinputField" style="display:none;">
    <label class="form-label">Design Inputs</label>

    <div id="designInputWrapper">
        <div class="d-flex gap-2 mb-2">
            <input type="file"
                   name="design_input[]"
                   class="form-control"
                   accept=".pdf,.docx,.jpg,.png">

            <button type="button"
                    class="btn btn-success addDesignInput">+</button>
        </div>
    </div>
</div>

                        <div class="form-group mt-4" id="specialField" style="display: none;">
                            <input type="hidden" name="assigndesign" id="assigndesign_hidden">
                            <label for="special_input" class="form-label">Assign Designer <span style="color:red">*</span></label>
                            <select class="form-select" id="special_input" >
                                <option value="">Choose Designer</option>
                                @if ($user = App\Models\User::get())
                                @foreach ($user as $us)
                                    <option value="{{ $us->id }}"
                                        >
                                        {{ $us->name }}</option>
                                @endforeach
                            @endif
                            </select>

                        </div>
                </div>


                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Incomplete Lead Details Warning Modal -->
<div class="modal fade" id="incompleteLeadModal" tabindex="-1" aria-labelledby="incompleteLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white d-flex align-items-center" id="incompleteLeadModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Incomplete Lead Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card bg-light border-0 p-3 mb-3">
                    <div class="row g-2 small">
                        <div class="col-6"><strong>Lead Ref:</strong> <span id="incompleteLeadRef" class="text-primary fw-bold">-</span></div>
                        <div class="col-6"><strong>Customer:</strong> <span id="incompleteLeadCustomer" class="text-dark fw-bold">-</span></div>
                    </div>
                </div>
                <h6 class="fw-bold mb-2 text-secondary small text-uppercase">Missing Fields to Complete:</h6>
                <ul id="incompleteMissingList" class="list-unstyled mb-0 ps-1">
                    <!-- Dynamically populated via JS -->
                </ul>
                <div class="mt-3 pt-2 text-muted small">
                    <span class="text-danger fw-bold">* Note:</span> The above fields are not filled. These fields are mandatory, please fill them.
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="incompleteEditBtn" href="#" class="btn btn-warning text-dark fw-bold px-3">
                    <i class="fas fa-edit me-1"></i> Edit Lead Details
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="astatusModal" tabindex="-1" aria-labelledby="astatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="astatusModalLabel">Change Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="achangeStatusForm">
                    @csrf
                    <input type="hidden" id="llead_id" name="lead_id">

                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status <span
                            style="color:red">*</span></label>
                        {{-- <select class="form-control" id="leadstatus" name="status" required>

                            <option value="">-- Choose Status --</option>
                            @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
                                <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">{{ $data->dropdowndata }}</option>
                            @endforeach
                        </select> --}}
                        <select class="form-control" id="lleadstatus" name="status" required>
    <option value="">-- Choose Status --</option>

    @foreach (
        App\Models\Dropdowndata::where('formid', 6)
            ->where('dropdowndata', '!=', 'WON')
            ->orderBy('orderno', 'asc')
            ->get() as $data
    )
        <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">
            {{ $data->dropdowndata }}
        </option>
    @endforeach
</select>

                    </div>
                    @php
                        $todayDate = date('Y-m-d', strtotime('today'));
                    @endphp
                    <div class="mb-3" id="convertedOptionsDiv" style="display: none;">
                        <div class="form-group mt-4">
                            <label for="converteddate" class="form-label">Converted Date <span style="color:red">*</span></label>
                            <input type="date" class="form-control" name="converteddate" id="converteddate" value="{{ $todayDate }}" max="{{$todayDate}}" placeholder="">
                        </div>
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1"
                                class="form-label">Product Segment <span
                                style="color:red">*</span></label>
                                <select class="form-select " name="category" id="catepro" >
                                    <option value=""> Choose Category </option>
                                    @if ($status = App\Models\ProductCategory::get())
                                    @foreach ($status as $data)
                                        <option value="{{ $data->id }}" data-sub="{{ $data->sub_category }}"
                                            >
                                            {{ $data->category_name }}</option>
                                    @endforeach
                                @endif

                                </select>
                        </div>
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1"
                                class="form-label">Product Group <span
                                style="color:red">*</span></label>
                                <select class="form-select " name="sub_category_id" id="lead_sub_category_id" >
                                    <option value=""> Choose Category </option>


                                </select>
                        </div>
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1"
                                class="form-label">Product Model<span
                                style="color:red">*</span></label>
                                <select class="form-select " name="products" id="pprod" >
                                    <option value=""> Choose Product </option>

                                </select>
                        </div>
                        <div class="form-group mt-4">
                            <label for="quantity" class="form-label">Quantity <span style="color:red">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="quantity"  placeholder="Enter Quantity">
                        </div>

                        <div class="form-group mt-4">
                            <label for="ga_number" class="form-label">GA Number </label>
                            <input type="file" class="form-control" name="ga_number" id="ga_number" accept=".pdf, .docx, .jpg, .png" >
                        </div>
                        <div class="form-group mt-4" id="specialField" style="display: none;">
                            <label for="special_input" class="form-label">Assign Designer <span style="color:red">*</span></label>
                            <select class="form-select" name="assigndesign" id="special_input" >
                                <option value="">Choose Designer</option>
                                @if ($user = App\Models\User::where('role','Design Engineer')->get())
                                @foreach ($user as $us)
                                    <option value="{{ $us->id }}"
                                        >
                                        {{ $us->name }}</option>
                                @endforeach
                            @endif
                            </select>

                        </div>
                </div>


                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control rrema" id="remarks" name="remarks" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leadStatusModal" tabindex="-1" aria-labelledby="leadStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Lead Status Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Status:</strong> <span id="lead-status"></span></p>
          <p><strong>Date:</strong> <span id="lead-status-date"></span></p>
          <p><strong>Remarks:</strong> <span id="lead-remarks"></span></p>
        </div>
      </div>
    </div>
  </div>


<style>
    #statusModal .select2-container {
        width: 100% !important;
        display: block;
    }
    #statusModal .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 6px;
        min-height: 38px;
        padding: 3px 6px;
        font-size: 13px;
        background-color: #fff;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    #statusModal .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    #statusModal .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #052c65;
        border: none;
        color: #ffffff;
        border-radius: 4px;
        padding: 3px 8px;
        font-size: 12px;
        margin: 2px 4px 2px 0;
    }
    #statusModal .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff;
        margin-right: 5px;
        font-weight: bold;
    }
    #statusModal .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff6b6b;
    }
    #statusModal .select2-dropdown {
        z-index: 99999 !important;
        border: 1px solid #ced4da;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    #statusModal .select2-container .select2-search--inline {
        flex: 1 1 auto;
        margin: 0;
    }
    #statusModal .select2-container .select2-search--inline .select2-search__field {
        width: 100% !important;
        min-width: 150px;
        font-size: 13px;
        margin: 0;
        padding: 4px 2px;
    }
</style>

@endcan

@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filter = document.getElementById('lead_status_filter');

            const applyLeadStatusFilter = function () {
                const table = $('#leads').DataTable();
                const selectedText = filter.options[filter.selectedIndex]?.dataset.status || '';
                const statusColumnIndex = table.columns().header().toArray().findIndex(function (header) {
                    return $(header).text().trim() === 'Lead Status';
                });

                if (statusColumnIndex !== -1) {
                    table.column(statusColumnIndex).search(filter.value === '0' ? '' : selectedText).draw();
                }
            };

            filter.addEventListener('change', applyLeadStatusFilter);
        });
    </script>
@endpush
@section('scripts')
<script>
    window.assignSettings = {
        mhe: "{{ $assignSetting->mhe_special_user_id ?? '' }}",
        rack: "{{ $assignSetting->racking_user_id ?? '' }}",
        pallet: "{{ $assignSetting->pallet_user_id ?? '' }}"
    };

    function multiKeywordMatcher(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (!data.text) {
            return null;
        }
        var searchTerms = params.term.toLowerCase().split(/\s+/).filter(Boolean);
        var text = data.text.toLowerCase();
        var isMatch = searchTerms.every(function (term) {
            return text.indexOf(term) > -1;
        });
        return isMatch ? data : null;
    }

    function initStatusModalProductSelect2() {
        var $pprod = $('#pprod');
        if ($pprod.length) {
            if ($pprod.hasClass('select2-hidden-accessible')) {
                $pprod.select2('destroy');
            }
            $pprod.select2({
                placeholder: "Search or Select Products",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#statusModal'),
                matcher: multiKeywordMatcher,
                closeOnSelect: false
            });
        }
    }

    $(document).ready(function () {
        initStatusModalProductSelect2();

        $('#statusModal').on('shown.bs.modal', function () {
            initStatusModalProductSelect2();
        });

        $(document).on('click', '.lead_won', function () {
            setTimeout(function () {
                initStatusModalProductSelect2();
            }, 150);
        });

        $(document).on('change', '#catepro', function () {
            var category = $(this).val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            $('#pprod').empty();
            initStatusModalProductSelect2();
            $('#pprod').val(null).trigger('change');

            if (category) {
                $.ajax({
                    type: 'POST',
                    url: '/getProductGroup',
                    data: {
                        category: category
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        $('#lead_sub_category_id').html(response.data);
                    },
                    error: function () {
                        $('#lead_sub_category_id').html('<option value="">-- Choose Product Group --</option>');
                    }
                });
            } else {
                $('#lead_sub_category_id').html('<option value="">-- Choose Product Group --</option>');
            }
        });

        $(document).on('change', '#lead_sub_category_id', function () {
            var category = $('#catepro').val();
            var subCategory = $(this).val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            if (category && subCategory) {
                $.ajax({
                    type: 'POST',
                    url: '/getfandfrep',
                    data: {
                        category: category,
                        subCategory: subCategory
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (data) {
                        $('#pprod').html(data.products);
                        initStatusModalProductSelect2();
                        $('#pprod').val(null).trigger('change');
                    },
                    error: function () {
                        $('#pprod').empty();
                        initStatusModalProductSelect2();
                        $('#pprod').val(null).trigger('change');
                    }
                });
            } else {
                $('#pprod').empty();
                initStatusModalProductSelect2();
                $('#pprod').val(null).trigger('change');
            }
        });

        // Form submit validation for multiple products
        $('#changeStatusForm').on('submit', function (e) {
            if ($('#convertedOptionsDiv').is(':visible')) {
                var selectedProducts = $('#pprod').val();
                if (!selectedProducts || selectedProducts.length === 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Product Required',
                        text: 'Please select at least one product.'
                    });
                    return false;
                }
            }
        });
    });
</script>

@can('leads-create')
    @include('partials.lead-bulk-upload-modal', [
        'modalId' => 'bulkUploadGeneralModal',
        'type' => 'general',
        'title' => 'Bulk Upload General Leads',
        'templateRoute' => route('leads.sample-template'),
        'uploadRoute' => route('leads.bulk-upload')
    ])
@endcan
@endsection
