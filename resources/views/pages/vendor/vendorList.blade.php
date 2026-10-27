@extends('layouts.app')
@section('title', 'Vendors Details')
@section('main-content')

    @can('vendors-display')

        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Vendor Details</h5>
                            </div>
                            <div class="col-lg-4">

                            </div>
                            @can('vendors-create')
                                <div class="col-lg-2">
                                    <a href="/add-vendors" class="btn btn-primary d-block add-employee-btn">Add Vendor</a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <div class="container-fluid mt-5">
            <div class="card">
                <div class="card-header" style="width: 15%;">
                    <select class="form-select vendor_type_list">
                        <option value="all">All Vendors</option>
                        <option value="active">Active Vendors</option>
                        <option value="inactive">Inactive Vendors</option>
                    </select>
                </div>
                <div class="card-body">
                    <table id="vendor-table" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Company Name</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Contact Name</th>
                                <th>Contact Number</th>
                                <th>Gst Number</th>
                                <th>Status</th>
                                @canany(['vendors-edit', 'vendors-delete'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $vendors = App\Models\Vendor::with(["stateData", "cityData"])->get();
                            @endphp
                            @foreach ($vendors as $key => $data)
                                @php
                                    $i = 1;
                                    $stateName = $data->stateData->state;
                                    $cityName = $data->cityData->city_name;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->company_name }}</td>
                                    <td>{{ $stateName }}</td>
                                    <td>{{ $cityName }}</td>
                                    <td>{{ $data->point_of_contact }}</td>
                                    <td>{{ $data->contact_number }}</td>
                                    <td>{{ $data->gst_number }}</td>
                                    <td>
                                        <span class="badge @if ($data->vendor_status == "active")
                                            bg-success @else bg-danger
                                        @endif">{{ ucfirst($data->vendor_status) }}</span>
                                    </td>
                                    @canany(['vendors-edit', 'vendors-delete'])
                                        <td>
                                            <div class="vendor-action d-flex">
                                                @can('vendors-edit')
                                                    <a href="/edit-vendors/{{ $data->id }}"><button
                                                            class="btn btn-info me-2">Edit</button></a>
                                                @endcan
                                                @can('vendors-delete')
                                                    <button data-url="/delete-vendors/{{ $data->id }}"
                                                        class="btn btn-danger common-delete-btn">Delete</button>
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
    @endcan
@endsection

@section("scripts")
    <script>
        let table = new DataTable('#vendor-table');
    </script>
@endsection