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
                            <h5 class="mt-2">Lost Lead Enquiries</h5>
                        </div>
                        <div class="col-lg-4">

                        </div>
                        {{-- @can('leads-create')
                            <div class="col-lg-2">
                                <a href="/addleads" class="btn btn-primary d-block add-employee-btn" >Add Lead Enquiry</a>
                            </div>
                        @endcan --}}
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
                        All Lost Lead Enquiries
                    </div>
                    {{-- <div class="col-lg-4">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-end">
                                <b>Lead Status: </b>
                            </div>
                            <div class="col-lg-6">
                                <select class="form-select" id="lead_status_filter" name="status" required>
                                    <option value="0">All</option>
                                    @foreach (App\Models\Dropdowndata::where('formid', 6)->where('id', 351)->orderBy('orderno', 'asc')->get() as $data)
                                        <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">{{ $data->dropdowndata }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-body">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Change Enquiry Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changeStatusForm">
                    @csrf
                    <input type="hidden" id="lead_id" name="lead_id">

                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status <span
                            style="color:red">*</span></label>
                        <select class="form-control" id="leadstatus" name="status" required>

                            <option value="">-- Choose Status --</option>
                            @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
                                <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}">{{ $data->dropdowndata }}</option>
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
                                class="form-label">Categories <span
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
                                class="form-label">Products <span
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
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
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


@endcan


@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
{{-- @section('scripts')

@endsection --}}
