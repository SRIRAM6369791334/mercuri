@extends('layouts.app')
@section('title', 'Edit Employee')
@section('main-content')
    <style>
        .editform-step {
            display: none;
        }

        .editform-step.active {
            display: block;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .just-validate-error-label {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .text-danger {
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }
    </style>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif


    <div class="container">
        <div class="">
            <div style="display: flex;justify-content: space-between;">
                <h5 class="mb-3 al"><i class="fa fa-user-plus" style="color: #fe6600;" aria-hidden="true"></i> Edit Employee
                </h5>
                <a href="javascript:history.back()" class="btn btn-success mb-3"
                    style="width:300px;background: #1b5583; border: none;margin-right:10px;"><span class="btn-text"><i
                            class="fa fa-hand-o-left mr-2" aria-hidden="true" style="color:#ffff;"></i>
                        &nbsp;&nbsp;Back</span></a>
            </div>
            <form method="POST" action="{{ route('employee.update') }}" id="editemplyoe" enctype="multipart/form-data">
                @csrf
                <div class="editform-step active" id="step1">
                    <h4 class="mt-2 mb-4"><i class="fa fa-database"></i> General Details</h4>
                    <div class="row">
                             <input type="hidden"  name="emid" 
                                    value="{{ $employee->id }}" >
                        <div class="col-lg-4">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Employee
                                    Photo(Optional)
                                </label>

                                <div style="">
                                    @if ($employee->photo)
                                        <img src="{{ asset('uploads/employees/' . $employee->photo) }}" width="100"
                                            height="100">
                                    @endif
                                    <input type="file" name="photo" id="fileUpload" class="form-control">
                                    {{-- <input type="file" class="form-control" placeholder="Enter Client Name"
                                        name="photo" id="fileUpload"> --}}
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Name<span style="color:red">
                                        *</span>(in block letters)</label>
                                <input type="text" class="form-control ename" minlength="3" maxlength="30"
                                    placeholder="Enter Employee Name" name="name" id="name"
                                    value="{{ old('name', $employee->name) }}" style="text-transform: uppercase">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Second Name (Optional)</label>
                                <input type="text" class="form-control enamese" minlength="1" maxlength="10"
                                    placeholder="Enter Second  Name" name="secondname"
                                    value="{{ old('secondname', $employee->secondname) }}" id="secondname"
                                    style="text-transform: uppercase">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Date Of
                                    Birth<span style="color:red"> *</span></label>
                                <input type="date" class="form-control" placeholder="" name="dob" id="dob"
                                    value="{{ old('dob', $employee->dob) }}">
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Education
                                    Qualification<span style="color:red"> *</span></label>
                                <input type="text" class="form-control" minlength="2" maxlength="20"
                                    placeholder="Enter Qualification" name="Qualification" id="Qualification"
                                    value="{{ old('qualification', $employee->qualification) }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Mobile
                                    Number<span style="color:red"> *</span></label>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                    class="form-control" placeholder="Enter Mobile Number" name="mobile_no" id="mobile_no"
                                    value="{{ old('mobile_no', $employee->mobile_no) }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">E-mail
                                    ID<span style="color:red"> *</span></label>
                                <input type="email" class="form-control emmail" placeholder="Enter E-mail ID"
                                    name="email" id="email" value="{{ old('email', $employee->email) }}">

                            </div>
                        </div>
                        {{-- @php
                            $countries = App\Models\Country::all();
                        @endphp --}}
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Nationality<span style="color:red">
                                        *</span></label>
                                <select class="form-control" name="nationality" id="nationality">
                                    <option value="">-- Choose Nationality --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}"
                                            {{ old('nationality', $employee->nationality) == $country->name ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Date Of
                                    Joining<span style="color:red"> *</span></label>
                                <input type="date" class="form-control" placeholder="" name="doj" id="doj"
                                    value="{{ old('doj', $employee->doj) }}">

                            </div>
                        </div>

                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Gender<span style="color:red">
                                        *</span></label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value="">-- Choose Gender --</option>
                                    <option value="Male"
                                        {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female"
                                        {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Designation<span style="color:red">
                                        *</span></label>
                                <input type="text" class="form-control" minlength="2"
                                    placeholder="Enter Designation" name="oridesignation" id="oridesignation"
                                    value="{{ old('oridesignation', $employee->oridesignation) }}">

                            </div>
                        </div>
                        <div class="col-lg-4 " id="PF1">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Salary</label>
                                <input type="text"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" name="basic"
                                    id="acbas" class="form-control" value="{{ old('salary', $employee->salary) }}">
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Blood
                                    Group<span style="color:red"> *</span></label>

                                <select class="form-control mb-3" id="bloodgroup" name="bloodgroup">
                                    <option value="">-- Choose Blood Group --</option>
                                    <option value="A+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A+' ? 'selected' : '' }}>A+
                                    </option>
                                    <option value="A-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A-' ? 'selected' : '' }}>A-
                                    </option>
                                    <option value="B+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'B+' ? 'selected' : '' }}>B+
                                    </option>
                                    <option value="B-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'B-' ? 'selected' : '' }}>B-
                                    </option>
                                    <option value="O+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'O+' ? 'selected' : '' }}>O+
                                    </option>
                                    <option value="O-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'O-' ? 'selected' : '' }}>O-
                                    </option>
                                    <option value="AB+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'AB+' ? 'selected' : '' }}>AB+
                                    </option>
                                    <option value="AB-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'AB-' ? 'selected' : '' }}>AB-
                                    </option>
                                    <option value="A1+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A1+' ? 'selected' : '' }}>A1+
                                    </option>
                                    <option value="A1-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A1-' ? 'selected' : '' }}>A1-
                                    </option>
                                    <option value="A2+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A2+' ? 'selected' : '' }}>A2+
                                    </option>
                                    <option value="A2-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A2-' ? 'selected' : '' }}>A2-
                                    </option>
                                    <option value="A1B+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A1B+' ? 'selected' : '' }}>A1B+
                                    </option>
                                    <option value="A1B-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A1B-' ? 'selected' : '' }}>A1B-
                                    </option>
                                    <option value="A2B+"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A2B+' ? 'selected' : '' }}>A2B+
                                    </option>
                                    <option value="A2B-"
                                        {{ old('bloodgroup', $employee->bloodgroup) == 'A2B-' ? 'selected' : '' }}>A2B-
                                    </option>
                                </select>


                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-2 text-left">Temporary
                                    Address &nbsp;&nbsp;<button type="button" class="btn btn-primary" id="copyaddress"
                                        style="padding: 8px 30px;background: #1b5583;border: none;"><i
                                            class="fa fa-files-o" aria-hidden="true"></i>&nbsp;&nbsp;Copy
                                        Address</button></label>
                                <textarea class="form-control textarea" placeholder="Temporary Address" name="temp_address" id="temp_address">{{ old('temp_address', $employee->temp_address) }}</textarea>

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Permanant
                                    Address</label>
                                <textarea class="form-control textarea" placeholder="Permanant Address" name="per_address" id="per_address">{{ old('per_address', $employee->per_address) }}</textarea>

                            </div>
                        </div>

                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Employee Status</label>

                                <select class="form-control" name="employestat" id="employestat">
                                    <option value="0"
                                        {{ old('employestat', $employee->employestat) == '0' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="1"
                                        {{ old('employestat', $employee->employestat) == '1' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 ">
                            <div class="form-group mb-3">
                                <label class="control-label mb-1 text-left">Marital Status</label>

                                <select class="form-control" name="maritalstatus" id="maritalstatus">
                                    <option value="0"> -- Choose -- </option>
                                    <option value="1"
                                        {{ old('maritalstatus', $employee->maritalstatus) == '1' ? 'selected' : '' }}>
                                        Single</option>
                                    <option value="2"
                                        {{ old('maritalstatus', $employee->maritalstatus) == '2' ? 'selected' : '' }}>
                                        Married</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="editform-step" id="step2">
                    <h4 class="mt-2 mb-4"><i class="fa fa-university"></i> Bank Details</h4>

                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">Bank Name
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Bank Name" id="bankname"
                                    name="bankname" value="{{ old('bankname', $employee->bank->bankname ?? '') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">Name of Branch</label>
                                <input type="text" class="form-control" placeholder="Enter Branch Name"
                                    id="branchname" name="branchname"
                                    value="{{ old('branchname', $employee->bank->branchname ?? '') }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">Account
                                    Holder Name</label>
                                <input type="text" class="form-control" placeholder="Enter Account Holder Name"
                                    id="accholdername" name="accholdername"
                                    value="{{ old('accholdername', $employee->bank->accholdername ?? '') }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">Account
                                    Number</label>
                                <input type="text" class="form-control" placeholder="Enter Account Number"
                                    id="acc_number" name="acc_number"
                                    value="{{ old('acc_number', $employee->bank->acc_number ?? '') }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">IFSC Code</label>
                                <input type="text" class="form-control" placeholder="Enter IFSC" name="acc_no"
                                    id="accno" value="{{ old('ifsc_code', $employee->bank->ifsc_code ?? '') }}">

                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group mb-2">
                                <label class="control-label mb-1 text-left">Swift
                                    Code</label>
                                <input type="text" class="form-control" placeholder="Enter Swift Code" id="ifsc_code"
                                    name="ifsc_code" value="{{ old('acc_no', $employee->bank->acc_no ?? '') }}">

                            </div>
                        </div>


                    </div>
                </div>
                <div class="editform-step" id="step3">
                    <h4 class="mt-2 mb-4"><i class="fa fa-users"></i> Emergency Details</h4>

                    <div class="row">
                        <table class="table table-bordered table-scroll mt-3" id="productTable">
                            <thead>
                                <tr>
                                    {{-- <th scope="col">E - Contact</th> --}}
                                    <th scope="col">Name<span id="fname" class=" fname"
                                            style="margin-left: 10px; color:red;"></span></th>
                                    <th scope="col">Relation<span id="frelation" class=" frelation"
                                            style="margin-left: 10px; color:red;"></span></th>
                                    <th scope="col">Mobile Number<span id="fmobile" class=" fmobile"
                                            style="margin-left: 10px; color:red;"></span></th>
                                    <th scope="col">Email<span id="fmobile" class=" fmobile"
                                            style="margin-left: 10px; color:red;"></span></th>
                                    <th scope="col"><button type="button" class="btn btn-primary"
                                            id="addProduct1">+</button></th>
                                </tr>
                            </thead>
                            <tbody id="app1">
                                @foreach ($employee->emergencyContacts as $contact)
                                    <tr>

                                        <td><input class="form-control f_name" type="text" name="f_name[]"
                                                placeholder="Name" value="{{ $contact->name }}"></td>
                                        <td><input class="form-control f_relation" type="text" name="f_relation[]"
                                                placeholder="Relation" value="{{ $contact->relation }}"></td>

                                        <td><input class="form-control f_mobile" type="text" name="f_mobile[]"
                                                placeholder="Mobile Number" value="{{ $contact->mobile }}"></td>
                                        <td><input class="form-control f_email" type="text" name="f_email[]"
                                                placeholder="Email" value="{{ $contact->email }}"></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="editform-step" id="step4">
                    <h4 class="mt-2 mb-4"><i class="fa fa-users"></i> Educational Details</h4>
                    <div class="row">

                        <table class="table table-bordered table-scroll mt-3" id="productTable1">
                            <thead>

                                <tr>
                                    <th scope="col">Level</th>
                                    <th scope="col">Board/Degree</th>
                                    <th scope="col">Institute/University</th>
                                    <th scope="col">Year Of Pass Out</th>
                                    <th scope="col">Certificate Attachment</th>
                                    <th scope="col"><button type="button" class="btn btn-primary"
                                            id="addProduct">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="app2">
                                @foreach ($employee->educations as $educ)
                                    <tr>
                                        <td>
                                            <select class="form-control" name="level[]">
                                                <option value="">Choose Level</option>
                                                <option value="Before SSLC"
                                                    {{ old('level', $educ->level) == 'Before SSLC' ? 'selected' : '' }}>
                                                    Before SSLC</option>
                                                <option value="SSLC"
                                                    {{ old('level', $educ->level) == 'SSLC' ? 'selected' : '' }}>SSLC
                                                </option>
                                                <option value="HSC"
                                                    {{ old('level', $educ->level) == 'HSC' ? 'selected' : '' }}>HSC
                                                </option>
                                                <option value="UG"
                                                    {{ old('level', $educ->level) == 'UG' ? 'selected' : '' }}>UG</option>
                                                <option value="PG"
                                                    {{ old('level', $educ->level) == 'PG' ? 'selected' : '' }}>PG</option>
                                                <option value="Others"
                                                    {{ old('level', $educ->level) == 'Others' ? 'selected' : '' }}>Others
                                                </option>
                                            </select>
                                        </td>
                                        <td><input class="form-control degree" type="text" name="degree[]"
                                                placeholder="Board" value="{{ $educ->degree }}">
                                        </td>

                                        <td><input class="form-control univers" type="text" name="university[]"
                                                placeholder="Institure" value="{{ $educ->university }}"></td>
                                        <td><input class="form-control passout" type="text" name="passedout[]"
                                                placeholder="Pass out" value="{{ $educ->passedout }}"></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-control docu" type="file" name="document[]">
                                                @if ($educ->document)
                                                    <a href="{{ asset('storage/education_docs/' . $educ->document) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        📄 Download
                                                    </a>
                                                @endif
                                            </div>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="editform-step" id="step5">
                    <h4 class="mt-2 mb-4"><i class="fa fa-users"></i>Working Experience</h4>
                    <div class="row">

                        <table class="table table-bordered table-scroll mt-3" id="productTable1">
                            <thead>

                                <tr>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Period</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Designation</th>
                                    <th scope="col"><button type="button" class="btn btn-primary"
                                            id="addexperiencedetails">+</button></th>
                                </tr>
                            </thead>
                            <tbody id="app3">
                                @foreach ($employee->experiences as $expe)
                                    <tr>

                                        <td>
                                            <input class="form-control exp_start_date" type="date"
                                                name="exp_start_date[]" placeholder=""
                                                value="{{ $expe->exp_start_date }}">
                                        </td>
                                        <td>
                                            <input class="form-control exp_end_date" type="date" name="exp_end_date[]"
                                                placeholder="" value="{{ $expe->exp_end_date }}">
                                        </td>
                                        <td><input class="form-control perioed" type="text" name="period[]"
                                                placeholder="Period" value="{{ $expe->period }}">
                                        </td>

                                        <td><input class="form-control rrole" type="text" name="role[]"
                                                placeholder="Role" value="{{ $expe->role }}"></td>
                                        <td><input class="form-control companyy" type="text" name="company[]"
                                                placeholder="Company" value="{{ $expe->company }}"></td>
                                        <td><input class="form-control dedis" type="text" name="empdesignation[]"
                                                placeholder="Designation" value="{{ $expe->empdesignation }}"></td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="editform-step" id="step6">
                    <h4 class="mt-2 mb-4"><i class="fa fa-users"></i> Document</h4>

                    <div class="row">


                        <div class="col-lg-12">
                            <div class="form-group">
                                <table class="table table-bordered table-scroll mt-3" id="productTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Document</th>
                                            <th scope="col">File</th>
                                            <th scope="col">Existing File</th>


                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td><label>Adhaar</label></td>
                                            <td>
                                                <div class="" style="display: flex;gap: 10px;">
                                                    <input class="form-control img" type="file" name="image1"
                                                        id="fileUpload1">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($employee->documents->aadhaar_file)
                                                    <a href="{{ asset('uploads/documents/' . $employee->documents->aadhaar_file) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('uploads/documents/' . $employee->documents->aadhaar_file) }}"
                                                            alt="Aadhaar" width="50" height="50" class="output">
                                                    </a>
                                                @else
                                                    <img src="/assets/images/document-icon.png" alt="Document Icon"
                                                        width="50" height="50" class="output">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Pan Card</label></td>
                                            <td>
                                                <div class="" style="display: flex;gap: 10px;">
                                                    <input class="form-control img" type="file" name="image2"
                                                        id="fileUpload2">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($employee->documents->pan_file)
                                                    <a href="{{ asset('uploads/documents/' . $employee->documents->pan_file) }}" target="_blank">
                                                        <img src="{{ asset('uploads/documents/' . $employee->documents->pan_file) }}" alt="PAN"
                                                            width="50" height="50" class="output">
                                                    </a>
                                                @else
                                                    <img src="/assets/images/document-icon.png" alt="Document Icon"
                                                        width="50" height="50" class="output">
                                                @endif
                                            </td>

                                        </tr>

                                    </tbody>
                                </table>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="submit" class="btn btn-success d-none" id="submitBtn">Submit</button>
                </div>

            </form>
        </div>
    </div>


    <div class="modal fade" id="desimodal1" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
        tabindex="-1">
        <div class="modal-dialog ">
            <form method="POST" action="designationinsert" id="adddesignation1">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlInput1" class="form-label">Department</label>
                            <input type="text" class="form-control" autocomplete="off" minlength="2" maxlength="30"
                                name="designation" value="" id="designation_input">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadcn-modal animate__fadeIn">
                <div class="modal-body">
                    <h5 class="modal-title fw-semibold mb-2">Are you absolutely sure?</h5>
                    <p class="text-muted mb-4">This action will create a new employee profile. Please ensure all entered
                        details are correct before proceeding.</p>
                    <div class="d-flex justify-content-end gap-2">
                        <!--<button type="button" class="btn shadcn-btn-outline" id="cancelBtn">Cancel</button>-->
                        <button type="button" class="btn shadcn-btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn shadcn-btn-primary"
                            onclick="submitEmployeeForm()">Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
