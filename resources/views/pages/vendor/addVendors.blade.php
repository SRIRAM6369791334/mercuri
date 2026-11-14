@extends('layouts.app')
@section('title', 'Add Vendor Details')
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
                            <h5 class="mt-2">Add Vendor Details</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('add-vendors') }}" id="add-vendor-form" autocomplete="new-password">
                <div class="row mb-4">
                    @csrf

                    <!-- 1. Left: Company Name -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="company_name" class="form-label">
                                Company Name <span style="color:red">*</span>
                            </label>
                            <input type="text" name="company_name" id="company_name" class="form-control"
                                placeholder="Company Name" required autocomplete="off">
                        </div>
                    </div>

                    <!-- 1. Right: GST NO (Auto-fetches Company & Primary Address Details) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor-gst" class="form-label">GST NO <span style="color:red">*</span></label>
                            <div class="gst-input-div position-relative">
                                <input type="text" name="gst_number" id="vendor-gst"
                                    class="form-control gst-number-field text-uppercase mb-1"
                                    placeholder="Enter 15-digit GST NO" maxlength="15" required autocomplete="off">
                                <i class="fas fa-check-circle" style="display:none; color: #28a745; position: absolute; right: 12px; top: 12px; font-size: 16px;"></i>
                                <i class="fas fa-times-circle" style="display:none; color: #dc3545; position: absolute; right: 12px; top: 12px; font-size: 16px;"></i>
                                <span class="spinner-border spinner-border-sm text-primary gst-loader" style="display:none; position: absolute; right: 12px; top: 12px;" role="status"></span>
                            </div>
                            <span class="error-msg text-danger small"></span>
                            <div class="gst-auto-msg text-success small" style="display:none;">
                                <i class="fas fa-bolt me-1"></i> <strong>Company details auto-fetched from GST!</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Left: Segment -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="addvendor_cat_list" class="form-label">Segment <span style="color:red">*</span></label>
                            <select class="form-select select2" name="pro_cat_id[]" id="addvendor_cat_list" multiple="multiple" required>
                                @if ($categories = App\Models\ProductCategory::orderBy('category_name')->get())
                                    @foreach ($categories as $data)
                                        <option value="{{ $data->id }}">
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
                            <label for="alternate_name" class="form-label">Alternate Name</label>
                            <input type="text" name="alternate_name" id="alternate_name" class="form-control"
                                placeholder="Alternate / Short Name" autocomplete="off">
                        </div>
                    </div>

                    <!-- 3. Left: Primary Registered Address (Auto-filled from GST) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor-address" class="form-label">Primary Registered Address <span style="color:red">*</span></label>
                            <textarea name="address" class="form-control" id="vendor-address" rows="3"
                                placeholder="Primary Registered Address (Auto-filled from GST)" required></textarea>
                        </div>
                    </div>

                    <!-- 3. Right: Secondary / Branch Address (In Same Form, Matching addleads Layout) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="secondary_branch_address" class="form-label mb-0">Secondary / Branch Address</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddBranchField" style="font-size: 12px; padding: 2px 10px;">
                                    <i class="fas fa-plus me-1"></i> Add Another Branch
                                </button>
                            </div>
                            <div id="branch_address_fields_container">
                                <div class="branch-entry-block mb-2" id="branch_block_0">
                                    <textarea name="branch_address[]" id="secondary_branch_address" class="form-control" rows="3"
                                        placeholder="Enter secondary or branch plant address (Optional)..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Left: Country -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor-country" class="form-label">Country <span style="color:red">*</span></label>
                            <select class="form-select" name="country_id" id="vendor-country">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 4. Right: Contact Person (POC) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor_poc_name" class="form-label">Contact Person (Optional)</label>
                            <input type="text" name="poc_name" id="vendor_poc_name" class="form-control"
                                placeholder="Contact Person Name" autocomplete="off">
                        </div>
                    </div>

                    <!-- 5. Left: State (for Tax Calculation) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="addvendor_state_list" class="form-label">State (for Tax Calculation) <span style="color:red">*</span></label>
                            <select class="form-select select2 state_list" name="state_id" id="addvendor_state_list" required>
                                <option value=""> -- Choose State -- </option>
                                @if($states = App\Models\Statelist::orderBy('state')->get())
                                    @foreach ($states as $p)
                                        <option value="{{ $p->id }}">{{ $p->state }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- 5. Right: E-mail -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor_cus_email" class="form-label">E-mail (Optional)</label>
                            <input type="email" name="email" class="form-control" id="vendor_cus_email"
                                placeholder="vendor@example.com" autocomplete="off">
                        </div>
                    </div>

                    <!-- 6. Left: City Dropdown (Auto-populated from State) -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="addvendor_city_list" class="form-label">City</label>
                            <select class="form-select select2" name="city_id" id="addvendor_city_list">
                                <option value=""> -- Choose City -- </option>
                            </select>
                        </div>
                    </div>

                    <!-- 6. Right: Contact Phone -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <input type="hidden" name="countrycode" value="76">
                            <label for="vendor_contact_no" class="form-label">Contact Number (Optional)</label>
                            <input type="text" name="contact_number" id="vendor_contact_no" class="form-control"
                                placeholder="10-digit Mobile Number" maxlength="10" autocomplete="off">
                        </div>
                    </div>

                    <!-- 7. Left: Postal Code -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="add-vendor-postalcode" class="form-label">Postal Code (Optional)</label>
                            <input type="text" name="postal_code" id="add-vendor-postalcode" class="form-control"
                                placeholder="Postal Code" maxlength="10" autocomplete="off">
                        </div>
                    </div>

                    <!-- 7. Right: Vendor Status -->
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="vendor_status" class="form-label">Vendor Status <span style="color:red">*</span></label>
                            <select class="form-select" name="vendor_status" id="vendor_status" required>
                                <option value="active" selected> Active </option>
                                <option value="inactive"> Inactive </option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Submit Button Centered (Matching addleads) -->
                <button type="submit" id="save-vendor-btn" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-4">Save Vendor</button>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Re-initialize select2 cleanly with explicit configuration
            if ($('#addvendor_cat_list').length) {
                $('#addvendor_cat_list').select2({ width: '100%', placeholder: 'Select Segment' });
            }
            if ($('#addvendor_state_list').length) {
                if ($('#addvendor_state_list').hasClass('select2-hidden-accessible')) {
                    $('#addvendor_state_list').select2('destroy');
                }
                $('#addvendor_state_list').select2({ width: '100%', placeholder: '-- Choose State --', allowClear: true });
            }
            if ($('#addvendor_city_list').length) {
                if ($('#addvendor_city_list').hasClass('select2-hidden-accessible')) {
                    $('#addvendor_city_list').select2('destroy');
                }
                $('#addvendor_city_list').select2({ width: '100%', placeholder: '-- Choose City --', allowClear: true });
            }
        });

        // Initialize JustValidate
        let add_vendor_form = new window.JustValidate('#add-vendor-form');

        add_vendor_form
            .addField('#vendor-gst', [
                { rule: 'required' },
                { rule: 'minLength', value: 15 },
                { rule: 'maxLength', value: 15 },
            ])
            .addField('#company_name', [
                { rule: 'required' },
                { rule: 'minLength', value: 2 },
            ])
            .addField('#addvendor_cat_list', [
                { rule: 'required' }
            ])
            .addField('#addvendor_state_list', [
                { rule: 'required' }
            ])
            .addField('#vendor-address', [
                { rule: 'required' },
                { rule: 'minLength', value: 5 },
            ]);

        add_vendor_form.onSuccess((event) => {
            document.getElementById("add-vendor-form").submit();
        });

        // State Mapping Helper (GST Prefix to state_list.id)
        const gstStateMap = {
            '01': 15, '02': 13, '03': 27, '04': 7,  '05': 33,
            '06': 14, '07': 9,  '08': 29, '09': 34, '10': 5,
            '11': 30, '12': 3,  '13': 25, '14': 22, '15': 24,
            '16': 32, '17': 20, '18': 4,  '19': 35, '20': 16,
            '21': 26, '22': 6,  '23': 23, '24': 12, '27': 21,
            '28': 2,  '29': 17, '30': 11, '31': 19, '32': 18,
            '33': 31, '34': 28, '35': 1,  '36': 36, '37': 2
        };

        // Real-time GST Auto-fetch on typing 15 characters
        let gstLookupTimeout = null;
        $('#vendor-gst').on('input change paste', function() {
            clearTimeout(gstLookupTimeout);
            let $input = $(this);
            let gstVal = ($input.val() || '').trim().toUpperCase();
            $input.val(gstVal);

            let $group = $input.closest('.form-group');
            $group.find('.gst-loader').hide();
            $group.find('i.fa-check-circle').hide();
            $group.find('i.fa-times-circle').hide();
            $group.find('.error-msg').text('');
            $group.find('.gst-auto-msg').hide();

            // Auto-select state from first 2 digits of GST immediately and load cities
            if (gstVal.length >= 2) {
                let code = gstVal.substring(0, 2);
                if (gstStateMap[code]) {
                    let stId = String(gstStateMap[code]);
                    if ($('#addvendor_state_list').val() != stId) {
                        $('#addvendor_state_list').val(stId).trigger('change');
                        if (typeof window.fetchCitiesVendor === 'function') {
                            window.fetchCitiesVendor(stId, null);
                        }
                    }
                }
            }

            if (gstVal.length === 15) {
                $group.find('.gst-loader').show();
                gstLookupTimeout = setTimeout(function() {
                    $.ajax({
                        url: '/api/get-addresses-by-gst/' + encodeURIComponent(gstVal),
                        method: 'GET',
                        success: function(res) {
                            $group.find('.gst-loader').hide();
                            if (res && res.found && res.primary_address) {
                                let p = res.primary_address;
                                $group.find('i.fa-check-circle').show();
                                $group.find('.gst-auto-msg').show();

                                // 1. Company Name
                                if (p.company_name) {
                                    $('#company_name').val(p.company_name);
                                    if (!$('#alternate_name').val()) {
                                        $('#alternate_name').val(p.company_name.substring(0, 15));
                                    }
                                }

                                // 2. Primary Registered Address
                                let fullAddr = p.raw_address || p.address || '';
                                if (fullAddr) {
                                    $('#vendor-address').val(fullAddr);
                                }

                                // 3. Postal Code
                                if (p.postalcode) {
                                    $('#add-vendor-postalcode').val(p.postalcode);
                                }

                                // 4. State & City Dropdown Auto-selection
                                let targetState = p.state_id || gstStateMap[gstVal.substring(0, 2)];
                                let targetCity = p.city_id || p.city_name || null;
                                if (targetState) {
                                    $('#addvendor_state_list').val(String(targetState)).trigger('change');
                                    if (typeof window.fetchCitiesVendor === 'function') {
                                        window.fetchCitiesVendor(targetState, targetCity);
                                    }
                                }

                                // 5. If secondary addresses already exist under this GST, populate them inline
                                if (res.addresses && res.addresses.length > 1) {
                                    for (let i = 1; i < res.addresses.length; i++) {
                                        let extra = res.addresses[i];
                                        let extraAddr = extra.raw_address || extra.address || '';
                                        if (i === 1) {
                                            $('#secondary_branch_address').val(extraAddr);
                                        } else {
                                            appendBranchField(extraAddr);
                                        }
                                    }
                                }

                                // Revalidate fields
                                add_vendor_form.revalidate();
                            } else {
                                $group.find('i.fa-times-circle').show();
                                $group.find('.error-msg').text('GST details not found on portal.');
                            }
                        },
                        error: function() {
                            $group.find('.gst-loader').hide();
                            $group.find('i.fa-times-circle').show();
                            $group.find('.error-msg').text('Unable to fetch GST details. Please enter manually.');
                        }
                    });
                }, 350);
            }
        });

        // Dynamic inline Branch Address fields (Simple Textarea, matching addleads)
        let branchCount = 1;
        function appendBranchField(address = '') {
            let blockId = `branch_block_${branchCount}`;
            let html = `
                <div class="branch-entry-block mb-2 pt-2 border-top" id="${blockId}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-bold text-muted">Additional Branch Address #${branchCount + 1}</small>
                        <button type="button" class="btn btn-sm text-danger p-0 btn-remove-branch-field" data-target="#${blockId}" title="Remove Branch">
                            <i class="fas fa-times-circle me-1"></i> Remove
                        </button>
                    </div>
                    <textarea name="branch_address[]" class="form-control mb-1" rows="3"
                        placeholder="Additional Branch Address #${branchCount + 1}...">${address}</textarea>
                </div>
            `;
            $('#branch_address_fields_container').append(html);
            branchCount++;
        }

        $('#btnAddBranchField').on('click', function() {
            appendBranchField();
        });

        $(document).on('click', '.btn-remove-branch-field', function() {
            let target = $(this).data('target');
            $(target).remove();
        });
    </script>
@endsection
