@extends('layouts.app')
@section('title', 'Employee List')

@section('main-content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
 <div class="text-end px-4 pb-3">
        <a href="{{ url('/addemployee') }}" class="btn btn-success">➕ Add Employee</a>
    </div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="employeeTable">
            <thead class="table-light">
                <tr>
                    <th>S.No</th>
                    <th>Employee ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Designation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $emp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $emp->emp_id }}</td>
                        <td>
                            @if($emp->photo && file_exists(public_path('uploads/employees/' . $emp->photo)))
                                <img src="{{ asset('uploads/employees/' . $emp->photo) }}" alt="Photo" class="rounded-circle" width="40" height="40">
                            @else
                                <img src="{{ asset('assets/images/user.png') }}" alt="User Icon" class="rounded-circle" width="40" height="40">
                            @endif
                        </td>
                        <td>{{ $emp->name }}</td>
                        <td>{{ $emp->email }}</td>
                        <td>{{ $emp->mobile_no }}</td>
                        <td>{{ $emp->oridesignation }}</td>
                        <td>
                            <a href="{{ route('employee.edit', $emp->id) }}" class="btn btn-sm btn-info">✏️ Edit</a>
                            <button type="button" class="btn btn-sm btn-danger empbtn-delete" data-id="{{ $emp->id }}">
        🗑️ Delete
    </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Employee Button in Bottom Right -->

</div>
@endsection

