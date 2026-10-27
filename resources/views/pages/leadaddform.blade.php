@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get(); // Fetching all countries from the database
    @endphp
    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Add Lead Enquiry</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="/saveleads" id="lead-add-form" autocomplete="new-password">
                <div class="row mb-4">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="customerSelect" class="form-label">
                                Customer/Company Name <span style="color:red">*</span>
                            </label>
                            <!-- Select2 dropdown -->
                            <select id="lead_customerSelect" name="LeadName" class="form-select" required>
                                <option value="">Select Customer</option>
                                <option value="new">New Customer</option>
                                @if ($customers = App\Models\Customer::where('soft_d', 0)->get())
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->customername }}" data-id="{{ $customer->id }}">
                                            {{ $customer->customername }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <!-- New Customer Name Input (Hidden initially) -->
                            <input type="text" id="newCustomerName" name="newCustomerName" class="form-control mt-2"
                                autocomplete="new-password"
                                placeholder="Enter New Customer Name" style="display: none;">

                            <div class="mt-2 text-danger customer-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">GST NO</label>
                            <div class="gst-input-div">
                                <input type="decimal" name="gst" id="lead_gst" class="form-control gst-number-field mb-2"
                                    autocomplete="new-password"
                                    placeholder="GST NO">
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                        </div>
                    </div>

                    <input type="hidden" name="customeriid" class="form-control" value="" placeholder="">

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mt-3">
                                    <label for="status" anme class="form-label">Select Status</label>
                                    <select class="form-select" id="leadstatus" name="Leadstatus">
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
                            </div>
                            @php
                                $todayDate = date('Y-m-d', strtotime('today'));
                            @endphp


                            <div class="col-lg-6">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Source of Enquiry</label>
                                    <select class="form-select " name="Leadsource" id="source_enquiry">
                                        <option value="">-- Choose Source --</option>
                                        @if ($status = App\Models\Dropdowndata::where('formid', 4)->orderBy('orderno', 'asc')->get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->dropdowndata }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4" id="convertedOptionsDiv" style="display: none;">
                        <div class="col-lg-6">
                            <div class="form-group mt-3">
                                <label for="converteddate" class="form-label">Converted Date <span
                                        style="color:red">*</span></label>
                                <input type="date" class="form-control" name="converteddate" id="converteddate"
                                    value="{{ $todayDate }}" max="{{ $todayDate }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mt-3">
                                <label class="form-label">Categories <span style="color:red">*</span></label>
                                <select class="form-select" name="category" id="catepro">
                                    <option value="">Choose Category</option>
                                    @foreach (App\Models\ProductCategory::get() as $data)
                                        <option value="{{ $data->id }}" data-sub="{{ $data->sub_category }}" data-type="{{ $data->type }}">
                                            {{ $data->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mt-3">
                                <label class="form-label">Products <span style="color:red">*</span></label>
                                <select class="form-select" name="products" id="pprod">
                                    <option value="">Choose Product</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mt-3">
                                <label class="form-label">Quantity <span style="color:red">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="quantity"
                                    placeholder="Enter Quantity">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mt-3">
                                <label class="form-label">GA Number</label>
                                <input type="file" class="form-control" name="ga_number" id="ga_number"
                                    accept=".pdf, .docx, .jpg, .png">
                            </div>
                        </div>

                        <div class="col-lg-6" id="specialField" style="display: none;">
                            <div class="form-group mt-3">
                                <label for="special_input" class="form-label">Assign Designer <span
                                        style="color:red">*</span></label>
                                <select class="form-select" name="assigndesign" id="special_input">
                                    <option value="">Choose Designer</option>
                                    @foreach (App\Models\User::where('role', 'Design Engineer')->get() as $us)
                                        <option value="{{ $us->id }}">{{ $us->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>




                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Communication Address </label>
                            <button type="button"
                class="btn btn-sm btn-outline-primary"
                onclick="copyBillingAddress()">
                Same as Billing
            </button>
                            <textarea name="communicate_address" id="lead_com_address" class="form-control"
                                autocomplete="new-password"
                                placeholder="Communication Address"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Billing Address</label>
                            <textarea name="billaddress" class="form-control" id="lead_address"
                                autocomplete="new-password"
                                placeholder="Billing Address"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Enquiry Details</label>
                            <textarea name="enquirydetail" class="form-control" id="enquiry_details"
                                placeholder="Enquiry Details"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Billing Country</label>
                            <select class="form-select " name="billingcountry" id="country">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Contact Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="contactname" id="lead_contactName" class="form-control"
                                autocomplete="new-password"
                                placeholder="Contact Name" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Billing State</label>
                            <select class="form-select " name="state_id" id="lead_state_list">
                                <option value=""> -- Choose State -- </option>
                                @if ($product = App\Models\Statelist::get())
                                    @foreach ($product as $p)
                                        <option value="{{ $p->id }}">{{ $p->state }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" id="lead_cus_email" placeholder="E-mail"
                                autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Billing City</label>
                            <select class="form-select " name="citylist" id="lead_city_list">
                                <option value=""> -- Choose City -- </option>

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="decimal" pattern="[6-9]{1}[0-9]{9}" maxlength="10"
                                autocomplete="new-password"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" name="phonumber"
                                id="lead_contact_no" class="form-control" placeholder="Contact Number" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Billing Postal Code</label>
                            <div class="gst-input-div">
                                <input type="text" name="postalcode" id="lead_postalcode"
                                    autocomplete="new-password"
                                    class="form-control pincode-field mb-2" placeholder="Postal Code">
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3">
    <div class="form-group mt-3">
        <label class="form-label">Designation</label>

        <select name="designation"
                id="lead-designation"
                class="form-control select2">
            <option value="">Select Designation</option>

            @foreach(App\Models\Dropdowndata::where('formid',80)->orderBy('orderno')->get() as $data)
                <option value="{{ $data->id }}">
                    {{ $data->dropdowndata }}
                </option>
            @endforeach

            <option value="add_new">➕ Add New Designation</option>
        </select>
    </div>
</div>
<div class="col-lg-3">
    <div class="form-group mt-3">
        <label class="form-label">Department</label>

        <select name="department"
                id="lead-department"
                class="form-control select2">
            <option value="">Select Department</option>

            @foreach(App\Models\Dropdowndata::where('formid',81)->orderBy('orderno')->get() as $data)
                <option value="{{ $data->id }}">
                    {{ $data->dropdowndata }}
                </option>
            @endforeach

            <option value="add_new">➕ Add New Department</option>
        </select>
    </div>
</div>

                    {{-- <div class="col-lg-3">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Designation </label>
                            <input type="text" name="designation" class="form-control" placeholder="Designation"
                                id="lead-designation">
                                @if ($status = App\Models\Dropdowndata::where('formid', 80)->orderBy('orderno', 'asc')->get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->dropdowndata }}
                                                </option>
                                            @endforeach
                                        @endif
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Department </label>
                            <input type="text" name="department" class="form-control" placeholder="Department"
                                id="lead-department">
                                @if ($status = App\Models\Dropdowndata::where('formid', 81)->orderBy('orderno', 'asc')->get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->dropdowndata }}
                                                </option>
                                            @endforeach
                                        @endif
                        </div>
                    </div> --}}

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Sales Manager</label>
                            <select class="form-select" name="assigned_to" id="assigned_to">
                                <option value="">Choose User</option>
                                <option value="{{ Auth::user()->id }}" selected>Self ({{ Auth::user()->name }})</option>
                                <!-- Self option -->
                                @foreach (App\Models\User::where('id', '!=', Auth::user()->id)->get() as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @php
                        $todayDate = date('Y-m-d', strtotime('today'));
                    @endphp

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="exampleFormControlInput1" class="form-label">Enquiry Date</label>
                            <input type="date" class="form-control" id="entry_date" name="Entrydate"
                                placeholder="Enquiry Date" min="" value="{{ $todayDate }}" max="{{ $todayDate }}">

                            <input type="hidden" name="proceed" value="1">
                        </div>
                    </div>

                </div>
        </div>
        <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mb-3">Save Enquiry</button>
        </form>
        {{-- </div> --}}
    </section>

    <div class="modal fade" id="addMasterModal">
<div class="modal-dialog">
<form method="POST" id="masterForm" action="{{ route('master.store') }}">
@csrf
<input type="hidden" name="formid" id="master_formid">

<div class="modal-content">
    <div class="modal-header">
        <h5 id="masterTitle"></h5>
    </div>

    <div class="modal-body">
        <div id="masterFormMessage" class="alert d-none" role="alert"></div>
        <input type="text" name="name"
               id="masterName"
               class="form-control mb-2"
               placeholder="Enter name"
               maxlength="30"
               required>

        <!--<input type="number" name="orderno"-->
        <!--       class="form-control"-->
        <!--       placeholder="Order No"-->
        <!--       required>-->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="masterSaveBtn">Save</button>
    </div>
</div>
</form>
</div>
</div>

@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let statusDropdown = document.getElementById("leadstatus");
            let convertedOptionsDiv = document.getElementById("convertedOptionsDiv");
            let categoryDropdown = document.getElementById("catepro");
            let specialField = document.getElementById("specialField");

            let convertedDate = document.getElementById("converteddate");
            let pprod = document.getElementById("pprod");
            let quantity = document.getElementById("quantity");

            function toggleRequiredFields(isRequired) {
                convertedDate.required = isRequired;
                categoryDropdown.required = isRequired;
                pprod.required = isRequired;
                quantity.required = isRequired;
            }

            statusDropdown.addEventListener("change", function () {
                let selectedOption = statusDropdown.options[statusDropdown.selectedIndex];
                let selectedText = selectedOption.getAttribute("data-status");

                if (selectedText === "WON") {
                    convertedOptionsDiv.style.display = "flex";
                    toggleRequiredFields(true);
                } else {
                    convertedOptionsDiv.style.display = "none";
                    toggleRequiredFields(false);
                    specialField.style.display = "none";
                    document.getElementById("special_input").required = false;
                }
            });

            categoryDropdown.addEventListener("change", function () {
                let selectedOption = categoryDropdown.options[categoryDropdown.selectedIndex];
                let subCategory = selectedOption.getAttribute("data-sub");
                let type = selectedOption.getAttribute("data-type");

                if (subCategory === "special") {
                    specialField.style.display = "block";
                    document.getElementById("special_input").required = true;
                } else {
                    specialField.style.display = "none";
                    document.getElementById("special_input").required = false;
                }
            });
        });
    </script>

    <script>
        let add_lead_form = new window.JustValidate('#lead-add-form');

        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        const nameRegex = /^[A-Za-z0-9\s().,&/-]+$/;
        const numberRegex = /^[0-9]*$/;

        add_lead_form
            .addField('#lead_customerSelect', [{
                rule: 'required',
            },])
            .addField('#newCustomerName', [{
                validator: (value) => {
                    const selected = document.querySelector('#lead_customerSelect').value;
                    if (selected === 'new') {
                        return value.trim().length >= 3;
                    }
                    return true; // Skip validation if not "new"
                },
                errorMessage: 'Minimum 3 characters required',
            },
            {
                validator: (value) => {
                    const selected = document.querySelector('#lead_customerSelect').value;
                    if (selected === 'new') {
                        return value.trim().length <= 80;
                    }
                    return true;
                },
                errorMessage: 'Maximum 80 characters allowed',
            },
            {
                validator: (value) => {
                    const selected = document.querySelector('#lead_customerSelect').value;
                    if (selected === 'new') {
                        return nameRegex.test(value);
                    }
                    return true;
                },
                errorMessage: 'Alphabets Only',
            },
            ])
            .addField('#lead_contactName', [
                {
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 2,
                },
                {
                    rule: 'maxLength',
                    value: 80,
                },
            ])
            .addField('#lead_contact_no', [{
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 10,
            },
            {
                rule: 'maxLength',
                value: 10,
            },
            {
                validator: (value) => {
                    if (numberRegex.test(value)) {
                        return true;
                    } else {
                        return false;
                    }
                },
                errorMessage: "Numbers Only",
            },
            ]);

        if (localSettingStorage) {

            if (gstValidationLocal == "active") {
                add_lead_form.addField('#lead_gst', [{
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 15,
                },
                {
                    rule: 'maxLength',
                    value: 15,
                },
                ])
            }
        }

        add_lead_form.onSuccess((event) => {
            var $this = $(".gst-number-field");
            var rawGst = $(".gst-number-field").val() || '';
            var gstNumber = rawGst.trim().toUpperCase();

            if (gstNumber.length == 15 && gstValidationLocal == "active") {
                // If existing customer is selected from dropdown, submit directly
                if ($("#lead_customerSelect").val() && $("#lead_customerSelect").val() !== 'new') {
                    document.getElementById("lead-add-form").submit();
                    return;
                }

                // Check local DB first
                $.ajax({
                    url: '/get-customer-by-gst/' + encodeURIComponent(gstNumber),
                    method: 'GET',
                    success: function (dbRes) {
                        if (dbRes && dbRes.found) {
                            document.getElementById("lead-add-form").submit();
                        } else {
                            // Check RapidAPI
                            $.ajax({
                                url: 'https://gst-return-status.p.rapidapi.com/free/gstin/' + encodeURIComponent(gstNumber),
                                method: 'GET',
                                async: true,
                                crossDomain: true,
                                beforeSend: function () {
                                    $(".web-loader").hide();
                                },
                                headers: {
                                    'x-rapidapi-key': 'f216e3cbefmsh74bb68190d526a5p1fcdb1jsn7712055aeb4a',
                                    'x-rapidapi-host': 'gst-return-status.p.rapidapi.com'
                                },
                                success: function (response) {
                                    if (response.success) {
                                        $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').show();
                                        $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').hide();
                                        $this.closest('.form-group').find('.error-msg').text('');
                                        document.getElementById("lead-add-form").submit();
                                    } else {
                                        $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                                        $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                                        $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                                    }
                                },
                                error: function () {
                                    // In case of network/rate-limit error, allow submission if form is valid
                                    document.getElementById("lead-add-form").submit();
                                }
                            });
                        }
                    },
                    error: function () {
                        document.getElementById("lead-add-form").submit();
                    }
                });
            } else {
                document.getElementById("lead-add-form").submit();
            }
        });
    </script>
<script>
function copyBillingAddress() {
    const billing = document.getElementById("lead_address").value;
    document.getElementById("lead_com_address").value = billing;
}
</script>
<script>
// $('.select2').select2({
//     width: '100%'
// });

$('#lead-designation').on('change', function () {
    if (this.value === 'add_new') {
        openMasterModal(80, 'Add Designation');
        $(this).val('').trigger('change');
    }
});

$('#lead-department').on('change', function () {
    if (this.value === 'add_new') {
        openMasterModal(81, 'Add Department');
        $(this).val('').trigger('change');
    }
});

function openMasterModal(formid, title) {
    $('#master_formid').val(formid);
    $('#masterTitle').text(title);
    $('#addMasterModal').modal('show');
}

// AJAX Master Form Submission
$('#masterForm').on('submit', function (e) {
    e.preventDefault();
    
    let form = $(this);
    let saveBtn = $('#masterSaveBtn');
    let messageDiv = $('#masterFormMessage');
    let formid = $('#master_formid').val();
    let name = $('#masterName').val();
    
    // Validate
    if (!name.trim()) {
        messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
        messageDiv.text('Please enter a name');
        return;
    }
    
    // Disable button and show loading
    saveBtn.prop('disabled', true);
    saveBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Saving...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        success: function (response) {
            messageDiv.removeClass('d-none alert-danger').addClass('alert-success');
            messageDiv.text('✓ ' + name + ' added successfully!');
            
            // Reset form
            form[0].reset();
            $('#masterName').val('');
            
            // Refresh the relevant dropdown
            if (formid == 80) {
                // Refresh Designation dropdown
                refreshDesignationDropdown();
            } else if (formid == 81) {
                // Refresh Department dropdown
                refreshDepartmentDropdown();
            }
            
            // Close modal after 2 seconds
            setTimeout(function () {
                $('#addMasterModal').modal('hide');
                messageDiv.addClass('d-none');
            }, 2000);
        },
        error: function (xhr, status, error) {
            messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
            let errorMsg = 'An error occurred while saving.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            messageDiv.text('✗ ' + errorMsg);
        },
        complete: function () {
            saveBtn.prop('disabled', false);
            saveBtn.html('Save');
        }
    });
});

// Refresh Designation Dropdown
function refreshDesignationDropdown() {
    $.ajax({
        url: '/get-designations',
        type: 'GET',
        success: function (response) {
            let designationSelect = $('#lead-designation');
            designationSelect.empty();
            designationSelect.append('<option value="">Select Designation</option>');
            
            $.each(response, function (key, value) {
                designationSelect.append('<option value="' + value.id + '">' + value.dropdowndata + '</option>');
            });
            
            designationSelect.append('<option value="add_new">➕ Add New Designation</option>');
            
            // Reinitialize Select2 if it exists
            if ($.fn.select2) {
                designationSelect.select2({width: '100%'});
            }
        }
    });
}

// Refresh Department Dropdown
function refreshDepartmentDropdown() {
    $.ajax({
        url: '/get-departments',
        type: 'GET',
        success: function (response) {
            let departmentSelect = $('#lead-department');
            departmentSelect.empty();
            departmentSelect.append('<option value="">Select Department</option>');
            
            $.each(response, function (key, value) {
                departmentSelect.append('<option value="' + value.id + '">' + value.dropdowndata + '</option>');
            });
            
            departmentSelect.append('<option value="add_new">➕ Add New Department</option>');
            
            // Reinitialize Select2 if it exists
            if ($.fn.select2) {
                departmentSelect.select2({width: '100%'});
            }
        }
    });
}
</script>

@endsection