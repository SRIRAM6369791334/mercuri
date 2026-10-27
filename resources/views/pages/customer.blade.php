@extends('layouts.app')
@section('main-content')

    @can('customer-display')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('import_errors'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h6 class="alert-heading fw-bold mb-2">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> Skipped Rows Details:
                </h6>
                <ul class="mb-0 ps-3 small" style="max-height: 180px; overflow-y: auto;">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">All Customers</h5>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-2">
                                <div class="btn btn-warning  waves-effect waves-light text-dark" data-bs-toggle="modal"
                                    data-bs-target="#cus_bulk_upload_modal">
                                    <i class="mdi mdi-plus"></i> Bulk Upload
                                </div>
                            </div>
                            <div class="col-lg-2">
                                @can('customer-create')
                                    <a href="/addcustomer" class="btn btn-primary  waves-effect waves-light">
                                        <i class="mdi mdi-plus"></i> Add Customer
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container-fluid bg-white form-card mt-5">
            <div class="row">
                <div class="col-lg-12 p-4">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th style="text-align: center;">S.No</th>
                                <th style="text-align: center;">Customer ID</th>
                                <th style="text-align: center;">Customer Name</th>
                                <th style="text-align: center;">Location</th>
                                <th style="text-align: center;">Email</th>
                                <th style="text-align: center;">Contact No</th>
                                @canany(['customer-edit', 'customer-delete'])
                                    <th style="text-align: center;">Action</th>
                                @endcanany

                            </tr>
                        </thead>
                        <tbody>

                            @php $i = 1; @endphp

                            @foreach ($customer as $c)
                                <tr style="text-align: center;">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $c->cusid }}</td>
                                    <td>{{ $c->customername }}</td>
                                    <td>{{ $c->location_display }}</td>
                                    <td>{{ $c->cus_email }}</td>
                                    <td>{{ $c->contact_no }}</td>
                                    @canany(['customer-edit', 'customer-delete'])
                                        <td>
                                            @can('customer-edit')
                                                <a href="{{ route('customer.edit', $c->id) }}" class="btn btn-warning">
                                                    <i class="mdi mdi-lead-pencil"></i> Edit
                                                </a>
                                            @endcan
                                            @can('customer-delete')
                                                <button class="btn btn-danger custodelete-btn" data-id="{{ $c->id }}">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </button>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endcan

    {{-- Customer Bulk Upload Modal --}}
    <div class="modal fade" id="cus_bulk_upload_modal" aria-hidden="true" aria-labelledby="cus_bulk_upload_modal"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cus_bulk_upload_modal">
                        <i class="mdi mdi-account-multiple-plus me-1 text-primary"></i> Customer Bulk Upload
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Sample Template Download Card --}}
                    <div class="card border-0 bg-light shadow-sm mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">
                                        <i class="mdi mdi-information-outline text-info me-1"></i> Need the official template?
                                    </h6>
                                    <p class="text-muted small mb-0">Download sample template with example entries and proper column headers:</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('customer.sample-template', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success">
                                        <i class="mdi mdi-file-excel me-1"></i> Sample Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('customer.sample-template', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="mdi mdi-file-delimited me-1"></i> Sample CSV (.csv)
                                    </a>
                                </div>
                            </div>

                            <hr class="my-2 border-secondary-subtle">
                            <div class="small text-dark mt-2">
                                <span class="badge bg-warning text-dark me-1" style="font-size: 11px;">Note:</span>
                                <ul class="mb-0 ps-3 mt-1 text-muted" style="line-height: 1.6;">
                                    <li><strong>Communication Address, Assigned User, Designation, and Department</strong> are optional.</li>
                                    <li><strong>All other fields</strong> are mandatory.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Form --}}
                    <form method="POST" action="/customer-bulk-upload" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_data_file" class="form-label fw-semibold">
                                Choose File <span style="color:red">*</span>
                            </label>
                            <input type="file" id="customer_data_file" name="customer_data_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text text-muted mt-1">
                                Supported formats: <strong>Microsoft Excel (.xlsx, .xls)</strong> and <strong>CSV (.csv)</strong>. Maximum file size: 20MB.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-upload me-1"></i> Upload Customers
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="/assets/js/pages/pass-addon.init.js"></script>


    <script>
        $(document).on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            var productname = $(this).data('productname');
            var gst = $(this).data('gst');
            var hsn = $(this).data('hsn');
            var rate = $(this).data('rate');


            $('.id').val(id);
            $('.productname').val(productname);
            $('.hsn').val(hsn);
            $('.gst').val(gst);
            $('.rate').val(rate);
        });
    </script>
    <script>
        $(document).on('click', '.custodelete-btn', function () {
            let customerId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this customer!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/customer/delete/' + customerId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: customerId
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Customer has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    'Failed to delete customer.',
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>



    <script>
        $(document).on('click', '.pass_edit', function () {
            var id = $(this).data('id');

            $('.id').val(id);
        })
    </script>

@endsection