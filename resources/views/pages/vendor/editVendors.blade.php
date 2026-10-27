@extends('layouts.app')
@section('title', 'Edit Vendor Details')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get();
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

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST NO <span
                                    style="color:red">*</span></label>
                            <div class="gst-input-div">
                                <input type="text" name="gst_number" id="edit_vendor_gst" class="form-control gst-number-field mb-2" placeholder="GST NO" value="{{ $vendorDetails->gst_number }}" required>
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                        </div>
                    </div>
                    <!-- Company Name Field -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customerSelect" class="form-label">
                                Company Name <span style="color:red">*</span>
                            </label>

                            <input type="text" name="company_name" id="edit_company_name" class="form-control"
                                placeholder="Company Name" value="{{ $vendorDetails->company_name }}">
                        </div>
                    </div>

                    <!-- Alternate Name Field -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customerSelect" class="form-label">
                                Alternate Name
                            </label>

                            <input type="text" name="alternate_name" id="edit_alternate_name" class="form-control"
                                placeholder="Alternate Name" value="{{ $vendorDetails->alternate_name }}" required>
                        </div>
                    </div>

                    <!-- product category -->

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Product Category <span
                                    style="color:red">*</span></label>
                            <select class="form-select" name="pro_cat_id[]" id="editvendor_cat_list" required multiple="multiple">
                                <option value="">-- Choose Category --</option>
                                @php
                                    // Decode the JSON string into an array
                                    $selectedCategories = json_decode($vendorDetails->product_cat_id, true) ?? [];
                                @endphp
                                @if ($status = App\Models\ProductCategory::get())
                                    @foreach ($status as $data)
                                        <option value="{{ $data->id }}" {{ in_array($data->id, $selectedCategories) ? 'selected' : '' }}> {{ $data->category_name }}</option>
                                    @endforeach
                                @endif

                            </select>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Address <span
                                    style="color:red">*</span></label>
                            <textarea name="address" class="form-control" id="edit_vendor_address" placeholder="Address"
                                required>{{ $vendorDetails->address }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Country <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="country_id" id="edit_vendor_country" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ $country->id == 76 ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">State <span
                                    style="color:red">*</span></label>
                            <select class="form-select state_list" name="state_id" id="edit_vendor_state_list" required>
                                <option value=""> -- Choose State -- </option>
                                @if($states = App\Models\Statelist::get())
                                    @foreach ($states as $data)
                                        <option value="{{ $data->id }}" @if($vendorDetails->state_id == $data->id) selected @endif>{{ $data->state }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">City <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="city_id" id="edit_vendor_city_list" required>
                                <option value=""> -- Choose City -- </option>
                                @if($cities = App\Models\Citylist::get())
                                    @foreach ($cities as $data)
                                        <option value="{{ $data->id }}" @if($vendorDetails->city_id == $data->id) selected @endif>{{ $data->city_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Postal Code <span
                                    style="color:red">*</span></label>
                            <input type="number" name="postal_code" id="edit_vendor_postalcode" class="form-control"
                                placeholder="Postal Code" value="{{ $vendorDetails->postal_code }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customerSelect" class="form-label">
                                Contact Name <span style="color:red">*</span>
                            </label>

                            <input type="text" name="poc_name" id="edit_vendor_poc_name" class="form-control" placeholder="Type here.."
                                value="{{ $vendorDetails->point_of_contact }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">E-mail <span
                                    style="color:red">*</span></label>
                            <input type="email" name="email" class="form-control" id="edit_vendor_cus_email" placeholder="E-mail"
                                value="{{ $vendorDetails->email }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <input type="hidden" name="countrycode" value="76">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="decimal" name="contact_number" id="edit_vendor_contact_no" class="form-control"
                                placeholder="Contact Number" value="{{ $vendorDetails->contact_number }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Vendor Status <span
                                    style="color:red">*</span></label>
                            <select class="form-select" name="vendor_status" required>
                                <option value="active" @if ($vendorDetails->vendor_status == "active")
                                selected @endif> Active </option>
                                <option value="inactive" @if ($vendorDetails->vendor_status == "inactive")
                                selected
                                @endif> Inactive </option>
                            </select>
                        </div>
                    </div>


                </div>
        </div>
        <button type="submit" class="btn btn-primary m-auto d-block edit-vendor-btn mt-4 mb-2">
            Update Changes</button>
        </form>
        {{-- </div> --}}
    </section>
@endsection

@section('scripts')

<script>
    const edit_vendor_form = new JustValidate('#edit_vendor_form');

    let localSettingStorage = localStorage.getItem("settings");
    let gstValidationLocal = localStorage.getItem("gst-status");

    const nameRegex = /^[A-Z a-z]+$/;
    const numberRegex = /^[0-9]*$/;

    edit_vendor_form
        .addField('#edit_company_name', [
            {
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 3,
            },
            {
                rule: 'maxLength',
                value: 50,
            },
        ])
        .addField('#edit_alternate_name', [
            {
                rule: 'minLength',
                value: 3,
            },
            {
                rule: 'maxLength',
                value: 15,
            },
        ])
        .addField('#editvendor_cat_list', [
            {
                rule: 'required',
            }
        ])
        .addField('#edit_vendor_address', [
            {
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 15,
            },
            {
                rule: 'maxLength',
                value: 100,
            },
        ])
        .addField('#edit_vendor_country', [
            {
                rule: 'required',
            }
        ])
        .addField('#edit_vendor_state_list', [
            {
                rule: 'required',
            }
        ])
        .addField('#edit_vendor_city_list', [
            {
                rule: 'required',
            }
        ])
        .addField('#edit_vendor_postalcode', [
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
        .addField('#edit_vendor_poc_name', [
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
        .addField('#edit_vendor_contact_no', [
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
        .addField('#edit_vendor_cus_email', [
            {
                rule: 'required',
            },
            {
                rule: 'email',
            },
        ]);
        if (localSettingStorage) {

            if (gstValidationLocal == "active") {
                edit_vendor_form.addField('#edit_vendor_gst', [{
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
        edit_vendor_form.onSuccess((event) => {
            var $this = $(".gst-number-field");
                var gstNumber = $(".gst-number-field").val();
                if (gstNumber.length == 15 && gstValidationLocal == "active") {
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
                            console.log(response.success);
                            if (response.success) {
                                $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').show();
                                $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').hide();
                                $this.closest('.form-group').find('.error-msg').text('');
                                document.getElementById("edit_vendor_form").submit();
                                $("#edit_company_name").val(response.data.lgnm);
                                $("#edit_vendor_address").val(response.data.adr);
                                $("#edit_vendor_postalcode").val(response.data.pincode);
                            } else {
                                $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                                $("#edit_company_name").val('');
                                $("#edit_vendor_address").val('');
                                $("#edit_vendor_postalcode").val('');
                            }

                        },
                        error: function (xhr, status, error) {
                            $("#edit_company_name").val('');
                            $("#edit_vendor_address").val('');
                            $("#edit_vendor_postalcode").val('');
                            console.error("Error:", error);
                            $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                            $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                            $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                        }
                    });
                } else {
                    document.getElementById("edit_vendor_form").submit();
                }
        });
</script>

@endsection