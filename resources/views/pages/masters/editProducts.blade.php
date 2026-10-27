@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    <div class="container">
        <h4 class="mt-3 mb-4 text-center">Edit Product</h4>
    </div>

    <section class="section leadsection">
        <div class="row justify-content-end">
            <div class="col-lg-2">
                <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
            </div>
        </div>
        <div class="container form-card">
            <form class="myForm" method="POST" action="{{ route('products.update') }}">
                <div class="row mb-4 mt-4">
                    @csrf
                    <input type="hidden" value="{{ $productData->id }}" name="productId">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Product Segment <span
                                            style="color:red">*</span></label>
                                    <select id="product_category_select" class="form-select " name="Productcategory"
                                        required>
                                        <option value="">-- Choose Segment --</option>
                                        @if ($status = App\Models\ProductCategory::get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}" @if ($data->id == $productData->Productcategory)
                                                selected @endif>
                                                    {{ $data->category_name }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Product Group <span
                                            style="color:red">*</span></label>
                                    <select id="product_sub_category_select" class="form-select "
                                        name="product_sub_category_id" required>
                                        <option value="">-- Choose Group --</option>
                                        @if ($status = App\Models\ProductSubCategory::get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}" data-segment-id="{{ $data->cat_id }}" @if ($data->id == $productData->sub_cat_id) selected
                                                @endif>
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
                                        value="{{ $productData->productname }}" required> --}}
                                         <textarea name="productname" class="form-control"
                                        placeholder="Product Name" required>{{ $productData->productname }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Product Model <span class="mandatory">*</span></label>
                                    <input type="text" class="form-control" name="productmodel" placeholder="Product Model"
                                        value="{{ $productData->productmodel }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Quantity<span class="mandatory">*</span></label>
                                    <input type="text" class="form-control " name="quantity" placeholder="Type here.."
                                        value="{{ $productData->stock->available_stock }}" required readonly>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Part NO <span class="mandatory">*</span></label>
                                    <input type="text" class="form-control " name="partno" placeholder="Part no"
                                        value="{{ $productData->partno }}" required>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">HSN <span class="mandatory">*</span></label>
                                    <input type="text" class="form-control" name="hsn" placeholder="HSN"
                                        value="{{ $productData->hsn }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">GST <span class="mandatory">*</span></label>
                                    <select name="gst" class="form-control" required>
                                        <option value="" disabled {{ $productData->gst == '' ? 'selected' : '' }}>Select
                                            GST...
                                        </option>
                                        <option value="00.00" {{ $productData->gst == '00.00' ? 'selected' : '' }}>0%</option>
                                        <option value="05.00" {{ $productData->gst == '05.00' ? 'selected' : '' }}>5%</option>
                                        <option value="12.00" {{ $productData->gst == '12.00' ? 'selected' : '' }}>12%
                                        </option>
                                        <option value="18.00" {{ $productData->gst == '18.00' ? 'selected' : '' }}>18%
                                        </option>
                                        <option value="28.00" {{ $productData->gst == '28.00' ? 'selected' : '' }}>28%
                                        </option>
                                    </select>
                                </div>

                            </div>
 <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Rate <span class="mandatory">*</span></label>
                                    <input type="text" class="form-control " name="rate" placeholder="Rate"
                                        value="{{ $productData->rate }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">Lead Time</label>
                                    <input type="text" class="form-control" name="dispatch_time" placeholder="e.g. Within a Week"
                                        value="{{ $productData->dispatch_time }}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Product Description</label>
                                    <textarea name="productdes" class="form-control"
                                        placeholder="Product Description">{{ $productData->productdes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <p class="my-4"><strong>Note: </strong>If you want to edit only quantity, kindly update in <a
                        href="/product-stocks">product stock</a> page.</p>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mb-3">Update</button>
                </div>
            </form>

        </div>
    </section>

    <script>
        function loadEditProductGroups(segmentId, selectedGroupId = '') {
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

        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('product_category_select');
            const currentGroupId = "{{ $productData->sub_cat_id }}";

            loadEditProductGroups(categorySelect.value, currentGroupId);

            categorySelect.addEventListener('change', function () {
                loadEditProductGroups(this.value);
            });
        });
    </script>

@endsection
