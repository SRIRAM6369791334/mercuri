@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Update Lead</h5>
                        </div>
                        <div class="col-lg-4">

                        </div>
                        <div class="col-lg-2 " style="text-align: -webkit-right;">
                            <a  onclick="window.history.back();" class="btn btn-primary d-block add-employee-btn">Back</a>
                            {{-- onclick="window.history.back();" --}}
                            {{-- href="/singlelead/{{ $row->id }}" --}}
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" id="lead-edit-form" autocomplete="new-password">
                <div class="row mb-4">
                    @csrf
                    {{-- @if ($fields = App\Models\Field::where('categoryid', '1')->where('visibility', 1)->get())
                    @foreach ($fields as $f)
                    @if ($f->fieldtype == 1)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">

                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="text" value="{{ $row->{str_replace(' ', '', $f->fieldname)} }}" @if ($f->fieldname
                            == 'Client Name') onblur="namevalidation(this)" @else onblur="textboxvalidation(this)" @endif
                            name="{{ str_replace(' ', '', $f->fieldname) }}" class="form-control"
                            placeholder="{{ $f->fieldname }}"
                            @if ($f->mandatory == 1) required @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 2)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="text" value="{{ $row->{str_replace(' ', '', $f->fieldname)} }} onblur="
                                numbersonlyvalidation(this)" name="{{ str_replace(' ', '', $f->fieldname) }}"
                                class="form-control" placeholder="{{ $f->fieldname }}" @if ($f->mandatory == 1) required
                            @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 3)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="text" value="{{ $row->{str_replace(' ', '', $f->fieldname)} }} onblur="
                                decimalnumvalidation(this)" name="{{ str_replace(' ', '', $f->fieldname) }}"
                                class="form-control " placeholder="{{ $f->fieldname }}" @if ($f->mandatory == 1) required
                            @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 4)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="text" value="{{ $row->{str_replace(' ', '', $f->fieldname)} }} onblur="
                                emailvalidation(this)" name="{{ str_replace(' ', '', $f->fieldname) }}" class="form-control"
                                placeholder="{{ $f->fieldname }}" @if ($f->mandatory == 1) required @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 5)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="number" value="{{ $row->{str_replace(' ', '', $f->fieldname)} }} id=" productname"
                                name="{{ str_replace(' ', '', $f->fieldname) }}" onblur="mobilenumvalidation(this)"
                                class="form-control mblduplicatecheck" placeholder="{{ $f->fieldname }}" @if ($f->mandatory
                            == 1) required @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 6)
                    @if ($f->fieldname == 'Entrydate')
                    @php
                    $dateformat = date('Y-m-d', strtotime($row->Entrydate));
                    @endphp
                    @else
                    @php
                    $dateformat = $row->{str_replace(' ', '', $f->fieldname)};
                    @endphp
                    @endif
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <input type="date" value="{{ $dateformat }}" class="form-control"
                                name="{{ str_replace(' ', '', $f->fieldname) }}" placeholder="{{ $f->fieldname }}" min=""
                                @if ($f->mandatory == 1) required @endif>
                        </div>
                    </div>
                    @endif
                    @if ($f->fieldtype == 7)
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">{{ $f->fieldname }}
                                @if ($f->mandatory == 1)
                                <span style="color:red">*</span>
                                @endif
                            </label>
                            <select class="form-select" @if ($f->mandatory == 1) required @endif
                                name="{{ str_replace(' ', '', $f->fieldname) }}">
                                <option value="">Choose {{ $f->fieldname }}</option>
                                @if ($dropdowndatas = App\Models\Dropdowndata::where('formid', $f->id)->orderBy('orderno',
                                'asc')->get())
                                @foreach ($dropdowndatas as $data)
                                <option value="{{ $data->id }}" @if ($row->{str_replace(' ', '', $f->fieldname)} ==
                                    $data->id) selected @endif>{{ $data->dropdowndata }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Products</label>
                            <select class="form-select " name="products" required>
                                <option value=""> Choose Product </option>
                                @if ($product = App\Models\Product::get())
                                @foreach ($product as $dataproduct)
                                <option value="{{ $dataproduct->id }}" @if ($row->products == $dataproduct->id) selected
                                    @endif
                                    >
                                    {{ $dataproduct->productname }}</option>
                                @endforeach
                                @endif

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Assigned to</label>
                            <select class="form-select" name="assigned_to" id="assigned_to">
                                <option value=""> Choose User </option>
                                @if ($users = App\Models\User::where('id', '!=', Auth::user()->id)->get())
                                @foreach ($users as $u)
                                <option value="{{ $u->id }}" @if ($row->assigned_to == $u->id) selected @endif>{{ $u->name
                                    }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div> --}}

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Customer Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="LeadName" value="{{ $row->LeadName }}" class="form-control"
                                placeholder="Customer Name" id="lead_edit_customer_name" required>
                            <div class="mt-2 text-danger customer-err-msg"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST NO</label>
                            <div class="gst-input-div">
                                <input type="decimal" name="gst" id="lead_edit_gst" class="form-control gst-number-field"
                                    value="{{ $row->gst }}" placeholder="GST NO">
                                <i class="fas fa-check-circle"></i>
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="error-msg text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mt-4">
                                        <label for="status" anme class="form-label">Select Status</label>
                                        <select class="form-select" id="leadstatus" name="Leadstatus">
                                            <option value="">-- Choose Status --</option>
                                            @foreach (App\Models\Dropdowndata::where('formid', 6)->where('dropdowndata', '!=', 'WON')->orderBy('orderno', 'asc')->get() as $data)
                                                <option value="{{ $data->id }}" data-status="{{ $data->dropdowndata }}" @if ($row->Leadstatus == $data->id) selected @endif>
                                                    {{ $data->dropdowndata }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Source of Enquiry</label>
                                    <select class="form-select" name="Leadsource" id="lead_edit_source_enquiry">
                                        <option value="">-- Choose Source --</option>
                                        @if ($status = App\Models\Dropdowndata::where('formid', 4)->orderBy('orderno', 'asc')->get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}" @if ($row->Leadsource == $data->id) selected @endif>
                                                    {{ $data->dropdowndata }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>

                                </div>
                            </div>
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
                            <textarea name="communicate_address" id="lead_editcom_address" class="form-control" placeholder="Communication Address" autocomplete="new-password">{{ $row->communicate_address }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Address</label>
                            <textarea name="billaddress" class="form-control" id="lead_edit_address" placeholder="Billing Address"
                                autocomplete="new-password">{{ $row->billaddress }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Enquiry Details</label>
                            <textarea name="enquirydetail" class="form-control" id="lead_edit_enquiry_details" placeholder="Enquiry Details">{{ $row->enquirydetail }}</textarea>
                        </div>
                    </div>
                    @php
                        $countries = DB::table('countries')->where('id', 76)->get(); // Fetching all countries from the database
                    @endphp
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Country</label>
                            <select class="form-select " name="billingcountry">
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
                            <label for="exampleFormControlInput1" class="form-label">Contact Name <span
                                    style="color:red">*</span></label>
                            <input type="text" name="contactname" id="lead_edit_contactName" class="form-control" value="{{ $row->contactname }}"
                                autocomplete="new-password"
                                placeholder="Contact Name" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing State</label>
                            <select class="form-select " name="state_id" id="lead_state_list">
                                <option value=""> -- Choose State -- </option>
                                @if($product = App\Models\Statelist::get())
                                    @foreach ($product as $p)

                                        <option value="{{ $p->id }}" @if ($row->state_id == $p->id) selected @endif>{{ $p->state }}
                                        </option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">E-mail</label>
                            <input type="email" name="email" id="lead_edit_cus_email" class="form-control" value="{{ $row->email }}" placeholder="E-mail" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing City</label>
                            <select class="form-select " name="citylist" id="lead_city_list">
                                <option value=""> -- Choose City -- </option>
                                @if($city = App\Models\Citylist::get())
                                    @foreach ($city as $c)
                                        <option value="{{ $c->id }} " @if($row->citylist == $c->id) selected @endif>
                                            {{ $c->city_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="countrycode" value="76">
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Contact Number <span
                                    style="color:red">*</span></label>
                            <input type="decimal" name="phonumber" pattern="[6-9]{1}[0-9]{9}"
                            maxlength="10"
                            autocomplete="new-password"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"  class="form-control" id="lead_edit_contact_no" value="{{ $row->phonumber }}" placeholder="Contact Number" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Billing Postal Code</label>
                            <input type="number" name="postalcode" id="lead_edit_postalcode" class="form-control" value="{{ $row->postalcode }}"
                                autocomplete="new-password"
                                placeholder="Postal Code">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Designation </label>
                                    {{-- <input type="text" name="designation"  class="form-control"
                                    placeholder="Designation" value="{{ $row->designation }}" id="lead-designation"> --}}
                            <select name="designation"
                id="lead-designation"
                class="form-control select2">
            <option value="">Select Designation</option>

            @foreach(App\Models\Dropdowndata::where('formid',80)->orderBy('orderno')->get() as $data)
                <option value="{{ $data->id }}"@if($row->designation == $data->id) selected @endif>
                    {{ $data->dropdowndata }}
                </option>
            @endforeach

            <option value="add_new">➕ Add New Designation</option>
        </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Department </label>
                                    {{-- <input type="text" name="department"  class="form-control"
                                    placeholder="Department" value="{{ $row->department }}" id="lead-department"> --}}
                            <select name="department"
                id="lead-department"
                class="form-control select2">
            <option value="">Select Department</option>

            @foreach(App\Models\Dropdowndata::where('formid',81)->orderBy('orderno')->get() as $data)
                <option value="{{ $data->id }}"@if($row->department == $data->id) selected @endif>
                    {{ $data->dropdowndata }}
                </option>
            @endforeach

            <option value="add_new">➕ Add New Department</option>
        </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Sales Manager</label>
                            {{-- <select class="form-select" name="assigned_to" id="lead_edit_assigned_to">
                                <option value=""> Choose User </option>
                                @if ($users = App\Models\User::where('id', '!=', Auth::user()->id)->get())
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @if($row->assigned_to == $u->id) selected @endif>{{ $u->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select> --}}
                            <select class="form-select" name="assigned_to" id="lead_edit_assigned_to">
                            <option value="">Choose User</option>
                            <option value="{{ Auth::user()->id }}"  @if($row->assigned_to == Auth::user()->id) selected @endif>Self ({{ Auth::user()->name }})</option>
                            <!-- Self option -->
                            @foreach (App\Models\User::where('id', '!=', Auth::user()->id)->get() as $u)
    <option value="{{ $u->id }}" @if($row->assigned_to == $u->id) selected @endif>{{ $u->name }}</option>
    @endforeach
</select>
                        </div>
                    </div>
                    {{-- <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Products <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="products" required>
                                <option value=""> Choose Product </option>
                                @if ($product = App\Models\Product::get())
                                @foreach ($product as $dataproduct)
                                <option value="{{ $dataproduct->id }}" @if($row->products == $dataproduct->id) selected
                                    @endif>
                                    {{ $dataproduct->productname }}</option>
                                @endforeach
                                @endif

                            </select>
                        </div>
                    </div> --}}
                    {{-- <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Status of Enquiry <span
                                    style="color:red">*</span></label>
                            <select class="form-select " name="Leadstatus" required>
                                <option value="">-- Choose Status --</option>
                                @if ($status = App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno',
                                'asc')->get())
                                @foreach ($status as $data)
                                <option value="{{ $data->id }}" @if($row->Leadstatus == $data->id) selected @endif>
                                    {{ $data->dropdowndata }}</option>
                                @endforeach
                                @endif

                            </select>

                        </div>
                    </div> --}}
                    @php
                        $dateformat = date('Y-m-d', strtotime($row->Entrydate));
                    @endphp
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Enquiry Date</label>
                            <input type="date" class="form-control" id="lead_edit_entry_date" name="Entrydate" value="{{ $dateformat }}"
                                placeholder="Enquiry Date" min="">
                            <input type="hidden" name="proceed" value="1">
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $row->id }}">

                </div>
        </div>
        <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn">Update Lead</button>
        </form>
        </div>
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
        let edit_lead_form = new window.JustValidate('#lead-edit-form');

        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        const nameRegex = /^[A-Z a-z]+$/;
        const numberRegex = /^[0-9]*$/;

        edit_lead_form
            .addField('#lead_edit_customer_name', [
                {
                    rule: 'required',
                },
                {
                    rule: 'minLength',
                    value: 3,
                },
                {
                    rule: 'maxLength',
                    value: 80,
                },
            ])
            .addField('#lead_edit_contactName', [
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
            .addField('#lead_edit_contact_no', [
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
            ]);
            if (localSettingStorage) {

                if (gstValidationLocal == "active") {
                    edit_lead_form.addField('#lead_edit_gst', [{
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
            edit_lead_form.onSuccess((event) => {
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
                        if (response.success) {

                            var companyName = $("#lead_edit_customer_name").val();
                            // if (response.data.lgnm != companyName) {
                            //     $(".customer-err-msg").text("This company name doesn't match with GST number");
                            //     return;
                            // }
                            leadUpdateAjaxFunction();

                        } else {
                            $gstField.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                        }
                    },
                    error: function () {
                        $gstField.closest('.form-group').find('.error-msg').text('Invalid GST Number');
                    }
                });
                } else {
                    leadUpdateAjaxFunction();
                }
            });

            function leadUpdateAjaxFunction(){
                // GST is valid, proceed with form submission via AJAX
                $.ajax({
                    type: "POST",
                    url: "/updatelead",
                    data: new FormData(document.getElementById('lead-edit-form')),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Lead Updated Successfully!",
                                showConfirmButton: false,
                                timer: 1500,
                            }).then(() => {
                                window.location.href = "/viewleads";
                            });
                        } else if (data.error) {
                            Swal.fire({
                                icon: "error",
                                title: "Update Failed",
                                text: data.error
                            });
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Server Error",
                            text: "Please try again later."
                        });
                    }
                });
            }
    </script>
    <script>
function copyBillingAddress() {
    const billing = document.getElementById("lead_edit_address").value;
    document.getElementById("lead_editcom_address").value = billing;
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
    $('#masterFormMessage').addClass('d-none').removeClass('alert-success alert-danger').text('');
    $('#masterForm')[0].reset();
    $('#master_formid').val(formid);
    $('#addMasterModal').modal('show');
}

$('#masterForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let saveBtn = $('#masterSaveBtn');
    let messageDiv = $('#masterFormMessage');
    let formid = $('#master_formid').val();
    let name = $('#masterName').val().trim();

    if (!name) {
        messageDiv.removeClass('d-none alert-success').addClass('alert-danger').text('Please enter a name');
        return;
    }

    saveBtn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
            const newItemId = response?.data?.id || '';

            messageDiv.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || 'Created successfully');

            if (formid == 80) {
                refreshDesignationDropdown(newItemId);
            } else if (formid == 81) {
                refreshDepartmentDropdown(newItemId);
            }

            setTimeout(function () {
                $('#addMasterModal').modal('hide');
            }, 800);
        },
        error: function (xhr) {
            let errorMsg = 'An error occurred while saving.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            messageDiv.removeClass('d-none alert-success').addClass('alert-danger').text(errorMsg);
        },
        complete: function () {
            saveBtn.prop('disabled', false).text('Save');
        }
    });
});

function refreshDesignationDropdown(selectedId = '') {
    $.ajax({
        url: '/get-designations',
        type: 'GET',
        success: function (response) {
            let designationSelect = $('#lead-designation');
            designationSelect.empty();
            designationSelect.append('<option value="">Select Designation</option>');

            $.each(response, function (_, value) {
                designationSelect.append('<option value="' + value.id + '">' + value.dropdowndata + '</option>');
            });

            designationSelect.append('<option value="add_new">➕ Add New Designation</option>');
            if (selectedId) {
                designationSelect.val(String(selectedId));
            }
        }
    });
}

function refreshDepartmentDropdown(selectedId = '') {
    $.ajax({
        url: '/get-departments',
        type: 'GET',
        success: function (response) {
            let departmentSelect = $('#lead-department');
            departmentSelect.empty();
            departmentSelect.append('<option value="">Select Department</option>');

            $.each(response, function (_, value) {
                departmentSelect.append('<option value="' + value.id + '">' + value.dropdowndata + '</option>');
            });

            departmentSelect.append('<option value="add_new">➕ Add New Department</option>');
            if (selectedId) {
                departmentSelect.val(String(selectedId));
            }
        }
    });
}
</script>

@endsection