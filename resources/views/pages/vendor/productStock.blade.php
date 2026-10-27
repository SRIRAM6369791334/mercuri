@extends('layouts.app')
@section('title', 'Product Stock Details')
@section('main-content')

@can("product-stock-display")

    <section class="section leadsection container">
        <div class=" form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Product Stock Details</h5>
                        </div>
                        @php
                            $settingsDetails = App\Models\AdminSetting::first();
                        @endphp

                        @can('stock-alert-edit-option')
                            <div class="col-lg-6">
                                <div class="stock-alert-count">
                                <p class="m-0">Product Stock Alert Count: <strong>{{ $settingsDetails->product_stock_alert_count ?? "0" }}</strong> </p>
                                <i class="fas fa-edit" data-bs-toggle="modal" data-bs-target="#product_stock_alert_count_modal"></i>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $products = App\Models\Product::with(['category',"stock"])->leftJoin('products_stock_tb', 'products.id', '=', 'products_stock_tb.product_id')
        ->orderByRaw('CASE WHEN products_stock_tb.available_stock <= ? THEN 0 ELSE 1 END', [$settingsDetails->product_stock_alert_count ?? 0])
        ->select('products.*')
        ->get();
        $i = 1;
    @endphp

    <div class="container-fluid mt-5">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-6">
                        Product Stock Details
                    </div>
                    <div class="col-lg-6">
                        <div class="low-stock-info">
                            <div class="info-color"></div>
                            <p>Low Stocks</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="product-stock-table" class="display">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Product Category</th>
                            <th>Products</th>
                            <th>Available Stock</th>
                            <th>In Detail</th>
                            @can("product-stock-edit")
                            <th>Action</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($products as $data)
                            @if ($settingsDetails)
                                @php
                                    $category = $data->category;
                                    $stock = $data->stock;
                                @endphp
                                <tr style="text-align: center;">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $data->productname }}</td>
                                    <td style="@if($stock && $stock->available_stock <= $settingsDetails->product_stock_alert_count) background:  #dc35457d;@endif">{{ $stock ? $stock->available_stock : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-info me-2 product_stock_detail_btn" data-bs-toggle="modal" data-proid="{{ $data->id }}"
                                            data-bs-target="#product_stock_detail_modal">View</button>
                                    </td>
                                    @can("product-stock-edit")
                                    <td>
                                        <button class="btn btn-info me-2 pro_stock_edit_btn" data-proid="{{ $data->id }}" data-catid="{{ $category->id ?? '' }}" data-proavailqty="{{ $stock ? $stock->available_stock : 'N/A' }}"
                                        data-productname="{{ $data->productname }}" data-categoryname="{{ $category->category_name ?? 'N/A' }}" data-bs-toggle="modal" data-bs-target="#edit_product_stock_modal">Edit</button>
                                    </td>
                                    @endcan
                                </tr>
                            @endif
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="product_stock_alert_count_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add_po_modalLabel">Product Stock Alert Count</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/product-stock-alert-update">
                        <div class="row mb-4">
                            @csrf
                            <div class="col-lg-12">
                                <div class="form-group mt-2">
                                    <label for="exampleFormControlInput1" class="form-label">Stock Alert Count<span
                                            style="color:red">*</span></label>
                                    <input type="number" name="pro_stock_alert_count" class="form-control pro_stock_alert_count" required>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-lg" id="edit_product_stock_modal" aria-hidden="true" aria-labelledby="add_po_modalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add_po_modalLabel">Edit Product Stock</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/product-stock-edit" id="edit-product-stock">
                        @csrf
                        <div class="row mb-4">
                            <input type="hidden" name="edit_prostock_category" class="prostock_category_id">
                            <input type="hidden" name="edit_prostock_product" class="prostock_product_id">
                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="customerSelect" class="form-label">
                                        Category <span style="color:red">*</span>
                                    </label>
                                    
                                    <input type="text" id="edit_prostock_category" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Products<span
                                            style="color:red">*</span></label>

                                    <input type="text" id="edit_prostock_product" class="form-control" required>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Existing Available Quantity<span
                                            style="color:red">*</span></label>
                                    <input type="number" name="pro_stock_existing_avail_qty" class="form-control pro_stock_existing_avail_qty" required readonly>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Type<span
                                            style="color:red">*</span></label>
                                    <select class="form-select pro_stock_type" id="pro_stock_type" name="pro_stock_type" required>
                                        <option value="">-- Choose Type --</option>
                                        <option value="purchase">Purchase</option>
                                        <option value="sale">Sale</option>
                                        <option value="adjustment">Adjustment</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Vendors<span
                                            style="color:red">*</span></label>

                                    <select id="edit_prostock_vendor" class="form-select edit_prostock_vendor" name="edit_prostock_vendor">
                                        <option value="">-- Choose Vendor --</option>
                                    </select>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Quantity<span
                                            style="color:red">*</span></label>
                                    <input type="number" name="pro_stock_qty" class="form-control pro_stock_qty" required min="0" oninput="preventNegativeNumber.call(this)">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Updated Available Quantity<span
                                            style="color:red">*</span></label>
                                    <input type="number" name="pro_stock_updated_qty" class="form-control pro_stock_updated_qty" required readonly>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-4">
                                    <label for="exampleFormControlInput1" class="form-label">Remarks</label>
                                    <textarea name="pro_stock_remarks" id="pro_stock_remarks" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Edit
                            Stock</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-lg" id="product_stock_detail_modal" aria-hidden="true"
        aria-labelledby="product_stock_detail_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="product_stock_detail_modal">Product Stock Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="product-stock-details-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>Quantity</th>
                                <th>Type</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endcan

@endsection

@section("scripts")
<script>
    const edit_product_stock = new JustValidate('#edit-product-stock');

    edit_product_stock
        .addField('#edit_prostock_category', [
            {
                rule: 'required',
            },
        ])
        .addField('#edit_prostock_product', [
            {
                rule: 'required',
            },
        ])
        .addField('.pro_stock_updated_qty', [
            {
                rule: 'required',
            },
        ])
        .addField('.pro_stock_existing_avail_qty', [
            {
                rule: 'required',
            },
            {
                rule: 'number',
            },
            {
                rule: 'minLength',
                value: 1,
            },
            {
                rule: 'maxLength',
                value: 5,
            },
        ])
        .addField('.pro_stock_qty', [
            {
                rule: 'required',
            },
            {
                rule: 'number',
            },
            {
                rule: 'minLength',
                value: 1,
            },
            {
                rule: 'maxLength',
                value: 5,
            },
            {
                validator: (value, fields) => {
                    var stockType = $(fields['#pro_stock_type'].elem).closest(".row").find("#pro_stock_type").val();
                    var existQty = $(fields['.pro_stock_existing_avail_qty'].elem).closest(".row").find(".pro_stock_existing_avail_qty").val();
                    if (stockType == "sale" || stockType == "adjustment") {
                        if(parseInt(existQty) < parseInt(value)){
                            return false;
                        }else{
                            return true;
                        }
                        
                    } else {
                        return true;
                    }
                },
                errorMessage: "Should be less than existing quantity",
            },
        ])
        .addField('#pro_stock_type', [
            {
                rule: 'required',
            }
        ])
        .addField('#pro_stock_remarks', [
            {
                rule: 'minLength',
                value: 5,
            },
            {
                rule: 'maxLength',
                value: 50,
            },
        ])
        .onSuccess((event) => {
            document.getElementById("edit-product-stock").submit();
        });
</script>
@endsection
