@extends('layouts.app')
@section('title', 'Add Advance List')
@section('main-content')

<div class="container">
    <div class="table table-view shadow col-md-12">
        <div style="display: flex;justify-content: space-between;">
            <h5 class="mb-3 al">
                <i class="fa fa-user-plus" style="color: #ea1f27;" aria-hidden="true"></i> Employee Advance Summary
            </h5>
            <a href="{{ route('employee.advance.create') }}" class="btn btn-success mb-3"
                style="width:300px;background: #192a9f; border: none;margin-right:10px;">
                &nbsp;&nbsp;Add Advance
            </a>
        </div>
        <table class="table table-bordered table-striped" id="advancetable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Employee Name</th>
                    <th>Total Advance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($advances as $index => $advance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $advance->employee->name ?? '-' }}</td>
                        <td>₹{{ number_format($advance->total_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('employee.advance.show', $advance->employee_id) }}" class="btn btn-primary btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
