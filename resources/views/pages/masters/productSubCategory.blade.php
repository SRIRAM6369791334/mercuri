@extends('layouts.app')
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
    @can('product-categories-display')
        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Product Group</h5>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-4">
                                @can('product-categories-create')
                                <div class="d-flex justify-content-end">
                                <a data-bs-target="#add_category_modal" data-bs-toggle="modal"
                                        class="btn btn-primary  waves-effect waves-light">
                                        <i class="mdi mdi-plus"></i> Add group
                                    </a>
                                      <a href="{{ url('/addproduct') }}" class="btn btn-outline-secondary ms-2">
                                    Use In Product
                                </a>
                                </div>
                                  
                                @endcan
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container bg-white form-card mt-5">

            <div class="row">
                <div class="col-lg-12 py-4">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100 p-4">
                        <thead>
                            <tr>
                                <th style="text-align: center;">S.No</th>
                                <th style="text-align: center;">Segment</th>
                                <th style="text-align: center;">Group</th>
                                {{-- <th style="text-align: center;">Product</th> --}}
                                <th style="text-align: center;">Action</th>

                            </tr>
                        </thead>
                        <tbody>

                            @php $i = 1; @endphp

                            @foreach ($productCategory as $data)
                                @php
                                    $attachedProductIds = App\Models\Product::where('sub_cat_id', $data->id)->pluck('id')->toArray();
                                @endphp
                                <tr style="text-align: center;">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $data->segmentDetails->category_name ?? 'N/A' }}</td>
                                    <td>{{ $data->subcategory_name }}</td>
                                    <td>
                                        <div class="buttons_td">

                                            <button type="button" class="btn btn-bull_yellow edit-sub-cat-btn waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#edit_sub_category_modal"
                                                data-id="{{ $data->id }}" data-catid="{{ $data->cat_id }}" data-subcatname="{{ $data->subcategory_name }}"
                                                data-productids="{{ implode(',', $attachedProductIds) }}">
                                                <i class="mdi mdi-lead-pencil"></i> Edit
                                            </button>

                                            <button data-url="/delete-sub-category/{{ $data->id }}" type="button"
                                                class="common-delete-btn me-3 btn btn-bull_red waves-effect waves-light">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade modal-lg" id="add_category_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Add Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/add-sub-category-action">
                            <div class="row mb-4">
                                @csrf

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Product Segment <span
                                                style="color:red">*</span></label>
                                        <select class="form-select" id="add_product_segment_id" name="product_segment_id" required>
                                            <option value="">-- Choose Segment --</option>
                                            @if ($status = App\Models\ProductCategory::get())
                                                @foreach ($status as $data)
                                                    <option value="{{ $data->id }}">
                                                        {{ $data->category_name }}
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Group name <span style="color:red">*</span>
                                        </label>

                                        <input type="text" id="add_sub_category" name="sub_category" class="form-control" placeholder="Type group name..."
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <div class="form-group mt-2">
                                        <label for="productSelect" class="form-label">
                                            Product (Multiple Select)
                                        </label>
                                        <select class="form-select select2-add-products" id="add_product_ids" name="product_ids[]" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Add
                                Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade modal-lg" id="edit_sub_category_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Edit Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/edit-sub-category-action">
                            <div class="row mb-4">
                                @csrf

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Segment <span style="color:red">*</span>
                                        </label>
                                        <input type="hidden" name="sub_hidden_id" id="sub_hidden_id">
                                        <select class="form-select" id="segmentId" name="product_segment_id" required>
                                            <option value="">-- Choose Segment --</option>
                                            @if ($status = App\Models\ProductCategory::get())
                                                @foreach ($status as $data)
                                                    <option value="{{ $data->id }}">
                                                        {{ $data->category_name }}
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Group name <span style="color:red">*</span>
                                        </label>
                                        <input type="text" id="edit_sub_category_name" name="edit_sub_category_name"
                                            class="form-control" placeholder="Type group name..." required>
                                    </div>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <div class="form-group mt-2">
                                        <label for="editProductSelect" class="form-label">
                                            Product (Multiple Select)
                                        </label>
                                        <select class="form-select select2-edit-products" id="edit_product_ids" name="edit_product_ids[]" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block mt-4 mb-2">Edit Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        </div>

    @endcan

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            // Initialize Select2 on multiple product dropdowns
            if ($.fn.select2) {
                $('.select2-add-products').select2({
                    placeholder: '-- Choose Products --',
                    allowClear: true,
                    dropdownParent: $('#add_category_modal')
                });

                $('.select2-edit-products').select2({
                    placeholder: '-- Choose Products --',
                    allowClear: true,
                    dropdownParent: $('#edit_sub_category_modal')
                });
            }

            // Reset Add Group modal on show
            $('#add_category_modal').on('show.bs.modal', function () {
                $('#add_product_segment_id').val('');
                $('#add_sub_category').val('');
                $('#add_product_ids').empty();
                if ($.fn.select2) {
                    $('#add_product_ids').trigger('change.select2');
                }
            });

            // Function to fetch products via AJAX based on keyword and segment
            function fetchProductsByKeyword(keyword, segmentId, targetSelectEl, selectedValues) {
                if (!segmentId) {
                    $(targetSelectEl).empty();
                    if ($.fn.select2) {
                        $(targetSelectEl).trigger('change.select2');
                    } else {
                        $(targetSelectEl).trigger('change');
                    }
                    return;
                }

                $.ajax({
                    url: '/get-products-by-keyword',
                    type: 'GET',
                    data: { keyword: keyword, segment_id: segmentId },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status) {
                            let currentSelected = selectedValues || $(targetSelectEl).val() || [];
                            if (!Array.isArray(currentSelected)) {
                                currentSelected = [currentSelected];
                            }
                            currentSelected = currentSelected.map(String);

                            $(targetSelectEl).empty();

                            if (res.products && res.products.length > 0) {
                                res.products.forEach(function (prod) {
                                    let isSel = currentSelected.includes(String(prod.id));
                                    let option = new Option(prod.productname, prod.id, isSel, isSel);
                                    $(targetSelectEl).append(option);
                                });
                            }

                            if ($.fn.select2) {
                                $(targetSelectEl).trigger('change.select2');
                            } else {
                                $(targetSelectEl).trigger('change');
                            }
                        }
                    }
                });
            }

            // Trigger product search on typing Group Name or changing Segment in Add Modal
            let addDebounce;
            $('#add_sub_category, #add_product_segment_id').on('input keyup change', function () {
                clearTimeout(addDebounce);
                addDebounce = setTimeout(function () {
                    let keyword = $('#add_sub_category').val().trim();
                    let segmentId = $('#add_product_segment_id').val();
                    fetchProductsByKeyword(keyword, segmentId, '#add_product_ids');
                }, 300);
            });

            // Trigger product search on typing Group Name or changing Segment in Edit Modal
            let editDebounce;
            $('#edit_sub_category_name, #segmentId').on('input keyup change', function () {
                clearTimeout(editDebounce);
                editDebounce = setTimeout(function () {
                    let keyword = $('#edit_sub_category_name').val().trim();
                    let segmentId = $('#segmentId').val();
                    fetchProductsByKeyword(keyword, segmentId, '#edit_product_ids');
                }, 300);
            });

            // Edit button click event
            $(document).on("click", ".edit-sub-cat-btn", function () {
                var catId = $(this).data('catid');
                var subCatName = $(this).data('subcatname');
                var id = $(this).data('id');
                var rawProductIds = String($(this).data('productids') || '');
                var productIds = rawProductIds ? rawProductIds.split(',').filter(Boolean) : [];

                $("#sub_hidden_id").val(id);
                $("#segmentId").val(catId);
                $("#edit_sub_category_name").val(subCatName);

                fetchProductsByKeyword(subCatName, catId, '#edit_product_ids', productIds);
            });
        });
    </script>

@endsection
