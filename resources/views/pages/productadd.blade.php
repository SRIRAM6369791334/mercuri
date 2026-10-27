@extends('layouts.app')
@section('title', 'Add Product')
@section('main-content')

    <div class="container">
        <h4 class="mt-3 mb-5 text-center">Add Product</h4>
    </div>

    <section class="section leadsection">
        <div class="row justify-content-end">
            <div class="col-lg-2">
                <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
            </div>
        </div>
        <div class="container form-card">
            <form class="myForm" method="POST" action="{{ route('products.store') }}">
                <div class="row mb-4 mt-4">
                    @csrf

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group mb-2">
                                    <label for="exampleFormControlInput1" class="form-label">Product Segment <span
                                            style="color:red">*</span><button type="button"
        class="btn btn-sm btn-outline-primary ms-2"
        data-bs-toggle="modal"
        data-bs-target="#add_category_modal">
        + Add Segment
    </button></label>
                                    <select id="product_category_select" class="form-select " name="Productcategory"
                                        required>
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
                            <div class="col-lg-12">
                                <div class="form-group mt-2">
                                    <label for="exampleFormControlInput1" class="form-label">Product Group <span
                                            style="color:red">*</span><button type="button"
        class="btn btn-sm btn-outline-primary ms-2"
        data-bs-toggle="modal"
        data-bs-target="#add_subcategory_modal">
        + Add Group
    </button><a href="{{ url('/product-sub-categories') }}" class="btn btn-sm btn-outline-danger ms-2">Delete Group</a></label>
                                    <select id="product_sub_category_select" class="form-select "
                                        name="product_sub_category_id" required>
                                        <option value="">-- Choose Group --</option>
                                        @if ($status = App\Models\ProductSubCategory::get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}" data-segment-id="{{ $data->cat_id }}">
                                                    {{ $data->subcategory_name }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Product Name <span class="mandatory">*</span></label>
                                    {{-- <input type="text" class="form-control" name="productname" placeholder="Product Name"
                                        required> --}}
                                         <textarea name="productname" class="form-control"
                                        placeholder="Product Name" required></textarea>
                                </div>
                            </div>
                             <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Product Model <span class="mandatory">*</span></label>
                                    <input type="text" class="form-control" name="productmodel" placeholder="Product Model"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Quantity<span class="mandatory">*</span></label>
                                    <input type="number" class="form-control " name="quantity" placeholder="Type here.."
                                        required>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">Part NO<span class="mandatory">*</span></label>
                                    <input type="number" class="form-control " name="partno" placeholder="Part no" required>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">HSN <span class="mandatory">*</span></label>
                                    <input type="number" class="form-control" name="hsn" placeholder="HSN" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">GST <span class="mandatory">*</span></label>
                                    <select name="gst" class="form-control" required>
                                        <option value="" disabled selected>Select GST...</option>
                                        <option value="00.00">0%</option>
                                        <option value="5.00">5%</option>
                                        <option value="12.00">12%</option>
                                        <option value="18.00">18%</option>
                                        <option value="28.00">28%</option>
                                    </select>
                                </div>
                            </div>
 <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Rate <span class="mandatory">*</span></label>
                                    <input type="number" class="form-control " name="rate" placeholder="Rate" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Lead Time</label>
                                    <input type="text" class="form-control" name="dispatch_time" placeholder="e.g. Within a Week">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Product Description</label>
                                    <textarea name="productdes" class="form-control"
                                        placeholder="Product Description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mb-3">Add product</button>
                </div>
            </form>
        </div>
    </section>
    <div class="modal fade modal-lg" id="add_category_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Add Segment</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addSegmentForm">
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
                                        <label for="exampleFormControlInput1" class="form-label">Workflow <span style="color:red">*</span></label>
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
                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Add
                                Segment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-lg" id="add_subcategory_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_po_modalLabel">Add Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addSubCategoryForm">
                            <div class="row mb-4">
                                @csrf

                                <div class="col-lg-6">
                                    <div class="form-group mt-4">
                                        <label for="exampleFormControlInput1" class="form-label">Product Segment <span
                                                style="color:red">*</span></label>
                                        <select class="form-select" name="product_segment_id" required>
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

                                        <input type="text" name="sub_category" class="form-control" placeholder="Type here..."
                                            required>
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

@endsection
@section('scripts')
<script>
$('#addSegmentForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();

    $.ajax({
        url: "{{ route('ajax.segment.store') }}",
        type: "POST",
        data: formData,
        success: function(res) {
    if (res.status) {

        let option = `<option value="${res.data.id}" selected>
            ${res.data.category_name}
        </option>`;

        // 1️⃣ Main Product Segment dropdown
        $('#product_category_select').append(option);

        // 2️⃣ Segment dropdown inside Add Group modal
        $('select[name="product_segment_id"]').append(option);

        // Reset form
        $('#addSegmentForm')[0].reset();

        // Close modal
        $('#add_category_modal').modal('hide');
    }
},error: function(xhr) {
            alert('Something went wrong');
        }
    });
});
</script>
<script>
$('#addSubCategoryForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('ajax.subcategory.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res) {
            if (res.status) {

                // Append new option
                $('#product_sub_category_select').append(
                    `<option value="${res.data.id}" selected>
                        ${res.data.subcategory_name}
                    </option>`
                );

                // Reset form
                $('#addSubCategoryForm')[0].reset();

                // Close modal
                $('#add_subcategory_modal').modal('hide');
            }
        },
        error: function(xhr) {
            alert('Something went wrong');
        }
    });
});
</script>

<script>
function loadProductGroups(segmentId, selectedGroupId = '') {
    const $groupSelect = $('#product_sub_category_select');

    $groupSelect.html('<option value="">-- Choose Group --</option>');

    if (!segmentId) {
        return;
    }

    $.get(`/get-sub-category/${segmentId}`, function (res) {
        (res.subcategory_details || []).forEach(function (item) {
            const isSelected = String(selectedGroupId) === String(item.id) ? 'selected' : '';
            $groupSelect.append(`<option value="${item.id}" ${isSelected}>${item.subcategory_name}</option>`);
        });
    });
}

$('#product_category_select').on('change', function () {
    loadProductGroups($(this).val());
});

$('#add_subcategory_modal').on('show.bs.modal', function () {
    $(this).find('select[name="product_segment_id"]').val($('#product_category_select').val());
});

$('#addSegmentForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('ajax.segment.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function (res) {
            if (!res.status) {
                return;
            }

            const option = `<option value="${res.data.id}" selected>${res.data.category_name}</option>`;

            $('#product_category_select').append(option).val(res.data.id);
            $('#add_subcategory_modal select[name="product_segment_id"]').append(option).val(res.data.id);
            loadProductGroups(res.data.id);

            $('#addSegmentForm')[0].reset();
            $('#add_category_modal').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});

$('#addSubCategoryForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('ajax.subcategory.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function (res) {
            if (!res.status) {
                return;
            }

            const newOption = `<option value="${res.data.id}" data-segment-id="${res.data.cat_id}" selected>${res.data.subcategory_name}</option>`;
            $('#product_sub_category_select').append(newOption).val(res.data.id);

            $('#addSubCategoryForm')[0].reset();
            $('#add_subcategory_modal').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});
</script>


@endsection