@extends('layouts.app')

@section('main-content')



    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 my-3">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold text-primary">Add Accessories to Product</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('accessories.store') }}" method="POST">
                    @csrf

                    <!-- 1. Select Product -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Category</label>
                        <select name="product_category_id" id="category" class="form-select mb-3">
                            <option value="">-- Choose Category (Optional) --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Select Products *</label>
                            <span class="badge bg-primary px-3 py-2" id="selectedCount">0 Selected</span>
                        </div>

                        <div id="productSelectionError" class="alert alert-danger py-2 mb-2 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Please select at least one product before saving.
                        </div>

                        <div class="card border shadow-sm rounded-3" id="productCheckboxCard">
                            <div class="card-header bg-light py-2 px-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="productSearch" class="form-control border-start-0 ps-0" placeholder="Search products by name...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <button type="button" class="btn btn-sm btn-outline-success me-1" id="selectAllProducts">
                                            Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="deselectAllProducts">
                                            Deselect All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-3" style="max-height: 280px; overflow-y: auto;" id="productCheckboxContainer">
                                <div class="row g-2" id="productCheckboxList">
                                    @foreach($products as $product)
                                        @php
                                            $isChecked = in_array($product->id, $selectedProductIds ?? []);
                                        @endphp
                                        <div class="col-md-6 col-lg-4 product-item-col" data-name="{{ strtolower($product->productname) }}">
                                            <div class="form-check p-2 border rounded bg-white hover-shadow d-flex align-items-center">
                                                <input class="form-check-input product-checkbox ms-1 me-2" 
                                                       type="checkbox" 
                                                       name="product_ids[]" 
                                                       value="{{ $product->id }}" 
                                                       id="product_{{ $product->id }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark text-wrap text-break mb-0 flex-grow-1 cursor-pointer" 
                                                       for="product_{{ $product->id }}">
                                                    {{ $product->productname }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="noProductsFound" class="text-center py-3 text-muted d-none">
                                    No matching products found
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- 2. Multiple Accessories Dynamic Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Accessory Details</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addMoreRow">
                            <i class="bi bi-plus-lg"></i> + Add More Accessory
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="accessoriesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Accessory Name / Description *</th>
                                    <th>Part Number</th><th>HSN Code</th>
                                    <th style="width: 200px;">Price (₹) *</th>
                                    <th style="width: 150px;">GST % *</th>
                                    <th>Status *</th>
                                    <th style="width: 80px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Row 1 (Default Row) -->
                                <tr>
                                    <td>
                                        <input type="text" name="accessories[0][name]" class="form-control"
                                            placeholder="e.g. Biometric Scanner">
                                    </td>
                                    <td><input type="text" name="accessories[0][part_no]" class="form-control"></td>
                                    <td><input type="text" name="accessories[0][hsn]" class="form-control"></td>
                                    <td>
                                        <input type="number" step="0.01" name="accessories[0][price]" class="form-control"
                                            placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="accessories[0][tax_percent]" value="18.00"
                                            class="form-control">
                                    </td>
                                    <td><select name="accessories[0][status]" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success px-4" id="saveAccessoriesBtn">Save All Accessories</button>
                        <a href="{{ route('accessories.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript to Add / Remove Dynamic Rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#accessoriesTable tbody');
            const addMoreBtn = document.getElementById('addMoreRow');

            if (!tableBody || !addMoreBtn) return;

            let rowIndex = tableBody.querySelectorAll('tr').length;

            addMoreBtn.addEventListener('click', function () {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="accessories[${rowIndex}][name]" class="form-control" placeholder="e.g. Biometric Scanner">
                    </td>
                    <td><input type="text" name="accessories[${rowIndex}][part_no]" class="form-control"></td>
                    <td><input type="text" name="accessories[${rowIndex}][hsn]" class="form-control"></td>
                    <td>
                        <input type="number" step="0.01" name="accessories[${rowIndex}][price]" class="form-control" placeholder="0.00">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="accessories[${rowIndex}][tax_percent]" value="18.00" class="form-control">
                    </td>
                    <td><select name="accessories[${rowIndex}][status]" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                    </td>
                `;
                tableBody.appendChild(newRow);
                rowIndex++;
                updateRemoveButtons();
            });

            tableBody.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.remove-row');
                if (removeBtn) {
                    removeBtn.closest('tr').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach((row) => {
                    const removeBtn = row.querySelector('.remove-row');
                    if (removeBtn) {
                        removeBtn.disabled = (rows.length === 1);
                    }
                });
            }

            updateRemoveButtons();
        });
    </script>
    
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    function updateSelectedCount() {
        let count = $('.product-checkbox:checked').length;
        $('#selectedCount').text(count + ' Selected');
        if (count > 0) {
            $('#productSelectionError').addClass('d-none');
            $('#productCheckboxCard').removeClass('border-danger');
        }
    }

    $(document).on('change', '.product-checkbox', function() {
        updateSelectedCount();
        if ($(this).is(':checked')) {
            $(this).closest('.form-check').addClass('border-primary bg-light-subtle');
        } else {
            $(this).closest('.form-check').removeClass('border-primary bg-light-subtle');
        }
    });

    updateSelectedCount();
    $('.product-checkbox:checked').closest('.form-check').addClass('border-primary bg-light-subtle');

    $('form').on('submit', function(e) {
        let hasProduct = $('.product-checkbox:checked').length > 0;
        if (!hasProduct) {
            e.preventDefault();
            $('#productSelectionError').removeClass('d-none');
            $('#productCheckboxCard').addClass('border-danger');
            $('html, body').animate({ scrollTop: $('#productCheckboxCard').offset().top - 120 }, 300);
            return false;
        } else {
            $('#productSelectionError').addClass('d-none');
            $('#productCheckboxCard').removeClass('border-danger');
        }

        let accValid = true;
        $('#accessoriesTable tbody tr').each(function() {
            let nameInput = $(this).find('input[name*="[name]"]');
            let priceInput = $(this).find('input[name*="[price]"]');

            if (!nameInput.val() || nameInput.val().trim() === '') {
                nameInput.addClass('is-invalid');
                accValid = false;
            } else {
                nameInput.removeClass('is-invalid');
            }

            if (!priceInput.val() || priceInput.val().trim() === '' || isNaN(priceInput.val())) {
                priceInput.addClass('is-invalid');
                accValid = false;
            } else {
                priceInput.removeClass('is-invalid');
            }
        });

        if (!accValid) {
            e.preventDefault();
            alert('Please fill out all required accessory names and prices.');
            return false;
        }
    });

    $('#productSearch').on('keyup input', function() {
        let searchTerm = $(this).val().toLowerCase().trim();
        let visibleCount = 0;
        
        $('.product-item-col').each(function() {
            let name = $(this).data('name') || '';
            if (name.includes(searchTerm)) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        if (visibleCount === 0) {
            $('#noProductsFound').removeClass('d-none');
        } else {
            $('#noProductsFound').addClass('d-none');
        }
    });

    $('#selectAllProducts').click(function() {
        $('.product-item-col:not(.d-none) .product-checkbox').prop('checked', true).trigger('change');
    });

    $('#deselectAllProducts').click(function() {
        $('.product-item-col:not(.d-none) .product-checkbox').prop('checked', false).trigger('change');
    });

    $('#category').change(function () {
        let categoryId = $(this).val() || 'all';

        let currentlyChecked = [];
        $('.product-checkbox:checked').each(function() {
            currentlyChecked.push(parseInt($(this).val()));
        });

        $('#productCheckboxList').html('<div class="col-12 text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Loading products...</div>');

        $.ajax({
            url: '/get-accproducts/' + categoryId,
            type: 'GET',
            success: function(products){
                let html = '';
                if (products.length === 0) {
                    html = '<div class="col-12 text-center py-3 text-muted">No products available for this category</div>';
                } else {
                    $.each(products, function(index, product){
                        let isChecked = currentlyChecked.includes(product.id) ? 'checked' : '';
                        let activeClass = isChecked ? 'border-primary bg-light-subtle' : '';
                        html += `
                            <div class="col-md-6 col-lg-4 product-item-col" data-name="${product.productname.toLowerCase()}">
                                <div class="form-check p-2 border rounded bg-white hover-shadow d-flex align-items-center ${activeClass}">
                                    <input class="form-check-input product-checkbox ms-1 me-2" 
                                           type="checkbox" 
                                           name="product_ids[]" 
                                           value="${product.id}" 
                                           id="product_${product.id}"
                                           ${isChecked}>
                                    <label class="form-check-label text-dark text-wrap text-break mb-0 flex-grow-1 cursor-pointer" 
                                           for="product_${product.id}">
                                        ${product.productname}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#productCheckboxList').html(html);
                updateSelectedCount();
                $('#productSearch').val('').trigger('keyup');
            }
        });
    });

});
</script>
@endsection