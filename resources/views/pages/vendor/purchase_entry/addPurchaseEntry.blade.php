@extends('layouts.app')
@section('title', 'Add Vendor Details')
@section('main-content')

    @can('purchase-entry-create')

        <div class="container">
            <h4 class="mt-4 mb-5 text-center">Add Purchase Entry</h4>
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
                <form method="POST" action="/save-pe" id="add-pe-form" class="pe-form">
                    @csrf
                    <div class="row mb-4 po-form-box position-relative">
                        <div class="po-remove-btn">
                            <i class="fas fa-trash"></i>
                        </div>
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
                                <input type="hidden" name="po_id[]" class="multi_po_id">
                                <div class="col-lg-12">
                                    <select class="form-select po_number_dropdown" id="po_number_dropdown" name="po_number[]"
                                        required>
                                        <option value="">-- Choose Invoice --</option>
                                        @if ($status = App\Models\PurchaseOrder::get()->unique('po_no'))
                                            @foreach ($status as $data)
                                                <option value="{{ $data->po_no }}" data-poid="{{ $data->id }}">
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
                                    <select class="form-select products_dropdown_pe" name="proId[]" id="products_dropdown_pe"
                                        required>
                                        <!-- comes from ajax -->
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
                                    <select class="form-select vendor_dropdown_po vendor_dropdown_pe" id="vendor_dropdown_pe"
                                        name="vendorId[]" required>
                                        <!-- comes from ajax -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">Requested Quantity<span
                                        style="color:red">*</span></label>
                                <input type="text" name="pe_req_qty[]" class="form-control pe_req_qty" id="pe_req_qty" required
                                    readonly>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">Received Quantity<span
                                        style="color:red">*</span></label>
                                <input type="text" name="pe_received_qty[]" class="form-control pe_received_qty bg-white"
                                    id="pe_received_qty" required>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">Pending Quantity<span
                                        style="color:red">*</span></label>
                                <input type="text" name="pe_pending_qty[]" class="form-control pe_pending_qty"
                                    id="pe_pending_qty" required readonly>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">Unit Price<span
                                        style="color:red">*</span></label>
                                <input type="text" name="unit_price[]" class="form-control po_unit_price pe_unit_price"
                                    id="pe_unit_price" required readonly>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">GST(%)<span
                                        style="color:red">*</span></label>
                                <input type="text" name="gst[]" class="form-control po_gst pe_gst" id="pe_gst" required
                                    readonly>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group mt-4">
                                <label for="exampleFormControlInput1" class="form-label">Received Total(₹)<span
                                        style="color:red">*</span></label>
                                <input type="decimal" name="product_total[]" id="pe_pro_tot_amt"
                                    class="form-control pro_received_amt pe_pro_tot_amt" required readonly>
                                <input type="hidden" name="sub_total[]" class="product_subtotal">
                            </div>
                        </div>

                    </div>

                    <div class="multi-field-btn">
                        <button type="button" class="btn btn-warning my-4" id="pe_add_product_plus">Add Another
                            Product</button>
                    </div>

                    <button type="submit" class="btn btn-primary m-auto d-block mt-4 mb-3 form-submit-btn">Create</button>
                </form>

            </div>
        </section>
    @endcan
@endsection

@section('scripts')

    <script>

        const add_pe_form = new window.JustValidate('#add-pe-form');

        const nameRegex = /^[A-Z a-z]+$/;

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
            document.getElementById("add-pe-form").submit();
        });

        function addValidate(el, rule) {
            console.log(el);
            add_pe_form.addField(el, rule)
        }

    </script>

@endsection