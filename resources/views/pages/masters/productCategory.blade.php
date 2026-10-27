@extends('layouts.app')
@section('main-content')
    @can('product-categories-display')
        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Product Segment</h5>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-2">
                                @can('product-categories-create')
                                    <a data-bs-target="#add_category_modal" data-bs-toggle="modal"
                                        class="btn btn-primary  waves-effect waves-light">
                                        <i class="mdi mdi-plus"></i> Add Segment
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container bg-white form-card mt-5">

            <div class="row">
                <div class="col-lg-12 p-4">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th style="text-align: center;">S.No</th>
                                <th style="text-align: center;">Segment</th>
                                <th style="text-align: center;">Segment Type</th>
                                <th style="text-align: center;">Workflow</th>
                                <th style="text-align: center;">Lead Time</th>
                                @canany(['product-categories-edit', 'product-categories-delete'])
                                    <th style="text-align: center;">Action</th>
                                @endcanany

                            </tr>
                        </thead>
                        <tbody>

                            @php $i = 1; @endphp

                            @foreach ($productCategory as $data)
                                <tr style="text-align: center;">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $data->category_name}}</td>
                                    <td>{{ \App\Models\ProductCategory::SEGMENT_TYPES[$data->type] ?? strtoupper($data->type) }}</td>
                                    <td>{{ \App\Models\ProductCategory::WORKFLOW_TYPES[$data->sub_category] ?? ucfirst($data->sub_category) }}</td>
                                    <td>{{ $data->dispatch_time }}</td>
                                    @canany(['product-categories-edit', 'product-categories-delete'])
                                        <td>
                                            <div class="buttons_td">
                                                @can('product-categories-edit')
                                                    <button type="button" class="btn btn-bull_yellow edit-cat-btn waves-effect waves-light"
                                                        data-bs-toggle="modal" data-bs-target="#edit_category_modal"
                                                        data-catid="{{ $data->id }}" data-categoryname="{{ $data->category_name }}"
                                                        data-subcatname="{{ $data->sub_category }}" data-type="{{ $data->type }}" data-dispatch="{{ $data->dispatch_time }}"
                                                        data-product-keyword="{{ $data->product_keyword }}" data-keyword-dispatch="{{ $data->keyword_dispatch_time }}">
                                                        <i class="mdi mdi-lead-pencil"></i> Edit
                                                    </button>
                                                @endcan

                                                @can('product-categories-delete')

                                                    <button data-url="/delete-category/{{ $data->id }}" type="button"
                                                        class="common-delete-btn me-3 btn btn-bull_red waves-effect waves-light">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
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
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Add Segment</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/add-category-action">
                            <div class="row mb-4">
                                @csrf

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Segment <span style="color:red">*</span>
                                        </label>

                                        <input type="text" name="category_name" class="form-control" placeholder="Type here..."
                                            required>
                                    </div>
                                </div>



                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Segment Type<span
                                                style="color:red">*</span></label>
                                        <select class="form-select " name="type" required>
                                            <option value="">-- Choose --</option>
                                            <option value="mhe">MHE</option>
                                            <option value="rack">RACK</option>
                                            <option value="pallet">Pallet</option>
                                            <option value="spares">Spares</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Workflow <span
                                                style="color:red">*</span></label>
                                        <select class="form-select " name="sub_category" required>
                                            <option value="">-- Choose Workflow --</option>
                                            <option value="special">Special</option>
                                            <option value="standard">Standard</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Lead Time <span style="color:red">*</span>
                                        </label>

                                        <input type="text" name="dispatch_time" class="form-control" placeholder="Type here..."
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <label class="form-label fw-bold">Product Keywords & Specific Lead Times (Optional)</label>
                                    <div id="add_keyword_rows_container">
                                        <div class="row align-items-end mb-3 keyword-row">
                                            <div class="col-lg-5">
                                                <label class="form-label small text-muted">Product Keyword</label>
                                                <input type="text" name="product_keyword[]" class="form-control" placeholder="e.g. Hydraulic Hand Pallet">
                                            </div>
                                            <div class="col-lg-5">
                                                <label class="form-label small text-muted">Keyword Lead Time</label>
                                                <input type="text" name="keyword_dispatch_time[]" class="form-control" placeholder="e.g. Within a Week">
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-success btn-add-keyword-row w-100 mt-2 mt-lg-0" title="Add Row">
                                                    <i class="mdi mdi-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Add
                                Segment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade modal-lg" id="edit_category_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Edit Segment</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/edit-category-action">
                            <div class="row mb-4">
                                @csrf

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Segment <span style="color:red">*</span>
                                        </label>
                                        <input type="hidden" name="catid" id="catId">
                                        <input type="text" id="edit_category_name" name="edit_category_name"
                                            class="form-control" placeholder="Type here..." required>
                                    </div>
                                </div>



                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Segment Type<span
                                                style="color:red">*</span></label>
                                        <select class="form-select " id="edit_type" name="edit_type" required>
                                            <option value="">-- Choose --</option>
                                            <option value="mhe">MHE</option>
                                            <option value="rack">RACK</option>
                                            <option value="pallet">Pallet</option>
                                            <option value="spares">Spares</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Workflow<span
                                                style="color:red">*</span></label>
                                        <select class="form-select " id="edit_sub_category_name" name="edit_sub_category_name"
                                            required>
                                            <option value="">-- Choose Workflow --</option>
                                            <option value="special">Special</option>
                                            <option value="standard">Standard</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Lead Time <span style="color:red">*</span>
                                        </label>

                                        <input type="text" name="edit_dispatch_time" id="edit_dispatch_time" class="form-control" placeholder="Type here..."
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <label class="form-label fw-bold">Product Keywords & Specific Lead Times (Optional)</label>
                                    <div id="edit_keyword_rows_container">
                                        <!-- Dynamically rendered via JS -->
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Edit
                                Category</button>
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
            // Function to generate keyword row HTML
            function renderKeywordRow(keywordName, keywordVal, dispatchName, dispatchVal, isFirstRow) {
                let buttonHtml = isFirstRow
                    ? `<button type="button" class="btn btn-success btn-add-keyword-row w-100 mt-2 mt-lg-0" title="Add Row"><i class="mdi mdi-plus"></i> Add</button>`
                    : `<button type="button" class="btn btn-danger btn-remove-keyword-row w-100 mt-2 mt-lg-0" title="Remove Row"><i class="mdi mdi-minus"></i> Remove</button>`;

                let safeKeywordVal = $('<div>').text(keywordVal || '').html();
                let safeDispatchVal = $('<div>').text(dispatchVal || '').html();

                return `
                    <div class="row align-items-end mb-3 keyword-row">
                        <div class="col-lg-5">
                            <label class="form-label small text-muted">Product Keyword</label>
                            <input type="text" name="${keywordName}[]" class="form-control" placeholder="e.g. Hydraulic Hand Pallet" value="${safeKeywordVal}">
                        </div>
                        <div class="col-lg-5">
                            <label class="form-label small text-muted">Keyword Lead Time</label>
                            <input type="text" name="${dispatchName}[]" class="form-control" placeholder="e.g. Within a Week" value="${safeDispatchVal}">
                        </div>
                        <div class="col-lg-2">
                            ${buttonHtml}
                        </div>
                    </div>
                `;
            }

            // Add new row in Add Modal
            $(document).on('click', '#add_keyword_rows_container .btn-add-keyword-row', function () {
                $('#add_keyword_rows_container').append(
                    renderKeywordRow('product_keyword', '', 'keyword_dispatch_time', '', false)
                );
            });

            // Add new row in Edit Modal
            $(document).on('click', '#edit_keyword_rows_container .btn-add-keyword-row', function () {
                $('#edit_keyword_rows_container').append(
                    renderKeywordRow('edit_product_keyword', '', 'edit_keyword_dispatch_time', '', false)
                );
            });

            // Remove a keyword row
            $(document).on('click', '.btn-remove-keyword-row', function () {
                $(this).closest('.keyword-row').remove();
            });

            // Edit button click event
            $(document).on("click", ".edit-cat-btn", function () {
                var catId = $(this).data('catid');
                var categoryName = $(this).data('categoryname');
                var subCatName = $(this).data('subcatname');
                var type = $(this).data('type');
                var dispatch = $(this).data('dispatch');
                
                var rawProductKeyword = $(this).attr('data-product-keyword') || '';
                var rawKeywordDispatch = $(this).attr('data-keyword-dispatch') || '';

                $("#catId").val(catId);
                $("#edit_category_name").val(categoryName);
                $("#edit_sub_category_name").val(subCatName);
                $("#edit_type").val(type);
                $("#edit_dispatch_time").val(dispatch);

                let keywords = [];
                let dispatches = [];

                if (rawProductKeyword) {
                    try {
                        let parsed = JSON.parse(rawProductKeyword);
                        keywords = Array.isArray(parsed) ? parsed : [rawProductKeyword];
                    } catch (e) {
                        keywords = [rawProductKeyword];
                    }
                }

                if (rawKeywordDispatch) {
                    try {
                        let parsed = JSON.parse(rawKeywordDispatch);
                        dispatches = Array.isArray(parsed) ? parsed : [rawKeywordDispatch];
                    } catch (e) {
                        dispatches = [rawKeywordDispatch];
                    }
                }

                let $editContainer = $('#edit_keyword_rows_container');
                $editContainer.empty();

                let totalRows = Math.max(keywords.length, dispatches.length, 1);
                for (let i = 0; i < totalRows; i++) {
                    let kw = keywords[i] || '';
                    let disp = dispatches[i] || '';
                    let isFirst = (i === 0);
                    $editContainer.append(
                        renderKeywordRow('edit_product_keyword', kw, 'edit_keyword_dispatch_time', disp, isFirst)
                    );
                }
            });
        });
    </script>

@endsection
