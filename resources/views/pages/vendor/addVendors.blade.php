@extends('layouts.app')
@section('title', 'Add Vendor Details')
@section('main-content')

    @php
        $countries = DB::table('countries')->where('id', 76)->get();
    @endphp
    <div class="container">
        <h4 class="mt-4 mb-5 text-center">Add Vendor Details</h4>
    </div>
    <section class="section leadsection">

        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">

                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2 mt-3">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route("add-vendors") }}" id="add-vendor-form">
                <div class="row mb-4">
                    @csrf

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST NO <span
                                    style="color:red">*</span></label>
                            <div class="gst-input-div">
                                <input type="decimal" name="gst_number" id="vendor-gst"
                                    class="form-control gst-number-field mb-2" placeholder="GST NO" required>
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

                            <input type="text" name="company_name" id="company_name" class="form-control"
                                placeholder="Company Name">
                        </div>
                    </div>

                    <!-- Alternate Name Field -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customerSelect" class="form-label">
                                Alternate Name
                            </label>

                            <input type="text" name="alternate_name" id="alternate_name" class="form-control"
                                placeholder="Alternate Name" required>
                        </div>
                    </div>

                    <!-- product category -->

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Product Category <span
                                    style="color:red">*</span></label>
                            <select class="form-select" name="pro_cat_id[]" id="addvendor_cat_list" required
                                multiple="multiple">
                                <option value="">-- Choose Category --</option>
                                @if ($status = App\Models\ProductCategory::get())
                                    @foreach ($status as $data)
                                        <option value="{{ $data->id }}">
                                            {{ $data->category_name }}
                                        </option>
                                    @endforeach
                                @endif

                            </select>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Address <span
                                    style="color:red">*</span></label>
                            <textarea name="address" class="form-control" id="vendor-address" placeholder="Address"
                                required></textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Country <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="country_id" id="vendor-country" required>
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
                            <select class="form-select state_list" name="state_id" id="addvendor_state_list" required>
                                <option value=""> -- Choose State -- </option>
                                @if($product = App\Models\Statelist::get())
                                    @foreach ($product as $p)
                                        <option value="{{ $p->id }}">{{ $p->state }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">City <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="city_id" id="addvendor_city_list" required>
                                <option value=""> -- Choose City -- </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Postal Code <span
                                    style="color:red">*</span></label>
                            <input type="number" name="postal_code" id="add-vendor-postalcode" class="form-control"
                                placeholder="Postal Code" required>
                        </div>
                    </div>

                    <!-- contact Details -->

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="customerSelect" class="form-label">
                                Contact Name <span style="color:red">*</span>
                            </label>

                            <input type="text" name="poc_name" id="vendor_poc_name" class="form-control"
                                placeholder="Type here.." required>
                        </div>
                    </div>

                    
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <input type="hidden" name="countrycode" value="76">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="number" name="contact_number" id="vendor_contact_no" class="form-control"
                                placeholder="Contact Number" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">E-mail <span
                                    style="color:red">*</span></label>
                            <input type="email" name="email" class="form-control" id="vendor_cus_email" placeholder="E-mail"
                                required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Vendor Status <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="vendor_status" required>
                                <option value="active"> Active </option>
                                <option value="inactive"> Inactive </option>
                            </select>
                        </div>
                    </div>


                </div>
        </div>
        <button type="submit" class="btn btn-primary m-auto d-block add-venodr-btn mt-5 mb-4">Save Vendor</button>
        </form>
        {{-- </div> --}}
    </section>
@endsection

@section('scripts')

    <script>
        let add_vendor_form = new window.JustValidate('#add-vendor-form');

        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        const nameRegex = /^[A-Z a-z]+$/;
        const numberRegex = /^[0-9]*$/;

        add_vendor_form
            .addField('#company_name', [
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
            .addField('#alternate_name', [
                {
                    rule: 'minLength',
                    value: 3,
                },
                {
                    rule: 'maxLength',
                    value: 15,
                },
            ])
            .addField('#addvendor_cat_list', [
                {
                    rule: 'required',
                }
            ])
            .addField('#vendor-address', [
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
            .addField('#addvendor_state_list', [
                {
                    rule: 'required',
                }
            ])
            .addField('#addvendor_city_list', [
                {
                    rule: 'required',
                }
            ])
            .addField('#add-vendor-postalcode', [
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
            .addField('#vendor_poc_name', [
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
            .addField('#vendor_contact_no', [
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
            .addField('#vendor_cus_email', [
                {
                    rule: 'required',
                },
                {
                    rule: 'email',
                },
            ])
        if (localSettingStorage) {

            if (gstValidationLocal == "active") {
                add_vendor_form.addField('#vendor-gst', [{
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
        add_vendor_form.onSuccess((event) => {
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
                            $("#company_name").val(response.data.lgnm);
                            $("#vendor-address").val(response.data.adr);
                            $("#add-vendor-postalcode").val(response.data.pincode);
                            document.getElementById("add-vendor-form").submit();
                        } else {
                            $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                            $("#company_name").val('');
                            $("#vendor-address").val('');
                            $("#add-vendor-postalcode").val('');
                        }

                    },
                    error: function (xhr, status, error) {
                        $("#company_name").val('');
                        $("#vendor-address").val('');
                        $("#add-vendor-postalcode").val('');
                        console.error("Error:", error);
                        $this.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                        $this.closest('.form-group').find('.gst-input-div i.fa-check-circle').hide();
                        $this.closest('.form-group').find('.gst-input-div i.fa-times-circle').show();
                    }
                });
            } else {
                document.getElementById("add-vendor-form").submit();
            }
        });
    </script>

@endsection