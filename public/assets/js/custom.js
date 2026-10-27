// dark theme script
// function addDarkmodeWidget() {
//     new Darkmode().showWidget();
// }
// window.addEventListener('load', addDarkmodeWidget);

// const options = {
//   bottom: '64px', // default: '32px'
//   right: 'unset', // default: '32px'
//   left: '32px', // default: 'unset'
//   time: '0.5s', // default: '0.3s'
//   mixColor: '#fff', // default: '#fff'
//   backgroundColor: '#fff',  // default: '#fff'
//   buttonColorDark: '#fff',  // default: '#100f2c'
//   buttonColorLight: '#fff', // default: '#fff'
//   saveInCookies: false, // default: true,
//   label: '🌓', // default: ''
//   autoMatchOsTheme: true // default: true
// }

// const darkmode = new Darkmode(options);
// darkmode.showWidget();

// ajax setup
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).on("change", ".state_list", function () {
    fetchCitiesVendor($(this).val());
});

function fetchCitiesVendor(stateId) {
    if (stateId) {
        $.ajax({
            url: "/get-cities/" + stateId,
            method: "GET",
            success: function (response) {
                $("#addvendor_city_list")
                    .empty()
                    .append('<option value=""> -- Choose City -- </option>');
                $("#edit_vendor_city_list")
                    .empty()
                    .append('<option value=""> -- Choose City -- </option>');

                $.each(response, function (index, city) {
                    $("#addvendor_city_list").append(
                        `<option value="${city.id}">${city.city_name}</option>`
                    );
                    $(".edit_vendor_city_list").append(
                        `<option value="${city.id}">${city.city_name}</option>`
                    );
                });
            },
            error: function () {
                alert("Unable to fetch cities.");
            },
        });
    }
}

$(document).on("click", ".po-remove-btn", function () {
    $(this).closest(".po-form-box").remove();
    const validation = new JustValidate(".add-po-form");
    validation.refresh();
});

// ajax functions
// po add and edit pages scripts

$(document).on(
    "change",
    ".product_category_po,.product_category_po_edit",
    function () {
        let catId = $(this).val();
        let $this = $(this);
        if (catId == "") {
            $this
                .closest(".po-form-box")
                .find(".products_dropdown_po")
                .val(null)
                .trigger("change");
            $this
                .closest(".po-form-box")
                .find(".vendor_dropdown_po")
                .val(null)
                .trigger("change");
            $(this).closest(".po-form-box").find(".po_qty").val("");
            $(this).closest(".po-form-box").find(".po_exist_qty").val("");
            $(this).closest(".po-form-box").find(".po_unit_price").val("");
            $(this).closest(".po-form-box").find(".po_gst").val("");
            $(this).closest(".po-form-box").find(".product_subtotal").val("");
            $(this).closest(".po-form-box").find(".pro_tot_amt").val("");
            return;
        }
        $.ajax({
            url: "/get-products/" + catId,
            method: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data) {
                    let $productDropdown = $this
                        .closest(".po-form-box")
                        .find(".products_dropdown_po");

                    $productDropdown.empty();
                    $productDropdown.append(
                        '<option value="">-- Choose Product --</option>'
                    );

                    let $vendorDropdown = $this
                        .closest(".po-form-box")
                        .find(".vendor_dropdown_po");
                    $vendorDropdown.empty();
                    $vendorDropdown.append(
                        '<option value="">-- Choose Vendor --</option>'
                    );

                    // append products data
                    if (data.productData.length > 0) {
                        data.productData.forEach(function (item) {
                            let option = `<option value="${item.id}" data-existquantity="${item.stock.available_stock}" data-rate="${item.rate}" data-hsn="${item.hsn}" data-gst="${item.gst}">
                            ${item.productname}
                        </option>`;
                            $productDropdown.append(option);
                        });
                    } else {
                        $productDropdown.append(
                            '<option value="">No products available</option>'
                        );
                    }

                    // append vendor data
                    if (data.vendors.length > 0) {
                        data.vendors.forEach(function (item) {
                            let option = `<option value="${item.id}">
                            ${item.company_name}
                        </option>`;
                            $vendorDropdown.append(option);
                        });
                    } else {
                        $vendorDropdown.append(
                            '<option value="">No vendors available</option>'
                        );
                    }
                }
            },
            error: function (e) {
                Swal.fire({
                    title: "Erorr",
                    text: "Unable to fetch data, try again.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
        });
    }
);

$(document).on("change", ".products_dropdown_po", function () {
    let selectedOption = $(this).find(":selected");
    let rate = parseFloat(selectedOption.data("rate"));
    if (isNaN(rate)) {
        $(this).closest(".po-form-box").find(".po_qty").val("");
        $(this).closest(".po-form-box").find(".po_exist_qty").val("");
        $(this).closest(".po-form-box").find(".po_unit_price").val("");
        $(this).closest(".po-form-box").find(".po_gst").val("");
        $(this).closest(".po-form-box").find(".product_subtotal").val("");
        $(this).closest(".po-form-box").find(".pro_tot_amt").val("");
        return;
    }
    let gst = parseFloat(selectedOption.data("gst"));
    let existQty = selectedOption.data("existquantity");
    $(this).closest(".po-form-box").find(".po_qty").val("1");
    $(this).closest(".po-form-box").find(".po_exist_qty").val(existQty);
    $(this).closest(".po-form-box").find(".po_unit_price").val(rate);
    $(this).closest(".po-form-box").find(".po_gst").val(gst);
    let qty = $(this).closest(".po-form-box").find(".po_qty").val();
    let subtotal = parseFloat(qty) * rate;
    $(this).closest(".po-form-box").find(".product_subtotal").val(subtotal);
    let productGstAmt = (rate / 100) * gst;
    let productTotal = rate + productGstAmt;
    $(this).closest(".po-form-box").find(".pro_tot_amt").val(productTotal);

    let roundOffTotal = Math.round(productTotal);

    if ($(this).closest(".po-form-box").find(".roundoff").prop("checked")) {
        $(this).closest(".po-form-box").find(".pro_tot_amt").val(roundOffTotal);
    } else {
        $(this).closest(".po-form-box").find(".pro_tot_amt").val(productTotal);
    }
});

// ajax functions
// purchase entry page scripts

$(document).on("change", ".product_category_pe", function () {
    let catId = $(this).val();
    let poNo = $(".po_number_dropdown").val();

    let $this = $(this);

    $.ajax({
        url: "/vendor/get-pe-pro-details",
        method: "POST",
        dataType: "JSON",
        data: { poNo: poNo, catId: catId },
        success: function (data) {
            if (data) {
                let $productDropdown = $this
                    .closest(".po-form-box")
                    .find(".products_dropdown_pe");

                $productDropdown.empty();
                $productDropdown.append(
                    '<option value="">-- Choose Product --</option>'
                );

                // append products data
                if (data.productData.length > 0) {
                    data.productData.forEach(function (item) {
                        let option = `<option value="${item.product_id}" data-existquantity="${item.product_qty}" data-rate="${item.unit_price}" data-gst="${item.gst_amount}">
                            ${item.product_details.productname}
                        </option>`;
                        $productDropdown.append(option);
                    });
                } else {
                    $productDropdown.append(
                        '<option value="">No products available</option>'
                    );
                }
            }
        },
        error: function (e) {
            Swal.fire({
                title: "Erorr",
                text: "Unable to fetch data, try again.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(document).on("change", ".po_number_dropdown", function () {
    $this = $(this);
    let poNo = $(this).val();
    let selectedOption = $(this).find(":selected");
    let rate = parseFloat(selectedOption.data("rate"));
    let gst = parseFloat(selectedOption.data("gst"));
    $(this)
        .closest(".po-form-box")
        .find(".multi_po_id")
        .val(selectedOption.data("poid"));
    $.ajax({
        url: "/vendor/get-po-details",
        method: "POST",
        data: { poNo: poNo },
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                let $catDropdown = $this
                    .closest(".po-form-box")
                    .find(".product_category_pe");

                $catDropdown.empty();
                $catDropdown.append(
                    '<option value="">-- Choose Category --</option>'
                );

                let $vendorDropdown = $this
                    .closest(".po-form-box")
                    .find(".vendor_dropdown_po");
                $vendorDropdown.empty();
                $vendorDropdown.append(
                    '<option value="">-- Choose Vendor --</option>'
                );

                let addedCategories = new Set();
                let addedVendors = new Set();
                if (data.poDetails.length == 1) {
                    data.poDetails.forEach(function (item) {
                        if (!addedCategories.has(item.cat_id)) {
                            let option = `<option value="${item.cat_id}" selected>
                                ${item.category_details.category_name}
                            </option>`;
                            $catDropdown.append(option);
                            addedCategories.add(item.cat_id);

                            $.ajax({
                                url: "/vendor/get-pe-pro-details",
                                method: "POST",
                                dataType: "JSON",
                                data: { poNo: poNo, catId: item.cat_id },
                                success: function (data) {
                                    if (data) {
                                        let $productDropdown = $this
                                            .closest(".po-form-box")
                                            .find(".products_dropdown_pe");

                                        $productDropdown.empty();
                                        $productDropdown.append(
                                            '<option value="">-- Choose Product --</option>'
                                        );

                                        // append products data
                                        if (data.productData.length > 0) {
                                            data.productData.forEach(function (
                                                item
                                            ) {
                                                let option = `<option value="${item.product_id}" data-existquantity="${item.product_qty}" data-rate="${item.unit_price}" data-gst="${item.gst_amount}" selected>
                                                    ${item.product_details.productname}
                                                </option>`;
                                                $productDropdown.append(option);
                                            });
                                        } else {
                                            $productDropdown.append(
                                                '<option value="">No products available</option>'
                                            );
                                        }
                                    }
                                },
                                error: function (e) {
                                    Swal.fire({
                                        title: "Erorr",
                                        text: "Unable to fetch data, try again.",
                                        icon: "error",
                                        confirmButtonText: "OK",
                                    });
                                },
                            });
                        } else {
                            $catDropdown.append(
                                '<option value="">No products available</option>'
                            );
                        }
                    });

                    data.poDetails.forEach(function (item) {
                        if (!addedVendors.has(item.vendor_id)) {
                            let option = `<option value="${item.vendor_id}" selected>
                                ${item.vendor_details.company_name}
                            </option>`;
                            $vendorDropdown.append(option);
                            addedVendors.add(item.vendor_id);
                        } else {
                            $vendorDropdown.append(
                                '<option value="">No vendor available</option>'
                            );
                        }
                    });
                } else {
                    data.poDetails.forEach(function (item) {
                        if (!addedCategories.has(item.cat_id)) {
                            let option = `<option value="${item.cat_id}">
                                ${item.category_details.category_name}
                            </option>`;
                            $catDropdown.append(option);
                            addedCategories.add(item.cat_id);
                        } else {
                            $catDropdown.append(
                                '<option value="">No products available</option>'
                            );
                        }
                    });
                    data.poDetails.forEach(function (item) {
                        if (!addedCategories.has(item.cat_id)) {
                            let option = `<option value="${item.cat_id}">
                                ${item.category_details.category_name}
                            </option>`;
                            $catDropdown.append(option);
                            addedCategories.add(item.cat_id);
                        } else {
                            $catDropdown.append(
                                '<option value="">No products available</option>'
                            );
                        }
                    });
                }
            }
        },
    });
});

// purchase entry scripts

$(document).on("change", ".products_dropdown_pe", function () {
    $this = $(this);
    let proId = $(this).val();
    let po_no = $this.closest(".po-form-box").find(".po_number_dropdown").val();
    // alert(proId);
    $.ajax({
        url: "/vendor/get-po-prodetails",
        method: "POST",
        data: { poNo: po_no, proId: proId },
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                if (data.poDetails) {
                    $this
                        .closest(".po-form-box")
                        .find(".pe_req_qty")
                        .val(data.poDetails.product_qty);
                    $this
                        .closest(".po-form-box")
                        .find(".po_unit_price")
                        .val(data.poDetails.unit_price);
                    $this
                        .closest(".po-form-box")
                        .find(".po_gst")
                        .val(parseInt(data.poDetails.product_details.gst));
                    $this
                        .closest(".po-form-box")
                        .find(".po_number")
                        .val(data.poDetails.po_no);
                    let productGstAmt =
                        (parseInt(data.poDetails.unit_price) / 100) *
                        parseInt(data.poDetails.product_details.gst);
                    let productTotal =
                        parseInt(data.poDetails.unit_price) + productGstAmt;
                    $this
                        .closest(".po-form-box")
                        .find(".pro_tot_amt")
                        .val(productTotal);
                }

                if (data.peDetails) {
                    $this
                        .closest(".po-form-box")
                        .find(".pe_req_qty")
                        .val(data.peDetails.requested_qty);
                    $this
                        .closest(".po-form-box")
                        .find(".po_unit_price")
                        .val(data.peDetails.unit_price);
                    $this
                        .closest(".po-form-box")
                        .find(".po_gst")
                        .val(parseInt(data.peDetails.product_details.gst));
                    $this
                        .closest(".po-form-box")
                        .find(".po_number")
                        .val(data.peDetails.po_no);
                    $this
                        .closest(".po-form-box")
                        .find(".pe_pending_qty")
                        .val(data.peDetails.pending_qty);
                    $this
                        .closest(".po-form-box")
                        .find(".pe_received_qty")
                        .val(data.peDetails.received_qty);
                    let productGstAmt =
                        (parseInt(data.peDetails.unit_price) / 100) *
                        parseInt(data.peDetails.product_details.gst);
                    let productTotal =
                        parseInt(data.peDetails.unit_price) + productGstAmt;
                    $this
                        .closest(".po-form-box")
                        .find(".pe_pro_tot_amt")
                        .val(productTotal);
                }
            }
        },
    });
});

$(document).on("input", ".pe_received_qty", function () {
    let reqQty = parseInt(
        $(this).closest(".po-form-box").find(".pe_req_qty").val()
    );
    let unitPrice = parseInt(
        $(this).closest(".po-form-box").find(".po_unit_price").val()
    );
    let gst = parseInt($(this).closest(".po-form-box").find(".po_gst").val());
    let receivedQty = parseInt($(this).val());
    let penQty = reqQty - receivedQty;
    let tempTotal = unitPrice * receivedQty;
    let productGstAmt = (tempTotal / 100) * gst;
    let receivedProAmount = tempTotal + productGstAmt;

    if (penQty >= 0) {
        $(this).closest(".po-form-box").find(".pe_pending_qty").val(penQty);
        $(this)
            .closest(".po-form-box")
            .find(".pro_received_amt")
            .val(receivedProAmount);
    } else {
        $(this).closest(".po-form-box").find(".pe_pending_qty").val("");
        $(this).closest(".po-form-box").find(".pro_received_amt").val("");
    }
});

$(document).on("click", ".roundoffCheckbox", function () {
    let rate = parseFloat(
        $(this).closest(".po-form-box").find(".po_unit_price").val()
    );
    let gst = parseFloat($(this).closest(".po-form-box").find(".po_gst").val());
    let qty = parseInt($(this).closest(".po-form-box").find(".po_qty").val());
    let productGstAmt = (rate / 100) * gst;
    let productTempTotal = rate + productGstAmt;
    let productTotal = qty * productTempTotal;
    let roundOffTotal = Math.round(productTotal);
    let subtotal = qty * rate;
    $(this).closest(".po-form-box").find(".product_subtotal").val(subtotal);

    if ($(this).prop("checked")) {
        $(this).closest(".form-group").find(".roundoff").val("1");
        if (!isNaN(productTotal)) {
            $(this)
                .closest(".po-form-box")
                .find(".pro_tot_amt")
                .val(roundOffTotal);
        } else {
            $(this).closest(".po-form-box").find(".pro_tot_amt").val("");
        }
    } else {
        $(this).closest(".form-group").find(".roundoff").val("0");
        if (!isNaN(productTotal)) {
            $(this)
                .closest(".po-form-box")
                .find(".pro_tot_amt")
                .val(productTotal);
        } else {
            $(this).closest(".po-form-box").find(".pro_tot_amt").val("");
        }
    }
});

// to calculate total amt when user type the quantity

$(document).on("input", ".po_qty", function () {
    let qty = parseInt($(this).val());
    let rate = parseFloat(
        $(this).closest(".po-form-box").find(".po_unit_price").val()
    );

    if (qty != "") {
        if (!isNaN(rate)) {
            let gst = parseFloat(
                $(this).closest(".po-form-box").find(".po_gst").val()
            );
            let productGstAmt = (rate / 100) * gst;
            let productTempTotal = rate + productGstAmt;
            let productTotal = qty * productTempTotal;
            let roundOffTotal = Math.round(productTotal);
            let subtotal = qty * rate;
            if (!isNaN(productTotal)) {
                $(this)
                    .closest(".po-form-box")
                    .find(".product_subtotal")
                    .val(subtotal);
                if ($(this).prop("checked")) {
                    $(this)
                        .closest(".po-form-box")
                        .find(".pro_tot_amt")
                        .val(roundOffTotal);
                } else {
                    $(this)
                        .closest(".po-form-box")
                        .find(".pro_tot_amt")
                        .val(productTotal);
                }
            } else {
                $(this).closest(".po-form-box").find(".pro_tot_amt").val("");
            }
        }
    }
});

// product stock page scripts

$(document).on("click", ".pro_stock_edit_btn", function () {
    let catId = $(this).data("catid");
    let proid = $(this).data("proid");
    let productname = $(this).data("productname");
    let categoryname = $(this).data("categoryname");
    let proAvailQty = $(this).data("proavailqty");
    $("#edit_prostock_category").val(categoryname);
    $("#edit_prostock_product").val(productname);
    $(".pro_stock_existing_avail_qty").val(proAvailQty);
    $(".prostock_product_id").val(proid);
    $(".prostock_category_id").val(catId);

    $.ajax({
        url: "/get-vendors/" + catId,
        method: "GET",
        dataType: "JSON",
        success: function (data) {
            if (data) {
                let $vendorDropdown = $("#edit_prostock_vendor");
                $vendorDropdown.empty();
                $vendorDropdown.append(
                    '<option value="">-- Choose Vendor --</option>'
                );

                if (data.vendors.length > 0) {
                    data.vendors.forEach(function (item) {
                        let option = `<option value="${item.id}">
                            ${item.company_name}
                        </option>`;
                        $vendorDropdown.append(option);
                    });
                } else {
                    $vendorDropdown.append(
                        '<option value="">No vendors available</option>'
                    );
                }
            }
        },
        error: function (e) {
            Swal.fire({
                title: "Erorr",
                text: "Unable to fetch data, try again.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(".edit_prostock_vendor").closest(".col-lg-6").hide();
$(".edit_prostock_vendor").prop("required", false);

$(document).on("change", ".pro_stock_type", function () {
    let type = $(this).val();

    $(".pro_stock_qty").val("");

    if (type == "purchase") {
        $(".edit_prostock_vendor").closest(".col-lg-6").show();
        $(".edit_prostock_vendor").prop("required", true);
    } else {
        $(".edit_prostock_vendor").closest(".col-lg-6").hide();
        $(".edit_prostock_vendor").prop("required", false);
    }
});

$(document).on("input", ".pro_stock_qty", function () {
    let availQty = $(".pro_stock_existing_avail_qty").val();
    let qty = $(this).val();
    let type = $("#pro_stock_type").val();
    let updatedQty = 0;
    if (type == "purchase") {
        updatedQty = parseInt(availQty) + parseInt(qty);
    } else if (type == "adjustment" || type == "sale") {
        updatedQty = parseInt(availQty) - parseInt(qty);
    }

    if (updatedQty > 0) {
        $(".pro_stock_updated_qty").val(updatedQty);
    }
});

$(document).on("click", ".product_stock_detail_btn", function () {
    let stockProId = $(this).data("proid");
    $.ajax({
        url: "/get-pro-stock-details/" + stockProId,
        method: "GET",
        dataType: "JSON",
        success: function (data) {
            if (data) {
                let $modelBody = $("#product-stock-details-table tbody");
                $modelBody.empty();
                if (data.proStockDetails.length > 0) {
                    data.proStockDetails.forEach(function (item, index) {
                        let formattedDate = formatDate(item.updated_at);
                        let type = item.type;
                        let typeClass = "";
                        if (type == "purchase") {
                            type = "Purchase";
                            typeClass = "purchase-text";
                        } else if (type == "sale") {
                            type = "Sale";
                            typeClass = "sale-text";
                        } else if ((type = "adjustment")) {
                            type = "Adjustment";
                            typeClass = "adjustment-text";
                        }

                        let value = item.quantity;
                        let positiveValue = Math.abs(value);

                        if (item.quantity < 0) {
                            value = Math.abs(item.quantity);
                        }

                        let stockDetailHtml = `<tr>
                            <td>${index + 1}</td>
                            <td>${formattedDate}</td>
                            <td>${item.vendors?.company_name ?? "N/A"}</td>
                            <td>${value}</td>
                            <td><div class='${typeClass} stock-type'>${type}</div></td>
                            <td>${item.remarks ?? "N/A"}</td>
                        </tr>`;
                        $modelBody.append(stockDetailHtml);
                    });
                }
            }
        },
        error: function (e) {
            Swal.fire({
                title: "Erorr",
                text: "Unable to fetch data, try again.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

function formatDate(dateString) {
    let date = new Date(dateString);
    return date
        .toLocaleString("en-GB", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        })
        .replace(",", "");
}

$(".existing-po-file-btn").click(function () {
    let file = $(this).data("file");
    window.open("/uploads/po_files/" + file, "_blank");
});

$(".web-loader").hide();

$(".role_edit_btn").click(function () {
    let roleId = $(this).data("roleid");
    let rolename = $(this).data("rolename");
    $(".editRoleId").val(roleId);
    $(".role_name").val(rolename);
});

// permission check toggle
// when the user click the icon all the checkboxes will be selected

$(".permissions-box .title-box i").click(function () {
    let checkBoxCount = $(this)
        .closest(".permissions-box")
        .find("input[type=checkbox]");

    if ($(this).hasClass("fas fa-check-circle")) {
        $(this).addClass("permission-check");
        $(this).removeClass("fas fa-check-circle");
        checkBoxCount.each(function (i) {
            $(this).prop("checked", false);
        });
    } else {
        $(this).addClass("fas fa-check-circle");
        $(this).removeClass("permission-check");
        checkBoxCount.each(function (i) {
            $(this).prop("checked", true);
        });
    }
});

// when user click all the input checkboxes the parent checkbox will be checked

$(".permissions-box input[type=checkbox]").click(function () {
    let checkBoxCount = $(this)
        .closest(".permissions-box")
        .find("input[type=checkbox]");
    let checkBoxCheckedCount = $(this)
        .closest(".permissions-box")
        .find("input[type=checkbox]:checked");

    if (checkBoxCount.length != checkBoxCheckedCount.length) {
        $(this)
            .closest(".permissions-box")
            .find("i")
            .addClass("permission-check");
        $(this)
            .closest(".permissions-box")
            .find("i")
            .removeClass("fas fa-check-circle");
    } else {
        $(this)
            .closest(".permissions-box")
            .find("i")
            .addClass("fas fa-check-circle");
        $(this)
            .closest(".permissions-box")
            .find("i")
            .removeClass("permission-check");
    }
});

// after page loading if all the input checkboxes are checked the parent checkbox will be checked

$(document).ready(function () {
    // to add the animation to bell icon for alerts

    let badgeCount = $(".badge-count").text();
    const bell = document.querySelector(".notification-bell-div.active-bell");
    let isAlertRun = localStorage.getItem("stock-alert");

    if (parseInt(badgeCount) > 0) {
        if (isAlertRun) {
            $(".notification-bell-div").removeClass("active-bell");
        } else {
            $(".notification-bell-div").addClass("active-bell");
            setTimeout(() => {
                localStorage.setItem("stock-alert", true);
            }, 2000);
        }
    }

    $(".remind_me_later_stock").click(function () {
        let isRemindMeClick = localStorage.getItem("remind-me-later-stock");

        // return;
        if (!isRemindMeClick) {
            localStorage.setItem("remind-me-later-stock", true);
        }
    });

    // remove the local storage after 30 minutes

    setTimeout(() => {
        localStorage.removeItem("stock-alert");
        localStorage.removeItem("remind-me-later-stock");
    }, 30 * 60 * 1000);

    function updatePermissionBoxes() {
        $(".permissions-box").each(function () {
            const checkboxes = $(this).find('input[type="checkbox"]');
            const checked = checkboxes.filter(":checked");
            const icon = $(this).find(".permission-check");

            if (checkboxes.length && checkboxes.length === checked.length) {
                icon.addClass("fas fa-check-circle");
                icon.removeClass("permission-check");
            } else {
                icon.removeClass("fas fa-check-circle");
                icon.addClass("permission-check");
            }
        });
    }

    // Initial run on page load
    updatePermissionBoxes();

    // Run on checkbox change
    $('input[type="checkbox"][name="permissionsId[]"]').on(
        "change",
        updatePermissionBoxes
    );

    // get product stock counts for alerts

    $.ajax({
        url: "/get-product-stocks-count",
        method: "GET",
        dataType: "JSON",
        success: function (response) {
            const url = window.location;
            const path = new URL(url).pathname;
            let isRemindMeClick = localStorage.getItem("remind-me-later-stock");

            if (response.status == "success") {
                if (response.is_sales || (response.role && response.role.toLowerCase().includes("sales"))) {
                    return;
                }
                if (response.data && response.data.length > 0) {
                    let $modelBody = $("#product-stock-alert-table tbody");
                    $modelBody.empty();
                    if (
                        response.role == "Purchase Manager" ||
                        response.role == "Super Admin" ||
                        response.role == "Director" ||
                        response.role == "Admin"
                    ) {
                        $(".product-stock-alert-header span").text(
                            response.data.length
                        );
                        if (path != "/product-stocks") {
                            if (!isRemindMeClick) {
                                $("#product_stock_alert_modal").modal("show");
                            }
                        }
                        response.data.forEach(function (item, index) {
                            let stockDetailHtml = `<tr>
                                <td class="text-center" style="width: 10%;">${index + 1}</td>
                                <td style="width: 72%; white-space: normal; word-break: break-word;">
                                    <a href='/product-stocks'>${
                                        item.product.productname ?? ""
                                    }</a>
                                </td>
                                <td class="text-center" style="width: 18%;">
                                    <input type='number' value='${
                                        item.available_stock ?? "0"
                                    }' class='low_stock_value'>
                                </td>
                            </tr>`;
                            $modelBody.append(stockDetailHtml);
                        });
                    }
                }
            }
        },
        error: function () {
            alert("Unable to fetch stocks.");
        },
    });

    function updateLocalStorageFunction() {
        let localSettingStorage = localStorage.getItem("settings");
        let gstValidationLocal = localStorage.getItem("gst-status");

        if (localSettingStorage) {
            let settingsDetails = JSON.parse(localSettingStorage);

            if (gstValidationLocal != "active") {
                $(".gst-number-field")
                    .closest(".form-group")
                    .find("span")
                    .hide();
            } else {
                $(".gst-number-field")
                    .closest(".form-group")
                    .find("span")
                    .show();
            }
        } else {
            getSettingsDetailsFunction();
        }
    }

    // inital call
    updateLocalStorageFunction();

    // gst validation enable or disable local storage
    function getSettingsDetailsFunction() {
        $.ajax({
            url: "/get-settings-details",
            method: "GET",
            dataType: "json",
            success: function (response) {
                console.log(response.data);

                if (response.status == "success") {
                    localStorage.setItem(
                        "settings",
                        JSON.stringify(response.data)
                    );

                    localStorage.setItem(
                        "gst-status",
                        response.data.gst_validation
                    );

                    let tempStorage = localStorage.getItem("settings");
                    if (response.data.gst_validation != "active") {
                        $(".gst-number-field")
                            .closest(".form-group")
                            .find("span")
                            .hide();
                    } else {
                        $(".gst-number-field")
                            .closest(".form-group")
                            .find("span")
                            .show();
                    }
                }
            },
            error: function (e) {
                console.log("settings fetch error: " + e);
            },
        });
    }

    $("#gst_checkbox").on("click", function () {
        if ($(this).prop("checked") == true) {
            localStorage.setItem("gst-status", "active");
        } else {
            localStorage.setItem("gst-status", "inactive");
        }

        updateLocalStorageFunction();

        $.ajax({
            url: "/settings-update-form",
            method: "POST",
            data: $("#settingForm").serialize(),
            beforeSend: function () {},
            success: function (response) {
                if (response.status == "success") {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 1000,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                    });
                    Toast.fire({
                        icon: "success",
                        title: "Updated successfully",
                    });
                } else {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 1000,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                    });
                    Toast.fire({
                        icon: "error",
                        title: "Something went wrong, please try again later",
                    });
                }
            },
            complete: function () {},
            error: function () {},
        });
    });
});

// to product stock update

$(".stock-alert-count").click(function () {
    let existingCount = $(this).closest(".col-lg-6").find("p strong").text();

    $(".pro_stock_alert_count").val(existingCount);
});

// gst verification api

$(document).on("input", ".gst-number-field", function () {
    gstValidationFunction(this);
});

// GST 2-digit State Code Mapping to state_list table IDs
const gstStateCodeMap = {
    '01': 15, // JAMMU AND KASHMIR
    '02': 13, // HIMACHAL PRADESH
    '03': 27, // PUNJAB
    '04': 7,  // CHANDIGARH
    '05': 33, // UTTARAKHAND
    '06': 14, // HARYANA
    '07': 9,  // DELHI
    '08': 29, // RAJASTHAN
    '09': 34, // UTTAR PRADESH
    '10': 5,  // BIHAR
    '11': 30, // SIKKIM
    '12': 3,  // ARUNACHAL PRADESH
    '13': 25, // NAGALAND
    '14': 22, // MANIPUR
    '15': 24, // MIZORAM
    '16': 32, // TRIPURA
    '17': 20, // MEGHALAYA
    '18': 4,  // ASSAM
    '19': 35, // WEST BENGAL
    '20': 16, // JHARKHAND
    '21': 26, // ORISSA
    '22': 6,  // CHATTISGARH
    '23': 23, // MADHYA PRADESH
    '24': 12, // GUJARAT
    '25': 8,  // DAMAN AND DIU
    '26': 10, // DADRA AND NAGAR HAVELI
    '27': 21, // MAHARASHTRA
    '29': 17, // KARNATAKA
    '30': 11, // GOA
    '31': 19, // LAKSHADWEEP
    '32': 18, // KERALA
    '33': 31, // TAMIL NADU
    '34': 28, // PONDICHERRY
    '35': 1,  // ANDAMAN AND NICOBAR ISLANDS
    '36': 36, // TELANGANA
    '37': 2,  // ANDHRA PRADESH
    '38': 15  // LADAKH
};

function gstValidationFunction(thisKeyWord) {
    let $this = $(thisKeyWord);
    let rawGst = $(thisKeyWord).val() || '';
    let gstNumber = rawGst.trim().toUpperCase();

    // Reset indicator if empty or incomplete without wiping user's typed values
    if (gstNumber.length === 0) {
        $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
        $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").hide();
        $this.closest(".form-group").find(".error-msg").text("");
        return;
    }

    if (gstNumber.length < 15) {
        $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
        $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").hide();
        $this.closest(".form-group").find(".error-msg").text("");
        return;
    }

    if (gstNumber.length > 15) {
        $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
        $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").show();
        $this.closest(".form-group").find(".error-msg").text("GST Number must be 15 characters");
        return;
    }

    // Format input with uppercase
    $this.val(gstNumber);

    // Stage 1: Check Local DB First
    $.ajax({
        url: "/get-customer-by-gst/" + encodeURIComponent(gstNumber),
        method: "GET",
        beforeSend: function () {
            $(".web-loader").hide();
        },
        success: function (localRes) {
            if (localRes && localRes.found) {
                // GST found in local database!
                $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").show();
                $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").hide();
                $this.closest(".form-group").find(".error-msg").text("");

                // 1. Lead Add Form
                if ($("#lead_customerSelect").length) {
                    window.isGstAutoFilling = true;

                    // Match option in Select2 dropdown
                    let customerFoundInDropdown = false;
                    $('#lead_customerSelect option').each(function () {
                        if ($(this).data('id') == localRes.customer_id || $(this).val() == localRes.customername) {
                            $('#lead_customerSelect').val($(this).val()).trigger('change');
                            customerFoundInDropdown = true;
                            return false;
                        }
                    });

                    if (!customerFoundInDropdown) {
                        var newOption = new Option(localRes.customername, localRes.customername, true, true);
                        $(newOption).data('id', localRes.customer_id);
                        $('#lead_customerSelect').append(newOption).trigger('change');
                    }

                    $('#newCustomerName').hide().prop('required', false);
                    $('.customer-err-msg').hide();

                    // Fill fields from local DB (all remain 100% editable)
                    if (localRes.contactname) $('#lead_contactName').val(localRes.contactname);
                    if (localRes.postalcode) $('#lead_postalcode').val(localRes.postalcode);
                    if (localRes.address) $('#lead_address').val(localRes.address);
                    if (localRes.communication_address || localRes.address) {
                        $('#lead_com_address').val(localRes.communication_address || localRes.address);
                    }
                    if (localRes.contact_no) $('#lead_contact_no').val(localRes.contact_no);
                    if (localRes.cus_email) $('#lead_cus_email').val(localRes.cus_email);
                    if (localRes.designation) $('#lead-designation').val(localRes.designation).trigger('change');
                    if (localRes.department) $('#lead-department').val(localRes.department).trigger('change');
                    if (localRes.country) $('#country, #lead_country').val(localRes.country).trigger('change');
                    if (localRes.state) $('#lead_state_list').val(localRes.state).trigger('change');
                    if (localRes.assigned_user) $('#assigned_to').val(localRes.assigned_user).trigger('change');

                    if (typeof window.fetchCities === 'function' && localRes.state) {
                        window.fetchCities(localRes.state, localRes.city);
                    }
                }

                // 2. Lead Edit Form
                if ($("#lead_edit_customer_name").length) {
                    $("#lead_edit_customer_name").val(localRes.customername || "");
                    $("#lead_edit_address").val(localRes.address || "");
                    $("#lead_edit_postalcode").val(localRes.postalcode || "");
                }

                // 3. Customer Add/Edit Form
                if ($("#customer_name").length) {
                    $("#customer_name").val(localRes.customername || "");
                    $("#cus-address").val(localRes.address || "");
                    $("#cus-pincode").val(localRes.postalcode || "");
                    if (localRes.state && $("#state_listt").length) {
                        $("#state_listt").val(localRes.state).trigger("change");
                    }
                }

                // 4. Vendor Add/Edit Form
                if ($("#company_name").length) {
                    $("#company_name").val(localRes.customername || "");
                    $("#vendor-address").val(localRes.address || "");
                    $("#add-vendor-postalcode").val(localRes.postalcode || "");
                }
                if ($("#edit_company_name").length) {
                    $("#edit_company_name").val(localRes.customername || "");
                    $("#edit_vendor_address").val(localRes.address || "");
                    $("#edit_vendor_postalcode").val(localRes.postalcode || "");
                }

            } else {
                // Stage 2: Not found in Local DB -> Query External RapidAPI
                fetchExternalGstApi(gstNumber, $this);
            }
        },
        error: function () {
            // Fallback to External API if local lookup fails
            fetchExternalGstApi(gstNumber, $this);
        }
    });
}

function fetchExternalGstApi(gstNumber, $this) {
    $.ajax({
        url: "https://gst-return-status.p.rapidapi.com/free/gstin/" + encodeURIComponent(gstNumber),
        method: "GET",
        async: true,
        crossDomain: true,
        beforeSend: function () {
            $(".web-loader").hide();
        },
        headers: {
            "x-rapidapi-key": "f216e3cbefmsh74bb68190d526a5p1fcdb1jsn7712055aeb4a",
            "x-rapidapi-host": "gst-return-status.p.rapidapi.com",
        },
        success: function (response) {
            if (response.success && response.data) {
                $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").show();
                $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").hide();
                $this.closest(".form-group").find(".error-msg").text("");

                let companyName = response.data.tradeName || response.data.lgnm || "";
                let address = response.data.adr || "";
                let pincode = response.data.pincode || "";

                // Determine State from first 2 digits of GST
                let statePrefix = gstNumber.substring(0, 2);
                let stateId = gstStateCodeMap[statePrefix] || null;

                // 1. Lead Add Form
                if ($("#lead_customerSelect").length) {
                    window.isGstAutoFilling = true;
                    $("#lead_customerSelect").val("new").trigger("change");
                    $("#newCustomerName").val(companyName).show().prop("required", true);
                    $(".customer-err-msg").hide();

                    $("#lead_address").val(address);
                    $("#lead_com_address").val(address);
                    $("#lead_postalcode").val(pincode);

                    // India = 76
                    $("#country, #lead_country").val("76").trigger("change");

                    // Set state and load cities
                    if (stateId && $("#lead_state_list").length) {
                        $("#lead_state_list").val(stateId).trigger("change");
                        if (typeof window.fetchCities === "function") {
                            window.fetchCities(stateId, null);
                        }
                    }
                }

                // 2. Lead Edit Form
                if ($("#lead_edit_customer_name").length) {
                    $("#lead_edit_customer_name").val(companyName);
                    $("#lead_edit_address").val(address);
                    $("#lead_edit_postalcode").val(pincode);
                }

                // 3. Customer Add / Edit
                if ($("#customer_name").length) {
                    $("#customer_name").val(companyName);
                    $("#cus-address").val(address);
                    $("#cus-pincode").val(pincode);
                    if (stateId && $("#state_listt").length) {
                        $("#state_listt").val(stateId).trigger("change");
                    }
                }

                // 4. Vendor Add / Edit
                if ($("#company_name").length) {
                    $("#company_name").val(companyName);
                    $("#vendor-address").val(address);
                    $("#add-vendor-postalcode").val(pincode);
                }
                if ($("#edit_company_name").length) {
                    $("#edit_company_name").val(companyName);
                    $("#edit_vendor_address").val(address);
                    $("#edit_vendor_postalcode").val(pincode);
                }

            } else {
                $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
                $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").show();
                $this.closest(".form-group").find(".error-msg").text(response.message || "Invalid GST Number");
            }
        },
        error: function (xhr, status, error) {
            console.error("GST API Error:", error);
            $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
            $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").show();
            $this.closest(".form-group").find(".error-msg").text("Invalid GST Number or API limit reached");
        }
    });
}

$(document).on("input", ".pincode-field", function () {
    let $this = $(this);
    let pincode = $(this).val() || '';
    if (pincode.length == 6) {
        $.ajax({
            url: "https://api.postalpincode.in/pincode/" + pincode,
            method: "GET",
            dataType: "json",
            beforeSend: function () {
                $(".web-loader").hide();
            },
            success: function (response) {
                console.log(response);
                if (response.Status == "Success") {
                    $this
                        .closest(".form-group")
                        .find(".gst-input-div i.fa-check-circle")
                        .show();
                    $this
                        .closest(".form-group")
                        .find(".gst-input-div i.fa-times-circle")
                        .hide();
                    $this.closest(".form-group").find(".error-msg").text("");
                } else {
                    $this
                        .closest(".form-group")
                        .find(".error-msg")
                        .text("Invalid Postal Code");
                    $this
                        .closest(".form-group")
                        .find(".gst-input-div i.fa-check-circle")
                        .hide();
                    $this
                        .closest(".form-group")
                        .find(".gst-input-div i.fa-times-circle")
                        .show();
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            },
        });
    } else {
        if (pincode.length > 6) {
            $this
                .closest(".form-group")
                .find(".error-msg")
                .text("Invalid Postal Code");
        } else {
            $this.closest(".form-group").find(".gst-input-div i.fa-check-circle").hide();
            $this.closest(".form-group").find(".gst-input-div i.fa-times-circle").hide();
            $this.closest(".form-group").find(".error-msg").text("");
        }
    }
});

$(document).on("submit", ".allocate-permission-form", function (e) {
    let checkBoxCheckedCount = $(".add-permission-container").find(
        "input[type=checkbox]:checked"
    ).length;
    if (parseInt(checkBoxCheckedCount) == 0) {
        e.preventDefault();
        Swal.fire({
            title: "Alert",
            text: "Select atleast one of the permission",
            icon: "warning",
        });
    }
});

let allowAddRoleSubmit = false;

$(document).on("submit", "#add-role-form", function (e) {
    if (allowAddRoleSubmit) {
        // Allow actual form submission
        return true;
    }

    e.preventDefault();

    const $form = $(this);
    const roleName = $form.find("input[type=text]").val();

    $.ajax({
        url: "/check-role-name",
        method: "POST",
        dataType: "json",
        data: { role: roleName },
        success: function (response) {
            if (response.status === "success") {
                $(".role_err_msg").text("Role already exists");
            } else {
                $(".role_err_msg").text("");
                allowAddRoleSubmit = true;
                $form.submit(); // Now the submit will be allowed
            }
        },
        error: function () {
            $(".role_err_msg").text("Server error. Please try again.");
        },
    });
});

let allowEditRoleSubmit = false;

$(document).on("click", ".edit-role-btn", function (e) {
    if (allowEditRoleSubmit) {
        // Allow actual form submission
        return true;
    }

    e.preventDefault();

    const $form = $("#edit-role-form");
    const roleName = $form.find("input[type=text]").val();
    const roleId = $(".editRoleId").val();

    $.ajax({
        url: "/updateRole",
        method: "POST",
        dataType: "json",
        data: { name: roleName, roleId: roleId },
        success: function (response) {
            if (response.status != "success") {
                $(".role_err_msg").text("Role already exists");
            } else {
                $(".role_err_msg").text("");
                Swal.fire({
                    icon: "success",
                    title: "Edited!",
                    text: "Role edited successfully",
                    timer: 2000,
                    showConfirmButton: false,
                });
                window.location.reload();
            }
        },
        error: function () {
            $(".role_err_msg").text("Role already exists");
        },
    });
});

$("#edit_role_modal").on("hidden.bs.modal", function () {});

// vendor list according to type

$(".vendor_type_list").on("change", function () {
    let type = $(this).val();
    $.ajax({
        url: "/get-vendors-ajax/" + type,
        method: "GET",
        dataType: "JSON",
        success: function (response) {
            let vendorTableBody = $("#vendor-table tbody");

            if (response.status === "success") {
                vendorTableBody.empty();
                response.vendors.forEach(function (item, index) {
                    let statusBadgeClass =
                        item.vendor_status === "active"
                            ? "bg-success"
                            : "bg-danger";
                    let statusText =
                        item.vendor_status.charAt(0).toUpperCase() +
                        item.vendor_status.slice(1);

                    let appendHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.company_name}</td>
                            <td>${item.state_data.state}</td>
                            <td>${item.city_data.city_name}</td>
                            <td>${item.point_of_contact}</td>
                            <td>${item.contact_number}</td>
                            <td>${item.gst_number}</td>
                            <td>
                                <span class="badge ${statusBadgeClass}">${statusText}</span>
                            </td>
                            <td>
                                <div class="vendor-action d-flex">
                                    <a href="/edit-vendors/${
                                        item.id
                                    }"><button class="btn btn-info me-2">Edit</button></a>
                                    <button data-url="/delete-vendors/${
                                        item.id
                                    }" class="btn btn-danger common-delete-btn">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    vendorTableBody.append(appendHtml);
                });
            } else {
                console.warn("Failed to load vendors.");
            }
        },
        error: function (e) {
            console.log("vendor fetching error: ", e);
        },
    });
});

// prevent negative number
function preventNegativeNumber() {
    this.value = Math.abs(this.value) || "";
}

// to fetch lead status automatically for converted

$(document).on("click", ".lead_won", function (e) {
    let $btn = $(this);
    let leadId = $btn.data("leadid");
    let isComplete = $btn.data("complete") == 1 || $btn.data("complete") == "1";
    let missing = $btn.data("missing") || "";
    let editUrl = $btn.data("edit-url") || ("/lead-edit/" + leadId);
    let customer = $btn.data("customer") || "N/A";
    let ref = $btn.data("ref") || ("LEAD" + leadId);

    if (!isComplete) {
        e.preventDefault();
        e.stopPropagation();

        $("#incompleteLeadRef").text(ref);
        $("#incompleteLeadCustomer").text(customer);
        $("#incompleteEditBtn").attr("href", editUrl);

        let missingItems = missing ? missing.split(", ") : [];
        let html = "";
        missingItems.forEach(function (item) {
            html += '<li class="mb-2 text-danger d-flex align-items-center"><i class="fas fa-times-circle me-2 text-danger"></i> <span>' + item + '</span></li>';
        });
        $("#incompleteMissingList").html(html);

        $("#incompleteLeadModal").modal("show");
        return false;
    }

    $("#lead_id").val(leadId);
    let statusDropdown = document.getElementById("leadstatus");
    let convertedOptionsDiv = document.getElementById("convertedOptionsDiv");
    let categoryDropdown = document.getElementById("catepro");
    let specialField = document.getElementById("specialField");

    let convertedDate = document.getElementById("converteddate");
    let pprod = document.getElementById("pprod");
    let quantity = document.getElementById("quantity");

    function toggleRequiredFields(isRequired) {
        if (convertedDate) convertedDate.required = isRequired;
        if (categoryDropdown) categoryDropdown.required = isRequired;
        if (pprod && !$(pprod).hasClass('select2-hidden-accessible')) {
            pprod.required = isRequired;
        }
        if (quantity) quantity.required = isRequired;
    }

    if (statusDropdown) statusDropdown.value = "343";

    if (convertedOptionsDiv) convertedOptionsDiv.style.display = "block";
    toggleRequiredFields(true);

    // Reset dropdowns
    $('#catepro').val('');
    $('#lead_sub_category_id').html('<option value="">-- Choose Product Group --</option>');
    if ($('#pprod').data('select2')) {
        $('#pprod').empty().trigger('change');
    }

    $("#statusModal").modal("show");
    setTimeout(function () {
        if (typeof initStatusModalProductSelect2 === 'function') {
            initStatusModalProductSelect2();
        }
    }, 150);

    // Show special field and make it required if category is "special"
    categoryDropdown.addEventListener("change", function () {
        let selectedOption =
            categoryDropdown.options[categoryDropdown.selectedIndex];
        let subCategory = selectedOption ? selectedOption.getAttribute("data-sub") : null;

        if (subCategory === "special") {
            specialField.style.display = "block";
            document.getElementById("special_input").required = true;
        } else {
            specialField.style.display = "none";
            document.getElementById("special_input").required = false;
        }
    });
});



// document.getElementById('catepro').addEventListener('change', function () {

//     let selectedOption = this.options[this.selectedIndex];
//     let type = selectedOption.getAttribute('data-type'); // mhe | rack | pallet
//     let subCategory = selectedOption.getAttribute('data-sub');

//     let specialField   = document.getElementById('specialField');
//     let designerSelect = document.getElementById('special_input');
//     let designSpecField = document.getElementById('designSpecField');

//     if (subCategory === 'special' && ['mhe','rack','pallet'].includes(type)) {
//         designSpecField.style.display = 'block';
//     } else {
//         designSpecField.style.display = 'none';
//     }

//     // reset
//     designerSelect.disabled = false;

//     // only when category is Special
//     if (subCategory === 'special' && type && window.assignSettings[type]) {

//         // show field (your existing script already handles this)
//         specialField.style.display = 'block';

//         // auto select assigned user
//         designerSelect.value = window.assignSettings[type];

//         // make readonly
//         designerSelect.disabled = true;
//     }
// });

// document.getElementById('catepro').addEventListener('change', function () {

//     let selectedOption = this.options[this.selectedIndex];
//     let categoryId  = this.value;
//     let type        = selectedOption.getAttribute('data-type');   // mhe | rack | pallet
//     let subCategory = selectedOption.getAttribute('data-sub');

//     let specialField    = document.getElementById('specialField');
//     let designerSelect  = document.getElementById('special_input');
//     let designSpecField = document.getElementById('designSpecField');
//     let wrapper         = document.getElementById('specificationWrapper');

//     /* ---------------- RESET ALL ---------------- */
//     wrapper.innerHTML = '';
//     designSpecField.style.display = 'none';
//     specialField.style.display = 'none';

//     designerSelect.disabled = false;
//     designerSelect.value = '';

//     /* ---------------- SPECIAL CATEGORY LOGIC ---------------- */
//     if (subCategory === 'special' && ['mhe','rack','pallet'].includes(type)) {

//         /* ===== Assign Designer (OLD LOGIC) ===== */
//         if (window.assignSettings && window.assignSettings[type]) {

//             specialField.style.display = 'block';

//             designerSelect.value = window.assignSettings[type];
//             designerSelect.disabled = true;
//         }

//         /* ===== Load Design Specifications (NEW LOGIC) ===== */
//         fetch(`/design-specs/${categoryId}/${type}`)
//             .then(res => res.json())
//             .then(specs => {

//                 if (!Array.isArray(specs) || specs.length === 0) {
//                     return;
//                 }

//                 designSpecField.style.display = 'block';

//                 specs.forEach(spec => {

//                     // safety fallback
//                     let specName = spec.spec_name ?? spec.specification ?? '';

//                     wrapper.insertAdjacentHTML('beforeend', `
//                         <div class="row mb-2">
//                             <div class="col-md-5">
//                                 <input type="text"
//                                        class="form-control"
//                                        value="${specName}"
//                                        readonly>
//                                 <input type="hidden"
//                                        name="spec_ids[]"
//                                        value="${spec.id}">
//                             </div>

//                             <div class="col-md-7">
//                                 <input type="text"
//                                        name="spec_values[]"
//                                        class="form-control"
//                                        placeholder="Enter ${specName}">
//                             </div>
//                         </div>
//                     `);
//                 });
//             })
//             .catch(err => {
//                 console.error('Spec fetch error:', err);
//             });
//     }
// });

document.getElementById('catepro').addEventListener('change', function () {

    let selectedOption = this.options[this.selectedIndex];
    let categoryId  = this.value;
    let type        = selectedOption.getAttribute('data-type');   // mhe | rack | pallet
    let subCategory = selectedOption.getAttribute('data-sub');

    let specialField     = document.getElementById('specialField');
    let designerSelect   = document.getElementById('special_input');
    let designerHidden   = document.getElementById('assigndesign_hidden');
    let designSpecField  = document.getElementById('designSpecField');
    let designinputField  = document.getElementById('designinputField');
    let wrapper          = document.getElementById('specificationWrapper');
    let gaNumberField = document.getElementById('ganumberfield');


    /* ---------------- RESET ALL ---------------- */
    wrapper.innerHTML = '';
    designSpecField.style.display = 'none';
    specialField.style.display = 'none';
    designinputField.style.display = 'none';
    gaNumberField.style.display = 'block'; // default show


    designerSelect.disabled = false;
    designerSelect.value = '';
    designerHidden.value = ''; // IMPORTANT reset

    /* ---------------- DESIGN FLOW LOGIC ---------------- */
    if ((subCategory === 'special' || type === 'mhe') && ['mhe','rack','pallet'].includes(type)) {

        /* ===== Assign Designer (AUTO) ===== */
        if (window.assignSettings && window.assignSettings[type]) {

            let assignedUserId = window.assignSettings[type];

            specialField.style.display = 'block';
            designinputField.style.display = 'block';
            gaNumberField.style.display = 'none';


            // set dropdown (UI)
            designerSelect.value = assignedUserId;

            // set hidden input (FORM SUBMIT)
            designerHidden.value = assignedUserId;

            // disable dropdown
            designerSelect.disabled = true;
        }

        /* ===== Load Design Specifications ===== */
        fetch(`/design-specs/${categoryId}/${type}`)
            .then(res => res.json())
            .then(specs => {

                if (!Array.isArray(specs) || specs.length === 0) {
                    return;
                }

                designSpecField.style.display = 'block';

                specs.forEach(spec => {

                    let specName = spec.spec_name ?? spec.specification ?? '';

                    wrapper.insertAdjacentHTML('beforeend', `
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <input type="text"
                                       class="form-control"
                                       value="${specName}"
                                       readonly>
                                <input type="hidden"
                                       name="spec_ids[]"
                                       value="${spec.id}">
                            </div>

                            <div class="col-md-7">
                                <input type="text"
                                       name="spec_values[]"
                                       class="form-control"
                                       placeholder="Enter ${specName}">
                            </div>
                        </div>
                    `);
                });
            })
            .catch(err => console.error('Spec fetch error:', err));
    }
});



// document.getElementById('catepro').addEventListener('change', function () {

//     let selectedOption = this.options[this.selectedIndex];
//     let type = selectedOption.getAttribute('data-type');
//     let subCategory = selectedOption.getAttribute('data-sub');

//     let specWrapper = document.getElementById('specificationWrapper');
//     let designSpecField = document.getElementById('designSpecField');

//     // reset
//     specWrapper.innerHTML = '';
//     designSpecField.style.display = 'none';

//     if (subCategory === 'special' && window.designSpecTemplates[type]) {

//         designSpecField.style.display = 'block';

//         let specs = window.designSpecTemplates[type];

//         specs.forEach((label, index) => {
//             let key = label.replace(/\s+/g, '_').toLowerCase();

//             let html = `
//                 <div class="row mb-2 spec-row">
//                     <div class="col-md-4">
//                         <input type="text" class="form-control"
//                                name="design_specifications[${key}][label]"
//                                value="${label}" readonly>
//                     </div>
//                     <div class="col-md-7">
//                         <input type="text" class="form-control"
//                                name="design_specifications[${key}][value]"
//                                placeholder="Enter ${label}">
//                     </div>
//                     <div class="col-md-1">
//                         <button type="button" class="btn btn-danger removeSpec">×</button>
//                     </div>
//                 </div>`;
//             specWrapper.insertAdjacentHTML('beforeend', html);
//         });
//     }
// });


// $(document).on('click', '.addSpec', function () {
//     let html = `
//         <div class="input-group mb-2">
//             <input type="text" name="design_specifications[]" class="form-control"
//                    placeholder="Enter specification">
//             <button type="button" class="btn btn-danger removeSpec">−</button>
//         </div>`;
//     $('#specificationWrapper').append(html);
// });

// $(document).on('click', '.removeSpec', function () {
//     $(this).closest('.input-group').remove();
// });






$("#lead_status_filter").on("change", function () {
    let statusId = $(this).val();

    $.ajax({
        url: "/lead-filter/" + statusId,
        method: "GET",
        dataType: "JSON",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status === "success") {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable("#leads")) {
                    $("#leads").DataTable().clear().destroy();
                    $('#leads').empty();
                }

                let userRole = response.role;

                let columns = [
                    { data: "Entrydate", title: "Entry Date" },
                    { data: "LeadName", title: "Customer Name" },
                    { data: "city_name", title: "City" },
                    { data: "contactname", title: "Contact Name" },
                    { data: "phonumber", title: "Contact No" },
                    { data: "email", title: "Email" },
                ];

                // Conditionally add "Sales Manager" column
                if (userRole === "Super Admin" || userRole === "Admin") {
                    columns.push({ data: "name", title: "Sales Manager" });
                }

                // Push remaining columns
                columns = columns.concat([
                    { data: "lead_id", title: "Enq Ref No" },
                    {
                        data: "status_name",
                        title: "Lead Status",
                        render: function (data, type, row) {
                            let bgColor = "#b6d3fe";

                            if (row.status_name == "HOT") {
                                bgColor = "#8c81f4";
                            } else if (row.status_name == "WARM") {
                                bgColor = "#f4f281";
                            } else if (row.status_name == "LOST") {
                                bgColor = "#f4818c";
                            } else if (row.status_name == "HOLD") {
                                bgColor = "#f49c81";
                            } else if (row.status_name == "WON") {
                                bgColor = "#b6fec8";
                            }

                            let style = `background: ${bgColor};padding: 5px 0px;border-radius: 3px;`;
                            return `<div class='lead_status_div' data-lleadid='${row.id}' data-lleadstatusid='${statusId}' data-leadstatusid='${row.status_name}' data-bs-toggle='modal' data-bs-target='#astatusModal' style='${style}'>${row.status_name}</div>`;
                        },
                    },
                    {
    data: "move_to",
    title: "Move to",
    render: function (data, type, row) {
        return `
            <div
                data-bs-toggle="modal"
                data-bs-target="#statusModal"
                data-leadid="${row.id}"
                style="width: 130px"
                class="btn btn-primary lead_won">
                Move to Enquiry
            </div>
        `;
    },
},

                    // {
                    //     data: "move_to",
                    //     title: "Move to",
                    //     render: function () {
                    //         return `<div data-bs-toggle='modal' data-bs-target='#statusModal' style='width: 130px' class='btn btn-primary lead_won' >Move to Enquiry</div>`;
                    //     },
                    // },
                    {
                        data: "action",
                        title: "Action",
                    },
                ]);

                // Initialize DataTable with new data
                $("#leads").DataTable({
                    data: response.data,
                    columns: columns,
                    scrollX: true,
                });
            } else {
                alert("Not Received");
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                title: "Error",
                text: "Something went wrong!",
                icon: "error",
            });
        },
    });
});

$(document).on("change", ".freight_selection", function () {
    let value = $(this).val();

    if (value === "new") {
        $(this).closest(".form-group").find("input").show();
    } else {
        $(this).closest(".form-group").find("input").hide();
    }
});

// to check edit estimate page

$(document).ready(function () {
    const path = window.location.pathname.split("/")[1];
    const sameAsValue = $(".hiddenSameAsBillingValue").val();
    // to paste billing address to shipping address
    if (path === "editquota" && sameAsValue === "1") {
        setTimeout(() => {
            $(".editSameAsBilling").click();
        }, 500);
    }

    $.ajax({
        url: "/get-settings-details",
        type: "GET",
        success: function (res) {},
    });
});



// setting page submit ajax

$(document).on("click", "#setting-submit", function () {
    let updateButton = $(this);
    $.ajax({
        url: "/settings-update-form",
        method: "POST",
        data: $("#settingForm").serialize(),
        beforeSend: function () {
            updateButton.prop("disabled", true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...
            `);
        },
        success: function (response) {
            if (response.status == "success") {
                let Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 1000,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });
                Toast.fire({
                    icon: "success",
                    title: "Updated successfully",
                });
            } else {
                let Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 1000,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });
                Toast.fire({
                    icon: "error",
                    title: "Something went wrong, please try again later",
                });
            }
        },
        complete: function () {
            updateButton.prop("disabled", false).html("Update");
        },
        error: function () {},
    });
});

// chart details

$(document).ready(function () {
    quotationChartFunction();
    orfChartFunction();
});

function quotationChartFunction() {
    const quotationChart = document.getElementById("quotation_chart");

    let quotationSummaryList = [];
    let quotationSummaryValueList = [];

    let quotationCount = $("#quotation_total_count").val() ?? 0;
    let quotationTotal = $("#quotation_total_value").val() ?? 0;
    let quotationApprovedCount = $("#quotation_approved_count").val() ?? 0;
    let quotationApprovedTotal = $("#quotation_approved_total").val() ?? 0;
    let quotationPendingCount = $("#quotation_pending_count").val() ?? 0;
    let quotationPendingTotal = $("#quotation_pending_Total").val() ?? 0;

    quotationSummaryList.push(
        quotationCount,
        quotationApprovedCount,
        quotationPendingCount
    );

    quotationSummaryValueList.push(
        quotationTotal,
        quotationApprovedTotal,
        quotationPendingTotal
    );

    new Chart(quotationChart, {
        type: "doughnut",
        data: {
            labels: ["Total", "Approved", "Pending"],
            datasets: [
                {
                    data: [
                        quotationCount,
                        quotationApprovedCount,
                        quotationPendingCount,
                    ],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Quotation Summary",
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const index = context.dataIndex;
                            const label = context.label;
                            const count = context.dataset.data[index];
                            const amount = quotationSummaryValueList[index];
                            return `${label} - ${count} (₹${amount})`;
                        },
                    },
                },
            },
        },
    });
}

function orfChartFunction() {
    const orfChart = document.getElementById("orf_chart");

    let orfSummaryList = [];
    let orfSummaryValueList = [];

    let orfCount = $("#orf_total_count").val() ?? 0;
    let orfTotal = $("#orf_total_value").val() ?? 0;
    let orfApprovedCount = $("#orf_approved_count").val() ?? 0;
    let orfApprovedTotal = $("#orf_approved_total").val() ?? 0;
    let orfPendingCount = $("#orf_pending_count").val() ?? 0;
    let orfPendingTotal = $("#orf_pending_Total").val() ?? 0;

    orfSummaryList.push(orfCount, orfApprovedCount, orfPendingCount);

    orfSummaryValueList.push(orfTotal, orfApprovedTotal, orfPendingTotal);

    new Chart(orfChart, {
        type: "pie",
        data: {
            labels: ["Total", "Approved", "Pending"],
            datasets: [
                {
                    data: [orfCount, orfApprovedCount, orfPendingCount],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "ORF Summary",
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const index = context.dataIndex;
                            const label = context.label;
                            const count = context.dataset.data[index];
                            const amount = orfSummaryValueList[index];
                            return `${label} - ${count} (₹${amount})`;
                        },
                    },
                },
            },
        },
    });
}




// design table filter


// $("#dlead_cat_filter").on("change", function () {
//     let categoryId = $(this).val();

//     // Update product dropdown based on selected category
//     $.ajax({
//         url: "/get-sub-category/" + categoryId,
//         type: "GET",
//         success: function (res) {
//             let subcathtml = $("#dquotation_pro_group_filter");
//             subcathtml.html(`<option value="0">All</option>`);

//             res.subcategory_details.forEach((item) => {
//                 subcathtml.append(
//                     `<option value="${item.id}">${item.subcategory_name}</option>`
//                 );
//             });

//             fetchdesignData();
//         },
//     });
// });

// $("#dquotation_pro_group_filter").on("change", function () {
//     let categoryId = $("#dlead_cat_filter").val();
//     let subCategoryId = $(this).val();

//     // Update product dropdown based on selected category
//     $.ajax({
//         url: "/get-products-by-category/" + categoryId + "/" + subCategoryId,
//         type: "GET",
//         success: function (res) {
//             let proSelect = $("#dlead_pro_filter");
//             proSelect.html(`<option value="0">All</option>`);

//             res.products.forEach((product) => {
//                 proSelect.append(
//                     `<option value="${product.id}">${product.productname}</option>`
//                 );
//             });

//             // Trigger reload after updating product
//             fetchdesignData();
//         },
//     });
// });

// $("#dlead_cat_filter").on("change", function () {
//     let categoryId = $(this).val();

//     // Update product dropdown based on selected category
//     $.ajax({
//         url: "/get-products-by-category/" + categoryId,
//         type: "GET",
//         success: function (res) {
//             let proSelect = $("#dlead_pro_filter");
//             proSelect.html(`<option value="0">All</option>`);

//             res.products.forEach((product) => {
//                 proSelect.append(
//                     `<option value="${product.id}">${product.productname}</option>`
//                 );
//             });

//             // Trigger reload after updating product
//             fetchdesignData();
//         },
//     });
// });

// // Also reload table on product change
// $("#dlead_pro_filter").on("change", function () {
//     fetchdesignData();
// });

// function fetchdesignData() {
//     let categoryId = $("#dlead_cat_filter").val();
//     let subCategoryId = $("#dquotation_pro_group_filter").val();
//     let productId = $("#dlead_pro_filter").val();
//     // let statusId = $("#dlead_status_filter").val();

//     $.ajax({
//         url: "/design-filter",
//         method: "GET",
//         data: {
//             category_id: categoryId,
//             sub_category_id: subCategoryId,
//             product_id: productId,
//             // status_id: statusId,
//         },
//         dataType: "JSON",
//         success: function (response) {
//             if (response.status === "success") {

//                 // Destroy safely
//                 if ($.fn.DataTable.isDataTable("#leadsd")) {
//                     $("#leadsd").DataTable().clear().destroy();
//                 }

//                 let table = $("#leadsd").DataTable({
//                     data: response.data,

//                     scrollX: true,
//                     autoWidth: false,
//                     destroy: true,

//                     columns: [
//                         { data: "Entrydate", title: "Entry Date", width: "90px" },
//                         {
//                             data: "LeadName",
//                             title: "Customer Name",
//                             width: "220px",
//                             render: function (data) {
//                                 return `<div class="dt-wrap">${data ?? "-"}</div>`;
//                             },
//                         },
//                         { data: "citylist", title: "City", width: "120px" },
//                         { data: "contactname", title: "Contact Name", width: "140px" },
//                         { data: "phonumber", title: "Contact No", width: "130px" },
//                         { data: "category_name", title: "Segment", width: "150px" },
//                         { data: "assigned_to", title: "Sales Manager", width: "160px" },
//                         { data: "lead_id", title: "Enq Ref No", width: "120px" },
//                     ],

//                     columnDefs: [
//                         { targets: "_all", className: "align-middle" }
//                     ],

//                     // ⭐ CRITICAL FIX
//                     initComplete: function () {
//                         this.api().columns.adjust();
//                     },
//                     drawCallback: function () {
//                         this.api().columns.adjust();
//                     },
//                 });

//             } else {
//                 alert("No data found.");
//             }
//         },
//         error: function () {
//             Swal.fire("Error", "Something went wrong!", "error");
//         },
//     });
// }