<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <!-- preloader css -->
    <link rel="stylesheet" href="/assets/css/preloader.min.css" type="text/css" />
    <!-- Bootstrap Css -->
    <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Custom Css -->
    <link rel="stylesheet" href="/assets/css/custom.css?v=2" type="text/css" />
    <link href="/assets/libs/admin-resources/rwd-table/rwd-table.min.css" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet" />

    <!-- Lottie animation Js -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>


</head>

<body>

    <div class="web-loader">
        <lottie-player src="/assets/lottie_animations/web-loader.json" background="transparent" speed="1" loop
            autoplay></lottie-player>
    </div>

    <div id="layout-wrapper">

        @include('layouts.topbar')

        <div class="page-content">
            <div class="container-fluid">

                @yield('main-content')

            </div>
            <!-- container-fluid -->
        </div>

    </div>

    @if(Auth::check() && !str_contains(strtolower(Auth::user()->role ?? ''), 'sales'))
    <div class="modal fade" id="product_stock_alert_modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Products Low Stocks List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="max-height:60vh; overflow-y:auto; overflow-x:hidden;">
                <table id="product-stock-alert-table" class="table table-bordered table-striped w-100" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%; text-align: center;">S.No</th>
                            <th style="width: 72%;">Products</th>
                            <th style="width: 18%; text-align: center;">Stock</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary remind_me_later_stock" data-bs-dismiss="modal">
                    Remind Me Later
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>

        </div>
    </div>
</div>
    @endif
    {{-- <div class="modal fade modal-lg" id="product_stock_alert_modal" aria-hidden="true"
        aria-labelledby="add_po_modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add_po_modalLabel">Products Low Stocks List</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="product-stock-alert-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Products</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary remind_me_later_stock" data-bs-dismiss="modal">Remind
                        Me Later</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> --}}

    @stack('scripts')
    {{-- SCRIPTS --}}

    <!-- JAVASCRIPT -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>
    <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/libs/node-waves/waves.min.js"></script>
    <script src="/assets/libs/feather-icons/feather.min.js"></script>
    <script src="/assets/libs/app.js"></script>
    <!-- pace js -->
    <script src="/assets/libs/pace-js/pace.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/libs/admin-resources/rwd-table/rwd-table.min.js"></script>

    <!-- Init js -->
    <script src="/assets/js/pages/table-responsive.init.js"></script>

    <!-- Required datatable js -->
    <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="/assets/libs/jszip/jszip.min.js"></script>
    <script src="/assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="/assets/libs/pdfmake/build/vfs_fonts.js"></script>
    <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

    <!-- Responsive examples -->
    <script src="/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="/assets/js/pages/datatables.init.js"></script>
    <script src="/assets/js/select2_init.js"></script>
    <script src="/assets/js/datatable_init.js"></script>
    <script src="/assets/js/sweet_alert.js"></script>
    <!-- <script src="/assets/js/dark_theme.js"></script> -->
    <script src="/assets/js/custom.js"></script>
    <script src="/assets/js/hr.js"></script>
    <script src="/assets/js/app.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function loadDashboardData() {
            $.ajax({
                url: '/dashboard-data',
                type: 'POST',
                data: $('#dashboardFilter').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        for (const [type, items] of Object.entries(response.data)) {
                            $(`.dashboard-count[data-type="${type}"]`).text(items.length || 0);

                            // Store detailed data for modal
                            window.dashboardDetails = window.dashboardDetails || {};
                            window.dashboardDetails[type] = items;
                        }
                    }
                }
            });
        }

        // On card click, show modal with related data
        $(document).on('click', '.dashboard-card', function () {
            const type = $(this).data('type');
            const data = window.dashboardDetails?.[type] || [];

            let tbody = $('#orderTable tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5">No records found.</td></tr>');
            } else {
                data.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.custo || ''}</td>
                            <td>${item.catepro || ''}</td>
                            <td>${item.orfref || ''}</td>
                            <td>${item.orfdate || ''}</td>
                        </tr>
                    `);
                });
            }

            $('#orderModal').modal('show');
        });

        // Submit filter form
        $('#dashboardFilter').on('submit', function (e) {
            e.preventDefault();
            loadDashboardData();
        });

        // Initial load
        $(document).ready(function () {
            loadDashboardData();
        });
    </script>


    <script>
        $(document).ready(function () {
            $('#filterToggle').on('click', function (e) {
                e.stopPropagation();
                $('#filterBox').toggle();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#filterBox, #filterToggle').length) {
                    $('#filterBox').hide();
                }
            });

            $('#dashboardFilter').on('submit', function (e) {
                e.preventDefault();
                loadDashboard(); // your AJAX load function
                $('#filterBox').hide();
            });
        });
    </script>
    <script>
        $('#closeFilterBox').on('click', function () {
            $('#filterBox').hide();
        });
    </script>


    <script>
        $(document).ready(function () {
            let table = $('#pendingTasks').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });

        });
    </script>


    <script>
        $(document).ready(function () {
            $('#technicalClosure').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#pendingEnquiry').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#businessRequirement').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#openorder').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#monthlybilling').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#monthlybooking').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [6, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#monthlycollection').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#payablelist').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#receivablelist').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#listsupplier').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#listcustomer').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#pandl').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#stockdetails').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#popending').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>


    <script>
        $(document).ready(function () {
            // Initialize Select2 for searchable dropdown
            $('#lead_customerSelect').select2({
                placeholder: 'Search or Select Customer',
                allowClear: true
            });

            // Show the input field if "New Customer" is selected
            $('#lead_customerSelect').change(function () {
                var customerName = $(this).val();
                if (customerName === 'new') {
                    $('#newCustomerName').show().prop('required', true);
                    $('.customer-err-msg').show();
                    if (!window.isGstAutoFilling) {
                        $('#lead_contactName').val(''); // Clear the input fields
                        $('#lead_postalcode').val('');
                        $('#lead_address').val('');
                        $('#lead_contact_no').val('');
                        $('#lead_cus_email').val('');
                        $('#lead_gst').val('');
                        $('#lead_com_address').val('');
                        $('#lead-designation').val('').trigger('change');
                        $('#lead-department').val('').trigger('change');
                        $('#assigned_to').val('').change();
                        // $('#country').val('').change();
                        // $('#country_code').val('').change();
                        $('#lead_state_list').val('').change();
                    }
                    window.isGstAutoFilling = false;
                } else {
                    $('#newCustomerName').hide().prop('required', false);
                    $('.customer-err-msg').hide();
                    // Fetch the customer data when an existing customer is selected
                    var customerId = $(this).find(':selected').data('id');
                    if (customerId) {
                        // Perform an AJAX request to fetch the customer's contact information
                        $.ajax({
                            url: '/get-customer-data/' + customerId, // Route to fetch customer data
                            method: 'GET',
                            success: function (data) {
                                if (data) {
                                    // Populate the input fields with the customer's data
                                    $('#lead_contactName').val(data.contactname);
                                    $('#lead_gst').val(data.gstno);
                                    $('#lead_postalcode').val(data.postalcode);
                                    $('#lead_address').val(data.address);
                                    $('#lead_com_address').val(data.communication_address);
                                    $('#lead_contact_no').val(data.contact_no);
                                    $('#lead_cus_email').val(data.cus_email);
                                    $('#lead_ggst').val(data.gstno);
                                    $('#lead-designation').val(data.designation).trigger('change');
                                    $('#lead-department').val(data.department).trigger('change');
                                    $('#lead_country, #country').val(data.country).change();
                                    $('#country_code').val(data.country_code).change();
                                    $('#lead_state_list').val(data.state).change();
                                    if (data.assigned_user) {
                                        $('#assigned_to').val(data.assigned_user).change();
                                    }

                                    // Fetch cities based on the selected state
                                    fetchCities(data.state, data.city);

                                    // Also show checkmark for GST if valid
                                    if (data.gstno && data.gstno.length === 15) {
                                        $('.gst-input-div i.fa-check-circle').show();
                                        $('.gst-input-div i.fa-times-circle').hide();
                                        $('.error-msg').text('');
                                    }
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Unable to fetch customer data!'
                                });
                            }
                        });
                    }
                }
            });

            function fetchCities(stateId, selectedCity) {
                if (stateId) {
                    $.ajax({
                        url: '/get-cities/' + stateId, // Fetch cities based on state
                        method: 'GET',
                        success: function (response) {
                            $('#lead_city_list').empty().append(
                                '<option value=""> -- Choose City -- </option>');

                            $.each(response, function (index, city) {
                                $('#lead_city_list').append(
                                    `<option value="${city.id}" ${city.id == selectedCity ? 'selected' : ''}>${city.city_name}</option>`
                                );
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Unable to fetch cities!'
                            });
                        }
                    });
                }
            }
            window.fetchCities = fetchCities;

        });
    </script>
    <script>
        $(document).ready(function () {
            // Initialize Select2 for searchable dropdown
            $('#termSelect').select2({
                placeholder: 'Search or Select Term',
                allowClear: true
            });

            // Handle term selection change
            $('#termSelect').change(function () {
                const selectedValue = $(this).val();

                if (selectedValue === 'new') {
                    // Show new term content input
                    $('#newTermContent').show().prop('required', true);
                    $('#termDetails').val(''); // Clear term details field if any
                } else {
                    $('#newTermContent').hide().prop('required', false);
                    $('#termDetails').val('');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Initialize Select2 for searchable dropdown
            $('#warrantySelect').select2({
                placeholder: 'Search or Select Warranty Term',
                allowClear: true
            });

            // Handle term selection change
            $('#warrantySelect').change(function () {
                const selectedValue = $(this).val();

                if (selectedValue === 'new') {
                    // Show new term content input
                    $('#newwarContent').show().prop('required', true);
                    $('#warranDetails').val(''); // Clear term details field if any
                } else {
                    // Hide new term input field
                    $('#newwarContent').hide().prop('required', false);
                    $('#warranDetails').val(''); // Clear the term details field
                    // Removed AJAX call here
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('mobileInput');
            const hiddenInput = document.getElementById('fullMobileInput');

            input.addEventListener('input', function () {
                // Remove non-digit characters and limit to 10 digits
                let digits = this.value.replace(/\D/g, '').slice(0, 10);
                this.value = digits;

                // Update hidden input with +91- prefix
                hiddenInput.value = '+91-' + digits;
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.lead-status-btn', function () {
                var status = $(this).data('lests');      // status name
                var remarks = $(this).data('rema');      // remarks
                var rawDate = $(this).data('stup');      // raw datetime

                var formattedDate = 'N/A';
                if (rawDate) {
                    var dateObj = new Date(rawDate);
                    var day = String(dateObj.getDate()).padStart(2, '0');
                    var month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    var year = dateObj.getFullYear();
                    formattedDate = `${day}-${month}-${year}`;
                }

                $('#lead-status').text(status || 'N/A');
                $('#lead-status-date').text(formattedDate);
                $('#lead-remarks').text(remarks || 'N/A');
            });
        });
    </script>



    {{-- <script>
        var updatellStatusUrl = "{{ route('lead.updateStatusdf') }}"; // Laravel will process this

        $(document).ready(function () {
            $(document).on('click', '.change-status-btn', function () {
                var leadId = $(this).data('ids');
                $('#lead_id').val(leadId);
                $('#statusModal').modal('show');
            });

            $('#changeStatusForm').on('submit', function (e) {
                e.preventDefault();
                let submitButton = $(this).find('button[type="submit"]');
                $.ajax({
                    url: updatellStatusUrl, // This will now be parsed correctly
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...
                    `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).html('Update');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Status Updated Successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#statusModal').modal('hide');
                            if (response.redirect_url) {
                                setTimeout(function () {
                                    window.location.href = response.redirect_url;
                                }, 2000); // wait for alert before redirect
                            } else {
                                $('#leads').DataTable().ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong!'
                            });
                        }
                    },
                    error: function (xhr) {
                        submitButton.prop('disabled', false).html('Update');
                        console.log(xhr.responseText);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            });
        });
    </script> --}}
    <script>
var updatellStatusUrl = "{{ route('lead.updateStatusdf') }}";

$(document).ready(function () {

    $('#changeStatusForm').on('submit', function (e) {
        e.preventDefault();

        let submitButton = $(this).find('button[type="submit"]');

        // ✅ IMPORTANT
        let formData = new FormData(this);

        $.ajax({
            url: updatellStatusUrl,
            method: "POST",
            data: formData,
            processData: false, // ❗ REQUIRED
            contentType: false, // ❗ REQUIRED
            beforeSend: function () {
                submitButton.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm"></span> Updating...
                `);
            },
            // success: function (response) {
            //     submitButton.prop('disabled', false).html('Update');

            //     if (response.success) {
            //         Swal.fire({
            //             icon: 'success',
            //             title: 'Success!',
            //             text: 'Status Updated Successfully!',
            //             timer: 2000,
            //             showConfirmButton: false
            //         });

            //         $('#statusModal').modal('hide');
            //         $('#leads').DataTable().ajax.reload();
            //     }
            // },
            success: function (response) {
    submitButton.prop('disabled', false).html('Update');

    if (response.success) {

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message ?? 'Status Updated Successfully!',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {

            // ✅ IMPORTANT: redirect if URL exists
            if (response.redirect_url) {
                window.location.href = response.redirect_url;
            } else {
                $('#statusModal').modal('hide');
                $('#leads').DataTable().ajax.reload();
            }

        });
    }
},
            error: function (xhr) {
                submitButton.prop('disabled', false).html('Update');
                console.log(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Server error occurred.'
                });
            }
        });
    });

});
</script>

    <script>
        $(document).on("click", ".lead_status_div", function () {
    let statusId = $(this).data("lleadstatusid");
    let leadId = $(this).data("lleadid");
    $("#lleadstatus").val(statusId);
    $("#llead_id").val(leadId);
});

        </script>

    {{-- <script>
        var leupdatellStatusUrl = "{{ route('lead.leupdateStatusdf') }}"; // Laravel will process this

        $(document).ready(function () {


            $('#achangeStatusForm').on('submit', function (e) {
                e.preventDefault();
                let ssubmitButton = $(this).find('button[type="submit"]');
                $.ajax({
                    url: leupdatellStatusUrl, // This will now be parsed correctly
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        ssubmitButton.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...
                    `);
                    },
                    success: function (response) {
                        ssubmitButton.prop('disabled', false).html('Update');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Status Updated Successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#astatusModal').modal('hide');
                            if (response.redirect_url) {
                                setTimeout(function () {
                                    window.location.href = response.redirect_url;
                                }, 2000); // wait for alert before redirect
                            } else {
                                $('#leads').DataTable().ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong!'
                            });
                        }
                    }

                });
            });
        });
    </script> --}}

    <script>
        var updateorStatusUrl = "{{ route('orf.insertStatusorf') }}"; // Laravel will process this

        $(document).ready(function () {
            $(document).on('click', '.orf-status-btn', function () {
                var leadId = $(this).data('id');
                var cssta = $(this).data('cssta');
                $('#orfid').val(leadId);
                $('#cssta').val(cssta);
                $('.approval-check').prop('checked', false);
                $('#statusModale').modal('show');
            });

            $('#approval_status').on('change', function () {
                if ($(this).val() == '2') { // No selected
                    $('#reject_remark_field').show();
                } else { // Yes selected
                    $('#reject_remark_field').hide();
                    $('#reject_remark').val(''); // Clear field when hidden
                }
            });

            $('#orfstatus').on('submit', function (e) {
                e.preventDefault();
                if (!$('.approval-check:checked').length || $('.approval-check:checked').length !== $('.approval-check').length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Checklist Required',
                        text: 'Verify PO, Drawing, Quotation, and ORF before approval.'
                    });
                    return;
                }
                let submitButton = $(this).find('button[type="submit"]');
                $.ajax({
                    url: updateorStatusUrl,
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...
                    `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).text("Submit");
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Status Updated Successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#statusModale').modal('hide');
                            $('#orf').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Something went wrong!'
                            });
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating.'
                        });
                        submitButton.prop('disabled', false).text("Submit");
                    }
                });
            });
        });
    </script>

    <script>
        var updateCsStatusUrl = "{{ route('orf.updatecsStatusorf') }}";

        $(document).ready(function () {
            $(document).on('click', '.orf-csstatus-btn', function () {
                var leadId = $(this).data('rid');
                var appsta = $(this).data('appsta'); // Fix: Use correct data attribute
                $('#orfidcs').val(leadId);
                $('#appsta').val(appsta);
                $('.cs-approval-check').prop('checked', false);
                $('#statusModalcs').modal('show');
            });

            $('#cs_status').on('change', function () {
                if ($(this).val() == '2') { // No selected
                    $('#csreject_remark_field').show();
                } else { // Yes selected
                    $('#csreject_remark_field').hide();
                    $('#csreject_remark').val(''); // Clear field when hidden
                }
            });

            $('#orfcsstatus').on('submit', function (e) {
                e.preventDefault();
                if (!$('.cs-approval-check:checked').length || $('.cs-approval-check:checked').length !== $('.cs-approval-check').length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Checklist Required',
                        text: 'Verify PO, Drawing, Quotation, and ORF before approval.'
                    });
                    return;
                }
                let submitButton = $(this).find('button[type="submit"]');
                $.ajax({
                    url: updateCsStatusUrl,
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        submitButton.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...
                    `);
                    },
                    success: function (response) {
                        submitButton.prop('disabled', false).text("Submit");
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'CS Status Updated Successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#statusModalcs').modal('hide');
                            $('#orf').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Something went wrong!'
                            });
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        submitButton.prop('disabled', false).text("Submit");
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating.'
                        });
                    }
                });
            });
        });
    </script>
    <script>
$(document).ready(function () {

    // When clicking status button → open modal
    $(document).on('click', '.qutochange-status-btn', function () {
        let quotationId = $(this).data('id');
        let quotationStatusId = $(this).data('quotstatusid');
        let statusText = $(this).text().trim().toUpperCase();
        let leadRemark = $(this).data('leadremark');
        let gaNumber = $(this).data('leadga');

        $('#quotation_id').val(quotationId);
        $('#quot-status-select').val(quotationStatusId);
         $('#lead-remarks').val(leadRemark ?? '');

        // ✅ GA Preview
        if (gaNumber) {
            $('#ga-preview-image').attr('src', '/' + gaNumber);
            $('#ga-preview-wrapper').removeClass('d-none');
        } else {
            $('#ga-preview-image').attr('src', '');
            $('#ga-preview-wrapper').addClass('d-none');
        }

        toggleRemarks(statusText);
    });

    // When status dropdown changes
    $('#quot-status-select').on('change', function () {
        let statusText = $("#quot-status-select option:selected").text().trim().toUpperCase();
        toggleRemarks(statusText);
    });

    function toggleRemarks(statusText) {
        if (['HOLD', 'LOST', 'POSTPONED'].includes(statusText)) {
            $('#quto-remarks-div').removeClass('d-none');
            $('#quto-remarks').prop('required', true);
        } else {
            $('#quto-remarks-div').addClass('d-none');
            $('#quto-remarks').prop('required', false).val('');
        }
    }

    // Submit form
    $('#change-qutostatus-form').on('submit', function (e) {
        e.preventDefault();

        let quotationId = $('#quotation_id').val();
        let statusId = $('#quot-status-select').val();
        let statusText = $("#quot-status-select option:selected").text().trim().toUpperCase();
        let remarks = $('#quto-remarks').val().trim();

        // Client-side validation
        if (['HOLD', 'LOST', 'POSTPONED'].includes(statusText) && remarks === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Remarks Required',
                text: 'Remarks are mandatory for Hold, Lost, or Postponed status.'
            });
            return;
        }

        $.ajax({
            url: "{{ route('leads.update-qutostatus') }}",
            type: "POST",
            data: {
    _token: "{{ csrf_token() }}",
    lead_id: quotationId,
    quto_status: statusId,
    quotastatus_remarks: remarks // ✅ MATCHES VALIDATION
},
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: response.message || 'Quotation status updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#qutostatusModal').modal('hide');
                setTimeout(() => window.location.reload(), 1500);
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Something went wrong.',
                });
            }
        });
    });

});
</script>
<script>
    $(document).on('click', '.closure-date-btn', function () {
    let leadId = $(this).data('id');
    let date = $(this).data('date');

    $('#closure_lead_id').val(leadId);
    $('#closure_date').val(date);
});

$('#closure-date-form').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('leads.update-closure-date') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            lead_id: $('#closure_lead_id').val(),
            closure_date: $('#closure_date').val()
        },
        success: function () {
            $('#closureDateModal').modal('hide');
            $('#leadsq').DataTable().ajax.reload(null, false);

            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Closure date updated successfully',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
});

    </script>

    {{-- <script>
        $(document).ready(function () {

            // Set lead_id in hidden input when modal is opened
            $(document).on('click', '.qutochange-status-btn', function () {
                var quotationId = $(this).data('id');
                var quotationStatusId = $(this).data('quotstatusid');
                $('#quot-status-select').val(quotationStatusId);
                $('#quotation_id').val(quotationId);
            });

            // Handle form submission via AJAX
            $('#change-qutostatus-form').on('submit', function (e) {
                e.preventDefault();

                var quotationId = $('#quotation_id').val();
                var statusId = $('#quot-status-select').val();

                $.ajax({
                    url: "{{ route('leads.update-qutostatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        lead_id: quotationId,
                        quto_status: statusId
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: response.message || 'Quotation status updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#qutostatusModal').modal('hide');
                        // Optionally reload DataTable or update UI
                        setTimeout(function (params) {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'Something went wrong.',
                        });
                    }
                });
            });

        });
    </script> --}}

    <script>
        $(document).ready(function () {
            $(document).on('click', '.qutocheck-status-btn', function () {
                var statusId = $(this).data('quto_status');
                var statusDate = $(this).data('qutostatus_date');

                // Format date to dd-mm-yyyy
                if (statusDate) {
                    var dateObj = new Date(statusDate);
                    var day = ("0" + dateObj.getDate()).slice(-2);
                    var month = ("0" + (dateObj.getMonth() + 1)).slice(-2);
                    var year = dateObj.getFullYear();
                    var formattedDate = `${day}-${month}-${year}`;
                } else {
                    var formattedDate = 'N/A';
                }

                // Fetch status name from ID using a lookup array or server render
                var statusName = 'Unknown';
                @php
                    $statuses = \App\Models\QuotationStatus::all()->pluck('name', 'id')->toArray();
                @endphp
            var statusMap = @json($statuses);

                if (    statusId && statusMap[statusId]) {
                    statusName = statusMap[statusId];
            }

                    $('#show_quto_status').text(statusName);
                $('#show_qutostatus_date').text(formattedDate);
            });
        });
</script>


    <script>
        $(document).on('click', '.delete-btn', function () {
            let url = $(this).data('url');
        let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
         }).then((result) => {
               if ( result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                       data : {
                            _token: '{{ csrf_token() }}'
                        },
                     succ   ess: function (response) {
                           if ( response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#leads').DataTable().ajax.reload(); // Reload DataTable
                            } else {
                            Swal    .fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: 'Failed to delete the lead.'
                                });
                            }
                        },
                    erro    r: function () {
                         Swal   .fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong.'
                            });
                        }
                    });
                }
            });
        });
</script>


    <script>
        $('#summernote').summernote({
            placeholder: 'Customize Design',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>


    <script>
         $(document).ready(function() {
             $(".lead-card").on("click", function() {
                $.ajax({
                    url: "{{ route('getLeads') }}",
                    type: "GET",
                     success: function(response) {
                        let tableBody = $("#leadsTable tbody");
                        tableBody.empty();
                         response.leads.forEach(function(lead) {
                            tableBody.append(`
                        <tr>
                            <td>${lead.Entrydate}</td>
                            <td>${lead.LeadName}</td>
                            <td>${lead.Leadsource}</td>
                            <td>${lead.Leadstatus}</td>

                        </tr>
                    `);
                        });
                    }
                });
            });
        });
   </script>
   <script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [1, 6] }
            ]
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('#employeeassetTable').DataTable({
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [1, 3] }
            ]
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('#advancetable').DataTable();
    });
    </script>
    <script>
$(document).ready(function() {
    $('#employeeAdvanceTable').DataTable();
});
</script>


    <script>
    $('#holidayTable').DataTable({
    pageLength: 10,
    columnDefs: [
        { orderable: false, targets: [1, 4] }
    ],
    error: function (e, settings, techNote, message) {
        console.error('DataTables error:', message);
    }
});

    </script>
    <script>
         $(document).ready(function () {
        $('.empbtn-delete').click(function () {
            var id = $(this).data('id');
            var row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/employee') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            if (response.success) {
                                row.remove();
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
        </script>

        <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Click event for delete button
        document.querySelectorAll('.delete-holiday').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to delete this holiday?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/deleteholiday/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                Swal.fire('Deleted!', 'The holiday has been deleted.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    });
</script>
{{-- <script>
document.addEventListener('change', function (e) {

    if (e.target.classList.contains('categorySelect')) {

        let categoryId = e.target.value;
        let row = e.target.closest('tr');
        let productSelect = row.querySelector('.productSelect');

        // Reset product dropdown
        productSelect.innerHTML = '<option value="">Select Product</option>';

        if (!categoryId) return;

        fetch(`/get-products-by-category/${categoryId}`)
            .then(response => response.json())
            .then(products => {

                products.forEach(product => {
                    let option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.productname;

                    // store extra data for auto-fill
                    option.dataset.partno = product.partno;
                    option.dataset.rate = product.rate;
                    option.dataset.tax = product.gst;

                    productSelect.appendChild(option);
                });
            });
    }
});
</script> --}}
{{-- <script>
function loadProductsByCategory(categorySelect) {

    let categoryId = categorySelect.value;
    let row = categorySelect.closest('tr');
    let productSelect = row.querySelector('.productSelect');

    productSelect.innerHTML = '<option value="">Select Product</option>';

    if (!categoryId) return;

    fetch(`/get-products-by-category/${categoryId}`)
        .then(response => response.json())
        .then(products => {
            products.forEach(product => {
                let option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.productname;

                option.dataset.partno = product.partno;
                option.dataset.rate   = product.rate;
                option.dataset.tax    = product.gst;

                productSelect.appendChild(option);
            });
        });
}
</script> --}}
{{-- <script>
document.addEventListener('change', function (e) {

    if (e.target.classList.contains('productSelect')) {

        let row = e.target.closest('tr');
        let selectedOption = e.target.options[e.target.selectedIndex];

        // Auto-fill Part No
        row.querySelector('input[name="part_no[]"]').value =
            selectedOption.dataset.partno || '';

        // Optional: Auto-fill Rate
        row.querySelector('.rate').value =
            selectedOption.dataset.rate || '';

        // Optional: Auto-fill Tax
        row.querySelector('.tax').value =
            selectedOption.dataset.tax || '';

        // Trigger recalculation
        row.querySelector('.qty').dispatchEvent(new Event('input'));
    }
});
</script> --}}
<script>
function getSegmentKeywordsAndDispatches(categoryOpt) {
    if (!categoryOpt || !categoryOpt.dataset) return [];
    let rawKeywords = categoryOpt.dataset.productKeyword || '';
    let rawDispatches = categoryOpt.dataset.keywordDispatchTime || '';

    let keywords = [];
    let dispatches = [];

    try {
        keywords = JSON.parse(rawKeywords);
        if (!Array.isArray(keywords)) keywords = [keywords];
    } catch (e) {
        keywords = rawKeywords ? [rawKeywords] : [];
    }

    try {
        dispatches = JSON.parse(rawDispatches);
        if (!Array.isArray(dispatches)) dispatches = [dispatches];
    } catch (e) {
        dispatches = rawDispatches ? [rawDispatches] : [];
    }

    let list = [];
    keywords.forEach((kw, i) => {
        let k = (kw || '').toString().trim();
        let d = (dispatches[i] || '').toString().trim();
        if (k) list.push({ keyword: k, dispatch: d });
    });
    return list;
}

function updateLeadTimeFromCategories() {
    const dispatchTimeInput = document.getElementById('dispatchTimeInput');

    if (!dispatchTimeInput) {
        return;
    }

    const productSelects = document.querySelectorAll('.productSelect');
    let dispatchTime = '';

    productSelects.forEach((select) => {
        if (dispatchTime) {
            return;
        }

        const selectedOption = $(select).find('option:selected')[0] || select.options[select.selectedIndex];
        const selectedText = selectedOption ? ($(selectedOption).text() || '').trim() : '';
        let optionDispatchTime = selectedOption && selectedOption.dataset
            ? (selectedOption.dataset.dispatchTime || '').trim()
            : '';

        if (optionDispatchTime) {
            dispatchTime = optionDispatchTime;
        } else if (selectedText && selectedText !== 'Select Product') {
            const row = select.closest('tr');
            const categorySelect = row ? row.querySelector('.categorySelect') : null;
            const categoryOpt = categorySelect && categorySelect.options ? categorySelect.options[categorySelect.selectedIndex] : null;

            const pairs = getSegmentKeywordsAndDispatches(categoryOpt);
            for (const pair of pairs) {
                if (pair.keyword && selectedText.toLowerCase().includes(pair.keyword.toLowerCase())) {
                    dispatchTime = pair.dispatch || 'Within a Week';
                    break;
                }
            }

            if (!dispatchTime && /hydraulic\s+hand\s+pallet/i.test(selectedText)) {
                dispatchTime = 'Within a Week';
            }
        }
    });

    if (dispatchTime) {
        dispatchTimeInput.value = dispatchTime;
        return;
    }

    const categorySelects = document.querySelectorAll('.categorySelect');
    let categoryDispatchTime = '';

    categorySelects.forEach((select) => {
        if (categoryDispatchTime) {
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const optionDispatchTime = selectedOption && selectedOption.dataset
            ? (selectedOption.dataset.dispatchTime || '').trim()
            : '';

        if (optionDispatchTime) {
            categoryDispatchTime = optionDispatchTime;
        }
    });

    dispatchTimeInput.value = categoryDispatchTime || dispatchTimeInput.dataset.defaultDispatchTime || '';
}

function loadProductsByCategory(categorySelect) {

    let categoryId = categorySelect.value;
    let row = categorySelect.closest('tr');
    let productSelect = row.querySelector('.productSelect');
    let selectedProductId = productSelect.dataset.selected; // 👈 KEY
    let subCategoryId = categorySelect.dataset.subCategoryId || row.dataset.subCategoryId || '';

    productSelect.innerHTML = '<option value="">Select Product</option>';

    if (!categoryId) return;

    let url = `/get-products-by-category/${categoryId}`;
    if (subCategoryId) {
        url += `?sub_category_id=${encodeURIComponent(subCategoryId)}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(products => {

            products.forEach(product => {
                let option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.productname;

                option.dataset.partno = product.partno;
                option.dataset.rate   = product.rate;
                option.dataset.tax    = product.gst;
                option.dataset.dispatchTime = product.dispatch_time || '';
                option.dataset.alloDis = product.allo_dis || '';

                // ✅ AUTO-SELECT EXISTING PRODUCT (EDIT / CREATE FROM LEAD MODE)
                if (selectedProductId && product.id == selectedProductId) {
                    option.selected = true;
                }

                productSelect.appendChild(option);
            });

            // Initialize select2 with search
            initProductSearch(productSelect);

            // 🔁 trigger autofill if product is selected
            if (selectedProductId) {
                let selectedOpt = productSelect.options[productSelect.selectedIndex];
                if (selectedOpt && selectedOpt.value) {
                    let partNoInput = row.querySelector('input[name="part_no[]"]');
                    let rateInput = row.querySelector('.rate');
                    let taxInput = row.querySelector('.tax');
                    let qtyInput = row.querySelector('.qty');

                    if (partNoInput) partNoInput.value = selectedOpt.dataset.partno || '';
                    if (rateInput) rateInput.value = selectedOpt.dataset.rate || '';
                    if (taxInput) taxInput.value = selectedOpt.dataset.tax || '';
                    if (qtyInput && (!qtyInput.value || qtyInput.value == '0')) {
                        qtyInput.value = 1;
                    }
                    if (qtyInput) {
                        $(qtyInput).trigger('input');
                    }
                }
                $(productSelect).trigger('change');
                $(productSelect).trigger('change.select2');
            }

            updateLeadTimeFromCategories();
        });
}
</script>
<script>
function initProductSearch(select) {
    if (!select) return;
    if ($(select).hasClass("select2-hidden-accessible")) {
        $(select).select2('destroy');
    }

    $(select).select2({
        placeholder: "Search Product",
        allowClear: true,
        width: '100%',
        dropdownParent: $(select).closest('td'), // important for table
        matcher: typeof window.multiKeywordMatcher === 'function' ? window.multiKeywordMatcher : undefined
    });
}

// Init search when products are loaded
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('categorySelect')) {

        let row = e.target.closest('tr');
        let productSelect = row.querySelector('.productSelect');

        // Delay to allow fetch() to populate options
        setTimeout(() => {
            initProductSearch(productSelect);
        }, 300);
    }
});

// Init for edit mode (already selected category)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.productSelect').forEach(select => {
        initProductSearch(select);
    });
});
</script>


<script>
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('categorySelect')) {
        loadProductsByCategory(e.target);
        updateLeadTimeFromCategories();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.categorySelect').forEach(select => {
        if (select.value) {
            loadProductsByCategory(select);
        }
    });

    updateLeadTimeFromCategories();

});
</script>

<script>
$(document).on('change', '.productSelect', function () {
    updateLeadTimeFromCategories();
});
</script>

<script>
$(document).on('select2:select', '.productSelect', function (e) {

    let row = $(this).closest('tr');
    let selectedOption = e.params.data.element;

    // ✅ Auto-fill Part No
    row.find('input[name="part_no[]"]').val(
        selectedOption.dataset.partno || ''
    );

    // ✅ Auto-fill Rate
    row.find('.rate').val(
        selectedOption.dataset.rate || ''
    );

    // ✅ Auto-fill Tax
    row.find('.tax').val(
        selectedOption.dataset.tax || ''
    );

    let qtyInput = row.find('.qty');
    if (!qtyInput.val() || qtyInput.val() == '0') {
        qtyInput.val(1);
    }

    // Trigger recalculation
    qtyInput.trigger('input');
    updateLeadTimeFromCategories();
});
</script>




<script>
document.addEventListener('click', function (e) {

if (e.target.classList.contains('addRow')) {

    let originalRow = document.querySelector('.productRow');

    // Destroy select2 before cloning
    $(originalRow).find('.productSelect').select2('destroy');

    let row = originalRow.cloneNode(true);

    // Re-init select2 on original
    initProductSearch(originalRow.querySelector('.productSelect'));

    // Reset inputs
    row.querySelectorAll('input').forEach(i => {
        if (i.type === 'checkbox') {
            i.checked = false;
        } else if (!i.classList.contains('allowdis')) {
            i.value = '';
        }
    });

    // Reset accessories wrapper & list
    let accWrapper = row.querySelector('.accessories-wrapper');
    if (accWrapper) {
        accWrapper.style.display = 'none';
    }
    let accList = row.querySelector('.accessories-list');
    if (accList) {
        accList.innerHTML = '';
    }

    // Keep category selection and sub_category_id from originalRow
    let origCategorySelect = originalRow.querySelector('.categorySelect');
    let newCategorySelect = row.querySelector('.categorySelect');
    let newProductSelect = row.querySelector('.productSelect');

    newProductSelect.removeAttribute('data-selected');
    newProductSelect.innerHTML = '<option value="">Select Product</option>';

    if (origCategorySelect && newCategorySelect) {
        newCategorySelect.value = origCategorySelect.value;
        if (origCategorySelect.dataset.subCategoryId) {
            newCategorySelect.dataset.subCategoryId = origCategorySelect.dataset.subCategoryId;
        }
    }

    // Change button
    row.querySelector('td:last-child').innerHTML = `
        <button type="button" class="btn btn-danger btn-sm removeRow rounded-circle">✕</button>
    `;

    document.getElementById('productRows').appendChild(row);

    // Load products for the new row with the same category/group filter
    if (newCategorySelect && newCategorySelect.value) {
        loadProductsByCategory(newCategorySelect);
    } else {
        initProductSearch(newProductSelect);
    }
    updateLeadTimeFromCategories();
}



    // REMOVE ROW
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('tr').remove();
        updateLeadTimeFromCategories();
        calculateSubtotal();
    }
});

// CALCULATION
// document.addEventListener('input', function (e) {

//     if (
//         e.target.classList.contains('qty') ||
//         e.target.classList.contains('rate') ||
//         e.target.classList.contains('tax') ||
//         e.target.classList.contains('requestdis-percent')
//     ) {

//         let row = e.target.closest('tr');

//         let qty  = parseFloat(row.querySelector('.qty').value) || 0;
//         let rate = parseFloat(row.querySelector('.rate').value) || 0;
//         let tax  = parseFloat(row.querySelector('.tax').value) || 0;

//         // let reqDis = parseFloat(row.querySelector('.requestdis').value) || 0;
//         let reqDis = parseFloat(row.querySelector('.requestdis-percent').value) || 0;

//         let gross = qty * rate;

//         // ✅ APPLY REQUESTED DISCOUNT ONLY
//         let discountAmt = (gross * reqDis) / 100;
//         let lbt = gross - discountAmt;

//         let taxamt = (lbt * tax) / 100;
//         let total = lbt + taxamt;

//         row.querySelector('.lbt').value = lbt.toFixed(2);
//         row.querySelector('.taxamt').value = taxamt.toFixed(2);
//         row.querySelector('.total').value = total.toFixed(2);

//         calculateSubtotal();
//     }
// });

document.addEventListener('input', function (e) {

    let row = e.target.closest('tr');
    if (!row) return;

    let qty  = parseFloat(row.querySelector('.qty')?.value) || 0;
    let rate = parseFloat(row.querySelector('.rate')?.value) || 0;
    let tax  = parseFloat(row.querySelector('.tax')?.value) || 0;

    let gross = qty * rate;

    let percentInput = row.querySelector('.requestdis-percent');
    let amountInput  = row.querySelector('.requestdis-amount');

    let discountAmount = 0;

    // % → Amount
    if (e.target.classList.contains('requestdis-percent')) {
        let percent = parseFloat(percentInput.value) || 0;
        discountAmount = (gross * percent) / 100;
        amountInput.value = discountAmount.toFixed(2);
    }

    // Amount → %
    if (e.target.classList.contains('requestdis-amount')) {
        discountAmount = parseFloat(amountInput.value) || 0;
        let percent = gross > 0 ? (discountAmount / gross) * 100 : 0;
        percentInput.value = percent.toFixed(2);
    }

    // Qty / Rate changed → recompute using amount
    if (
        e.target.classList.contains('qty') ||
        e.target.classList.contains('rate')
    ) {
        discountAmount = parseFloat(amountInput.value) || 0;
    }

    // FINAL CALCULATION
    let lbt = gross - discountAmount;
    let taxamt = (lbt * tax) / 100;
    let total = lbt + taxamt;

    row.querySelector('.lbt').value = lbt.toFixed(2);
    row.querySelector('.taxamt').value = taxamt.toFixed(2);
    row.querySelector('.total').value = total.toFixed(2);

    calculateSubtotal();
});


// TOTALS
function calculateSubtotal() {

    let subtotal = 0;
    let taxTotal = 0;
    let grandTotal = 0;

    document.querySelectorAll('.productRow').forEach(row => {
        subtotal += parseFloat(row.querySelector('.lbt').value) || 0;
        taxTotal += parseFloat(row.querySelector('.taxamt').value) || 0;
        grandTotal += parseFloat(row.querySelector('.total').value) || 0;
    });

    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('taxtotal').value = taxTotal.toFixed(2);
    document.getElementById('grandtotal').value = grandTotal.toFixed(2);
}
</script>
<script>
document.addEventListener('input', function (e) {

    let row = e.target.closest('tr');
    if (!row) return;

    let qty  = parseFloat(row.querySelector('.qty')?.value) || 0;
    let rate = parseFloat(row.querySelector('.rate')?.value) || 0;

    let gross = qty * rate;

    let percentInput = row.querySelector('.requestdis-percent');
    let amountInput  = row.querySelector('.requestdis-amount');

    // % → AMOUNT
    if (e.target.classList.contains('requestdis-percent')) {
        let percent = parseFloat(percentInput.value) || 0;
        let amount = (gross * percent) / 100;
        amountInput.value = amount.toFixed(2);
    }

    // AMOUNT → %
    if (e.target.classList.contains('requestdis-amount')) {
        let amount = parseFloat(amountInput.value) || 0;
        let percent = gross > 0 ? (amount / gross) * 100 : 0;
        percentInput.value = percent.toFixed(2);
    }

});
</script>

{{-- <script>
$(document).on('change', '#lleadstatus', function () {

    let selectedStatus = $(this).find(':selected').data('status');
    let remarksField = $('#remarks');
    let label = remarksField.closest('.mb-3').find('label');

    // Normalize to uppercase (SAFETY)
    selectedStatus = selectedStatus ? selectedStatus.toUpperCase() : '';

    // Remove existing star
    label.find('.required-star').remove();

    if (['LOST', 'HOLD', 'POSTPONED'].includes(selectedStatus)) {
        remarksField.prop('required', true);
        label.append(' <span class="required-star" style="color:red">*</span>');
    } else {
        remarksField.prop('required', false);
    }
});


$(document).on('submit', '#achangeStatusForm', function (e) {

    let selectedStatus = $('#lleadstatus').find(':selected').data('status');
    let remarks = $('#remarks').val().trim();

    selectedStatus = selectedStatus ? selectedStatus.toUpperCase() : '';

    if (['LOST', 'HOLD', 'POSTPONED'].includes(selectedStatus) && remarks === '') {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Remarks Required',
            text: 'Please enter remarks for Lost, Hold, or Postponed status.'
        });

        $('#remarks').focus();
        return false;
    }
});
</script> --}}

<script>
var leupdatellStatusUrl = "{{ route('lead.leupdateStatusdf') }}";

$(document).on('change', '#lleadstatus', function () {

    let selectedStatus = $(this).find(':selected').data('status');
    let remarksField = $('.rrema');
    let label = remarksField.closest('.mb-3').find('label');

    selectedStatus = selectedStatus ? selectedStatus.toUpperCase() : '';

    label.find('.required-star').remove();

    if (['LOST', 'HOLD', 'POSTPONED'].includes(selectedStatus)) {
        remarksField.prop('required', true);
        label.append(' <span class="required-star" style="color:red">*</span>');
    } else {
        remarksField.prop('required', false);
    }
});


$(document).on('submit', '#achangeStatusForm', function (e) {
    e.preventDefault();

    let selectedStatus = $('#lleadstatus').find(':selected').data('status');
    let remarks = $('.rrema').val().trim();
    let submitButton = $(this).find('button[type="submit"]');

    selectedStatus = selectedStatus ? selectedStatus.toUpperCase() : '';

    // 🔴 VALIDATION FIRST
    if (['LOST', 'HOLD', 'POSTPONED'].includes(selectedStatus) && remarks === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Remarks Required',
            text: 'Please enter remarks for Lost, Hold, or Postponed status.'
        });

        $('.rrema').focus();
        return false; // ⛔ STOP AJAX
    }

    // ✅ AJAX SUBMIT
    $.ajax({
        url: leupdatellStatusUrl,
        method: "POST",
        data: $(this).serialize(),
        beforeSend: function () {
            submitButton.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm"></span> Updating...
            `);
        },
        success: function (response) {
            submitButton.prop('disabled', false).html('Update Status');

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Status Updated Successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#astatusModal').modal('hide');

                if (response.redirect_url) {
                    setTimeout(function () {
                        window.location.href = response.redirect_url;
                    }, 2000);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something went wrong!'
                });
            }
        }
    });
});
</script>
<script>
    $("#dlead_cat_filter").on("change", function () {
    let categoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-sub-category/" + categoryId,
        type: "GET",
        success: function (res) {
            let subcathtml = $("#dquotation_pro_group_filter");
            subcathtml.html(`<option value="0">All</option>`);

            res.subcategory_details.forEach((item) => {
                subcathtml.append(
                    `<option value="${item.id}">${item.subcategory_name}</option>`
                );
            });

            fetchdesignData();
        },
    });
});

$("#dquotation_pro_group_filter").on("change", function () {
    let categoryId = $("#dlead_cat_filter").val();
    let subCategoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-products-by-category/" + categoryId + "/" + subCategoryId,
        type: "GET",
        success: function (res) {
            let proSelect = $("#dlead_pro_filter");
            proSelect.html(`<option value="0">All</option>`);

            res.products.forEach((product) => {
                proSelect.append(
                    `<option value="${product.id}">${product.productname}</option>`
                );
            });

            // Trigger reload after updating product
            fetchdesignData();
        },
    });
});

$("#dlead_cat_filter").on("change", function () {
    let categoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-products-by-category/" + categoryId,
        type: "GET",
        success: function (res) {
            let proSelect = $("#dlead_pro_filter");
            proSelect.html(`<option value="0">All</option>`);

            res.products.forEach((product) => {
                proSelect.append(
                    `<option value="${product.id}">${product.productname}</option>`
                );
            });

            // Trigger reload after updating product
            fetchdesignData();
        },
    });
});

// Also reload table on product change
$("#dlead_pro_filter").on("change", function () {
    fetchdesignData();
});

function fetchdesignData() {
    let categoryId = $("#dlead_cat_filter").val();
    let subCategoryId = $("#dquotation_pro_group_filter").val();
    let productId = $("#dlead_pro_filter").val();
    // let statusId = $("#dlead_status_filter").val();

    $.ajax({
        url: "/design-filter",
        method: "GET",
        data: {
            category_id: categoryId,
            sub_category_id: subCategoryId,
            product_id: productId,
            // status_id: statusId,
        },
        dataType: "JSON",
        success: function (response) {
            if (response.status === "success") {

                // Destroy safely
                if ($.fn.DataTable.isDataTable("#leadsd")) {
                    $("#leadsd").DataTable().clear().destroy();
                }

                let table = $("#leadsd").DataTable({
                    data: response.data,

                    scrollX: true,
                    autoWidth: false,
                    destroy: true,

                    columns: [
                        { data: "Entrydate", title: "Entry Date", width: "90px" },
                        {
                            data: "LeadName",
                            title: "Customer Name",
                            width: "220px",
                            render: function (data) {
                                return `<div class="dt-wrap">${data ?? "-"}</div>`;
                            },
                        },
                        { data: "citylist", title: "City", width: "120px" },
                        { data: "contactname", title: "Contact Name", width: "140px" },
                        { data: "phonumber", title: "Contact No", width: "130px" },
                        { data: "category_name", title: "Segment", width: "150px" },
                        { data: "assigned_to", title: "Sales Manager", width: "160px" },
                        { data: "lead_id", title: "Enq Ref No", width: "120px" },
                    ],

                    columnDefs: [
                        { targets: "_all", className: "align-middle" }
                    ],

                    // ⭐ CRITICAL FIX
                    initComplete: function () {
                        this.api().columns.adjust();
                    },
                    drawCallback: function () {
                        this.api().columns.adjust();
                    },
                });

            } else {
                alert("No data found.");
            }
        },
        error: function () {
            Swal.fire("Error", "Something went wrong!", "error");
        },
    });
}
    </script>
    <script>
        $("#lead_cat_filter").on("change", function () {
    let categoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-sub-category/" + categoryId,
        type: "GET",
        success: function (res) {
            let subcathtml = $("#quotation_pro_group_filter");
            subcathtml.html(`<option value="0">All</option>`);

            res.subcategory_details.forEach((item) => {
                subcathtml.append(
                    `<option value="${item.id}">${item.subcategory_name}</option>`
                );
            });

            fetchQuotationData();
        },
    });
});

$("#quotation_pro_group_filter").on("change", function () {
    let categoryId = $("#lead_cat_filter").val();
    let subCategoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-products-by-category/" + categoryId + "/" + subCategoryId,
        type: "GET",
        success: function (res) {
            let proSelect = $("#lead_pro_filter");
            proSelect.html(`<option value="0">All</option>`);

            res.products.forEach((product) => {
                proSelect.append(
                    `<option value="${product.id}">${product.productname}</option>`
                );
            });

            // Trigger reload after updating product
            fetchQuotationData();
        },
    });
});

$("#lead_cat_filter").on("change", function () {
    let categoryId = $(this).val();

    // Update product dropdown based on selected category
    $.ajax({
        url: "/get-products-by-category/" + categoryId,
        type: "GET",
        success: function (res) {
            let proSelect = $("#lead_pro_filter");
            proSelect.html(`<option value="0">All</option>`);

            res.products.forEach((product) => {
                proSelect.append(
                    `<option value="${product.id}">${product.productname}</option>`
                );
            });

            // Trigger reload after updating product
            fetchQuotationData();
        },
    });
});

// Also reload table on product change
$("#lead_pro_filter").on("change", function () {
    fetchQuotationData();
});

function fetchQuotationData() {
    let categoryId = $("#lead_cat_filter").val();
    let subCategoryId = $("#quotation_pro_group_filter").val();
    let productId = $("#lead_pro_filter").val();
    let statusId = $("#lead_status_filter").val();

    $.ajax({
        url: "/quotation-filter",
        method: "GET",
        data: {
            category_id: categoryId,
            sub_category_id: subCategoryId,
            product_id: productId,
            status_id: statusId,
        },
        dataType: "JSON",
        success: function (response) {
            if (response.status === "success") {
                if ($.fn.DataTable.isDataTable("#leadsq")) {
                    $("#leadsq").DataTable().clear().destroy();
                     $('#leadsq').empty();
                }

                $("#leadsq").DataTable({
                    data: response.data,
                    scrollX: true,
                    columns: [
                        { data: "quote_date", title: "Date" },
                        {
                            data: "quota_id",
                            title: "Quotation No",
                            render: function (data) {
                                return `<b>${data}</b>`;
                            },
                        },
                        {
                            data: "LeadName",
                            title: "Customer Name",
                            render: function (data) {
                                return `<div style="width: 200px">${data}</div>`;
                            },
                        },
                        { data: "city_name", title: "City" },
                        { data: "contactname", title: "Contact Name" },
                        { data: "phonumber", title: "Contact No" },
                        { data: "category_name", title: "Segment" },
                        // { data: "subcategory_name", title: "Group" },
                        // {
                        //     data: "productname",
                        //     title: "Model",
                        //     render: function (data) {
                        //         return `<div class='dt-wrap' style="width: 250px;">${data}</div>`;
                        //     },
                        // },
                        { data: "grandtotal", title: "Value" },
                        { data: "assigned_to", title: "Sales Manager" },
                        {
                            data: "quota_status",
                            title: "Status",
                            render: function (data, type, row) {
                                return `<div class='qutochange-status-btn cursor_pointer' data-bs-toggle='modal' data-bs-target='#qutostatusModal' data-id='${row.id}' data-quotstatusid='${row.quotation_status_id}'>${data}</div>`;
                            },
                        },
                        { data: "closure_date", title: "Closure Date" },
                        { data: "action", title: "Action" },
                    ],
                });
            } else {
                alert("No data found.");
            }
        },
        error: function () {
            Swal.fire("Error", "Something went wrong!", "error");
        },
    });
}
$("#lead_status_filter").on("change", function () {
    fetchQuotationData();
});
    </script>
    <script>
        $(document).on('click', '.assign-designer-btn', function () {
    let designId = $(this).data('design-id');
    $('#design_id').val(designId);
    $('#assignDesignerModal').modal('show');
});

$('#assignDesignerForm').on('submit', function (e) {
    e.preventDefault();

    $.post("{{ route('design.assign') }}", $(this).serialize())
        .done(function () {
            $('#assignDesignerModal').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Assigned Successfully',
                text: 'Designer has been assigned to this design.',
                timer: 2000,
                showConfirmButton: false
            });

            $('#leadsd').DataTable().ajax.reload(null, false);
        })
        .fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Assignment Failed',
                text: 'Please try again.'
            });
        });
});

// $('#assignDesignerForm').on('submit', function (e) {
//     e.preventDefault();

//     $.post("{{ route('design.assign') }}", $(this).serialize(), function () {
//         $('#assignDesignerModal').modal('hide');
//         $('#leadsd').DataTable().ajax.reload(null, false);
//     });
// });
        </script>
        <script>
$(document).on('click', '.view-design-specs', function () {

    let designId = $(this).data('design-id');

    $('#designSpecTable').html(
        '<tr><td colspan="2" class="text-center">Loading...</td></tr>'
    );

    $('#designInputPreview').html('');
    $('#designSpecModal').modal('show');

    $.get(`/design/specifications/${designId}`, function (response) {

        // ===== DESIGN INPUT PREVIEW =====
        // ===== DESIGN INPUT PREVIEW =====
        // ===== LEAD REMARK =====
if (response.lead_remark && response.lead_remark.trim() !== '') {
    $('#designLeadRemark').html(response.lead_remark);
} else {
    $('#designLeadRemark').html('<span class="text-muted">No remarks available</span>');
}


if (response.design_inputs && response.design_inputs.length > 0) {

    let html = '';

    response.design_inputs.forEach((item, index) => {

        let filePath = '/' + item.file_path;
        let ext = filePath.split('.').pop().toLowerCase();

        html += `<div class="mb-3 border rounded p-2">`;

        // IMAGE
        if (['jpg','jpeg','png','webp'].includes(ext)) {
            html += `
                <img src="${filePath}"
                     class="img-fluid rounded"
                     style="max-height:200px;">
            `;
        }

        // PDF
        else if (ext === 'pdf') {
            html += `
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                    <a href="${filePath}" target="_blank"
                       class="btn btn-outline-primary btn-sm">
                        View PDF
                    </a>
                    <a href="${filePath}" download
                       class="btn btn-outline-secondary btn-sm">
                        Download
                    </a>
                </div>
            `;
        }

        // EXCEL
        else if (['xls','xlsx'].includes(ext)) {
            html += `
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-excel text-success fs-2"></i>
                    <a href="${filePath}" download
                       class="btn btn-outline-primary btn-sm">
                        Download Excel
                    </a>
                </div>
            `;
        }

        // OTHER FILES
        else {
            html += `
                <a href="${filePath}" target="_blank"
                   class="btn btn-outline-primary btn-sm">
                    View File
                </a>
            `;
        }

        html += `</div>`;
    });

    $('#designInputPreview').html(html);
}
else {
    $('#designInputPreview').html(
        '<div class="text-muted">No design inputs uploaded</div>'
    );
}



        // ===== SPECIFICATIONS TABLE =====
        if (!response.specs || response.specs.length === 0) {
            $('#designSpecTable').html(
                '<tr><td colspan="2" class="text-center">No specifications found</td></tr>'
            );
            return;
        }

        let rows = '';
        response.specs.forEach(spec => {
            rows += `
                <tr>
                    <td>${spec.spec_name}</td>
                    <td>${spec.spec_value}</td>
                </tr>`;
        });

        $('#designSpecTable').html(rows);
    });
});
</script>


        {{-- <script>
           $(document).on('click', '.view-design-specs', function () {

    let designId = $(this).data('design-id');

    $('#designSpecTable').html(
        '<tr><td colspan="2" class="text-center">Loading...</td></tr>'
    );

    $('#designSpecModal').modal('show');

    $.get(`/design/specifications/${designId}`, function (data) {

        if (!data || data.length === 0) {
            $('#designSpecTable').html(
                '<tr><td colspan="2" class="text-center">No specifications found</td></tr>'
            );
            return;
        }

        let rows = '';
        data.forEach(spec => {
            rows += `
                <tr>
                    <td>${spec.spec_name}</td>
                    <td>${spec.spec_value}</td>
                </tr>`;
        });

        $('#designSpecTable').html(rows);
    });
});

            </script> --}}
            <script>
//                 $(document).on('click', '.upload-design-btn', function () {
//     $('#design_iid').val($(this).data('design-id'));
//     $('#uploadDesignModal').modal('show');
// });


$(document).on('click', '.upload-design-btn', function () {

    let designId = $(this).data('design-id');

    $('#design_iid').val(designId);
    $('#existingDrawings').html('<span class="text-muted">Loading drawings...</span>');

    $('#uploadDesignModal').modal('show');

    // Load existing drawings
    $.get(`/design/drawings/${designId}`, function (res) {

        if (!res.length) {
            $('#existingDrawings').html('<span class="text-muted">No drawings available</span>');
            return;
        }

        let html = '';
        res.forEach(file => {
            html += `
                <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                    <span>${file.file_name}</span>
                    <a href="/storage/${file.file_path}"
                       target="_blank"
                       class="btn btn-sm btn-outline-secondary">
                        Preview
                    </a>
                </div>
            `;
        });

        $('#existingDrawings').html(html);
    });
});
$('#uploadDesignForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: '/design/upload-files',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {
            $('#uploadDesignModal').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Moved to Sales',
                text: 'Design has been successfully moved to Sales.',
                timer: 2000,
                showConfirmButton: false
            });

            $('#leadsd').DataTable().ajax.reload(null, false);
        },

        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: 'Please try again.'
            });
        }
    });
});

                </script>
                <script>
let selectedDesignId = null;

$(document).on('click', '.move-quotation-btn', function () {

    selectedDesignId = $(this).data('design-id');
    if (!selectedDesignId) return;

    $('#viewQuotationBtn').attr('href', `/designs/${selectedDesignId}/quotation`);
    $('#viewDrawingBtn').attr('href', `/designs/${selectedDesignId}/drawing`);

    $('#moveQuotationModal').modal('show');
});

$('#confirmMoveQuotation').on('click', function () {

    $.post('/designs/move-to-quotation', {
        design_id: selectedDesignId,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function (res) {

        if (res.success) {
            $('#moveQuotationModal').modal('hide');
            if (res.redirect) {
                window.location.href = res.redirect;
                return;
            }
            $('#leadsd').DataTable().ajax.reload(null, false);
        } else {
            alert(res.message);
        }
    });
});

</script>
<script>
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
    </script>

    <script>
$(document).on('click', '.return-design-btn', function () {

    let designId = $(this).data('design-id');

    Swal.fire({
        title: 'Return Design',
        input: 'textarea',
        inputLabel: 'Remarks',
        inputPlaceholder: 'Enter return remarks...',
        inputAttributes: {
            required: true
        },
        showCancelButton: true,
        confirmButtonText: 'Yes, Return',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Remarks is required';
            }
        }
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "{{ route('design.return') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    design_id: designId,
                    return_remarks: result.value
                },
                success: function () {

                    Swal.fire({
                        icon: 'success',
                        title: 'Returned!',
                        text: 'Design returned successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
        location.reload();
    });

                    $('#leads').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                }
            });

        }
    });
});
</script>


<script>
$(document).on('click', '.edit-design-btn', function () {

    let designId = $(this).data('design-id');

    $('#editDesignForm')[0].reset();
    $('#edit_design_id').val(designId);
    $('#editSpecTable').html('<tr><td colspan="2">Loading...</td></tr>');
    $('#existingDesignInput').html('');

    $('#editDesignModal').modal('show');

    // Load specs + design input
    $.get(`/design/edit-data/${designId}`, function (res) {


// ===== RETURN REMARKS =====
if (res.return_remarks) {
    $('#returnRemarksWrapper').show();
    $('#returnRemarksText').val(res.return_remarks);
} else {
    $('#returnRemarksWrapper').hide();
    $('#returnRemarksText').val('');
}

if (res.design_inputs && res.design_inputs.length > 0) {

    let html = '<label class="fw-bold mb-2">Existing Files</label>';

    res.design_inputs.forEach(file => {

        let filePath = '/' + file.file_path;
        let ext = filePath.split('.').pop().toLowerCase();

        html += `
        <div class="border p-2 mb-2">
            <input type="hidden" name="existing_file_ids[]" value="${file.id}">

            <div class="mb-1">
                <a href="${filePath}" target="_blank">${file.file_path}</a>
            </div>

            <div class="d-flex gap-2">
                <input type="file"
                       name="replace_files[${file.id}]"
                       class="form-control form-control-sm">

                <label class="form-check-label">
                    <input type="checkbox"
                           name="delete_files[]"
                           value="${file.id}">
                    Delete
                </label>
            </div>
        </div>`;
    });

    $('#existingDesignInput').html(html);

} else {
    $('#existingDesignInput').html('<span class="text-muted">No files uploaded</span>');
}


        // Specs
        if (res.specs.length === 0) {
            $('#editSpecTable').html(
                '<tr><td colspan="2">No specifications found</td></tr>'
            );
            return;
        }

        let rows = '';
        res.specs.forEach((spec, i) => {
            rows += `
                <tr>
                    <td>${spec.spec_name}</td>
                    <td>
                        <input type="hidden" name="spec_ids[]" value="${spec.spec_id}">
                        <input type="text"
                               class="form-control"
                               name="spec_values[]"
                               value="${spec.spec_value}">
                    </td>
                </tr>
            `;
        });

        $('#editSpecTable').html(rows);
    });
});
</script>
<script>
$(document).on('click', '.addNewDesignInput', function () {
    $('#newDesignInputWrapper').append(`
        <div class="d-flex gap-2 mb-2">
            <input type="file"
                   name="new_design_input[]"
                   class="form-control"
                   accept=".pdf,.docx,.jpg,.png,.xls,.xlsx">
            <button type="button"
                    class="btn btn-danger removeNewDesignInput">−</button>
        </div>
    `);
});

$(document).on('click', '.removeNewDesignInput', function () {
    $(this).closest('.d-flex').remove();
});
</script>

<script>
$('#editDesignForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('design.update.after.return') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function () {

            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Sent back to design team',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload(); // ✅ FULL PAGE RELOAD
            });

            $('#editDesignModal').modal('hide');
            $('#leads').DataTable().ajax.reload(null, false);
        }
    });
});
</script>
<script>
/* =====================================================
   COMMON SELECT
===================================================== */
$(document).on('click', '.select-file', function () {
    $('#' + $(this).data('input')).trigger('click');
});

/* =====================================================
   PREVIEW URL HANDLER
===================================================== */
function getPreviewUrl(file, blobUrl) {

    if (file.type.startsWith('image/')) {
        return blobUrl; // image preview
    }

    if (file.type === 'application/pdf') {
        return blobUrl; // pdf browser preview
    }

    return blobUrl;
}

function fileIcon(file) {
    if (file.type.startsWith('image/')) return '🖼️';
    if (file.type === 'application/pdf') return '📄';
    if (file.type.includes('excel')) return '📊';
    return '📎';
}

/* =====================================================
   QUOTATION (SINGLE FILE)
===================================================== */
$('#quotationInput').on('change', function () {

    let file = this.files[0];
    if (!file) return;

    let blobUrl = URL.createObjectURL(file);
    let previewUrl = getPreviewUrl(file, blobUrl);

    $('#quotationPreview').html(`
        <div class="attachment-item">
            <div class="attachment-left">
                ${file.type.startsWith('image/')
                    ? `<img src="${blobUrl}">`
                    : `<span class="fs-4">${fileIcon(file)}</span>`}

                <span class="attachment-name">${file.name}</span>
            </div>

            <div class="attachment-actions">
                <a href="${previewUrl}" target="_blank"
                   class="btn btn-sm btn-outline-secondary">Preview</a>

                <button type="button"
                    class="btn btn-sm btn-outline-primary replace-quotation">
                    Replace
                </button>

                <button type="button"
                    class="btn btn-sm btn-outline-danger remove-quotation">
                    ✕
                </button>
            </div>
        </div>
    `);
});

$(document).on('click', '.replace-quotation', function () {
    $('#quotationInput').trigger('click');
});

$(document).on('click', '.remove-quotation', function () {
    $('#quotationInput').val('');
    $('#quotationPreview').html('<span class="text-muted">No file selected</span>');
});

/* =====================================================
   DRAWINGS (MULTIPLE FILES)
===================================================== */
let drawingFiles = new DataTransfer();

$('#drawingInput').on('change', function () {
    for (let file of this.files) {
        drawingFiles.items.add(file);
    }
    this.files = drawingFiles.files;
    renderDrawings();
});

function renderDrawings() {

    if (!drawingFiles.files.length) {
        $('#drawingPreview').html('<span class="text-muted">No file selected</span>');
        return;
    }

    let html = '';

    [...drawingFiles.files].forEach((file, index) => {

        let blobUrl = URL.createObjectURL(file);
        let previewUrl = getPreviewUrl(file, blobUrl);

        html += `
            <div class="attachment-item">
                <div class="attachment-left">
                    ${file.type.startsWith('image')
                        ? `<img src="${blobUrl}">`
                        : `<span class="fs-4">${fileIcon(file)}</span>`}

                    <span class="attachment-name">${file.name}</span>
                </div>

                <div class="attachment-actions">
                    <a href="${previewUrl}" target="_blank"
                       class="btn btn-sm btn-outline-secondary">Preview</a>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary replace-drawing"
                        data-index="${index}">
                        Replace
                    </button>

                    <button type="button"
                        class="btn btn-sm btn-outline-danger remove-drawing"
                        data-index="${index}">
                        ✕
                    </button>
                </div>
            </div>
        `;
    });

    $('#drawingPreview').html(html);
}

$(document).on('click', '.remove-drawing', function () {
    let index = $(this).data('index');
    let dt = new DataTransfer();

    [...drawingFiles.files].forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });

    drawingFiles = dt;
    $('#drawingInput')[0].files = dt.files;
    renderDrawings();
});

$(document).on('click', '.replace-drawing', function () {

    let index = $(this).data('index');
    let tempInput = $('<input type="file" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx">');

    tempInput.on('change', function () {
        let dt = new DataTransfer();

        [...drawingFiles.files].forEach((file, i) => {
            if (i === index) {
                dt.items.add(this.files[0]);
            } else {
                dt.items.add(file);
            }
        });

        drawingFiles = dt;
        $('#drawingInput')[0].files = dt.files;
        renderDrawings();
    });

    tempInput.trigger('click');
});
</script>




{{-- <script>
$(document).on('click', '.select-file', function () {
    let inputId = $(this).data('input');
    $('#' + inputId).trigger('click');
});

$('#quotationInput').on('change', function () {
    previewFile(this, '#quotationBox');
});

// $('#drawingInput').on('change', function () {
//     previewFile(this, '#drawingBox');
// });
$('#drawingInput').on('change', function () {
    let files = this.files;
    if (!files.length) return;

    let html = '';
    for (let i = 0; i < files.length; i++) {
        html += `<div>${files[i].name}</div>`;
    }

    $('#drawingBox').html(`
        <div>
            ${html}
            <button type="button"
                class="btn btn-sm btn-outline-danger replace-file mt-2">
                Replace
            </button>
        </div>
    `);
});


function previewFile(input, box) {
    let file = input.files[0];
    if (!file) return;

    let fileName = file.name;
    let fileType = file.type;
    let html = '';

    if (fileType.startsWith('image/')) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $(box).html(`
                <div class="d-flex align-items-center gap-2">
                    <img src="${e.target.result}"
                         style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                    <div>
                        <div>${fileName}</div>
                        <button type="button"
                            class="btn btn-sm btn-outline-danger replace-file mt-1">
                            Replace
                        </button>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    }

    else if (fileType === 'application/pdf') {
        $(box).html(`
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                <div>
                    <div>${fileName}</div>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger replace-file mt-1">
                        Replace
                    </button>
                </div>
            </div>
        `);
    }

    else if (
        fileType === 'application/vnd.ms-excel' ||
        fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ) {
        $(box).html(`
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel text-success fs-2"></i>
                <div>
                    <div>${fileName}</div>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger replace-file mt-1">
                        Replace
                    </button>
                </div>
            </div>
        `);
    }
}

</script> --}}
<script>
$(document).on('click', '.replace-file', function () {
    let box = $(this).closest('.attachment-box');

    let inputId = box.attr('id') === 'quotationBox'
        ? 'quotationInput'
        : 'drawingInput';

    // Clear old file
    let input = $('#' + inputId);
    input.val('');

    // Reset UI
    box.html(`
        <span class="text-muted">No file selected</span>
        <button type="button"
            class="btn btn-sm btn-outline-primary ms-2 select-file"
            data-input="${inputId}">
            Attach
        </button>
    `);

    // 🔥 AUTO OPEN FILE SELECTOR
    setTimeout(() => {
        input.trigger('click');
    }, 100);
});
</script>
<script>
const GST_PERCENT = 18;

let basicTotal   = parseFloat($('#basic_total').val()) || 0;
let originalTotal = parseFloat($('#total').val()) || 0;

function recalculate(discountAmount) {

    let discountedTotal = originalTotal - discountAmount;
    if (discountedTotal < 0) discountedTotal = 0;

    let gstAmount = discountedTotal * GST_PERCENT / 100;
    let grandTotal = discountedTotal + gstAmount;

    $('#total').val(discountedTotal.toFixed(2));
    $('#gst').val(gstAmount.toFixed(2));
    $('#grand_total').val(grandTotal.toFixed(2));
}

// % → ₹
$('#disc_percent').on('input', function () {

    let percent = parseFloat($(this).val()) || 0;
    let discountAmount = (originalTotal * percent) / 100;

    $('#disc_amount').val(discountAmount.toFixed(2));
    recalculate(discountAmount);
});

// ₹ → %
$('#disc_amount').on('input', function () {

    let amount = parseFloat($(this).val()) || 0;
    let percent = (amount / originalTotal) * 100;

    $('#disc_percent').val(percent.toFixed(2));
    recalculate(amount);
});
</script>
<script>
function syncSpecialTotals() {
    $('#sp_subtotal').val($('#total').val());
    $('#sp_tax_total').val($('#gst').val());
    $('#sp_grand_total').val($('#grand_total').val());
    $('#sp_request_discount').val($('#disc_percent').val());
     $('#sp_request_disamt').val($('#disc_amount').val());
      $('#sp_basic_total').val($('#basic_total').val());
}

// Call whenever discount changes
$('#disc_percent, #disc_amount').on('input', function () {
    syncSpecialTotals();
});
</script>
<script>
document.addEventListener('click', function (e) {

    // ADD
    if (e.target.classList.contains('addDesignInput')) {
        const wrapper = document.getElementById('designInputWrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="d-flex gap-2 mb-2">
                <input type="file"
                       name="design_input[]"
                       class="form-control"
                       accept=".pdf,.docx,.jpg,.png">

                <button type="button"
                        class="btn btn-danger removeDesignInput">−</button>
            </div>
        `);
    }

    // REMOVE
    if (e.target.classList.contains('removeDesignInput')) {
        e.target.closest('.d-flex').remove();
    }

});
</script>

<script>
$(document).on('click', '.view-drawings-btn', function () {

    let leadId = $(this).data('lead-id');
    $('#designDrawingsContainer').html(
        '<div class="col-12 text-center">Loading...</div>'
    );

    $('#designDrawingsModal').modal('show');

    $.get('/lead/design-drawings/' + leadId, function (files) {

        if (!files.length) {
            $('#designDrawingsContainer').html(
                '<div class="col-12 text-center text-muted">No drawings found</div>'
            );
            return;
        }

        let html = '';

        files.forEach(file => {

            let icon = 'fa-file-o text-secondary';

            if (['jpg','jpeg','png','webp'].includes(file.ext))
                icon = 'fa-file-image-o text-info';
            else if (file.ext === 'pdf')
                icon = 'fa-file-pdf-o text-danger';
            else if (['xls','xlsx'].includes(file.ext))
                icon = 'fa-file-excel-o text-success';

            html += `
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-2 text-center h-100">

                        <i class="fa ${icon}" style="font-size:40px"></i>

                        <div class="mt-2 d-flex justify-content-center gap-2">
                            <a href="${file.url}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Preview
                            </a>

                            <a href="${file.url}"
                               download
                               class="btn btn-sm btn-outline-secondary">
                                Download
                            </a>
                        </div>

                    </div>
                </div>
            `;
        });

        $('#designDrawingsContainer').html(html);
    });
});
</script>

         {{-- <td> <a href="singlelead/${lead.id}" class="btn btn-sm btn-primary">
            <i class="fas fa-eye"></i>
    </a></td> --}}

<script>
/* ---------- COMMON ---------- */
$(document).on('click', '.select-filee', function () {
    $('#' + $(this).data('input')).trigger('click');
});

function fileIcon(file) {
    if (file.type.startsWith('image')) return '🖼️';
    if (file.type === 'application/pdf') return '📄';
    if (file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) return '📊';
    return '📎';
}

/* ---------- MULTI FILE UPLOADER (SCOPED) ---------- */
function initMultiUploader(inputId, previewId) {

    let fileStore = new DataTransfer();
    const $input = $('#' + inputId);
    const $preview = $('#' + previewId);

    $input.on('change', function () {
        for (let file of this.files) {
            fileStore.items.add(file);
        }
        this.files = fileStore.files;
        render();
    });

    function render() {
        let html = '';

        [...fileStore.files].forEach((file, index) => {
            let url = URL.createObjectURL(file);

            html += `
                <div class="attachment-item">
                    ${
                        file.type.startsWith('image')
                            ? `<img src="${url}">`
                            : `<span class="fs-4">${fileIcon(file)}</span>`
                    }

                    <div class="attachment-name">${file.name}</div>

                    <div class="attachment-actions">
                        <a href="${url}" target="_blank"
                           class="btn btn-sm btn-outline-secondary">Preview</a>

                        <button type="button"
                                class="btn btn-sm btn-outline-primary replace-filee">
                            Replace
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-danger remove-filee"
                                data-index="${index}">
                            ✕
                        </button>
                    </div>
                </div>
            `;
        });

        $preview.html(html || '<span class="text-muted">No file selected</span>');
    }

    /* ✅ REMOVE (SCOPED TO THIS PREVIEW ONLY) */
    $preview.on('click', '.remove-filee', function () {
        let index = $(this).data('index');
        let dt = new DataTransfer();

        [...fileStore.files].forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });

        fileStore = dt;
        $input[0].files = dt.files;
        render();
    });

    /* ✅ REPLACE (SCOPED) */
    $preview.on('click', '.replace-filee', function () {
        $input.trigger('click');
    });
}

/* ---------- INIT ---------- */
initMultiUploader('attcuspoInput', 'attcuspoPreview');
initMultiUploader('sigdrawInput', 'sigdrawPreview');
</script>
<script>
$(document).on('click', '.view-attachments', function () {

    let orfId = $(this).data('orf');
    let type  = $(this).data('type');

    $('#attachmentsModal').modal('show');
    $('#attachmentsTable').html('<tr><td colspan="3">Loading...</td></tr>');

    $.get(`/orf/${orfId}/attachments/${type}`, function (files) {

        if (!files.length) {
            $('#attachmentsTable').html(`
                <tr>
                    <td colspan="3" class="text-center">No files found</td>
                </tr>
            `);
            return;
        }

        let html = '';
        files.forEach((file, index) => {

            let url = '/' + file.file_path;

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${file.file_path.split('/').pop()}</td>
                    <td>
                        <a href="${url}" target="_blank" class="btn btn-sm btn-primary">View</a>
                        <a href="${url}" download class="btn btn-sm btn-success">Download</a>
                    </td>
                </tr>
            `;
        });

        $('#attachmentsTable').html(html);
    });
});
</script>

<script>
/* -----------------------------------------
   GLOBAL VARIABLE FOR NEW FILES
----------------------------------------- */
// let selectedFiles = [];

// /* -----------------------------------------
//    OPEN UPLOAD DRAWING MODAL
// ----------------------------------------- */
// $(document).on('click', '.upload-drawing-btn', function () {

//     let designId = $(this).data('design-id');

//     $('#drawing_design_id').val(designId);
//     $('#drawingList').html('Loading...');
//     $('#newDrawingPreview').html('');
//     selectedFiles = [];

//     $('#uploadDrawingModal').modal('show');

//     // Load existing drawings
//     $.get(`/design/drawings/${designId}`, function (res) {

//         if (!res.length) {
//             $('#drawingList').html('<p class="text-muted">No drawings uploaded</p>');
//             return;
//         }

//         let html = '';
//         res.forEach(file => {
//             html += `
//             <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
//                 <div>${file.file_name}</div>

//                 <div class="d-flex gap-2">
//                     <a href="/storage/${file.file_path}" target="_blank"
//                        class="btn btn-sm btn-outline-secondary">
//                        Preview
//                     </a>

//                     <input type="file"
//                            name="replace_files[${file.id}]"
//                            hidden
//                            id="replace_${file.id}">

//                     <button type="button"
//                         class="btn btn-sm btn-outline-primary"
//                         onclick="document.getElementById('replace_${file.id}').click()">
//                         Replace
//                     </button>

//                     <button type="button"
//                         class="btn btn-sm btn-outline-danger remove-drawing"
//                         data-id="${file.id}">
//                         ✕
//                     </button>
//                 </div>
//             </div>`;
//         });

//         $('#drawingList').html(html);
//     });
// });

// /* -----------------------------------------
//    REMOVE EXISTING DRAWING
// ----------------------------------------- */
// $(document).on('click', '.remove-drawing', function () {

//     $('<input>').attr({
//         type: 'hidden',
//         name: 'delete_drawings[]',
//         value: $(this).data('id')
//     }).appendTo('#uploadDrawingForm');

//     $(this).closest('.border').remove();
// });

// /* -----------------------------------------
//    OPEN FILE PICKER
// ----------------------------------------- */
// $(document).on('click', '.attach-drawing-btn', function () {
//     $('#newDrawingInput').click();
// });

// /* -----------------------------------------
//    HANDLE NEW FILE SELECTION
// ----------------------------------------- */
// $('#newDrawingInput').on('change', function () {

//     selectedFiles = Array.from(this.files);
//     renderNewFiles();
// });

// /* -----------------------------------------
//    RENDER NEW FILES (LIKE SCREENSHOT)
// ----------------------------------------- */
// function renderNewFiles() {

//     let html = '';

//     selectedFiles.forEach((file, index) => {

//         const previewUrl = URL.createObjectURL(file);

//         html += `
//         <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
//             <div>${file.name}</div>

//             <div class="d-flex gap-2">
//                 <a href="${previewUrl}" target="_blank"
//                    class="btn btn-sm btn-outline-secondary">
//                    Preview
//                 </a>

//                 <input type="file"
//                        hidden
//                        id="replace_new_${index}"
//                        class="replace-new-input"
//                        data-index="${index}"
//                        accept=".pdf,.jpg,.png,.jpeg,.dwg">

//                 <button type="button"
//                     class="btn btn-sm btn-outline-primary replace-new-btn"
//                     data-index="${index}">
//                     Replace
//                 </button>

//                 <button type="button"
//                     class="btn btn-sm btn-outline-danger remove-new-file"
//                     data-index="${index}">
//                     ✕
//                 </button>
//             </div>
//         </div>`;
//     });

//     $('#newDrawingPreview').html(html);
// }



// /* -----------------------------------------
//    REMOVE NEWLY ATTACHED FILE
// ----------------------------------------- */
// $(document).on('click', '.replace-new-btn', function () {
//     let index = $(this).data('index');
//     $(`#replace_new_${index}`).click();
// });

// $(document).on('change', '.replace-new-input', function () {

//     let index = $(this).data('index');
//     let newFile = this.files[0];

//     if (!newFile) return;

//     selectedFiles[index] = newFile;

//     // Rebuild file input
//     let dataTransfer = new DataTransfer();
//     selectedFiles.forEach(file => dataTransfer.items.add(file));
//     $('#newDrawingInput')[0].files = dataTransfer.files;

//     renderNewFiles();
// });


// /* -----------------------------------------
//    SUBMIT FORM
// ----------------------------------------- */
// $('#uploadDrawingForm').on('submit', function (e) {
//     e.preventDefault();

//     $.ajax({
//         url: "{{ route('design.upload.drawings') }}",
//         type: "POST",
//         data: new FormData(this),
//         processData: false,
//         contentType: false,

//         success: function () {
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Success',
//                 text: 'Drawings uploaded and moved for approval',
//                 timer: 2000,
//                 showConfirmButton: false
//             }).then(() => {
//                 location.reload();
//             });
//         },

//         error: function () {
//             Swal.fire('Error', 'Upload failed', 'error');
//         }
//     });
// });

</script>
<script>
/* -----------------------------------------
   GLOBAL VARIABLES
----------------------------------------- */
let selectedFiles = [];
let designId = null;

/* -----------------------------------------
   CLICK UPLOAD DRAWING → ASK ACTION
----------------------------------------- */
$(document).on('click', '.upload-drawing-btn', function () {
    designId = $(this).data('design-id');
    $('#drawingActionModal').modal('show');
});

/* -----------------------------------------
   SEND DRAWING TO APPROVAL
----------------------------------------- */
$('#sendForApprovalBtn').on('click', function () {

    $('#drawingActionModal').modal('hide');

    $('#drawing_design_id').val(designId);
    $('#drawingList').html('Loading...');
    $('#newDrawingPreview').html('');
    selectedFiles = [];

    $('#uploadDrawingModal').modal('show');

    // Load existing drawings
    $.get(`/design/drawings/${designId}`, function (res) {

        if (!res.length) {
            $('#drawingList').html('<p class="text-muted">No drawings uploaded</p>');
            return;
        }

        let html = '';
        res.forEach(file => {
            html += `
            <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                <div>${file.file_name}</div>

                <div class="d-flex gap-2">
                    <a href="/storage/${file.file_path}" target="_blank"
                       class="btn btn-sm btn-outline-secondary">
                       Preview
                    </a>

                    <input type="file"
                           name="replace_files[${file.id}]"
                           hidden
                           id="replace_${file.id}">

                    <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        onclick="document.getElementById('replace_${file.id}').click()">
                        Replace
                    </button>

                    <button type="button"
                        class="btn btn-sm btn-outline-danger remove-drawing"
                        data-id="${file.id}">
                        ✕
                    </button>
                </div>
            </div>`;
        });

        $('#drawingList').html(html);
    });
});

/* -----------------------------------------
   ALREADY APPROVED
----------------------------------------- */
$('#alreadyApprovedBtn').on('click', function () {

    $('#drawingActionModal').modal('hide');

    $('#approved_design_id').val(designId);
    $('#approvedDrawingModal').modal('show');
});

/* -----------------------------------------
   REMOVE EXISTING DRAWING
----------------------------------------- */
$(document).on('click', '.remove-drawing', function () {

    $('<input>').attr({
        type: 'hidden',
        name: 'delete_drawings[]',
        value: $(this).data('id')
    }).appendTo('#uploadDrawingForm');

    $(this).closest('.border').remove();
});

/* -----------------------------------------
   OPEN FILE PICKER
----------------------------------------- */
$(document).on('click', '.attach-drawing-btn', function () {
    $('#newDrawingInput').click();
});

/* -----------------------------------------
   HANDLE NEW FILE SELECTION
----------------------------------------- */
$('#newDrawingInput').on('change', function () {
    selectedFiles = Array.from(this.files);
    renderNewFiles();
});

/* -----------------------------------------
   RENDER NEW FILES
----------------------------------------- */
function renderNewFiles() {

    let html = '';

    selectedFiles.forEach((file, index) => {

        const previewUrl = URL.createObjectURL(file);

        html += `
        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
            <div>${file.name}</div>

            <div class="d-flex gap-2">
                <a href="${previewUrl}" target="_blank"
                   class="btn btn-sm btn-outline-secondary">
                   Preview
                </a>

                <input type="file"
                       hidden
                       id="replace_new_${index}"
                       class="replace-new-input"
                       data-index="${index}"
                       accept=".pdf,.jpg,.png,.jpeg,.dwg">

                <button type="button"
                    class="btn btn-sm btn-outline-primary replace-new-btn"
                    data-index="${index}">
                    Replace
                </button>

                <button type="button"
                    class="btn btn-sm btn-outline-danger remove-new-file"
                    data-index="${index}">
                    ✕
                </button>
            </div>
        </div>`;
    });

    $('#newDrawingPreview').html(html);
}

/* -----------------------------------------
   REPLACE NEWLY ATTACHED FILE
----------------------------------------- */
$(document).on('click', '.replace-new-btn', function () {
    let index = $(this).data('index');
    $(`#replace_new_${index}`).click();
});

$(document).on('change', '.replace-new-input', function () {

    let index = $(this).data('index');
    let newFile = this.files[0];

    if (!newFile) return;

    selectedFiles[index] = newFile;

    let dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    $('#newDrawingInput')[0].files = dataTransfer.files;

    renderNewFiles();
});

/* -----------------------------------------
   REMOVE NEW FILE
----------------------------------------- */
$(document).on('click', '.remove-new-file', function () {
    let index = $(this).data('index');
    selectedFiles.splice(index, 1);

    let dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    $('#newDrawingInput')[0].files = dataTransfer.files;

    renderNewFiles();
});

/* -----------------------------------------
   SUBMIT SEND-TO-APPROVAL FORM
----------------------------------------- */
$('#uploadDrawingForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('design.upload.drawings') }}",
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,

        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Drawings uploaded and sent for approval',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        },

        error: function () {
            Swal.fire('Error', 'Upload failed', 'error');
        }
    });
});

/* -----------------------------------------
   SUBMIT APPROVED FORM
----------------------------------------- */
$('#approvedDrawingForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('design.approved.drawings') }}",
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,

        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Approved',
                text: 'Approved drawings uploaded successfully',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        },

        error: function () {
            Swal.fire('Error', 'Upload failed', 'error');
        }
    });
});
</script>
<script>
/* -----------------------------------------
   APPROVED DRAWINGS HANDLING
----------------------------------------- */
let approvedFiles = [];

/* OPEN FILE PICKER */
$(document).on('click', '.approved-attach-btn', function () {
    $('#approvedDrawingInput').click();
});

/* FILE SELECTION */
$('#approvedDrawingInput').on('change', function () {
    approvedFiles = Array.from(this.files);
    renderApprovedFiles();
});

/* RENDER FILES */
function renderApprovedFiles() {

    let html = '';

    approvedFiles.forEach((file, index) => {

        const previewUrl = URL.createObjectURL(file);

        html += `
        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
            <div>${file.name}</div>

            <div class="d-flex gap-2">
                <a href="${previewUrl}" target="_blank"
                   class="btn btn-sm btn-outline-secondary">
                   Preview
                </a>

                <input type="file"
                       hidden
                       id="approved_replace_${index}"
                       class="approved-replace-input"
                       data-index="${index}"
                       accept=".pdf,.jpg,.png,.jpeg,.dwg">

                <button type="button"
                        class="btn btn-sm btn-outline-primary approved-replace-btn"
                        data-index="${index}">
                        Replace
                </button>

                <button type="button"
                        class="btn btn-sm btn-outline-danger approved-remove-btn"
                        data-index="${index}">
                        ✕
                </button>
            </div>
        </div>`;
    });

    $('#approvedDrawingPreview').html(html);
}

/* REPLACE FILE */
$(document).on('click', '.approved-replace-btn', function () {
    $('#approved_replace_' + $(this).data('index')).click();
});

$(document).on('change', '.approved-replace-input', function () {

    let index = $(this).data('index');
    let newFile = this.files[0];
    if (!newFile) return;

    approvedFiles[index] = newFile;
    syncApprovedInput();
    renderApprovedFiles();
});

/* REMOVE FILE */
$(document).on('click', '.approved-remove-btn', function () {
    approvedFiles.splice($(this).data('index'), 1);
    syncApprovedInput();
    renderApprovedFiles();
});

/* SYNC FILE INPUT */
function syncApprovedInput() {
    let dt = new DataTransfer();
    approvedFiles.forEach(f => dt.items.add(f));
    $('#approvedDrawingInput')[0].files = dt.files;
}

/* RESET WHEN MODAL CLOSES */
$('#approvedDrawingModal').on('hidden.bs.modal', function () {
    approvedFiles = [];
    $('#approvedDrawingPreview').html('');
    $('#approvedDrawingInput').val('');
});
</script>

<script>
// $(document).on('click', '.drawing-approval-btn', function () {

//     let designId = $(this).data('design-id');
//     $('#approval_design_id').val(designId);
//     $('#approvalDrawingList').html('Loading...');

//     $('#drawingApprovalModal').modal('show');

//     $.get(`/design/drawing-approval/${designId}`, function (res) {

//         let html = '';
//         let canApprove = res.can_approve;

//         res.drawings.forEach(d => {

//             html += `
//             <div class="border rounded p-3 mb-3">
//                 <div class="mb-2">
//                     <a href="/storage/${d.file_path}" target="_blank"
//                        class="btn btn-sm btn-outline-secondary">
//                        Preview
//                     </a>
//                 </div>`;

//             if (canApprove) {
//                 html += `
//                 <div class="d-flex gap-3 mb-2">
//                     <label>
//                         <input type="radio"
//                                name="drawings[${d.id}][status]"
//                                value="1"
//                                ${d.drawing_app == 1 ? 'checked' : ''}>
//                         Approve
//                     </label>

//                     <label>
//                         <input type="radio"
//                                name="drawings[${d.id}][status]"
//                                value="2"
//                                ${d.drawing_app == 2 ? 'checked' : ''}>
//                         Reject
//                     </label>
//                 </div>

//                 <textarea class="form-control mb-2"
//                     name="drawings[${d.id}][remarks]"
//                     placeholder="Remarks (required if rejected)"
//                     ${d.drawing_app == 2 ? '' : 'disabled'}>
//                     ${d.drawing_remarks ?? ''}
//                 </textarea>`;
//             } else {
//                 html += `
//                 <span class="badge bg-info">
//                     ${d.drawing_app == 1 ? 'Approved' :
//                       d.drawing_app == 2 ? 'Rejected' : 'Pending'}
//                 </span>

//                 ${d.drawing_remarks ?
//                   `<p class="mt-2 text-danger">Remarks: ${d.drawing_remarks}</p>` : ''}
//                 `;
//             }

//             html += `</div>`;
//         });

//         $('#approvalDrawingList').html(html);


//         if (!canApprove) {
//             $('#approvalSubmitBtn').hide();
//         } else {
//             $('#approvalSubmitBtn').show();
//         }
//     });
// });
</script>
<script>
$(document).on('click', '.drawing-approval-btn', function () {

    let designId = $(this).data('design-id');
    $('#approval_design_id').val(designId);
    $('#approvalDrawingList').html('Loading...');
    $('#drawingApprovalModal').modal('show');

    $.get(`/design/drawing-approval/${designId}`, function (res) {

        let html = '';
        let canApprove = res.can_approve;

        /* 🔥 SEND ALL DRAWINGS (ONLY SALES) */
        if (canApprove) {
            html += `
            <div class="mb-3 text-end">
                <button type="button"
                        class="btn btn-sm btn-dark send-all-drawings"
                        data-design-id="${designId}">
                    📧 Send All Drawings
                </button>
            </div>`;
        }

        res.drawings.forEach(d => {

            html += `
            <div class="border rounded p-3 mb-3">

                <div class="d-flex gap-2 mb-2">
                    <a href="/storage/${d.file_path}" target="_blank"
                       class="btn btn-sm btn-outline-secondary">Preview</a>

                    <a href="/storage/${d.file_path}" download
                       class="btn btn-sm btn-outline-success">Download</a>`;

            /* 🔥 SEND SINGLE DRAWING */
            if (canApprove) {
                html += `
                <button type="button"
                        class="btn btn-sm btn-outline-primary send-single-drawing"
                        data-id="${d.id}">
                    📧 Send Mail
                </button>`;
            }

            html += `</div>`;

            /* ATTACH FILE */
            if (canApprove) {
                html += `
                <input type="file"
                       class="form-control mb-2 sales-file"
                       data-id="${d.id}"
                       accept=".pdf,.jpg,.png,.jpeg,.doc,.docx">`;
            }

            /* APPROVAL */
            if (canApprove) {
                html += `
                <div class="d-flex gap-3">
                    <label>
                        <input type="radio"
                               name="drawings[${d.id}][status]"
                               value="1"
                               ${d.drawing_app == 1 ? 'checked' : ''}>
                        Approve
                    </label>

                    <label>
                        <input type="radio"
                               name="drawings[${d.id}][status]"
                               value="2"
                               ${d.drawing_app == 2 ? 'checked' : ''}>
                        Reject
                    </label>
                </div>

                <textarea class="form-control mt-2"
                    name="drawings[${d.id}][remarks]"
                    placeholder="Remarks"
                    ${d.drawing_app == 2 ? '' : 'disabled'}>
${d.drawing_remarks ?? ''}
                </textarea>`;
            }

            html += `</div>`;
        });

        $('#approvalDrawingList').html(html);
        canApprove ? $('#approvalSubmitBtn').show() : $('#approvalSubmitBtn').hide();
    });
});

/* ================= SEND SINGLE DRAWING ================= */
$(document).on('click', '.send-single-drawing', function () {

    let drawingId = $(this).data('id');
    let fileInput = $(`.sales-file[data-id="${drawingId}"]`)[0];

    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('drawing_id', drawingId);

    if (fileInput && fileInput.files[0]) {
        formData.append('sales_attach', fileInput.files[0]);
    }

    $.ajax({
        url: '/design/send-drawing-mail',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: () => Swal.fire('Sent', 'Drawing mail sent', 'success')
    });
});

/* ================= SEND ALL DRAWINGS ================= */
$(document).on('click', '.send-all-drawings', function () {

    let designId = $(this).data('design-id');
    let formData = new FormData();

    formData.append('_token', '{{ csrf_token() }}');
    formData.append('design_id', designId);

    $('.sales-file').each(function () {
        if (this.files[0]) {
            formData.append(
                `sales_attach[${$(this).data('id')}]`,
                this.files[0]
            );
        }
    });

    $.ajax({
        url: '/design/send-all-drawings-mail',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: () => Swal.fire('Sent', 'All drawings mailed', 'success')
    });
});

/* ================= APPROVAL SUBMIT ================= */
$('#drawingApprovalForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: '/design/drawing-approval',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Drawing approval updated',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        }
    });
});
</script>

<script>
    $(document).on('change', 'input[type=radio]', function () {

    let wrapper = $(this).closest('.border');
    let remarks = wrapper.find('textarea');

    if ($(this).val() == 2) {
        remarks.prop('disabled', false).attr('required', true);
    } else {
        remarks.prop('disabled', true).removeAttr('required');
    }
});

    </script>
    <script>
        $('#drawingApprovalForm').on('submit', function (e) {
    e.preventDefault();

    $.post('/design/drawing-approval', $(this).serialize(), function () {
        Swal.fire({
            icon: 'success',
            title: 'Updated',
            text: 'Drawing approval updated',
            timer: 1500,
            showConfirmButton: false
        }).then(() => location.reload());
    });
});

        </script>
        <script>
            $(document).on('click', '.drawing-rejected-btn', function () {

    let designId = $(this).data('design-id');

    $('#rejected_design_id').val(designId);
    $('#rejectedDrawingList').html('Loading...');
    $('#drawingRejectedModal').modal('show');

    $.get(`/design/rejected-drawings/${designId}`, function (res) {

        let html = '';
        let canEdit = res.can_edit; // only design_user

        res.drawings.forEach(d => {

            let statusBadge =
                d.drawing_app == 1 ? '<span class="badge bg-success">Approved</span>' :
                d.drawing_app == 2 ? '<span class="badge bg-danger">Rejected</span>' :
                                     '<span class="badge bg-warning">Pending</span>';

            html += `
            <div class="border rounded p-3 mb-3">

                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="/storage/${d.file_path}" target="_blank"
                           class="btn btn-sm btn-outline-secondary">
                            Preview
                        </a>`;

            /* ✅ DOWNLOAD SALES ATTACH (DESIGN USER ONLY) */
            if (canEdit && d.sales_attach) {
                html += `
                        <a href="/storage/${d.sales_attach}" download
                           class="btn btn-sm btn-outline-success">
                            Download Attachment
                        </a>`;
            }

            html += `
                    </div>
                    ${statusBadge}
                </div>`;

            if (d.drawing_app == 2 && d.drawing_remarks) {
                html += `
                    <p class="text-danger">
                        <strong>Remarks:</strong> ${d.drawing_remarks}
                    </p>`;
            }

            // ONLY DESIGN USER CAN REPLACE REJECTED
            if (canEdit && d.drawing_app == 2) {
                html += `
                <input type="file"
                       name="replace_files[${d.id}]"
                       class="form-control mt-2"
                       accept=".pdf,.jpg,.png,.jpeg,.dwg">`;
            }

            html += `</div>`;
        });

        $('#rejectedDrawingList').html(html);

        // hide update button for readonly users
        if (!canEdit) {
            $('#rejectedUpdateBtn').hide();
        } else {
            $('#rejectedUpdateBtn').show();
        }
    });
});

            </script>
            <script>
                $('#drawingRejectedForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: '/design/rejected-drawings/update',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,

        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Rejected drawings updated and sent for approval',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        }
    });
});

                </script>
                <script>
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-attachment')) {
        let id = e.target.dataset.id;

        if (!confirm('Remove this file?')) return;

        fetch(`/orf-attachment/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    }
});
</script>
<script>
$(document).ready(function () {
    $('.special-product-select').select2({
        placeholder: "Select Special Product",
        allowClear: true,
        width: '100%'
    });
});
</script>
<script>
$(document).ready(function () {

    $('#segment').change(function () {

        let segmentId = $(this).val();
        let segmentName = $("#segment option:selected").text().trim();

        $('#product').html('<option value="">-- Choose Product --</option>');

        if(segmentName === 'MHE Standard'){

            $('#productDiv').show();

            $.ajax({
                url: '/products/by-segment/' + segmentId,
                type: 'GET',
                success: function(products){

                    $.each(products, function(index, product){

                        $('#product').append(
                            '<option value="'+product.id+'">'+product.productname+'</option>'
                        );

                    });

                }
            });

        }else{

            $('#productDiv').hide();
            $('#product').val('');

        }

    // Session Keep-Alive & Dynamic CSRF Auto-Sync (Fix 1 & 3: prevents 419 Page Expired and stale form tokens)
    (function() {
        // Global AJAX CSRF header setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function updateCsrfTokens(token) {
            if (!token) return;
            $('meta[name="csrf-token"]').attr('content', token);
            $('input[name="_token"]').val(token);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
        }

        function pingKeepAlive() {
            $.ajax({
                url: '{{ route("session.keepalive") }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.csrf) {
                        updateCsrfTokens(data.csrf);
                    }
                },
                error: function(xhr) {
                    console.warn('Session keep-alive status:', xhr.status);
                }
            });
        }

        // Ping every 5 minutes while tab is open
        setInterval(pingKeepAlive, 300000);

        // Dynamic Form CSRF Sync: Synchronize fresh token before any form submission
        $(document).on('submit', 'form', function () {
            let currentToken = $('meta[name="csrf-token"]').attr('content');
            if (currentToken) {
                let tokenInput = $(this).find('input[name="_token"]');
                if (tokenInput.length) {
                    tokenInput.val(currentToken);
                } else if ($(this).attr('method') && $(this).attr('method').toUpperCase() !== 'GET') {
                    $(this).prepend('<input type="hidden" name="_token" value="' + currentToken + '">');
                }
            }
        });
    })();
</script>

    @yield('scripts')
</body>

</html>