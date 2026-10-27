@extends('layouts1.app')
@section('title', ' ')
@section('main-content')
    <style>
        .form-check input[type="radio"]:checked+.form-check-label img {
            filter: none !important;
            /* Reset filter */
        }
    </style>

    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\DB;

    @endphp

    <div class="container" style="margin-top: 155px;">
        <div class="table-responsive table-view shadow col-md-12">
            <div style="display: flex;justify-content: space-between;">
                <h5 class="mb-3 al"><i class="fa fa-book" style="color: #ea1f27;" aria-hidden="true"></i> Add Attendance</h5>
                <!--<a href="/dailyattendence" class="btn btn-success mb-3"-->
                <!--    style="width:300px;background: #192a9f; border: none;margin-right:10px;"><span class="btn-text"><i-->
                <!--            class="fa fa-user-plus" style="color:#fff" aria-hidden="true"></i>-->
                <!--        &nbsp;&nbsp;Add Attendance</span></a>-->
            </div>
            <table id="example" class="display table table-bordered">
                {{-- table table-bordered table-hover table-striped --}}
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        <!--<th>Employee ID</th>-->
                        <th>Employee Name</th>
                        <th>Employee Status</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Over Time Hours</th>
                        <th>working Hours</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 1;
                    ?>
                    @foreach ($attendanceentry as $at)
                        @php

                            $loginTime = Carbon::parse($at->login_time);
                            $logoutTime = Carbon::parse($at->logout_time);
                            $workingHours = $logoutTime->diff($loginTime);
                            $adjustedHours = max(0, $workingHours->h);
                            $employeeDetails = App\Models\Addemployee::where('emp_id',$at->emp_id)->first();
                            $overtime = (float) $at->over_time;
                            $totalWorkedHours = $adjustedHours + $overtime;
                        @endphp
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $at->a_date }}</td>
                            <!--<td>{{ $at->emp_id }}</td>-->
                            <td>{{ $at->name }}</td>
                            <td>
                                @if ($at->attendance_status == 'Y')
                                    <span
                                        style="background-color: #ff7600;
                                    color: white;
                                    padding: 2px 5px;
                                    border-radius: 10px;">Pending</span>
                                @elseif($at->attendance_status == 'W')
                                    <span
                                        style="background-color: #f8b32d;
                                color: white;
                                padding: 2px 5px;
                                border-radius: 10px;">Week
                                        off</span>
                                @elseif($at->attendance_status == 'A')
                                    <span
                                        style="background-color: #ed3833;
                                    color: white;
                                    padding: 2px 5px;
                                    border-radius: 10px;">Absent</span>
                                @elseif($at->attendance_status == 'P')
                                    <span
                                        style="background-color: #4aa23c;
                                    color: white;
                                    padding: 2px 5px;
                                    border-radius: 10px;">Present</span>
                                @elseif($at->attendance_status == 'H')
                                    <span
                                        style="background-color: #e7368d;
                                    color: white;
                                    padding: 2px 5px;
                                    border-radius: 10px;">Holiday</span>
                                @else
                                    {{ $at->attendance_status }}
                                @endif
                            </td>
                            <td>{{ $at->login_time }}</td>
                            <td>{{ $at->logout_time }}</td>
                            <td>{{ $at->over_time ?? 0 }}</td>
                            <td>{{ $totalWorkedHours }} hrs {{ $workingHours->i }} mins</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-pencil" style="color: #fff;" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a style="border-radius: 5px;color:#000" class="dropdown-item penattedit"
                                                data-bs-toggle="modal" data-bs-target="#editattmodal"
                                                data-login="{{ $at->login_time }}" data-logout="{{ $at->logout_time }}"
                                                data-atremark="{{ $at->remark }}" data-emnmae="{{ $at->name }} "
                                                data-atdate="{{ $at->a_date }}"
                                                data-attendancestatus="{{ $at->attendance_status }}"
                                                data-perdateto="{{ $at->perdateto }}" data-month="{{ $at->a_month }}"
                                                data-day="{{ $at->a_day }}" data-year="{{ $at->a_year }}"
                                                data-perdatefrom="{{ $at->perdatefrom }}"
                                                data-pertimeend="{{ $at->pertimeend }}"
                                                data-pertimestart="{{ $at->pertimestart }}"
                                                data-odtimeend="{{ $at->odtimeend }}"
                                                data-odtimestart="{{ $at->odtimestart }}"
                                                data-permistiming="{{ $at->permistiming }}"
                                                data-permission="{{ $at->permission }}"
                                                data-editattenemid="{{ $employeeDetails->new_batch_num }}" data-id="{{ $at->id }}"
                                                data-duty_time="{{ $at->duty_times }}"
                                                data-overtime="{{ $at->overtime_times }}"
                                                data-first_punchin_time="{{ $at->first_punchin_time }}"
                                                data-last_punchout_time="{{ $at->last_punchout_time }}"
                                                data-total_duty_time="{{ $at->du_time }}"
                                                data-total_overtime="{{ $at->over_time }}">Edit Attendance</a></li>
                                                <!--data-total_overtime="{{ $at->total_overtime }}">Edit Attendence</a></li>-->
                                        <!--<li><a class="dropdown-item" href="#" data-bs-toggle="modal"-->
                                        <!--        data-bs-target="#exampleLogModal" data-editattenemid="{{ $at->emp_id }}"-->
                                        <!--        id="log_fetch_button">View Log</a></li>-->
                                        {{-- <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Separated link</a></li> --}}
                                    </ul>
                                </div>



                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="editattmodal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog  modal-xl">
            <form method="POST" action="editattendance" id="editatten">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user-plus" style="color: #ea1f27;"
                                aria-hidden="true"></i> Edit Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" style="margin-bottom:20px; ">
                            <div class="col-lg-3">

                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control" name="atempid" value="" id="atempid"
                                        readonly>
                                </div>

                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Employee Name</label>
                                    <input type="text" class="form-control" name="" value=""
                                        id="atempname" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Login Time</label>
                                    <input type="text" class="form-control" name="" value=""
                                        id="logintime" readonly>
                                    <input type="time" class="form-control" name="llogin" value=""
                                        id="login_time_input">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Logout Time</label>
                                    <input type="text" class="form-control" name="" value=""
                                        id="logouttime" readonly>
                                    <input type="time" class="form-control" name="llogout" value=""
                                        id="logout_time_input">
                                </div>
                            </div>
                            <!--    <div class="col-lg-4">-->
                            <!--     <div class="form-group">-->
                            <!--          <label for="exampleFormControlInput1" class="form-label">Login & Logout</label>-->
                            <!-- <input type="time" class="form-control" name="punchintime" value="" id="punchin_time_input" >-->
                            <!-- </div>-->
                            <!-- </div>-->
                            <!-- <div class="col-lg-3">-->
                            <!-- <div class="form-group">-->
                            <!--     <label for="exampleFormControlInput1" class="form-label">Login Time</label>-->
                            <!--     <input type="text" class="form-control" name="" value="" id="first_punchintime" readonly >-->
                            <!--<input type="time" class="form-control" name="punchintime" value="" id="punchin_time_input" >-->
                            <!-- </div>-->
                            <!--</div>-->
                            <!-- <div class="col-lg-3">-->
                            <!-- <div class="form-group">-->
                            <!--     <label for="exampleFormControlInput1" class="form-label">Logout Time</label>-->
                            <!--     <input type="text" class="form-control" name="" value="" id="last_punchouttime" readonly >-->
                            <!--<input type="time" class="form-control" name="punchouttime" value="" id="punchout_time_input" >-->
                            <!-- </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="exampleFormControlInput1" class="form-label">Duty Time</label>-->
                            <!--        <input type="text" class="form-control" name="" value=""-->
                            <!--            id="total_dutytime" readonly>-->
                                    <!--<input type="time" class="form-control" name="dutytime" value="" id="duty_time_input" >-->
                            <!--    </div>-->
                            <!--</div>-->
                             <div class="col-lg-3">
                                 <div class="form-group">
                                     <label for="exampleFormControlInput1" class="form-label">Over Time</label>
                                     <!--<input type="text" class="form-control" name="" value="" id="total_overtime" readonly >-->
                                     <!--<input type="time" class="form-control" name="overtime" value="" id="over_time_input" >-->
                                     <select class="form-select" aria-label="Default select example" id="over_time_input" name="overtime_new">
                                          <option selected>Select OverTime Hours</option>
                                          <option value="1">1</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5</option>
                                          <option value="6">6</option>
                                          <option value="7">7</option>
                                          <option value="8">8</option>
                                          <option value="9">9</option>
                                          <option value="10">10</option>
                                          <option value="11">11</option>
                                          <option value="12">12</option>
                                    </select>
                                 </div>
                            </div>

                            <!--<div class="col-lg-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="permission" class="form-label">Permission</label>-->
                            <!--        <select class="form-control" name="permission" id="permission">-->
                            <!--            <option value="" selected>-- Select Options --</option>-->
                            <!--            <option value="1">OD</option>-->
                            <!--            <option value="2">Permission</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="details" class="form-label">Timing</label>-->
                            <!--        <select class="form-control" name="details" id="details">-->
                            <!--            <option value="0">-- Select Details --</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="fromdate-group">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="fromdate" class="form-label">From Date</label>-->
                            <!--        <input type="date" class="form-control" name="perdatefrom" value=""-->
                            <!--            id="perfromdate">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="todate-group">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="todate" class="form-label">To Date</label>-->
                            <!--        <input type="date" class="form-control" name="perdateto" value=""-->
                            <!--            id="pertodate">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="starttime-group">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="fromdate" class="form-label">Start Time</label>-->
                            <!--        <input type="time" class="form-control" name="pertimestart" value=""-->
                            <!--            id="perstarttime">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="endtime-group">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="todate" class="form-label">End Time</label>-->
                            <!--        <input type="time" class="form-control" name="pertimeend" value=""-->
                            <!--            id="perendtime">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="starttime-od">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="fromdate" class="form-label">Start Time</label>-->
                            <!--        <input type="time" class="form-control" name="odtimestart" value=""-->
                            <!--            id="odstarttime">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-lg-3" id="endtime-od">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="todate" class="form-label">End Time</label>-->
                            <!--        <input type="time" class="form-control" name="odtimeend" value=""-->
                            <!--            id="odendtime">-->
                            <!--    </div>-->
                            <!--</div>-->

                        </div>
                        <div class="col-lg-12">
                            <table class="table table-bordered table-hover color-bg-table">
                                <thead class="success">
                                    <tr>
                                        <th class="text-center" style="font-weight: 500;">Date</th>
                                        <th class="text-center "
                                            style="color:white;background-color:#4aa23c;font-weight: 500;">Present
                                        </th>
                                        <th class="text-center "
                                            style="color:white;background: #ed3833;font-weight: 500;">Absent</th>
                                        <th class="text-center "
                                            style="color:white;background: #f8b32d;font-weight: 500;">Week off</th>
                                        <th class="text-center "
                                            style="color:white;background: #e7368d;font-weight: 500;">Holiday</th>
                                        <th class="text-center" style="font-weight: 500;">Attendance Status</th>
                                        <th class="text-center" style="font-weight: 500;">Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr>
                                        <input type="hidden" class="form-control" id="dataaid" name="attid"
                                            value="" readonly>
                                        <input type="hidden" class="form-control" id="yeara" name="year"
                                            value="" readonly>
                                        <input type="hidden" class="form-control" id="montha" name="month"
                                            value="" readonly>
                                        <input type="hidden" class="form-control" id="daya" name="day"
                                            value="" readonly>
                                        <td>
                                            <input type="text" class="form-control" id="dataa" name="data"
                                                value="" readonly>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input style="display: none;" class="form-check-input" type="radio"
                                                    name="flexRadioDefault" id="flexRadioDefault1" value="P">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    <img src="images/man.png" class="img-responsive imgg"
                                                        style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer; filter: grayscale(1);">
                                                </label>
                                            </div>

                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input style="display: none;" class="form-check-input" type="radio"
                                                    name="flexRadioDefault" id="flexRadioDefault2" value="A">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    <img src="images/man.png" class="img-responsive imgg"
                                                        style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer; filter: grayscale(1);">
                                                </label>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input style="display: none;" class="form-check-input" type="radio"
                                                    name="flexRadioDefault" id="flexRadioDefault3" value="W">
                                                <label class="form-check-label" for="flexRadioDefault3">
                                                    <img src="images/man.png" class="img-responsive imgg"
                                                        style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer; filter: grayscale(1);">
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input style="display: none;" class="form-check-input" type="radio"
                                                    name="flexRadioDefault" id="flexRadioDefault4" value="H">
                                                <label class="form-check-label" for="flexRadioDefault4">
                                                    <img src="images/man.png" class="img-responsive imgg"
                                                        style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer; filter: grayscale(1);">
                                                </label>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div id="attstatus" style="border-radius:10px;color:#fff;"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="remarks" id="remat"
                                                value="">
                                        </td>

                                    </tr>

                                </tbody>

                            </table>


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


    <div class="modal fade" id="exampleLogModal" tabindex="-1" aria-labelledby="exampleLogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleLogModalLabel">Check-In and CheckOut Log</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table_append">
                    <table class="table table-bordered table-hover">
                        <thead class="success">
                            <tr>
                                <th>S.No</th>
                                <th>Image</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                            </tr>
                        </thead>
                        <tbody class="">

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to validate login time
        function validateLoginTime() {
            var loginTime = document.getElementById('login_time_input').value;
            var logoutTime = document.getElementById('logout_time_input').value;

            // Convert login and logout times to minutes since midnight
            var loginMinutes = loginTime ? timeToMinutes(loginTime) : null;
            var logoutMinutes = logoutTime ? timeToMinutes(logoutTime) : null;

            // Check if login time is later than logout time if both are provided
            if (loginMinutes !== null && logoutMinutes !== null && loginMinutes >= logoutMinutes) {
                // Display error message
                alert('Login time must be earlier than logout time');
                // Reset login time input value
                document.getElementById('login_time_input').value = '';
            }
        }

        // Function to validate logout time
        function validateLogoutTime() {
            var loginTime = document.getElementById('login_time_input').value;
            var logoutTime = document.getElementById('logout_time_input').value;

            // Convert login and logout times to minutes since midnight
            var loginMinutes = loginTime ? timeToMinutes(loginTime) : null;
            var logoutMinutes = logoutTime ? timeToMinutes(logoutTime) : null;

            // Check if logout time is earlier than login time if both are provided
            if (loginMinutes !== null && logoutMinutes !== null && logoutMinutes <= loginMinutes) {
                // Display error message
                alert('Logout time must be later than login time');
                // Reset logout time input value
                document.getElementById('logout_time_input').value = '';
            }
        }

        // Function to convert time to minutes since midnight
        function timeToMinutes(time) {
            var parts = time.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        // Add event listeners to input fields
        document.getElementById('login_time_input').addEventListener('change', function() {
            validateLoginTime();
            validateLogoutTime();
        });
        document.getElementById('logout_time_input').addEventListener('change', function() {
            validateLoginTime();
            validateLogoutTime();
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initially hide all conditional fields
            $('#fromdate-group, #todate-group, #starttime-group, #endtime-group, #starttime-od, #endtime-od')
                .hide();

            $('#permission').change(function() {
                var selectedValue = $(this).val();
                var detailsDropdown = $('#details');

                // Clear existing options
                detailsDropdown.empty();
                detailsDropdown.append('<option value="0">-- Select Details --</option>');

                if (selectedValue == '1') {
                    // If OD is selected, add Hours and Days
                    detailsDropdown.append('<option value="1">Hours</option>');
                    detailsDropdown.append('<option value="2">Days</option>');
                } else if (selectedValue == '2') {
                    // If Permission is selected, add Hours only
                    detailsDropdown.append('<option value="3">Hours</option>');
                }
            });

            $('#details').change(function() {
                var selectedDetail = $(this).val();

                // Hide all conditional fields
                $('#fromdate-group, #todate-group, #starttime-group, #endtime-group, #starttime-od, #endtime-od')
                    .hide();

                if (selectedDetail == '1') {
                    // If Hours is selected, show Start Time and End Time
                    $('#starttime-od, #endtime-od').show();
                } else if (selectedDetail == '2') {
                    // If Days is selected, show From Date and To Date
                    $('#fromdate-group, #todate-group').show();
                } else if (selectedDetail == '3') {
                    // If Days is selected, show From Date and To Date
                    $('#starttime-group, #endtime-group').show();
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#log_fetch_button', function() {
            let empid = $(this).data('editattenemid');

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
            });

            $.ajax({
                url: "/attendencelog/" + empid,
                type: "GET",
                success: function(response) {
                    if (response.status == 200) {
                        let tableRows = "";
                        let i = 1;

                        console.log(response);

                        response.data.forEach(function(log) {
                            tableRows += `<tr>
                                <td>${i++}</td>
                                <td><img src="http://127.0.0.1:9000/images/${log.employee_photo}" height="70" width="70"/></td>
                                <td>${log.punch_in_time || 'N/A'}</td>
                                <td>${log.punch_out_time || 'N/A'}</td>
                            </tr>`;
                        });

                        $('.table_append tbody').html(tableRows);
                        $('#examplelogoutModal').modal('show');
                    }
                }
            });
        });
    </script>

@endsection
