@extends('layouts.app')
@section('title', 'Product Master Bulk Upload')
@section('main-content')

<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-start gap-2 mb-4" role="alert" id="successAlert">
            <i class="bi bi-check-circle-fill mt-1 fs-5"></i>
            <div class="flex-grow-1">
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-2" onclick="document.getElementById('successAlert').remove()"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert" id="errorAlert">
            <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
            <div class="flex-grow-1">
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-2" onclick="document.getElementById('errorAlert').remove()"></button>
        </div>
    @endif

    {{-- ── Detailed Report Dashboard (Displays after upload) ── --}}
    @if(session('bulk_report'))
        @php $report = session('bulk_report'); @endphp
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-graph-up-arrow me-2"></i>Upload Report & Cross-Verification
                </h5>
                <a href="{{ url('/products') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Go to Products List
                </a>
            </div>
            <div class="card-body p-4">
                {{-- Stat Counters --}}
                <div class="row g-3 mb-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <span class="fs-2 fw-bold text-success d-block">{{ count($report['created']) }}</span>
                            <span class="text-muted small fw-semibold text-uppercase">Products Created</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <span class="fs-2 fw-bold text-primary d-block">{{ count($report['updated']) }}</span>
                            <span class="text-muted small fw-semibold text-uppercase">Products Updated</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <span class="fs-2 fw-bold text-warning d-block">{{ count($report['skipped']) }}</span>
                            <span class="text-muted small fw-semibold text-uppercase">Rows Skipped</span>
                        </div>
                    </div>
                </div>

                {{-- Skipped rows warning if any --}}
                @if(!empty($report['skipped']))
                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Skipped Rows Details:</h6>
                        <ul class="mb-0 small ps-3">
                            @foreach($report['skipped'] as $skipMsg)
                                <li>{{ $skipMsg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Cross-Verify Table --}}
                @if(!empty($report['products_list']))
                    <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-table me-2"></i>Uploaded Products Cross-Verify List:</h6>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm align-middle mb-0 small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Model</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Rate (₹)</th>
                                    <th>UOM</th>
                                    <th>Qty</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['products_list'] as $idx => $prod)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold">{{ $prod['model'] }}</td>
                                        <td>{{ $prod['name'] }}</td>
                                        <td>{{ $prod['category'] }}</td>
                                        <td>₹{{ \App\Helpers\IndianNumberHelper::formatIndianCurrency($prod['rate']) }}</td>
                                        <td><span class="badge bg-secondary">{{ $prod['uom'] }}</span></td>
                                        <td>{{ $prod['quantity'] }}</td>
                                        <td class="text-center">
                                            @if($prod['status'] === 'Created')
                                                <span class="badge bg-success">Created</span>
                                            @else
                                                <span class="badge bg-primary">Updated</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Upload Form Card ── --}}
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header" style="background-color: #20567e;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-white"><i class="bi bi-file-earmark-arrow-up me-2"></i>Product Master Bulk Upload</h5>
                        <a href="{{ url('/sample_product_master_with_specs.xlsx') }}" class="btn btn-sm btn-light text-dark fw-semibold" download>
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">

                    <div class="alert alert-info small mb-4">
                        <strong><i class="bi bi-info-circle me-1"></i> How it works:</strong>
                        Upload your Excel file. The system will automatically:
                        <ol class="mb-0 mt-2 ps-3">
                            <li>Auto-detect header rows (Row 1 or Row 2).</li>
                            <li>Link or create <strong>Categories & Sub-categories</strong> (Segment & Group).</li>
                            <li>Import <strong>Products</strong> with Rate, Unit (UOM), Stock, HSN, and GST.</li>
                            <li><strong>Duplicate Safety:</strong> If a model already exists, its details are updated without duplicates.</li>
                        </ol>
                    </div>

                    <form method="POST" action="{{ route('bulk-upload.master') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Choose Excel File <span class="text-danger">*</span></label>
                            <input type="file" name="master_file" class="form-control" accept=".xlsx,.xls" required>
                            <div class="form-text text-muted">Accepted formats: <code>.xlsx</code>, <code>.xls</code> (Max 10MB)</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-upload me-2"></i>Upload & Import Products
                        </button>
                    </form>

                </div>
            </div>
        </div>

        {{-- Column Information Guide --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-table me-2 text-primary"></i>Expected Excel Columns
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Column Header</th>
                                    <th>Maps To</th>
                                    <th class="text-center">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Product Segment</td><td>Category</td><td class="text-center"><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td>Product Group</td><td>Sub-category</td><td class="text-center"><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td>Product Model</td><td>Model Code</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                                <tr><td>Product Name</td><td>Product Name</td><td class="text-center"><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td>Rate / Price</td><td>Price (₹)</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                                <tr><td>Unit (UOM) / Quantity</td><td>Unit (NOS, etc.)</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                                <tr><td>Part NO</td><td>Part Number</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                                <tr><td>HSN</td><td>HSN Code</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                                <tr><td>GST %</td><td>GST % (Default: 18%)</td><td class="text-center"><span class="badge bg-secondary">Optional</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
