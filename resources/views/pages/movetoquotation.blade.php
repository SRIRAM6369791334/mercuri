@extends('layouts.app')
@section('title', 'Approve Move to Quotation')
@section('main-content')

<section class="section leadsection container">
    <div class="form-card">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <h5 class="mt-2">Approve Move to Quotation</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">Quotation Stage Approval</div>
        <div class="card-body">
            <table id="movetoq-table" class="display table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Lead No</th>
                        <th>Segment</th>
                        {{-- <th>Product Name</th> --}}
                        <th>Drawing</th>
                        <th>Approval</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $key => $lead)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $lead->lead_id }}</td>
                        <td>{{ $lead->category }}</td>
                        {{-- <td>{{ $lead->product }}</td> --}}
                        <td>
                            @if($lead->quota_doc)
                                <a href="{{ asset('' . $lead->quota_doc) }}" target="_blank" class="btn btn-primary btn-sm">
                                    View
                                </a>
                            @else
                                <span class="text-muted">No Doc</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm approve-btn"
                                    data-id="{{ $lead->id }}"
                                    data-toggle="modal"
                                    data-target="#approvalModal">
                                Approve
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Approval Confirmation -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proceed with Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to proceed with this approval?
                <input type="hidden" id="lead-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-approval">Proceed</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section("scripts")
<script>
    $(document).ready(function () {
        let table = new DataTable('#movetoq-table');

        $(document).on('click', '.approve-btn', function () {
            let leadId = $(this).data('id');
            $('#lead-id').val(leadId);
            $('#approvalModal').modal('show');
        });

        $('#confirm-approval').click(function () {
            let leadId = $('#lead-id').val();
            let submitButton = $(this);

            $.ajax({
                url: `/approve-lead/${leadId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    quota_proceed: 1
                },
                beforeSend: function () {
                    submitButton.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Approving...
                    `);
                },
                success: function (response) {
                    $('#approvalModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to approve lead.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        });
    });
    </script>

@endsection
