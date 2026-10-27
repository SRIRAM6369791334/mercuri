@extends('layouts.app')
@section('title', 'Charge Approvals')
@section('main-content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pending Charge Approvals</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Quotation</th>
                        <th>Customer</th>
                        <th>Installation<br><small class="text-muted">Default / Entered</small></th>
                        <th>Freight<br><small class="text-muted">Default / Entered</small></th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                        <tr>
                            <td><strong>{{ $quotation->quota_id }}</strong></td>
                            <td>{{ optional($quotation->lead)->LeadName }}</td>
                            <td>₹{{ number_format($quotation->default_installation_charge, 2) }} / <span class="{{ $quotation->installation_charge != $quotation->default_installation_charge ? 'text-primary fw-bold' : '' }}">₹{{ number_format($quotation->installation_charge, 2) }}</span></td>
                            <td>₹{{ number_format($quotation->default_freight_charge, 2) }} / <span class="{{ $quotation->freight_charge != $quotation->default_freight_charge ? 'text-primary fw-bold' : '' }}">₹{{ number_format($quotation->freight_charge, 2) }}</span></td>
                            <td>
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('quotation.view', $quotation) }}">View</a>
                                <form class="d-inline" method="post" action="{{ route('quotation.charges.decide', $quotation) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="Approved">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $quotation->id }}">Reject</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No quotations are awaiting charge approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Reject Modals placed outside the table container --}}
@foreach($quotations as $quotation)
    <div class="modal fade" id="rejectModal{{ $quotation->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $quotation->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="{{ route('quotation.charges.decide', $quotation) }}">
                @csrf
                <input type="hidden" name="decision" value="Rejected">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel{{ $quotation->id }}">Reject Charge Request ({{ $quotation->quota_id }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Correction Remarks <span class="text-danger">*</span></label>
                        <textarea required class="form-control" name="remarks" rows="3" placeholder="Enter correction remarks for sales team..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject and Return</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection