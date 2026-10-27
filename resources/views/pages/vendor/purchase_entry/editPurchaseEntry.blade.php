@extends('layouts.app')
@section('title', 'Add Vendor Details')
@section('main-content')

@can('purchase-entry-edit')


    <div class="container">
        <h4 class="mt-4 mb-5 text-center">Edit Purchase Entry</h4>
    </div>
    <section class="section leadsection">

        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">

                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2 mt-3">
                            <a onclick="return window.history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="/edit-pe" id="edit-pe-form">
                @csrf
                
                <div class="row mb-4 po-form-box position-relative">
                    <!-- po invoices -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="customerSelect" class="form-label">
                                        PO Number <span style="color:red">*</span>
                                    </label>
                                </div>
                            </div>
                            <!-- po id field -->
                            <input type="hidden" name="pe_id[]" class="multi_pe_id" value="{{ $peDetails->id }}">
                            <input type="hidden" name="po_id[]" class="multi_po_id" value="{{ $peDetails->po_id }}">
                            <div class="col-lg-12">
                                <select class="form-select po_number_dropdown" id="po_number_dropdown" name="po_number[]"
                                    required>
                                    <option value="">-- Choose Invoice --</option>
                                    @if ($status = App\Models\PurchaseOrder::get()->unique('po_no'))
                                        @foreach ($status as $data)
                                            <option value="{{ $data->po_no }}" data-poid="{{ $data->id }}"
                                                @if($peDetails->po_no == $data->po_no) selected @endif>
                                                {{ $data->po_no }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- product category drop -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="customerSelect" class="form-label">
                                        Product Category <span style="color:red">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select product_category_pe" id="product_category_pe" name="catId[]"
                                    required>
                                    <option value="">-- Choose Category --</option>
                                    @if ($status = App\Models\ProductCategory::get())
                                        @foreach ($status as $data)
                                            <option value="{{ $data->id }}" @if($data->id == $peDetails->cat_id) selected @endif>
                                                {{ $data->category_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- products drop -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="customerSelect" class="form-label">
                                        Products <span style="color:red">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <input type="hidden" name="old_product_id[]" value="{{ $peDetails->product_id }}">
                                <select class="form-select products_dropdown_pe" name="proId[]" required>
                                    <!-- comes from ajax -->
                                    @if ($status = App\Models\Product::where('Productcategory', $peDetails->cat_id)->get())
                                        @foreach ($status as $data)
                                            <option value="{{ $data->id }}" @if($data->id == $peDetails->product_id) selected @endif>
                                                {{ $data->productname }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Vendor Name dropdwon -->
                    <div class="col-lg-6">
                        <div class="form-group mt-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="customerSelect" class="form-label">
                                        Vendor <span style="color:red">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <select class="form-select vendor_dropdown_po vendor_dropdown_pe" name="vendorId[]" required>
                                @if ($status = App\Models\Vendor::get())
                                        @foreach ($status as $data)
                                            <option value="{{ $data->id }}" @if($data->id == $peDetails->vendor_id) selected @endif >
                                                {{ $data->company_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Requested Quantity<span
                                    style="color:red">*</span></label>
                            <input type="text" name="pe_req_qty[]" class="form-control pe_req_qty" id="pe_req_qty" required readonly value="{{ $peDetails->requested_qty }}">
                        </div>
                    </div>

                    <input type="hidden" name="old_product_qty[]" value="{{ $peDetails->received_qty }}">

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Received Quantity<span
                                    style="color:red">*</span></label>
                            <input type="text" name="pe_received_qty[]" class="form-control pe_received_qty"
                                id="pe_received_qty" required value="{{ $peDetails->received_qty }}">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Pending Quantity<span
                                    style="color:red">*</span></label>
                            <input type="text" name="pe_pending_qty[]" class="form-control pe_pending_qty"
                                id="pe_pending_qty" required readonly value="{{ $peDetails->pending_qty }}">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Unit Price<span
                                    style="color:red">*</span></label>
                            <input type="text" name="unit_price[]" class="form-control po_unit_price pe_unit_price" id="po_unit_price"
                                required readonly value="{{ $peDetails->unit_price }}">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">GST(%) <span
                                    style="color:red">*</span></label>
                            <input type="text" name="gst[]" class="form-control po_gst pe_gst" id="po_gst" required readonly value="{{ (int)$peDetails->productDetails->gst }}">
                        </div>
                    </div>

                    @php
                        $subTotal = (int)$peDetails->received_qty * (int)$peDetails->unit_price;
                        $gstAmount = ($subTotal / 100) * (int)$peDetails->productDetails->gst;
                        $receivedTotal = $gstAmount + $subTotal;
                    @endphp

                    <div class="col-lg-2">
                        <div class="form-group mt-4">
                            <label for="exampleFormControlInput1" class="form-label">Received Total(₹) <span
                                    style="color:red">*</span></label>
                            <input type="decimal" name="product_total[]" id="pro_tot_amt" class="form-control pro_received_amt pe_pro_tot_amt"
                                required readonly value="{{ $receivedTotal }}">
                            <input type="hidden" name="sub_total[]" class="product_subtotal" value="{{ $subTotal }}">
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary m-auto d-block mt-5 mb-3 form-submit-btn">Update Changes</button>
            </form>

        </div>
    </section>

@endcan
@endsection

@section('scripts')

    <script>

        const add_pe_form = new window.JustValidate('#edit-pe-form');

        var required = [
            {
                rule: 'required',
            },
        ];

        var qtyRequired = [
            {
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 1,
            },
            {
                rule: 'maxLength',
                value: 5,
            },
        ];

        var receivedQtyRequired = [
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
                    var reqQty = $(fields['.pe_req_qty'].elem).closest(".po-form-box").find(".pe_req_qty").val();
                    if (parseInt(reqQty) < parseInt(value)) {
                        return false;
                    } else {
                        return true;
                    }
                },
                errorMessage: "Should be less than requested quantity",
            },
        ];

        var priceRequired = [
            {
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 1,
            },
            {
                rule: 'maxLength',
                value: 15,
            },
        ];

        var gstRequired = [
            {
                rule: 'required',
            },
            {
                rule: 'minLength',
                value: 1,
            },
            {
                rule: 'maxLength',
                value: 2,
            },
        ];

        var descRequired = [
            {
                rule: 'minLength',
                value: 5,
            },
            {
                rule: 'maxLength',
                value: 50,
            },
        ];

        add_pe_form
            .addField('.po_number_dropdown', required)
            .addField('.product_category_pe', required)
            .addField('.products_dropdown_pe', required)
            .addField('.vendor_dropdown_pe', required)
            .addField('.pe_req_qty', qtyRequired)
            .addField('.pe_received_qty', receivedQtyRequired)
            .addField('.pe_pending_qty', qtyRequired)
            .addField('.pe_unit_price', priceRequired)
            .addField('.pe_gst', gstRequired)
            .addField('.pe_pro_tot_amt', priceRequired)
        add_pe_form.onSuccess((event) => {
            document.getElementById("edit-pe-form").submit();
        });

        function addValidate(el, rule) {
            console.log(el);
            add_pe_form.addField(el, rule)
        }

    </script>

@endsection