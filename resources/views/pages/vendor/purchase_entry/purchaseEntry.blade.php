@extends('layouts.app')
@section('title', 'Vendors Details')
@section('main-content')

    @can('purchase-entry-display')

        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Purchase Entry Details</h5>
                            </div>
                            <div class="col-lg-4">

                            </div>
                            @can('purchase-entry-create')
                                <div class="col-lg-2">
                                    <a href="/add-purchase-entry" class="btn btn-primary d-block add-employee-btn">Add Purchase
                                        Entry</a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container-fluid mt-5">
            <div class="card">
                <div class="card-header">Purchase Entry Details</div>
                <div class="card-body details-table">
                    <table id="purchase-entry-table" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>PO Number</th>
                                <th>PO Date</th>
                                <th>Vendor</th>
                                <th>Requested Quantity</th>
                                <th>Received Quantity</th>
                                <th>Pending Quantity</th>
                                <th>Unit Price</th>
                                <th>Product Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $peDetails = App\Models\PurchaseEntry::orderBy('id','desc')->get();
                            @endphp
                            @foreach ($peDetails as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->po_no }}</td>
                                    <td>{{ date('d-m-Y', strtotime($data->poDetails?->created_at ?? '')) }}</td>
                                    <td>{{ $data->vendorDetails?->company_name }}</td>
                                    <td>{{ $data->requested_qty }}</td>
                                    <td>{{ $data->received_qty }}</td>
                                    <td>{{ $data->pending_qty }}</td>
                                    <td>₹{{ (int)$data->unit_price }}</td>
                                    <td>₹{{ $data->total_amount }}</td>
                                    @canany(['purchase-entry-edit', 'purchase-entry-delete'])
                                        <td>
                                            <div class="vendor-action d-flex">
                                                @can('purchase-entry-edit')
                                                    <a href="/edit-pe-data/{{ $data->id }}"><button
                                                            class="btn btn-info me-2">Edit</button></a>
                                                @endcan
                                                @can('purchase-entry-delete')
                                                <button data-url="/pe-delete/{{ $data->id }}/{{ $data->received_qty }}/{{ $data->product_id }}" class="btn btn-danger common-delete-btn">Delete</button>
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
        let table = new DataTable('#purchase-entry-table');
    </script>
@endsection