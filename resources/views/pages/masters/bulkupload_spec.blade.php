{{-- resources/views/pages/masters/bulkupload_spec.blade.php --}}

@extends('layouts.app')

@section('main-content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Bulk Upload Product Specifications</h4>
        <p class="text-muted mb-0 small">
            Upload an Excel file with one sheet per product.
            Sheet names are matched to <strong>Product Model</strong> or <strong>Product Name</strong>.
        </p>
    </div>

    {{-- ── Result Alerts ─────────────────────────────────────────────────── --}}
    @if(session('bulk_results'))
        @php $r = session('bulk_results'); @endphp

        @if(!empty($r['success']))
            <div id="alert-success" style="display:block; opacity:1; background:#d1e7dd; border:1px solid #a3cfbb; border-radius:6px; padding:16px 20px; margin-bottom:16px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                    <div>
                        <div style="display:flex; align-items:center; margin-bottom:8px;">
                            <i class="bi bi-check-circle-fill" style="color:#0f5132; font-size:1.1rem; margin-right:8px;"></i>
                            <strong style="color:#0f5132;">{{ count($r['success']) }} Sheet(s) Imported Successfully</strong>
                        </div>
                        <ul style="margin:0; padding-left:20px; font-size:0.85rem; color:#0f5132;">
                            @foreach($r['success'] as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('alert-success').style.display='none';"
                            style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:#0f5132; line-height:1; padding:0; flex-shrink:0;">
                        &times;
                    </button>
                </div>
            </div>
        @endif

        @if(!empty($r['skipped']))
            <div id="alert-skipped" style="display:block; opacity:1; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; padding:16px 20px; margin-bottom:16px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                    <div>
                        <div style="display:flex; align-items:center; margin-bottom:8px;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#664d03; font-size:1.1rem; margin-right:8px;"></i>
                            <strong style="color:#664d03;">{{ count($r['skipped']) }} Sheet(s) Skipped</strong>
                        </div>
                        <ul style="margin:0; padding-left:20px; font-size:0.85rem; color:#664d03;">
                            @foreach($r['skipped'] as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('alert-skipped').style.display='none';"
                            style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:#664d03; line-height:1; padding:0; flex-shrink:0;">
                        &times;
                    </button>
                </div>
            </div>
        @endif

        @if(!empty($r['errors']))
            <div id="alert-errors" style="display:block; opacity:1; background:#f8d7da; border:1px solid #f1aeb5; border-radius:6px; padding:16px 20px; margin-bottom:16px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                    <div>
                        <div style="display:flex; align-items:center; margin-bottom:8px;">
                            <i class="bi bi-x-circle-fill" style="color:#842029; font-size:1.1rem; margin-right:8px;"></i>
                            <strong style="color:#842029;">Errors</strong>
                        </div>
                        <ul style="margin:0; padding-left:20px; font-size:0.85rem; color:#842029;">
                            @foreach($r['errors'] as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('alert-errors').style.display='none';"
                            style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:#842029; line-height:1; padding:0; flex-shrink:0;">
                        &times;
                    </button>
                </div>
            </div>
        @endif
    @endif

    {{-- ── Upload Card ────────────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>
                        Upload Excel File
                    </h6>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('bulkupload_spec') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                        @csrf

                        <div class="mb-4">
                            <label for="excel_file" class="form-label fw-semibold">
                                Select Excel File <span class="text-danger">*</span>
                            </label>
                            <input
                                type="file"
                                name="excel_file"
                                id="excel_file"
                                class="form-control @error('excel_file') is-invalid @enderror"
                                accept=".xlsx,.xls"
                                required
                            >
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Accepted: <code>.xlsx</code>, <code>.xls</code> &nbsp;|&nbsp; Max: 10 MB
                            </div>
                        </div>

                        {{-- Selected file preview - using border/bg-light instead of alert class --}}
                        <div id="filePreview" class="d-none mb-4 py-2 px-3 border rounded bg-light">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i>
                            <span id="fileName" class="small fw-semibold"></span>
                            <span id="fileSize" class="text-muted small ms-2"></span>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4" id="uploadBtn">
                                <i class="bi bi-upload me-1"></i> Upload Specifications
                            </button>
                            <a href="{{ route('bulkupload_spec_form') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ── Instructions ────────────────────────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        How It Works
                    </h6>
                </div>
                <div class="card-body p-4">
                    <ol class="mb-0 small text-muted ps-3">
                        <li class="mb-2">
                            Each <strong class="text-dark">Excel sheet name</strong> must match a product's
                            <strong class="text-dark">Model</strong> or <strong class="text-dark">Name</strong>
                            (case-insensitive, partial match supported).
                        </li>
                        <li class="mb-2">
                            The first row containing <strong class="text-dark">"Particulars"</strong> in Column A
                            is treated as the header row.
                        </li>
                        <li class="mb-2">
                            <strong class="text-dark">Column A</strong> — Description (Particulars)<br>
                            <strong class="text-dark">Column B</strong> — Unit (Kg, Mm, etc.)<br>
                            <strong class="text-dark">Column C</strong> — Specification value
                        </li>
                        <li class="mb-0 text-warning-emphasis">
                            <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                            Existing specifications for matched products will be <strong>replaced</strong>.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sample Format Table ─────────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-table me-2 text-secondary"></i>
                Expected Excel Sheet Format
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0 small align-middle">
                    <thead>
                        <tr class="table-secondary">
                            <th class="ps-3" style="width:10%">Row</th>
                            <th style="width:35%">A — Particulars</th>
                            <th style="width:15%">B — Unit</th>
                            <th>C — Technical Specification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-muted fst-italic">
                            <td class="ps-3 text-center">1</td>
                            <td colspan="3">TECHNICAL SPECIFICATIONS FOR HYDRAULIC HAND PALLET TRUCK. <span class="badge bg-secondary ms-1">ignored</span></td>
                        </tr>
                        <tr class="table-warning fw-semibold">
                            <td class="ps-3 text-center">2</td>
                            <td>Particulars <span class="badge bg-warning text-dark ms-1">header</span></td>
                            <td>Unit</td>
                            <td>Technical Specification</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-center text-muted">3</td>
                            <td>Make</td><td>-</td><td>Maini</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-center text-muted">4</td>
                            <td>Model</td><td>-</td><td>PV25E</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-center text-muted">5</td>
                            <td>Basic Capacity</td><td>Kg</td><td>2500</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-center text-muted">6</td>
                            <td>Fork Length</td><td>Mm</td><td>1150</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-center text-muted">7</td>
                            <td>Max Lift Height</td><td>Mm</td><td>205</td>
                        </tr>
                        <tr class="table-light text-muted fst-italic">
                            <td class="ps-3 text-center">…</td>
                            <td colspan="3">More rows…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    // Show file name + size preview after selection
    document.getElementById('excel_file').addEventListener('change', function () {
        const file    = this.files[0];
        const preview = document.getElementById('filePreview');
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent =
                '(' + (file.size / 1024).toFixed(1) + ' KB)';
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }
    });

    // Disable button + show spinner on submit
    document.getElementById('bulkUploadForm').addEventListener('submit', function () {
        const btn = document.getElementById('uploadBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading…';
        btn.disabled  = true;
    });
</script>
@endsection
