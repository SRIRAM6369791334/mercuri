@extends('layouts.app')
@section('title', 'Add Expo Details')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get();
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
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h5 class="mt-2 mb-0">Add Expo Details</h5>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('pages.expo') }}" class="btn btn-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="add-expo-form" data-action="{{ route('pages.expo.store') }}" autocomplete="off" data-form-type="other" data-lpignore="true">
                <div class="row mb-4">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customer_name" class="form-label">Customer / Company Name <span style="color:red">*</span></label>
                            <input type="text" name="customername" class="form-control" id="customer_name"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Customer Name" required>
                            <div class="mt-2 text-danger customer-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-gst-number" class="form-label">GST NO (Optional)</label>
                            <div class="gst-input-div">
                                <input type="text" name="gstno" class="form-control gst-number-field"
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                    placeholder="GST NO" id="cus-gst-number">
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                            <div class="mt-2 text-danger gst-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-address" class="form-label">Billing Address</label>
                            <textarea name="address" class="form-control" placeholder="Billing Address"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                id="cus-address"></textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label for="cus_com_address" class="form-label">Communication Address </label>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyBillingAddress()">
                                Same as Billing
                            </button>
                            <textarea name="communication_address" id="cus_com_address" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Communication Address"></textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-country" class="form-label">Billing Country</label>
                            <select class="form-select" name="country" id="cus-country" autocomplete="off" data-lpignore="true">
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
                                    <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-contact-name" class="form-label">Contact Person Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="contact_name" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Contact Name" id="cus-contact-name" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-state" class="form-label">Billing State</label>
                            <select class="form-select" name="state" id="cus-state" autocomplete="off" data-lpignore="true">
                                <option value=""> -- Choose State -- </option>
                                @if ($states = App\Models\Statelist::get())
                                    @foreach ($states as $p)
                                        <option value="{{ $p->id }}">{{ $p->state }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-email" class="form-label">E-mail</label>
                            <input type="email" name="cus_email" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Email" id="cus-email">
                            <div class="mt-2 text-danger email-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-city" class="form-label">Billing City</label>
                            <select class="form-select" name="city" id="cus-city" autocomplete="off" data-lpignore="true">
                                <option value=""> -- Choose City -- </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-mobile" class="form-label">Contact Number <span style="color:red">*</span></label>
                            <input type="hidden" name="country_code" value="76" autocomplete="off">
                            <input type="text" name="contact_no" maxlength="10"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="form-control"
                                placeholder="Contact Number" required id="cus-mobile">
                            <div class="mt-2 text-danger mobile-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-pincode" class="form-label">Billing Postal Code</label>
                            <input type="number" name="postalcode" class="form-control"
                                autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true"
                                placeholder="Postal Code" id="cus-pincode">
                            <div class="mt-2 text-danger pincode-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label class="form-label">Designation</label>
                            <select name="designation" id="cus-designation" class="form-control select2">
                                <option value="">Select Designation</option>
                                @foreach(App\Models\Dropdowndata::where('formid',80)->orderBy('orderno')->get() as $data)
                                    <option value="{{ $data->id }}">{{ $data->dropdowndata }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-3">
                            <label class="form-label">Department</label>
                            <select name="department" id="cus-department" class="form-control select2">
                                <option value="">Select Department</option>
                                @foreach(App\Models\Dropdowndata::where('formid',81)->orderBy('orderno')->get() as $data)
                                    <option value="{{ $data->id }}">{{ $data->dropdowndata }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" id="save-expo-btn" class="btn btn-primary m-auto d-block add-employee-btn mb-3 mt-2">
                    Save Expo Details &amp; Create Lead
                </button>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script>
        function copyBillingAddress() {
            var billingAddress = document.getElementById('cus-address').value;
            document.getElementById('cus_com_address').value = billingAddress;
        }

        $(document).ready(function () {
            // City loader based on state
            $('#cus-state').on('change', function () {
                var stateId = $(this).val();
                if (stateId) {
                    $.ajax({
                        url: '/get-cities/' + stateId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#cus-city').empty().append('<option value=""> -- Choose City -- </option>');
                            $.each(data, function (key, value) {
                                $('#cus-city').append('<option value="' + value.id + '">' + value.city_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#cus-city').empty().append('<option value=""> -- Choose City -- </option>');
                }
            });

            // Save Expo Button Handler
            $('#save-expo-btn').on('click', function () {
                const customerName = $('#customer_name').val().trim();
                const contactNo = $('#cus-mobile').val().trim();
                const contactName = $('#cus-contact-name').val().trim();

                if (!customerName || !contactNo || !contactName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required Fields Missing',
                        text: 'Please fill Customer / Company Name, Contact Number, and Contact Person Name.'
                    });
                    return;
                }

                const container = document.getElementById("add-expo-form");
                const formData = new FormData();
                container.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
                    formData.append(el.name, el.value);
                });

                const $btn = $('#save-expo-btn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving Expo Details...');

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
                                title: 'Expo Details Saved!',
                                text: response.message || 'Expo Details and Lead saved successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('Save Expo Details &amp; Create Lead');
                        let errMsg = 'Something went wrong while saving Expo details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errMsg
                        });
                    }
                });
            });
        });
    </script>
@endsection