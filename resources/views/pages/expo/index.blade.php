@extends('layouts.app')
@section('title', 'Expo Details')
@section('main-content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <section class="section leadsection container">
        <div class="form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h5 class="mt-2 mb-0">Expo Details List</h5>
                        </div>
                        <div class="col-lg-6 text-end">
                            <button type="button" class="btn btn-outline-primary waves-effect me-2" data-bs-toggle="modal" data-bs-target="#bulkUploadExpoModal">
                                <i class="mdi mdi-upload"></i> Bulk Upload
                            </button>
                            <a href="{{ route('pages.expo.add') }}" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid bg-white form-card mt-4">
        <div class="row">
            <div class="col-lg-12 p-4">
                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Lead ID</th>
                            <th style="text-align: center;">Customer / Company Name</th>
                            <th style="text-align: center;">Contact Name</th>
                            <th style="text-align: center;">Location</th>
                            <th style="text-align: center;">Email</th>
                            <th style="text-align: center;">Contact No</th>
                            <th style="text-align: center;">Date</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @forelse ($expoList as $item)
                            <tr style="text-align: center;">
                                <td>{{ $i++ }}</td>
                                <td><strong>{{ $item->lead_id ?? ('LEAD' . str_pad($item->id, 4, '0', STR_PAD_LEFT)) }}</strong></td>
                                <td style="text-align: left;">{{ $item->LeadName }}</td>
                                <td>{{ $item->contactname }}</td>
                                <td>{{ $item->city_name ?: ($item->state_name ?: $item->billaddress) }}</td>
                                <td>{{ $item->email ?: '-' }}</td>
                                <td>{{ $item->phonumber }}</td>
                                <td>{{ $item->Entrydate ? \Carbon\Carbon::parse($item->Entrydate)->format('d-m-Y') : optional($item->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 5px; justify-content: center;">
                                        <a href="/singlelead/{{ $item->id }}" class="btn btn-sm btn-info" title="View Lead">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('pages.expo.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit Expo Details">
                                            <i class="mdi mdi-lead-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger expo-delete-btn" data-id="{{ $item->id }}" title="Delete Expo Entry">
                                            <i class="mdi mdi-delete"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No Expo entries found. Click <strong>"Add Details"</strong> to register new Expo enquiries.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.expo-delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this Expo entry?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/expo/delete/' + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: res.message || 'Expo entry deleted.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', res.message || 'Unable to delete.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'Unable to delete Expo entry.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    @include('partials.lead-bulk-upload-modal', [
        'modalId' => 'bulkUploadExpoModal',
        'type' => 'expo',
        'title' => 'Bulk Upload Expo Leads',
        'templateRoute' => route('expo.sample-template'),
        'uploadRoute' => route('expo.bulk-upload')
    ])
@endsection