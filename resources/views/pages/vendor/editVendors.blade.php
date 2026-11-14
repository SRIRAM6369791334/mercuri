@extends('layouts.app')
@section('title', 'Edit Vendor Details')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get();
        $branchStates = App\Models\Statelist::orderBy('state')->get();
    @endphp
    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Edit Vendor Details</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="/editVendors" id="edit_vendor_form">
                <input type="hidden" name="vendorId" value="{{ $vendorDetails->id }}">
                <div class="row mb-4">
                    @csrf

                    <!-- 1. Left: Company Name -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_company_name" class="form-label">
                                Company Name <span style="color:red">*</span>
                            </label>
                            <input type="text" name="company_name" id="edit_company_name" class="form-control"
                                placeholder="Company Name" value="{{ $vendorDetails->company_name }}" required>
                        </div>
                    </div>

                    <!-- 1. Right: GST NO -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_gst" class="form-label">GST NO <span style="color:red">*</span></label>
                            <div class="gst-input-div position-relative">
                                <input type="text" name="gst_number" id="edit_vendor_gst"
                                    class="form-control gst-number-field text-uppercase mb-1"
                                    placeholder="Enter 15-digit GST NO" maxlength="15"
                                    value="{{ $vendorDetails->gst_number }}" required>
                                <i class="fas fa-check-circle" style="display:none; color: #28a745; position: absolute; right: 12px; top: 12px; font-size: 16px;"></i>
                                <i class="fas fa-times-circle" style="display:none; color: #dc3545; position: absolute; right: 12px; top: 12px; font-size: 16px;"></i>
                                <span class="spinner-border spinner-border-sm text-primary gst-loader" style="display:none; position: absolute; right: 12px; top: 12px;" role="status"></span>
                            </div>
                            <span class="error-msg text-danger small"></span>
                        </div>
                    </div>

                    <!-- 2. Left: Segment -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="editvendor_cat_list" class="form-label">Segment <span style="color:red">*</span></label>
                            <select class="form-select select2" name="pro_cat_id[]" id="editvendor_cat_list" required multiple="multiple">
                                @php
                                    $selectedCategories = json_decode($vendorDetails->product_cat_id, true) ?? [];
                                @endphp
                                @if ($categories = App\Models\ProductCategory::orderBy('category_name')->get())
                                    @foreach ($categories as $data)
                                        <option value="{{ $data->id }}" {{ in_array($data->id, $selectedCategories) ? 'selected' : '' }}>
                                            {{ $data->category_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- 2. Right: Alternate Name -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_alternate_name" class="form-label">Alternate Name</label>
                            <input type="text" name="alternate_name" id="edit_alternate_name" class="form-control"
                                placeholder="Alternate / Short Name" value="{{ $vendorDetails->alternate_name }}">
                        </div>
                    </div>

                    <!-- 3. Left: Primary Registered Address -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_address" class="form-label">Primary Registered Address <span style="color:red">*</span></label>
                            <textarea name="address" class="form-control" id="edit_vendor_address" rows="3"
                                placeholder="Primary Registered Address" required>{{ $vendorDetails->address }}</textarea>
                        </div>
                    </div>

                    <!-- 3. Right: Secondary / Branch Address (In Same Form Grid, Matching addleads) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="edit_secondary_branch_address" class="form-label mb-0">Secondary / Branch Address</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditAddBranchField" style="font-size: 12px; padding: 2px 10px;">
                                    <i class="fas fa-plus me-1"></i> Add Another Branch
                                </button>
                            </div>
                            <div id="edit_branch_address_fields_container">
                                @if (!empty($vendorAddresses) && count($vendorAddresses) > 0)
                                    @foreach ($vendorAddresses as $bIdx => $bAddr)
                                        <div class="branch-entry-block mb-2 {{ $bIdx > 0 ? 'pt-2 border-top' : '' }}" id="edit_branch_block_{{ $bIdx }}">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold text-muted">{{ $bAddr->address_type ?: ('Branch #' . ($bIdx + 1)) }}</small>
                                                @if ($bIdx > 0)
                                                    <button type="button" class="btn btn-sm text-danger p-0 btn-remove-edit-branch-field" data-target="#edit_branch_block_{{ $bIdx }}" title="Remove Branch">
                                                        <i class="fas fa-times-circle me-1"></i> Remove
                                                    </button>
                                                @endif
                                            </div>
                                            <textarea name="branch_address[]" class="form-control mb-1" rows="3"
                                                placeholder="Secondary or branch plant address...">{{ $bAddr->address }}</textarea>
                                            <input type="hidden" name="branch_id[]" value="{{ $bAddr->id }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="branch-entry-block mb-2" id="edit_branch_block_0">
                                        <input type="hidden" name="branch_id[]" value="">
                                        <textarea name="branch_address[]" id="edit_secondary_branch_address" class="form-control" rows="3"
                                            placeholder="Enter secondary or branch plant address (Optional)..."></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. Left: Country -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_country" class="form-label">Country <span style="color:red">*</span></label>
                            <select class="form-select" name="country_id" id="edit_vendor_country">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ ($vendorDetails->country_id == $country->id || (empty($vendorDetails->country_id) && $country->id == 76)) ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 4. Right: Contact Person (POC) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_poc_name" class="form-label">Contact Person (Optional)</label>
                            <input type="text" name="poc_name" id="edit_vendor_poc_name" class="form-control"
                                placeholder="Contact Person Name" value="{{ $vendorDetails->point_of_contact }}">
                        </div>
                    </div>

                    <!-- 5. Left: State (for Tax Calculation) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_state_list" class="form-label">State (for Tax Calculation) <span style="color:red">*</span></label>
                            <select class="form-select select2 state_list" name="state_id" id="edit_vendor_state_list" required>
                                <option value=""> -- Choose State -- </option>
                                @if($states = App\Models\Statelist::orderBy('state')->get())
                                    @foreach ($states as $data)
                                        <option value="{{ $data->id }}" @if($vendorDetails->state_id == $data->id) selected @endif>
                                            {{ $data->state }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- 5. Right: E-mail -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_cus_email" class="form-label">E-mail (Optional)</label>
                            <input type="email" name="email" class="form-control" id="edit_vendor_cus_email"
                                placeholder="vendor@example.com" value="{{ $vendorDetails->email }}">
                        </div>
                    </div>

                    <!-- 6. Left: City Dropdown (Auto-populated from State) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_city_list" class="form-label">City</label>
                            <select class="form-select select2" name="city_id" id="edit_vendor_city_list">
                                <option value=""> -- Choose City -- </option>
                                @if(!empty($vendorDetails->state_id))
                                    @foreach(App\Models\Citylist::where('state_code', $vendorDetails->state_id)->orderBy('city_name')->get() as $c)
                                        <option value="{{ $c->id }}" @if($vendorDetails->city_id == $c->id) selected @endif>
                                            {{ $c->city_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- 6. Right: Contact Phone -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <input type="hidden" name="countrycode" value="76">
                            <label for="edit_vendor_contact_no" class="form-label">Contact Number (Optional)</label>
                            <input type="text" name="contact_number" id="edit_vendor_contact_no" class="form-control"
                                placeholder="10-digit Mobile Number" value="{{ $vendorDetails->contact_number }}" maxlength="10">
                        </div>
                    </div>

                    <!-- 7. Left: Postal Code -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_postalcode" class="form-label">Postal Code (Optional)</label>
                            <input type="text" name="postal_code" id="edit_vendor_postalcode" class="form-control"
                                placeholder="Postal Code" value="{{ $vendorDetails->postal_code }}" maxlength="10">
                        </div>
                    </div>

                    <!-- 7. Right: Vendor Status -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="edit_vendor_status" class="form-label">Vendor Status <span style="color:red">*</span></label>
                            <select class="form-select" name="vendor_status" id="edit_vendor_status" required>
                                <option value="active" @if ($vendorDetails->vendor_status == 'active') selected @endif> Active </option>
                                <option value="inactive" @if ($vendorDetails->vendor_status == 'inactive') selected @endif> Inactive </option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Submit Button Centered (Matching addleads) -->
                <button type="submit" id="save-edit-vendor-btn" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-4">Save Changes</button>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            if ($('#editvendor_cat_list').length) {
                $('#editvendor_cat_list').select2({ width: '100%', placeholder: 'Select Segment' });
            }
            if ($('#edit_vendor_state_list').length) {
                if ($('#edit_vendor_state_list').hasClass('select2-hidden-accessible')) {
                    $('#edit_vendor_state_list').select2('destroy');
                }
                $('#edit_vendor_state_list').select2({ width: '100%', placeholder: '-- Choose State --', allowClear: true });
            }
            if ($('#edit_vendor_city_list').length) {
                if ($('#edit_vendor_city_list').hasClass('select2-hidden-accessible')) {
                    $('#edit_vendor_city_list').select2('destroy');
                }
                $('#edit_vendor_city_list').select2({ width: '100%', placeholder: '-- Choose City --', allowClear: true });
            }
        });

        // Initialize JustValidate
        let edit_vendor_form = new window.JustValidate('#edit_vendor_form');

        edit_vendor_form
            .addField('#edit_vendor_gst', [
                { rule: 'required' },
                { rule: 'minLength', value: 15 },
                { rule: 'maxLength', value: 15 },
            ])
            .addField('#edit_company_name', [
                { rule: 'required' },
                { rule: 'minLength', value: 2 },
            ])
            .addField('#editvendor_cat_list', [
                { rule: 'required' }
            ])
            .addField('#edit_vendor_state_list', [
                { rule: 'required' }
            ])
            .addField('#edit_vendor_address', [
                { rule: 'required' },
                { rule: 'minLength', value: 5 },
            ]);

        edit_vendor_form.onSuccess((event) => {
            document.getElementById("edit_vendor_form").submit();
        });

        // Dynamic inline Branch Address fields for Edit (Simple Textarea, matching addleads)
        let editBranchCount = {{ !empty($vendorAddresses) ? count($vendorAddresses) : 1 }};
        function appendEditBranchField() {
            let blockId = `edit_branch_block_${editBranchCount}`;
            let html = `
                <div class="branch-entry-block mb-2 pt-2 border-top" id="${blockId}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-bold text-muted">Additional Branch Address #${editBranchCount + 1}</small>
                        <button type="button" class="btn btn-sm text-danger p-0 btn-remove-edit-branch-field" data-target="#${blockId}" title="Remove Branch">
                            <i class="fas fa-times-circle me-1"></i> Remove
                        </button>
                    </div>
                    <textarea name="branch_address[]" class="form-control mb-1" rows="3"
                        placeholder="Additional Branch Address #${editBranchCount + 1}..."></textarea>
                    <input type="hidden" name="branch_id[]" value="">
                </div>
            `;
            $('#edit_branch_address_fields_container').append(html);
            editBranchCount++;
        }

        $('#btnEditAddBranchField').on('click', function() {
            appendEditBranchField();
        });

        $(document).on('click', '.btn-remove-edit-branch-field', function() {
            let target = $(this).data('target');
            $(target).remove();
        });
    </script>
@endsection
