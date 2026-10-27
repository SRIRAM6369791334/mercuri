@extends('layouts.app')
@section('title', 'Add Employee Advance')
@section('main-content')

<div class="container">
    <div class="card shadow">
        <div class="card-header">
            <h5><i class="fa fa-plus-circle"></i> Add Advance</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('employee.advance.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->emp_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" required step="0.01">
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <input type="text" name="reason" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="advance_date" class="form-label">Advance Date</label>
                    <input type="date" name="advance_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

@endsection
