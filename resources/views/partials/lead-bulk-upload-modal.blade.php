<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="mdi mdi-file-upload-outline me-2 text-primary"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="{{ $modalId }}Form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Step 1: Template Download Section -->
                    <div class="card border-0 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">
                                        <i class="mdi mdi-information-outline text-info me-1"></i> Need the official spreadsheet format?
                                    </h6>
                                    <p class="text-muted small mb-0">Download sample template with example entries and proper column headers:</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ $templateRoute }}?format=xlsx" class="btn btn-sm btn-outline-success">
                                        <i class="mdi mdi-file-excel me-1"></i> Sample Excel (.xlsx)
                                    </a>
                                    <a href="{{ $templateRoute }}?format=csv" class="btn btn-sm btn-outline-secondary">
                                        <i class="mdi mdi-file-delimited me-1"></i> Sample CSV (.csv)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form Elements -->
                    <div class="upload-form-fields">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Spreadsheet File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text text-muted">
                                Supported formats: Microsoft Excel (.xlsx, .xls) and Comma-Separated Values (.csv). Max file size: 40MB.
                            </div>
                        </div>

                        @if($type === 'expo')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Default Expo Event Name (Optional)</label>
                            <input type="text" name="expo_event" class="form-control" placeholder="e.g. Prompt Expo, Build Intec">
                            <div class="form-text text-muted">
                                If the spreadsheet's "Expo Event" or "Lead Source" column is empty for a row, this name will be applied automatically.
                            </div>
                        </div>
                        @endif

                        <div class="card border border-warning-subtle bg-warning-subtle p-3 mb-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="update_existing" value="1" id="{{ $modalId }}_updateExisting">
                                <label class="form-check-label fw-semibold text-dark" for="{{ $modalId }}_updateExisting">
                                    Update existing leads (fill empty fields only)
                                </label>
                            </div>
                            <small class="text-muted mt-1 d-block ps-4">
                                If checked, matching phone numbers will have any blank fields updated without overwriting existing data. If unchecked, duplicate phone numbers will be skipped.
                            </small>
                        </div>

                        <div class="alert alert-info py-2 px-3 small mb-0">
                            <strong><i class="mdi mdi-check-circle-outline me-1"></i> Guidelines:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Valid 10-digit mobile number is required for every row.</li>
                                <li>Either <strong>Name</strong> or <strong>Company Name</strong> must be present.</li>
                                @if($type === 'expo')
                                <li>Every Expo lead automatically links to or creates a Customer record (<code>CUSxxxx</code>) and displays directly on the Expo page.</li>
                                @else
                                <li>If a row has an Expo source, please use the <strong>Expo Details</strong> bulk upload screen instead.</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Progress / Spinner State -->
                    <div class="upload-progress-container text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary">Importing and Validating Leads...</h6>
                        <p class="text-muted small mb-0">Processing rows and verifying customer records. Please do not close this window.</p>
                    </div>

                    <!-- Result Summary State -->
                    <div class="upload-result-container d-none mt-2">
                        <div class="alert alert-success d-flex align-items-center mb-3">
                            <i class="mdi mdi-check-circle fs-3 me-2"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-0" id="{{ $modalId }}_resultTitle">Import Completed Successfully</h6>
                                <div class="small" id="{{ $modalId }}_resultSubtitle">Summary of processed records</div>
                            </div>
                        </div>

                        <div class="row g-2 text-center mb-3">
                            <div class="col">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Total Rows</div>
                                    <div class="fs-5 fw-bold text-dark" id="{{ $modalId }}_resTotal">0</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-2 bg-success-subtle">
                                    <div class="text-success small fw-semibold">Imported</div>
                                    <div class="fs-5 fw-bold text-success" id="{{ $modalId }}_resImported">0</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-2 bg-info-subtle">
                                    <div class="text-info small fw-semibold">Updated</div>
                                    <div class="fs-5 fw-bold text-info" id="{{ $modalId }}_resUpdated">0</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-2 bg-warning-subtle">
                                    <div class="text-warning small fw-semibold">Skipped</div>
                                    <div class="fs-5 fw-bold text-warning" id="{{ $modalId }}_resSkipped">0</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-2 bg-danger-subtle">
                                    <div class="text-danger small fw-semibold">Failed</div>
                                    <div class="fs-5 fw-bold text-danger" id="{{ $modalId }}_resFailed">0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Skipped Details List -->
                        <div id="{{ $modalId }}_skippedSection" class="mb-3 d-none">
                            <label class="fw-bold small text-warning mb-1">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> Skipped Rows (<span id="{{ $modalId }}_skippedCount">0</span>):
                            </label>
                            <div class="border rounded p-2 bg-light" style="max-height: 140px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0 small text-muted" id="{{ $modalId }}_skippedList"></ul>
                            </div>
                        </div>

                        <!-- Failed Details List -->
                        <div id="{{ $modalId }}_failedSection" class="mb-3 d-none">
                            <label class="fw-bold small text-danger mb-1">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Failed Rows (<span id="{{ $modalId }}_failedCount">0</span>):
                            </label>
                            <div class="border rounded p-2 bg-light" style="max-height: 140px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0 small text-danger" id="{{ $modalId }}_failedList"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary upload-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary upload-submit-btn">
                        <i class="mdi mdi-cloud-upload me-1"></i> Upload & Import
                    </button>
                    <button type="button" class="btn btn-success upload-done-btn d-none" data-bs-dismiss="modal">
                        <i class="mdi mdi-check me-1"></i> Done / Refresh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('{{ $modalId }}');
    const form = document.getElementById('{{ $modalId }}Form');
    const formFields = modalEl.querySelector('.upload-form-fields');
    const progressContainer = modalEl.querySelector('.upload-progress-container');
    const resultContainer = modalEl.querySelector('.upload-result-container');
    const submitBtn = modalEl.querySelector('.upload-submit-btn');
    const cancelBtn = modalEl.querySelector('.upload-cancel-btn');
    const doneBtn = modalEl.querySelector('.upload-done-btn');

    let importPerformed = false;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire('Error', 'Please select a spreadsheet file (.xlsx, .xls, .csv).', 'warning');
            return;
        }

        const formData = new FormData(form);

        // UI state: show progress
        formFields.classList.add('d-none');
        progressContainer.classList.remove('d-none');
        resultContainer.classList.add('d-none');
        submitBtn.disabled = true;
        submitBtn.classList.add('d-none');
        cancelBtn.classList.add('d-none');

        $.ajax({
            url: '{{ $uploadRoute }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (res) {
                progressContainer.classList.add('d-none');
                resultContainer.classList.remove('d-none');
                doneBtn.classList.remove('d-none');

                if (res.success) {
                    importPerformed = (res.imported > 0 || res.updated > 0);
                    document.getElementById('{{ $modalId }}_resultTitle').textContent = 'Import Finished';
                    document.getElementById('{{ $modalId }}_resultSubtitle').textContent = res.message;
                    document.getElementById('{{ $modalId }}_resTotal').textContent = res.total_rows || 0;
                    document.getElementById('{{ $modalId }}_resImported').textContent = res.imported || 0;
                    document.getElementById('{{ $modalId }}_resUpdated').textContent = res.updated || 0;
                    document.getElementById('{{ $modalId }}_resSkipped').textContent = res.skipped || 0;
                    document.getElementById('{{ $modalId }}_resFailed').textContent = res.failed || 0;

                    // Populate skipped
                    const skippedSection = document.getElementById('{{ $modalId }}_skippedSection');
                    const skippedList = document.getElementById('{{ $modalId }}_skippedList');
                    skippedList.innerHTML = '';
                    if (res.skipped_rows && res.skipped_rows.length > 0) {
                        document.getElementById('{{ $modalId }}_skippedCount').textContent = res.skipped_rows.length;
                        res.skipped_rows.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML = `<strong>Row ${item.row}:</strong> ${item.reason} <span class="text-muted">(${item.phone || ''})</span>`;
                            skippedList.appendChild(li);
                        });
                        skippedSection.classList.remove('d-none');
                    } else {
                        skippedSection.classList.add('d-none');
                    }

                    // Populate failed
                    const failedSection = document.getElementById('{{ $modalId }}_failedSection');
                    const failedList = document.getElementById('{{ $modalId }}_failedList');
                    failedList.innerHTML = '';
                    if (res.error_rows && res.error_rows.length > 0) {
                        document.getElementById('{{ $modalId }}_failedCount').textContent = res.error_rows.length;
                        res.error_rows.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML = `<strong>Row ${item.row}:</strong> ${item.reason} <span class="text-muted">(${item.phone || ''})</span>`;
                            failedList.appendChild(li);
                        });
                        failedSection.classList.remove('d-none');
                    } else {
                        failedSection.classList.add('d-none');
                    }
                } else {
                    Swal.fire('Import Error', res.message || 'File processing failed.', 'error');
                    resetModal();
                }
            },
            error: function (xhr) {
                progressContainer.classList.add('d-none');
                let errorMsg = 'Failed to upload spreadsheet. Please check the file and try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Upload Error', errorMsg, 'error');
                resetModal();
            }
        });
    });

    function resetModal() {
        formFields.classList.remove('d-none');
        progressContainer.classList.add('d-none');
        resultContainer.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.classList.remove('d-none');
        cancelBtn.classList.remove('d-none');
        doneBtn.classList.add('d-none');
        form.reset();
    }

    // When modal closes, if records were imported/updated, reload view/DataTable
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (importPerformed) {
            location.reload();
        } else {
            resetModal();
        }
    });

    doneBtn.addEventListener('click', function () {
        if (importPerformed) {
            location.reload();
        }
    });
});
</script>
