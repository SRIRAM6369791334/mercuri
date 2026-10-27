@extends('layouts.app')
@section('title', 'Employee Advance Details')
@section('main-content')

<div class="container">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fa fa-user-circle" style="color:#e7368d;"></i> Advance Details - {{ $employee->name }} ({{ $employee->emp_id }})</h5>
            <div>
                <a href="{{ route('employee.advance.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Add Advance
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="mb-3">
    <h6><strong>Total Advance:</strong> ₹{{ number_format($total, 2) }}</h6>
</div>


            <table class="table table-bordered table-striped" id="employeeAdvanceTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($advances as $index => $advance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($advance->advance_date)->format('d-m-Y') }}</td>
                            <td>₹{{ number_format($advance->amount, 2) }}</td>
                            <td>{{ $advance->reason ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($advance->status) {
                                        'Given' => 'badge bg-warning',
                                        'Settled' => 'badge bg-success',
                                        'Deducted' => 'badge bg-info',
                                        default => 'badge bg-secondary',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $advance->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No advances found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
