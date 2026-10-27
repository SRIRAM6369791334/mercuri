@extends('layouts.app')
@section('title', 'update customer')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get(); // Fetching all countries from the database
    @endphp
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
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
                            <h5 class="mt-2">Edit Customer</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('customer.update', $customer->id) }}" method="POST" id="edit-customer-form" autocomplete="off" data-form-type="other" data-lpignore="true">
                @csrf
                @method('PUT')
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Customer Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="customername" id="customer_name" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                value="{{ old('customername', $customer->customername) }}" placeholder="Customer Name"
                                required>
                            <div class="mt-2 text-danger customer-err-msg"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST NO <span
                                    style="color:red">*</span></label>
                            <div class="gst-input-div">
                                <input type="decimal" id="cus-gst-number" name="gstno"
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                    value="{{ old('gstno', $customer->gstno) }}" class="form-control gst-number-field"
                                    placeholder="GST NO" required>
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                                <span class="error-msg text-danger"></span>
                            </div>
                            <div class="mt-2 text-danger gst-err-msg"></div>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Address <span
                                    style="color:red">*</span></label>
                            <textarea name="address" class="form-control" id="cus-address" placeholder="Billing Address"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                required>{{ old('address', $customer->address) }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Communication Address </label>
                             <button type="button"
                class="btn btn-sm btn-outline-primary"
                onclick="copyBillingAddress()">
                Same as Billing
            </button>
                            <textarea name="communication_address" id="cus_com_address" class="form-control" placeholder="Communication Address"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                required>{{ old('address', $customer->communication_address) }}</textarea>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Country <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="country" id="cus-country" required autocomplete="off" data-lpignore="true">
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ $customer->country == $country->id ? 'selected' : '' }}>
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
                                    <option value="{{ $user->id }}" {{ (int) $customer->assigned_user === (int) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Contact Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="contact_name" id="customer-name" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                value="{{ old('contact_name', $customer->contact_name) }}" placeholder="Contact Name"
                                required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing State <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="state" id="state_listt" required autocomplete="off" data-lpignore="true">
                                <option value=""> -- Choose State -- </option>
                                @if($product = App\Models\Statelist::get())
                                    @foreach ($product as $p)

                                        <option value="{{ $p->id }}" @if ($customer->state == $p->id) selected @endif>{{ $p->state }}
                                        </option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">E-mail <span
                                    style="color:red">*</span></label>
                            <input type="email" name="cus_email" id="cus-email" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                value="{{ old('cus_email', $customer->cus_email) }}" placeholder="E-mail" required>
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

                    {{-- <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Country Code <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="country_code" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                        {{ $country->code }} ({{ $country->dial_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="decimal" name="contact_no" pattern="[6-9]{1}[0-9]{9}" maxlength="10"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                value="{{ old('contact_no', $customer->contact_no) }}" class="form-control"
                                placeholder="Contact Number" required id="cus-mobile">
                            <div class="mt-2 text-danger mobile-err-msg"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Postal Code <span
                                    style="color:red">*</span></label>
                            <input type="decimal" name="postalcode" value="{{ old('postalcode', $customer->postalcode) }}"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                class="form-control" placeholder="Postal Code" required id="cus-pincode">
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
                <option value="{{ $data->id }}"{{ $customer->designation == $data->id ? 'selected' : '' }}>
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
                <option value="{{ $data->id }}"{{ $customer->department == $data->id ? 'selected' : '' }}>
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
                            <input type="text" name="designation" class="form-control" placeholder="Designation"
                                value="{{ old('designation', $customer->designation) }}" id="cus-designation">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Department </label>
                            <input type="text" name="department" class="form-control" placeholder="Department"
                                value="{{ old('department', $customer->department) }}" id="cus-department">
                        </div>
                    </div> --}}
                </div>
                <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn my-3">Update Customer</button>
            </form>

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
               maxlength="30"
               required>

        <input type="number" name="orderno"
               class="form-control"
               placeholder="Order No"
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
        const selectedCustomerState = @json(old('state', $customer->state));
        const selectedCustomerCity = @json(old('city', $customer->city));

        function loadCustomerCities(stateId, selectedCity = '') {
            const $citySelect = $('#cus-city');
            $citySelect.empty().append('<option value=""> -- Choose City -- </option>');

            if (!stateId) {
                return;
            }

            $.ajax({
                url: '/get-cities/' + stateId,
                method: 'GET',
                success: function (response) {
                    $.each(response, function (index, city) {
                        $citySelect.append(
                            `<option value="${city.id}" ${city.id == selectedCity ? 'selected' : ''}>${city.city_name}</option>`
                        );
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Unable to fetch cities!'
                    });
                }
            });
        }

        $(document).ready(function () {
            loadCustomerCities(selectedCustomerState, selectedCustomerCity);

            $('#state_listt').on('change', function () {
                loadCustomerCities($(this).val());
            });
        });

        let edit_customer_form = new window.JustValidate('#edit-customer-form');

        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        const nameRegex = /^[A-Z a-z]+$/;
        const numberRegex = /^[0-9]*$/;

        edit_customer_form
            .addField('#customer_name', [
                {
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 3,
                },
                {
                    rule: 'maxLength',
                    value: 200,
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
                    value: 200,
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
            .addField('#cus-email', [
                {
                    rule: 'required',
                },
                {
                    rule: 'email',
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
            ]);
            if (localSettingStorage) {

                if (gstValidationLocal == "active") {
                    edit_customer_form.addField('#cus-gst-number', [{
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

            let editDupCheckTimeout = null;

            function checkEditGstPincodeDuplicate() {
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
                        exclude_id: {{ $customer->id }},
                        gstno: gstNo,
                        postalcode: pincode,
                        customername: customerName
                    },
                    success: function (res) {
                        if (res.exists) {
                            if (res.field === 'gst_pincode') {
                                $('.gst-err-msg').text(res.message).show();
                                $('.pincode-err-msg').text('Duplicate: Same GST and Pincode already exists for another customer.').show();
                                $('#cus-gst-number, #cus-pincode').addClass('is-invalid');
                            } else if (res.field === 'gstno') {
                                $('.gst-err-msg').text(res.message).show();
                                $('#cus-gst-number').addClass('is-invalid');
                            } else if (res.field === 'name_pincode') {
                                $('.customer-err-msg').text(res.message).show();
                                $('.pincode-err-msg').text('Duplicate: Same Name and Pincode already exists for another customer.').show();
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
                clearTimeout(editDupCheckTimeout);
                editDupCheckTimeout = setTimeout(checkEditGstPincodeDuplicate, 350);
            });

            edit_customer_form.onSuccess((event) => {
                var $this = $(".gst-number-field");
                var gstNumber = $(".gst-number-field").val();
                if (gstNumber && gstNumber.length == 15 && gstValidationLocal == "active") {
                    $.ajax({
                        url: 'https://gst-return-status.p.rapidapi.com/free/gstin/' + gstNumber,
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
                                document.getElementById("edit-customer-form").submit();
                            } else {
                                $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                                $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                                $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error:", error);
                            $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                            $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                            $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                        }
                    });
                } else {
                    document.getElementById("edit-customer-form").submit();
                }
            });
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
<script>
function copyBillingAddress() {
    const billing = document.getElementById("cus-address").value;
    document.getElementById("cus_com_address").value = billing;
}
</script>
@endsection
