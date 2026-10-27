@extends('layouts.app')
@section('title', 'Product Stock Details')
@section('main-content')

    @can('permissions-display')

        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Permissions</h5>
                            </div>
                            <div class="col-lg-4"></div>
                            @can('permissions-create')
                                <div class="col-lg-2">
                                    <a data-bs-toggle="modal" data-bs-target="#add_role_modal"
                                        class="btn btn-primary d-block add-employee-btn">Create Permission</a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container-fluid mt-5">
            <div class="card">
                <div class="card-body">
                    <table id="permissions-table" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Permission Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            $permissions = App\Models\Permission::get();
                        @endphp
                        <tbody>
                            @foreach ($permissions as $key => $data)
                                <tr style="text-align: center;">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ date("d-m-Y", strtotime($data->created_at)) }}</td>
                                    @canany('permissions-edit')
                                        <td>
                                            @can(['permissions-edit', 'permissions-delete'])
                                                <button class="btn btn-info me-2 role_edit_btn" data-bs-toggle="modal"
                                                    data-bs-target="#edit_role_modal" data-rolename="{{ $data->name }}"
                                                    data-roleid="{{ $data->id }}">Edit</button>
                                            @endcan
                                            @can('permissions-delete')
                                                <a href="/deleteRole/{{ $data->id }}"
                                                    onclick="return confirm('Are you sure, You want to delete this?')"><button
                                                        class="btn btn-danger me-2">Delete</button></a>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add_role_modal" aria-hidden="true" aria-labelledby="add_role_modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_role_modal">Add Permission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/createPermission">
                            <div class="row mb-4">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Permission Name<span style="color:red">*</span>
                                        </label>

                                        <input type="text" name="name" class="form-control Permission_name" required>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit_role_modal" aria-hidden="true" aria-labelledby="edit_role_modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="edit_role_modal">Edit Permission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/editPermission">
                            <div class="row mb-4">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="form-group mt-4">
                                        <label for="customerSelect" class="form-label">
                                            Role Name<span style="color:red">*</span>
                                        </label>
                                        <input type="hidden" name="roleId" class="editRoleId">
                                        <input type="text" name="name" class="form-control role_name" required>
                                    </div>
                                </div>

                            </div>

                            <button type="submit"
                                class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">Update</button>
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
        let table = new DataTable('#permissions-table');
    </script>
@endsection
