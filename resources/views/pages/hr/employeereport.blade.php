@extends('layouts.app')
@section('title', 'Employee Report')
@section('main-content')
<div class="container">
    <h4 class="mb-3"><i class="fa fa-user-circle text-primary"></i> Employee Report - {{ $employee->name }} ({{ $employee->emp_id }})</h4>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">Basic Details</div>
        <div class="card-body">
            <p><strong>Employee ID:</strong> {{ $employee->emp_id }}</p>
            <p><strong>Name</strong> {{ $employee->name }} {{ $employee->secondname }}</p>
            <p><strong>Email:</strong> {{ $employee->email }}</p>
            <p><strong>Phone:</strong> {{ $employee->mobile_no }}</p>
            <p><strong>Department:</strong> {{ $employee->oridesignation ?? '-' }}</p>
            {{-- <p><strong>Designation:</strong> {{ $employee->oridesignation ?? '-' }}</p> --}}
            <p><strong>Joining Date:</strong> {{ \Carbon\Carbon::parse($employee->doj)->format('d-m-Y') }}</p>
            <p><strong>Nationality:</strong> {{ $employee->nationality ?? '-' }}</p>
            <p><strong>Gender:</strong> {{ $employee->gender ?? '-' }}</p>
            <p><strong>Blood Group:</strong> {{ $employee->bloodgroup ?? '-' }}</p>
            <p><strong>Temporary Address:</strong> {{ $employee->temp_address ?? '-' }}</p>
            <p><strong>Gender:</strong> {{ $employee->per_address ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">Bank Details</div>
        <div class="card-body">
            <p><strong>Account Name:</strong> {{ $employee->bank->account_name ?? '-' }}</p>
            <p><strong>Account Number:</strong> {{ $employee->bank->account_number ?? '-' }}</p>
            <p><strong>Bank Name:</strong> {{ $employee->bank->bank_name ?? '-' }}</p>
            <p><strong>IFSC:</strong> {{ $employee->bank->ifsc ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">Emergency Contacts</div>
        <div class="card-body">
            @foreach ($employee->emergencyContacts as $contact)
                <p><strong>Name:</strong> {{ $contact->name }} | <strong>Relation:</strong> {{ $contact->relation }} | <strong>Phone:</strong> {{ $contact->phone }}</p>
            @endforeach
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-warning">Education</div>
        <div class="card-body">
            <ul>
                @foreach ($employee->educations as $edu)
                    <li>{{ $edu->level }} - {{ $edu->institution }} ({{ $edu->year }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Experience</div>
        <div class="card-body">
            <ul>
                @foreach ($employee->experiences as $exp)
                    <li>{{ $exp->company_name }} - {{ $exp->designation }} ({{ $exp->from }} to {{ $exp->to }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Assigned Assets</div>
        <div class="card-body">
            <ul>
                @foreach ($employee->assets as $asset)
                    <li>{{ $asset->asset_name }} - Qty: {{ $asset->quantity }} - Status: {{ $asset->status }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
