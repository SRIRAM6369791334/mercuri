/* ---------- Multi-keyword search matcher for Select2 ---------- */
function multiKeywordMatcher(params, data) {
    if ($.trim(params.term) === '') {
        return data;
    }
    if (!data.text) {
        return null;
    }
    var searchTerms = params.term.toLowerCase().split(/\s+/).filter(Boolean);
    var text = data.text.toLowerCase();
    var isMatch = searchTerms.every(function (term) {
        return text.indexOf(term) > -1;
    });
    return isMatch ? data : null;
}

window.multiKeywordMatcher = multiKeywordMatcher;

function initStatusModalProductSelect2() {
    var $pprod = $('#pprod');
    if ($pprod.length) {
        if ($pprod.hasClass('select2-hidden-accessible')) {
            $pprod.select2('destroy');
        }
        $pprod.select2({
            placeholder: "Search or Select Products",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#statusModal'),
            matcher: multiKeywordMatcher,
            closeOnSelect: false
        });
    }
}

window.initStatusModalProductSelect2 = initStatusModalProductSelect2;

$('#addvendor_state_list,#addvendor_cat_list,#editvendor_cat_list,#addvendor_city_list,#lead_state_list,#lead_city_list,#assigned_to,#lead_edit_assigned_to,#state_listt,#cus-city').select2({
    placeholder: 'Search or Select',
    allowClear: true,
});

$('#edit_prostock_vendor').select2({
    placeholder: 'Search or Select',
    allowClear: true,
    dropdownParent: $('#edit_product_stock_modal')
});

$('#product_category_select,#product_sub_category_select').select2({
    placeholder: 'Search or Select',
    allowClear: true,
});

function refreshSelect2Dropdown() {
    $('.product_category_po,.products_dropdown_po,.vendor_dropdown_po,.po_number_dropdown,.product_category_pe,.products_dropdown_pe').select2({
        placeholder: 'Search or Select',
        allowClear: true,
    });
}

refreshSelect2Dropdown();

$("#po_add_product_plus").click(function () {
    var productBox = $(".po-form-box").first().clone(); // Clone the first form box

    productBox.find("input").val(""); // Clear input values
    productBox.find("select").val(null).trigger("change"); // Reset Select2 dropdowns

    // Remove old Select2 instance
    productBox.find("select").removeClass("select2-hidden-accessible").next(".select2-container").remove();

    // var poFields = `<div class="row mb-4 po-form-box position-relative">
    //                     <div class="po-remove-btn">
    //                         <i class="fas fa-trash"></i>
    //                     </div>
    //                     <!-- product category drop -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Product Category <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select product_category_po" id="product_category_po" name="catId[]"
    //                                     required>
    //                                     <option value="">-- Choose Category --</option>
    //                                     @if ($status = App\Models\ProductCategory::get())
    //                                         @foreach ($status as $data)
    //                                             <option value="{{ $data->id }}">
    //                                                 {{ $data->category_name }}
    //                                             </option>
    //                                         @endforeach
    //                                     @endif
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>
    //                     <!-- products drop -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Products <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select products_dropdown_po" id="products_dropdown_po" name="proId[]"
    //                                     required>
    //                                     <!-- comes from ajax -->
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>
    //                     <!-- Vendor Name dropdwon -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Vendor <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select vendor_dropdown_po" id="vendor_dropdown_po" name="vendorId[]"
    //                                     required>
    //                                     <!-- comes from ajax -->
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Available Quantity<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="existQty[]" class="form-control po_exist_qty" id="po_exist_qty"
    //                                 required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-3">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Unit Price <span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="unit_price[]" class="form-control po_unit_price" id="po_unit_price"
    //                                 required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-3">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Required Quantity<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="qty[]" class="form-control po_qty" id="po_qty" required>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-3">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">GST<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="gst[]" class="form-control po_gst" id="po_gst" required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-3">
    //                         <div class="row">
    //                             <div class="col-lg-8">
    //                                 <div class="form-group mt-4">
    //                                     <label for="exampleFormControlInput1" class="form-label">Product Total <span
    //                                             style="color:red">*</span></label>
    //                                     <input type="decimal" name="product_total[]" id="po_pro_tot_amt"
    //                                         class="form-control pro_tot_amt po_pro_tot_amt" required readonly>
    //                                     <input type="hidden" name="sub_total[]" class="product_subtotal">
    //                                 </div>
    //                             </div>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Product Description</label>
    //                             <textarea name="pro_des[]" class="form-control pro_des" id="po_pro_des"></textarea>
    //                         </div>
    //                     </div>

    //                 </div>`;

    $(".po-form-box").last().after(productBox);
    // $(".po-form-box").last().after(poFields);

    $('.product_category_po,.products_dropdown_po,.vendor_dropdown_po').select2({
        placeholder: 'Search or Select',
        allowClear: false,
    });

    // Reinitialize Select2 for ALL dropdowns including new and old
    // refreshSelect2Dropdown();

    addValidate($(".po-form .po-form-box").last().find(".product_category_po")[0], required);
    addValidate($(".po-form .po-form-box").last().find(".products_dropdown_po")[0], required);
    addValidate($(".po-form .po-form-box").last().find(".vendor_dropdown_po")[0], required);
    addValidate($(".po-form .po-form-box").last().find(".po_exist_qty")[0], qtyRequired);
    addValidate($(".po-form .po-form-box").last().find(".po_unit_price")[0], priceRequired);
    addValidate($(".po-form .po-form-box").last().find(".po_qty")[0], qtyRequired);
    addValidate($(".po-form .po-form-box").last().find(".po_gst")[0], gstRequired);
    addValidate($(".po-form .po-form-box").last().find(".po_pro_tot_amt")[0], priceRequired);
    addValidate($(".po-form .po-form-box").last().find(".po_pro_des")[0], descRequired);
});

$("#pe_add_product_plus").click(function () {
    var productBox = $(".po-form-box").first().clone(); // Clone the first form box

    productBox.find("input").val(""); // Clear input values
    productBox.find("select").val(null).trigger("change"); // Reset Select2 dropdowns

    // Remove old Select2 instance
    productBox.find("select").removeClass("select2-hidden-accessible").next(".select2-container").remove();

    // var poFields = `<div class="row mb-4 po-form-box position-relative">
    //                     <div class="po-remove-btn">
    //                         <i class="fas fa-trash"></i>
    //                     </div>
    //                     <!-- po invoices -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         PO Number <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <!-- po id field -->
    //                             <input type="hidden" name="po_id[]" class="multi_po_id">
    //                             <div class="col-lg-12">
    //                                 <select class="form-select po_number_dropdown" id="po_number_dropdown" name="po_number[]"
    //                                     required>
    //                                     <option value="">-- Choose Invoice --</option>
    //                                     @if ($status = App\Models\PurchaseOrder::get()->unique('po_no'))
    //                                         @foreach ($status as $data)
    //                                             <option value="{{ $data->po_no }}" data-poid="{{ $data->id }}">
    //                                                 {{ $data->po_no }}
    //                                             </option>
    //                                         @endforeach
    //                                     @endif
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>

    //                     <!-- product category drop -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Product Category <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select product_category_pe" id="product_category_pe" name="catId[]"
    //                                     required>
    //                                     <option value="">-- Choose Category --</option>

    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>
    //                     <!-- products drop -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Products <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select products_dropdown_pe" name="proId[]" id="products_dropdown_pe"
    //                                     required>
    //                                     <!-- comes from ajax -->
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>
    //                     <!-- Vendor Name dropdwon -->
    //                     <div class="col-lg-6">
    //                         <div class="form-group mt-4">
    //                             <div class="row">
    //                                 <div class="col-lg-12">
    //                                     <label for="customerSelect" class="form-label">
    //                                         Vendor <span style="color:red">*</span>
    //                                     </label>
    //                                 </div>
    //                             </div>
    //                             <div class="col-lg-12">
    //                                 <select class="form-select vendor_dropdown_po vendor_dropdown_pe" id="vendor_dropdown_pe" name="vendorId[]" required>
    //                                     <!-- comes from ajax -->
    //                                 </select>
    //                             </div>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Requested Quantity<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="pe_req_qty[]" class="form-control pe_req_qty" id="pe_req_qty" required
    //                                 readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Received Quantity<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="pe_received_qty[]" class="form-control pe_received_qty bg-white"
    //                                 id="pe_received_qty" required>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Pending Quantity<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="pe_pending_qty[]" class="form-control pe_pending_qty"
    //                                 id="pe_pending_qty" required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Unit Price<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="unit_price[]" class="form-control po_unit_price pe_unit_price" id="pe_unit_price"
    //                                 required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">GST<span
    //                                     style="color:red">*</span></label>
    //                             <input type="text" name="gst[]" class="form-control po_gst pe_gst" id="pe_gst" required readonly>
    //                         </div>
    //                     </div>

    //                     <div class="col-lg-2">
    //                         <div class="form-group mt-4">
    //                             <label for="exampleFormControlInput1" class="form-label">Received Total(₹)<span
    //                                     style="color:red">*</span></label>
    //                             <input type="decimal" name="product_total[]" id="pe_pro_tot_amt"
    //                                 class="form-control pro_received_amt pe_pro_tot_amt" required readonly>
    //                             <input type="hidden" name="sub_total[]" class="product_subtotal">
    //                         </div>
    //                     </div>

    //                 </div>`;

    $(".po-form-box").last().after(productBox); // Append after the last form box

    // Reinitialize Select2 for ALL dropdowns including new and old
    refreshSelect2Dropdown();

    addValidate($(".pe-form .po-form-box").last().find(".po_number_dropdown")[0], required);
    addValidate($(".pe-form .po-form-box").last().find(".product_category_pe")[0], required);
    addValidate($(".pe-form .po-form-box").last().find(".products_dropdown_pe")[0], required);
    addValidate($(".pe-form .po-form-box").last().find(".vendor_dropdown_pe")[0], required);
    addValidate($(".pe-form .po-form-box").last().find(".pe_req_qty")[0], qtyRequired);
    addValidate($(".pe-form .po-form-box").last().find(".pe_received_qty")[0], priceRequired);
    addValidate($(".pe-form .po-form-box").last().find(".pe_pending_qty")[0], qtyRequired);
    addValidate($(".pe-form .po-form-box").last().find(".pe_unit_price")[0], qtyRequired);
    addValidate($(".pe-form .po-form-box").last().find(".pe_gst")[0], gstRequired);
    addValidate($(".pe-form .po-form-box").last().find(".pe_pro_tot_amt")[0], priceRequired);
});