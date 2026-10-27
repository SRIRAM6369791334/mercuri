@extends('layouts.app')

@section('main-content')
<style>
    #accessories-table {
        table-layout: fixed !important;
        width: 100% !important;
    }
    #accessories-table th,
    #accessories-table td {
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        vertical-align: middle;
    }
    #accessories-table td.product-cell,
    #accessories-table td.desc-cell {
        font-size: 13px;
        line-height: 1.45;
    }
    .accessories-card-body .table-responsive {
        overflow-x: visible !important;
    }
</style>

<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mt-3 mb-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                    <strong>{{ session('warning') }}</strong>
                </div>
                @if(session('skipped_products') && count(session('skipped_products')) > 0)
                    <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#skippedProductsCollapse" aria-expanded="false" aria-controls="skippedProductsCollapse">
                        <i class="bi bi-list-ul me-1"></i> View Skipped Products ({{ count(session('skipped_products')) }})
                    </button>
                @endif
            </div>

            @if(session('skipped_products') && count(session('skipped_products')) > 0)
                <div class="collapse mt-3" id="skippedProductsCollapse">
                    <div class="card card-body bg-light border p-2" style="max-height: 250px; overflow-y: auto;">
                        <ol class="mb-0 ps-3 small text-secondary">
                            @foreach(session('skipped_products') as $item)
                                <li class="py-1">
                                    @if(is_array($item))
                                        <span class="badge bg-secondary me-1">Row {{ $item['row'] }}</span> {{ $item['name'] }}
                                    @else
                                        {{ $item }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
        <h3 class="fw-bold mb-0">Accessories Master</h3>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkUploadAccessoriesModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Bulk Upload
            </button>
            <a href="{{ route('accessories.create') }}" class="btn btn-primary">Add Accessory</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 accessories-card-body">
            <div class="table-responsive">
                <table id="accessories-table" class="table table-bordered table-striped align-middle w-100 mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%; text-align: center;">S.No</th>
                            <th style="width: 25%;">Mapped Product</th>
                            <th style="width: 10%;">Part No</th>
                            <th style="width: 8%;">HSN</th>
                            <th style="width: 20%;">Accessory Description</th>
                            <th style="width: 11%; text-align: right;">Price (₹)</th>
                            <th style="width: 7%; text-align: center;">GST %</th>
                            <th style="width: 7%; text-align: center;">Status</th>
                            <th style="width: 7%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accessories as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td class="fw-bold text-primary product-cell">
                                {{ $item->product->productname ?? 'No Product Mapped' }}
                            </td>
                            <td class="text-break">{{ $item->part_no ?: '-' }}</td>
                            <td>{{ $item->hsn ?: '-' }}</td>
                            <td class="desc-cell">{{ $item->name }}</td>
                            <td class="text-end fw-semibold">{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->tax_percent }}%</td>
                            <td class="text-center"><span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($item->status) }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('accessories.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadAccessoriesModal" tabindex="-1" aria-labelledby="bulkUploadAccessoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="bulkUploadAccessoriesModalLabel">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Bulk Upload Accessories
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accessories.bulk_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Upload your <strong>Accessories Matrix Excel</strong> file (.xlsx or .xls). The system will automatically map the accessories and prices to each matching product in the database.
                    </div>
                    <div class="mb-3">
                        <label for="accessory_file" class="form-label fw-semibold">Choose Excel File <span class="text-danger">*</span></label>
                        <input type="file" name="accessory_file" id="accessory_file" class="form-control" accept=".xlsx,.xls" required>
                        <div class="form-text text-muted">Accepted formats: <code>.xlsx</code>, <code>.xls</code> (Max 10MB)</div>
                    </div>
                    <div class="form-check p-2 bg-light rounded border">
                        <input class="form-check-input ms-1" type="checkbox" name="reset_accessories" value="1" id="resetAccessoriesCheck">
                        <label class="form-check-label ms-2 small fw-semibold text-danger" for="resetAccessoriesCheck">
                            <i class="bi bi-trash me-1"></i> Reset & Replace: Delete existing accessories before importing
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-upload me-1"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#accessories-table').DataTable({
            autoWidth: false,
            scrollX: false,
            responsive: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { targets: [0, 6, 7, 8], className: 'text-center' },
                { targets: [5], className: 'text-end' },
                { targets: -1, orderable: false }
            ]
        });
    });
</script>
@endsection
