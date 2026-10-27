@extends('layouts.app')
@section('title', 'Add Customer')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get(); // Fetching all countries from the database
    @endphp
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Add Customer</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <form method="POST" action="/savecustomer" id="add-customer-form" autocomplete="section-customer-add off" data-form-type="other"> --}}
<div id="add-customer-form" data-action="/savecustomer" autocomplete="off" data-form-type="other" data-lpignore="true">
                <div class="row mb-4">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Customer/Company Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="customername" class="form-control" id="customer_name"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Customer Name" required>
                            <div class="mt-2 text-danger customer-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST NO <span
                                    style="color:red">*</span></label>
                            <div class="gst-input-div">

                                <input type="decimal" name="gstno" class="form-control gst-number-field"
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                    placeholder="GST NO" required id="cus-gst-number">
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                            <div class="mt-2 text-danger gst-err-msg"></div>
                        </div>
                    </div>

                    {{-- <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Communication Address </label>
                            <textarea name="communication_address" class="form-control" placeholder="Communication Address" required></textarea>
                        </div>
                    </div> --}}
                   <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Address <span
                                    style="color:red">*</span></label>
                            <textarea name="address" class="form-control" placeholder="Billing Address" required
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                id="cus-address"></textarea>
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
                            <textarea name="communication_address" id="cus_com_address" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Communication Address" required></textarea>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Country <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="country" required id="cus-country" autocomplete="off" data-lpignore="true">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="assigned_user" class="form-label">Assigned User</label>
                            <select class="form-select" name="assigned_user" id="assigned_user">
                                <option value="">-- Choose User --</option>
                                @foreach (App\Models\User::where('userstatus', 'Active')->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Contact Name <span
                                    style="color:red">*</span></label>
                            <input type="text" id="customer-name" name="contact_name" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Contact Name" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing State <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="state" id="state_listt" required autocomplete="off" data-lpignore="true">
                                <option value=""> -- Choose State -- </option>
                                @if ($product = App\Models\Statelist::get())
                                    @foreach ($product as $p)
                                        <option value="{{ $p->id }}" {{ strtoupper(trim($p->state)) === 'TAMIL NADU' ? 'selected' : '' }}>{{ $p->state }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">E-mail</label>
                            <input type="email" id="cus-email" name="cus_email" class="form-control" placeholder="E-mail"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                            <div class="mt-2 text-danger email-err-msg"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing City <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="city" required id="cus-city" autocomplete="off" data-lpignore="true">
                                <option value=""> -- Choose City -- </option>

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="hidden" name="country_code" value="76" autocomplete="off">
                            <input type="decimal" name="contact_no" pattern="[6-9]{1}[0-9]{9}" maxlength="10"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="form-control"
                                placeholder="Contact Number" id="cus-mobile" required>
                            <div class="mt-2 text-danger mobile-err-msg"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Postal Code <span
                                    style="color:red">*</span></label>
                            <input type="number" name="postalcode" class="form-control" id="cus-pincode"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Postal Code" required>
                            <div class="mt-2 text-danger pincode-err-msg"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
    <div class="form-group mt-3">
        <label class="form-label">Designation</label>

        <select name="designation"
                id="cus-designation"
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
                id="cus-department"
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
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Designation </label>
                            <input type="text" name="designation" class="form-control" id="cus-designation"
                                placeholder="Designation">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Department </label>
                            <input type="text" name="department" class="form-control" id="cus-department"
                                placeholder="Department">
                        </div>
                    </div> --}}


                </div>
                {{--
        </div> --}}
        {{-- <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mb-3 mt-2">Save Customer</button> --}}
         <button type="button" id="save-customer-btn" class="btn btn-primary m-auto d-block add-employee-btn mb-3 mt-2">Save Customer</button>
    </div>
        </div>
    </section>

<div class="modal fade" id="addMasterModal">
<div class="modal-dialog">
<form method="POST" action="{{ route('master.store') }}">
@csrf
<input type="hidden" name="formid" id="master_formid">

<div class="modal-content">
    <div class="modal-header">
        <h5 id="masterTitle"></h5>
    </div>

    <div class="modal-body">
        <input type="text" name="name"
               class="form-control mb-2"
               placeholder="Enter name"
               autocomplete="off"
               maxlength="30"
               required>

        <input type="number" name="orderno"
               class="form-control"
               placeholder="Order No"
               autocomplete="off"
               required>
    </div>

    <div class="modal-footer">
        <button class="btn btn-success">Save</button>
    </div>
</div>
</form>
</div>
</div>

@endsection

@section('scripts')

    <script>
        let add_customer_form = new window.JustValidate('#add-customer-form');

        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        const nameRegex = /^[A-Z a-z]+$/;
        const numberRegex = /^[0-9]*$/;

        add_customer_form
            .addField('#customer_name', [
                {
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 3,
                },
            ])
            .addField('#customer-name', [
                {
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 3,
                },
                {
                    rule: 'maxLength',
                    value: 20,
                },
                {
                    validator: (value) => {
                        if (nameRegex.test(value)) {
                            return true;
                        } else {
                            return false;
                        }
                    },
                    errorMessage: "Alphabets Only",
                }
            ])
            .addField('#cus-address', [
                {
                    rule: 'required',
                }
            ])
            .addField('#cus-country', [
                {
                    rule: 'required',
                }
            ])
            .addField('#state_listt', [
                {
                    rule: 'required',
                }
            ])
            .addField('#cus-city', [
                {
                    rule: 'required',
                }
            ])
            .addField('#cus-pincode', [
                {
                    rule: 'required',
                },
                {
                    rule: 'number',
                },
                {
                    rule: 'minLength',
                    value: 6,
                },
                {
                    rule: 'maxLength',
                    value: 6,
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
            ])
            .addField('#cus-mobile', [
                {
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
            ])
            .addField('#cus-designation', [
                {
                    rule: 'required',
                },
                // {
                //     rule: 'minLength',
                //     value: 3,
                // },
                // {
                //     rule: 'maxLength',
                //     value: 30,
                // },
                // {
                //     validator: (value) => {
                //         if (value != '') {
                //             if (nameRegex.test(value)) {
                //                 return true;
                //             } else {
                //                 return false;
                //             }
                //         } else {
                //             return true;
                //         }
                //     },
                //     errorMessage: "Alphabets Only",
                // }
            ])
            .addField('#cus-department', [
                {
                    rule: 'required',
                },
                // {
                //     rule: 'minLength',
                //     value: 3,
                // },
                // {
                //     rule: 'maxLength',
                //     value: 30,
                // },
                // {
                //     validator: (value) => {
                //         if (value != '') {
                //             if (nameRegex.test(value)) {
                //                 return true;
                //             } else {
                //                 return false;
                //             }
                //         } else {
                //             return true;
                //         }
                //     },
                //     errorMessage: "Alphabets Only",
                // }
            ])
            if (localSettingStorage) {

                if (gstValidationLocal == "active") {
                    add_customer_form.addField('#cus-gst-number', [{
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
            let dupCheckTimeout = null;

            function checkGstPincodeDuplicate() {
                const gstNo = $('#cus-gst-number').val() ? $('#cus-gst-number').val().trim() : '';
                const pincode = $('#cus-pincode').val() ? $('#cus-pincode').val().trim() : '';
                const customerName = $('#customer_name').val() ? $('#customer_name').val().trim() : '';

                if (!gstNo && !customerName) {
                    $('.gst-err-msg, .pincode-err-msg, .customer-err-msg').text('').hide();
                    $('#cus-gst-number, #cus-pincode, #customer_name').removeClass('is-invalid');
                    return;
                }

                $.ajax({
                    url: '{{ route("customer.check-duplicate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        gstno: gstNo,
                        postalcode: pincode,
                        customername: customerName
                    },
                    success: function (res) {
                        if (res.exists) {
                            if (res.field === 'gst_pincode') {
                                $('.gst-err-msg').text(res.message).show();
                                $('.pincode-err-msg').text('Duplicate: Same GST and Pincode already exists.').show();
                                $('#cus-gst-number, #cus-pincode').addClass('is-invalid');
                            } else if (res.field === 'gstno') {
                                $('.gst-err-msg').text(res.message).show();
                                $('#cus-gst-number').addClass('is-invalid');
                            } else if (res.field === 'name_pincode') {
                                $('.customer-err-msg').text(res.message).show();
                                $('.pincode-err-msg').text('Duplicate: Same Name and Pincode already exists.').show();
                                $('#customer_name, #cus-pincode').addClass('is-invalid');
                            }
                        } else {
                            $('.gst-err-msg, .pincode-err-msg, .customer-err-msg').text('').hide();
                            $('#cus-gst-number, #cus-pincode, #customer_name').removeClass('is-invalid');
                        }
                    }
                });
            }

            $('#cus-gst-number, #cus-pincode, #customer_name').on('input blur change', function () {
                clearTimeout(dupCheckTimeout);
                dupCheckTimeout = setTimeout(checkGstPincodeDuplicate, 350);
            });

            document.getElementById('save-customer-btn').addEventListener('click', function () {
                add_customer_form.revalidate().then((isValid) => {
                    if (isValid) {
                        handleCustomerSubmission();
                    }
                });
            });

            function handleCustomerSubmission() {
                var $this = $(".gst-number-field");
                var gstNumber = $this.val();

                if (gstNumber && gstNumber.length == 15 && gstValidationLocal == "active") {
                    $.ajax({
                        url: 'https://gst-return-status.p.rapidapi.com/free/gstin/' + gstNumber,
                        method: 'GET',
                        headers: {
                            'x-rapidapi-key': 'f216e3cbefmsh74bb68190d526a5p1fcdb1jsn7712055aeb4a',
                            'x-rapidapi-host': 'gst-return-status.p.rapidapi.com'
                        },
                        success: function (response) {
                            if (response.success) {
                                $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').show();
                                $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').hide();
                                $this.closest('.form-group').find('.error-msg').text('');
                                submitCustomerFormAjax();
                            } else {
                                $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                                $("#customer_name").val('');
                                $("#cus-address").val('');
                                $("#cus-pincode").val('');
                            }
                        },
                        error: function () {
                            $("#customer_name").val('');
                            $("#cus-address").val('');
                            $("#cus-pincode").val('');
                            $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                            $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                            $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                        }
                    });
                } else {
                    submitCustomerFormAjax();
                }
            }

            function buildFormData(container) {
                const formData = new FormData();
                container.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        if (el.checked) formData.append(el.name, el.value);
                    } else {
                        formData.append(el.name, el.value);
                    }
                });
                return formData;
            }

            function submitCustomerFormAjax() {
                const container = document.getElementById("add-customer-form");
                const formData = buildFormData(container);
                const $saveBtn = $('#save-customer-btn');

                $saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving Customer...');

                // Clear previous error messages
                $('.customer-err-msg, .gst-err-msg, .pincode-err-msg, .mobile-err-msg, .email-err-msg').text('').hide();
                $('#customer_name, #cus-gst-number, #cus-pincode, #cus-mobile, #cus-email').removeClass('is-invalid');

                $.ajax({
                    url: container.dataset.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Customer Saved',
                                text: response.message || 'Customer saved successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function (xhr) {
                        $saveBtn.prop('disabled', false).html('Save Customer');
                        console.error(xhr);

                        let errorMessage = 'Something went wrong while saving the customer. Please check the form and try again.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const firstKey = Object.keys(errors)[0];
                                errorMessage = errors[firstKey][0];
                            }

                            if (xhr.responseJSON.field === 'gst_pincode') {
                                $('.gst-err-msg').text(errorMessage).show();
                                $('.pincode-err-msg').text('Duplicate: Same GST and Pincode already exists.').show();
                                $('#cus-gst-number, #cus-pincode').addClass('is-invalid');
                                $('#cus-gst-number').focus();
                            } else if (xhr.responseJSON.field === 'gstno') {
                                $('.gst-err-msg').text(errorMessage).show();
                                $('#cus-gst-number').addClass('is-invalid').focus();
                            } else if (xhr.responseJSON.field === 'name_pincode') {
                                $('.customer-err-msg').text(errorMessage).show();
                                $('.pincode-err-msg').text('Duplicate: Same Customer Name and Pincode already exists.').show();
                                $('#customer_name, #cus-pincode').addClass('is-invalid');
                                $('#customer_name').focus();
                            } else if (xhr.responseJSON.field === 'customername') {
                                $('.customer-err-msg').text(errorMessage).show();
                                $('#customer_name').addClass('is-invalid').focus();
                            }
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Cannot Add Customer',
                            text: errorMessage,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
    </script>
    <script>
function copyBillingAddress() {
    const billing = document.getElementById("cus-address").value;
    document.getElementById("cus_com_address").value = billing;
}
</script>
<script>
// $('.select2').select2({
//     width: '100%'
// });

$('#cus-designation').on('change', function () {
    if (this.value === 'add_new') {
        openMasterModal(80, 'Add Designation');
        $(this).val('').trigger('change');
    }
});

$('#cus-department').on('change', function () {
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
</script>
@endsection
