@extends('layouts.app')
@section('title', 'Attendance List')
@section('main-content')

@php
    use Carbon\Carbon;
    $selectedDate = request('date') ?? Carbon::now()->format('Y-m-d');
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3" style="width: 300px;">
        <button id="prevDate" class="btn btn-light">&lt;</button>
        <h4 id="currentDate">{{ Carbon::parse($selectedDate)->format('d M Y | D') }}</h4>
        <button id="nextDate" class="btn btn-light">&gt;</button>
    </div>

    <input type="date" id="attendanceDate" style="width: 300px;" class="form-control mb-3" value="{{ $selectedDate }}">
    <input type="text" id="searchStaff" style="width: 300px;" class="form-control mb-3" placeholder="Search Staff">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Attendance List</h5>
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <strong>{{ $employee->name }}</strong><br>
                                    <small>{{ $employee->worked_hours ?? '00:00 Hrs' }}</small>
                                </td>
                                <td>
                                    @if($employee->punch_in_time)
                                        <img src="{{ asset('uploads/attendance/' . $employee->punch_in_image) }}" width="40" height="40" class="rounded-circle mb-1">
                                        <br>
                                        {{ \Carbon\Carbon::parse($employee->punch_in_time)->format('h:i A') }}<br>
                                        <small>{{ $employee->punch_in_location }}</small>
                                    @else
                                        <button class="btn btn-primary punchInBtn" data-id="{{ $employee->id }}">Punch In</button>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->punch_out_time)
                                        {{ \Carbon\Carbon::parse($employee->punch_out_time)->format('h:i A') }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Punch In Modal -->
<div class="modal fade" id="punchInModal" tabindex="-1" aria-labelledby="punchInModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('attendance.punchin') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="employee_id" id="modal_employee_id">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Punch In</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label>Upload Face Image</label>
                <input type="file" name="face_image" class="form-control" required>
                <label class="mt-2">Location</label>
                <input type="text" name="location" class="form-control" value="Auto-detected" readonly>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit Punch In</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection
@section('scripts')
<script>
               $(document).ready(function() {
    let table = $('#attendanceTable').DataTable();

    $('#searchStaff').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('.punchInBtn').on('click', function () {
        let empId = $(this).data('id');
        $('#modal_employee_id').val(empId);
        $('#punchInModal').modal('show');
    });

    let currentDate = new Date('{{ $selectedDate }}');

    function updateDateDisplay() {
        const options = { day: '2-digit', month: 'short', year: 'numeric', weekday: 'short' };
        const dateStr = currentDate.toLocaleDateString('en-GB', options);
        $('#currentDate').text(dateStr.replace(',', ' |'));
        $('#attendanceDate').val(currentDate.toISOString().split('T')[0]);
    }

    function goToDate(date) {
        const isoDate = date.toISOString().split('T')[0];
        window.location.href = `?date=${isoDate}`;
    }

    $('#prevDate').click(() => {
        currentDate.setDate(currentDate.getDate() - 1);
        goToDate(currentDate);
    });

    $('#nextDate').click(() => {
        currentDate.setDate(currentDate.getDate() + 1);
        goToDate(currentDate);
    });

    $('#attendanceDate').change(function () {
        currentDate = new Date(this.value);
        goToDate(currentDate);
    });

    updateDateDisplay();
});
            </script>

@endsection
