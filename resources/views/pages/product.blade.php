@extends('layouts.app')
@section('main-content')
    @can('products-display')
        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row" style="text-align: center;">
                            <div class="col-lg-2">
                                <h5 class="mt-2">Products</h5>
                            </div>

                            <div class="col-lg-3">
                                @can('product-categories-display')
                                    <a href="{{ url('/product-categories') }}" class="btn btn-primary  waves-effect waves-light"
                                        >Product Segment</a>
                                @endcan
                            </div>
                            <div class="col-lg-3">
                                @can('product-categories-display')
                                    <a href="{{ url('/product-sub-categories') }}" class="btn btn-primary  waves-effect waves-light"
                                        >Product group</a>
                                @endcan
                            </div>
                            <div class="col-lg-2">
                                 <a href="{{ url('/bulk-upload-master') }}" class="btn btn-warning  waves-effect waves-light"
                                        ><i class="mdi mdi-plus"></i> Bulk Upload</a>
                                {{-- <div class="btn btn-warning  waves-effect waves-light text-dark" data-bs-toggle="modal"
                                    data-bs-target="#product_bulk_upload_modal">
                                    <i class="mdi mdi-plus"></i> Bulk Upload
                                </div> --}}
                            </div>
                            <div class="col-lg-2">
                                @can('products-create')
                                    <a href="/addproduct" class="btn btn-primary  waves-effect waves-light">
                                        <i class="mdi mdi-plus"></i> Add Product
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container-fluid bg-white form-card mt-4">
            <div class="row">
                <div class="col-lg-12 p-4">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">S.No</th>
                                <th style="text-align: center;">Part No</th>
                                <th style="text-align: center;">HSN</th>
                                <th style="text-align: center;">Product Segment</th>
                                <th style="text-align: center;">Product Group</th>
                                <th style="text-align: center;">Product Model</th>
                                <th style="text-align: center;">Rate</th>
                                <th style="text-align: center;">Qty</th>
                                @canany(['products-edit', 'products-delete'])
                                    <th style="text-align: center;">Action</th>
                                @endcanany

                            </tr>
                        </thead>
                        <tbody>

                            @php $i = 1; @endphp

                            @foreach ($products as $data)

                                <tr style="text-align: center;">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $data->partno ?? '-' }}</td>
                                    <td>{{ $data->hsn }}</td>
                                    <td>{{ $data->category->category_name ?? '-' }}</td>
                                    <td>{{ $data->subcategory->subcategory_name ?? '-' }}</td>
                                    <td>{{ $data->productmodel }}</td>
                                    <td data-order="{{ (float) $data->rate }}">₹{{ \App\Helpers\IndianNumberHelper::formatIndianCurrency($data->rate) }}</td>
                                    <td>
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="">{{ $data->stock->available_stock }}</span>
                                    </td>
                                    @canany(['products-edit', 'products-delete'])
                                        <td>
                                            <div class="buttons_td">
                                                @can('products-edit')
                                                    <a href="/updateProductData/{{ $data->id }}"><button type="button"
                                                            class="btn btn-bull_yellow edit-btn waves-effect waves-light">
                                                            <i class="mdi mdi-lead-pencil"></i>
                                                        </button></a>
                                                @endcan

                                                @can('products-delete')
                                                    <button type="button" class="btn btn-bull_red prod-delete-btn waves-effect waves-light"
                                                        onclick="confirmDelete({{ $data->id }})">
                                                        <i class="mdi mdi-trash-can"></i>
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

        <div class="modal fade bs-example-modal-center2" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="myForm" method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-2">
                                <label class="form-label">Product Name <span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="productname" placeholder="Productname" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">Rate <span class="mandatory">*</span></label>
                                <input type="text" class="form-control " name="rate" placeholder="Rate" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">HSN <span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="hsn" placeholder="HSN" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">GST <span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="gst" placeholder="GST" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('products.update') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-2">
                                <input type="hidden" class="id" name="id">
                                <label for="" class="form-label">Product Name <span class="mandatory">*</span></label>
                                <input type="text" class="form-control productname" placeholder="Productname" name="productname"
                                    required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">Rate <span class="mandatory">*</span></label>
                                <input type="text" class="form-control rate" name="rate" placeholder="Rate" required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="input4" class="form-label">HSN <span class="mandatory">*</span></label>
                                <input type="text" class="form-control hsn" name="hsn" placeholder="HSN" required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="input4" class="form-label">GST <span class="mandatory">*</span></label>
                                <input type="text" class="form-control gst" name="gst" placeholder="GST" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endcan


@endsection


<div class="modal fade" id="product_bulk_upload_modal" aria-hidden="true" aria-labelledby="product_bulk_upload_modal"
    tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="product_bulk_upload_modal">product Bulk Upload</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/product-bulk-upload" enctype="multipart/form-data">
                    <div class="row mb-4">
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group mt-2">
                                <label for="exampleFormControlInput1" class="form-label">Choose File<span
                                        style="color:red">*</span></label>
                                <input type="file" name="product_data_file" class="form-control" required>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary m-auto d-block mt-4 mb-2">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="/assets/js/pages/pass-addon.init.js"></script>

    <!-- product action alert -->

    @if(session('delete-success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: "Deleted!",
                    text: {{ session('delete-success') }},
                    icon: "success"
                });
            });
        </script>
    @endif

    @if(session('delete-error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Oops...!',
                    text: '{{ session('delete-error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <script>

        function confirmDelete(prodId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/productDelete/" + prodId;
                }
            });
        }


    </script>

    <script>
        $('.country').on('change', function () {
            var country = $(this).val();

            $('.state').empty();
            $('.state').append(`<option value="" disabled selected>Processing...</option>`);
            $.ajax({
                type: 'GET',
                url: '/GetState/' + country,
                success: function (response) {
                    $('.state').empty();
                    $('.state').append(
                        `<option value="" disabled selected>Select State</option>`
                    );
                    response.forEach(element => {
                        $('.state').append(
                            `<option value="${element['id']}">${element['name']}</option>`
                        );
                    });
                }
            })
        });

        $('.state').on('change', function () {
            var state = $(this).val();

            $('.district').empty();
            $('.district').append(`<option value="" disabled selected>Processing...</option>`);
            $.ajax({
                type: 'GET',
                url: '/Getdistrict/' + state,
                success: function (response) {
                    $('.district').empty();
                    $('.district').append(
                        `<option value="" disabled selected>Select District</option>`
                    );
                    response.forEach(element => {
                        $('.district').append(
                            `<option value="${element['id']}">${element['district']}</option>`
                        );
                    });
                }
            })
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.confirmpassword').on('keyup', function () {
                const password = $('.password').val();
                const confirmPassword = $('.confirmpassword').val();
                const errorMsg = $('.confpassword-error');
                const submitButton = $('.add-employee-btn');

                if (!password) {
                    errorMsg.text('Enter the password first').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else if (password !== confirmPassword) {
                    errorMsg.text('Confirm password is wrong').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else {
                    errorMsg.text('Password is correct').css('color', 'green');
                    submitButton.attr('type', 'submit'); // Change to submit to allow form submission
                }
            });

            $('.password').on('keyup', function () {
                $('.confpassword-error').text('');
                $('.add-employee-btn').attr('type', 'button'); // Reset to button on typing in password
            });
        });
    </script>

    <script>
        $(document).on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            var productname = $(this).data('productname');
            var gst = $(this).data('gst');
            var alldia = $(this).data('alldis');
            var hsn = $(this).data('hsn');
            var rate = $(this).data('rate');


            $('.id').val(id);
            $('.productname').val(productname);
            $('.hsn').val(hsn);
            $('.gst').val(gst);
            $('.all_dis').val(alldia);
            $('.rate').val(rate);
        });
    </script>

    <script>
        $(document).on('click', '.pass_edit', function () {
            var id = $(this).data('id');

            $('.id').val(id);
        })
    </script>

@endsection
