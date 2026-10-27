@extends('layouts.app')
@section('title', 'Edit Expo Details')
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
                            <h5 class="mt-2 mb-0">Edit Expo Details ({{ $lead->lead_id ?? ('LEAD' . str_pad($lead->id, 4, '0', STR_PAD_LEFT)) }})</h5>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('pages.expo') }}" class="btn btn-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="edit-expo-form" data-action="{{ route('pages.expo.update', $lead->id) }}" autocomplete="off" data-form-type="other" data-lpignore="true">
                <div class="row mb-4">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customer_name" class="form-label">Customer / Company Name <span style="color:red">*</span></label>
                            <input type="text" name="customername" class="form-control" id="customer_name"
                                value="{{ old('customername', $lead->LeadName) }}"
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
                                    value="{{ old('gstno', $lead->gst) }}"
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
                                id="cus-address">{{ old('address', $lead->billaddress) }}</textarea>
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
                                placeholder="Communication Address">{{ old('communication_address', $lead->communicate_address ?: $lead->billaddress) }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-country" class="form-label">Billing Country</label>
                            <select class="form-select" name="country" id="cus-country" autocomplete="off" data-lpignore="true">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ ($lead->billingcountry == $country->id || $country->id == 76) ? 'selected' : '' }}>
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
                                    <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-contact-name" class="form-label">Contact Person Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="contact_name" class="form-control"
                                value="{{ old('contact_name', $lead->contactname) }}"
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
                                        <option value="{{ $p->id }}" {{ $lead->state_id == $p->id ? 'selected' : '' }}>{{ $p->state }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-email" class="form-label">E-mail</label>
                            <input type="email" name="cus_email" class="form-control"
                                value="{{ old('cus_email', $lead->email) }}"
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
                                @if ($lead->state_id && $cities = App\Models\Citylist::where('state_code', $lead->state_id)->get())
                                    @foreach ($cities as $c)
                                        <option value="{{ $c->id }}" {{ $lead->citylist == $c->id ? 'selected' : '' }}>{{ $c->city_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="cus-mobile" class="form-label">Contact Number <span style="color:red">*</span></label>
                            <input type="hidden" name="country_code" value="76" autocomplete="off">
                            <input type="text" name="contact_no" maxlength="10"
                                value="{{ old('contact_no', $lead->phonumber) }}"
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
                                value="{{ old('postalcode', $lead->postalcode) }}"
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
                                    <option value="{{ $data->id }}" {{ $lead->designation == $data->id ? 'selected' : '' }}>{{ $data->dropdowndata }}</option>
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
                                    <option value="{{ $data->id }}" {{ $lead->department == $data->id ? 'selected' : '' }}>{{ $data->dropdowndata }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" id="update-expo-btn" class="btn btn-primary m-auto d-block add-employee-btn mb-3 mt-2">
                    Update Expo Details
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

            // Update Expo Button Handler
            $('#update-expo-btn').on('click', function () {
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

                const container = document.getElementById("edit-expo-form");
                const formData = new FormData();
                formData.append('_method', 'PUT');
                container.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
                    if (el.name !== '_method') {
                        formData.append(el.name, el.value);
                    }
                });

                const $btn = $('#update-expo-btn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating Expo Details...');

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
                                title: 'Expo Details Updated!',
                                text: response.message || 'Expo Details updated successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('Update Expo Details');
                        let errMsg = 'Something went wrong while updating Expo details.';
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