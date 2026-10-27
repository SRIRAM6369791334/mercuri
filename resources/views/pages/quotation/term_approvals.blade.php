@extends('layouts.app')
@section('title', 'Term/Warranty Approvals')
@section('main-content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pending Payment Term & Warranty Approvals</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Quotation ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Old Term Value</th>
                        <th>New Term Value</th>
                        <th>Requester</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $requestItem)
                        <tr>
                            <td><strong>{{ optional($requestItem->quotation)->quota_id }}</strong></td>
                            <td>{{ optional($requestItem->quotation?->lead)->LeadName }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $requestItem->type === 'payment_term' ? 'Payment Term' : 'Warranty' }}
                                </span>
                            </td>
                            <td>{{ optional($requestItem->oldTerm)->content ?? 'None' }}</td>
                            <td><strong>{{ optional($requestItem->newTerm)->content ?? 'None' }}</strong></td>
                            <td>{{ optional($requestItem->requester)->name ?? 'N/A' }}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('quotation.view', $requestItem->quotation_id) }}">View</a>
                                <form class="d-inline" method="post" action="{{ route('quotation.terms.decide', $requestItem->id) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="Approved">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $requestItem->id }}">Reject</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No payment term or warranty requests are awaiting approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Reject Modals placed outside the table container --}}
@foreach($requests as $requestItem)
    <div class="modal fade" id="rejectModal{{ $requestItem->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $requestItem->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="{{ route('quotation.terms.decide', $requestItem->id) }}">
                @csrf
                <input type="hidden" name="decision" value="Rejected">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel{{ $requestItem->id }}">Reject Term Change Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Remarks <span class="text-danger">*</span></label>
                        <textarea required class="form-control" name="remarks" rows="3" placeholder="Enter rejection remarks for sales team..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection