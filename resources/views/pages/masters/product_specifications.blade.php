@extends('layouts.app')
@section('main-content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Product Specifications</h4>
        <a href="{{ route('bulkupload_spec_form') }}" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Bulk Upload
        </a>
    </div>
    {{-- Success / Error Alert --}}
    @if(session('success'))
        <div id="alert-success" style="display:block; background:#d1e7dd; border:1px solid #a3cfbb; border-radius:6px; padding:12px 16px; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="color:#0f5132;"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</span>
                <button onclick="document.getElementById('alert-success').style.display='none';"
                        style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#0f5132;line-height:1;padding:0;">&times;</button>
            </div>
        </div>
    @endif

    {{-- Product Selector --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <label class="form-label fw-semibold">Select Product <span class="text-danger">*</span></label>
            <select id="product_select" class="form-select">
                <option value="">— Choose a product —</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->productname }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Specifications Form (hidden until product selected) --}}
    <div id="spec_section" style="display:none;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2 text-primary"></i>
                    Specifications for: <span id="selected_product_name" class="text-primary"></span>
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add_row_btn">
                    <i class="bi bi-plus-lg me-1"></i> Add Row
                </button>
            </div>

            <div class="card-body p-0">
                <form method="POST" action="{{ route('product_specifications.store') }}" id="spec_form">
                    @csrf
                    <input type="hidden" name="productid" id="productid">

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 align-middle" id="spec_table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35%">Description</th>
                                    <th style="width:15%">Unit</th>
                                    <th style="width:40%">Specification</th>
                                    <th style="width:10%" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="spec_tbody">
                                {{-- rows injected by JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4" id="save_btn">
                            <i class="bi bi-save me-1"></i> Save Specifications
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" id="cancel_btn">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Empty state when no product selected --}}
    <div id="empty_state" class="text-center py-5 text-muted">
        <i class="bi bi-arrow-up-circle fs-1 d-block mb-3 text-secondary"></i>
        Select a product above to view or edit its specifications.
    </div>

</div>

@endsection

@section('scripts')
<script>
$(function () {

    // ── Helpers ──────────────────────────────────────────────────────────────

    function makeRow(description = '', unit = '', specification = '') {
        return `<tr>
            <td><input type="text" name="description[]" value="${escHtml(description)}" class="form-control form-control-sm" placeholder="e.g. Lift Height"></td>
            <td><input type="text" name="unit[]"        value="${escHtml(unit)}"        class="form-control form-control-sm" placeholder="e.g. mm"></td>
            <td><input type="text" name="specification[]" value="${escHtml(specification)}" class="form-control form-control-sm" placeholder="e.g. 3000"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove_row" title="Remove row">
                    <i class="mdi mdi-trash-can"></i>
                </button>
            </td>
        </tr>`;
    }

    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function addEmptyRow() {
        $('#spec_tbody').append(makeRow());
        // Focus the first input of the new row
        $('#spec_tbody tr:last-child input:first').focus();
    }

    // ── Product select change ─────────────────────────────────────────────────

    $('#product_select').on('change', function () {
        const productId   = $(this).val();
        const productName = $(this).find('option:selected').text();

        if (!productId) {
            $('#spec_section').hide();
            $('#empty_state').show();
            return;
        }

        // Show loading state
        $('#spec_section').show();
        $('#empty_state').hide();
        $('#selected_product_name').text(productName);
        $('#productid').val(productId);
        $('#spec_tbody').html('<tr><td colspan="4" class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>');

        // Load existing specifications
        $.get('/get-product-specifications/' + productId)
            .done(function (data) {
                $('#spec_tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (row) {
                        $('#spec_tbody').append(makeRow(row.description, row.unit, row.specification));
                    });
                } else {
                    // No specs yet — start with one blank row
                    addEmptyRow();
                }
            })
            .fail(function () {
                $('#spec_tbody').html('<tr><td colspan="4" class="text-center text-danger py-3">Failed to load specifications. Please try again.</td></tr>');
            });
    });

    // ── Add row ───────────────────────────────────────────────────────────────

    $('#add_row_btn').on('click', function () {
        addEmptyRow();
    });

    // ── Remove row ────────────────────────────────────────────────────────────

    $(document).on('click', '.remove_row', function () {
        const $tbody = $('#spec_tbody');
        if ($tbody.find('tr').length <= 1) {
            // Keep at least one row — just clear it instead of removing
            $(this).closest('tr').find('input').val('');
            return;
        }
        $(this).closest('tr').remove();
    });

    // ── Cancel ────────────────────────────────────────────────────────────────

    $('#cancel_btn').on('click', function () {
        $('#product_select').val('').trigger('change');
    });

    // ── Form submit: prevent if no product selected ───────────────────────────

    $('#spec_form').on('submit', function (e) {
        const productId = $('#productid').val();
        if (!productId) {
            e.preventDefault();
            alert('Please select a product first.');
            return;
        }

        // Remove rows where description AND specification are both empty
        $('#spec_tbody tr').each(function () {
            const desc = $(this).find('input[name="description[]"]').val().trim();
            const spec = $(this).find('input[name="specification[]"]').val().trim();
            if (!desc && !spec) {
                $(this).remove();
            }
        });

        // If all rows were empty, add one blank to avoid empty array error
        if ($('#spec_tbody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one specification row.');
            return;
        }

        const btn = document.getElementById('save_btn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
        btn.disabled  = true;
    });
    
     $('#product_select').select2({
    placeholder: "Search or select a product",
    allowClear: true,
    width: '100%'
});

});
</script>
@endsection