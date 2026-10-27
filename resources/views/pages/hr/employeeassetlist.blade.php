@extends('layouts.app')
@section('title', 'Add Asset List')
@section('main-content')

    <div class="container">
        <div class="table table-view shadow col-md-12">
            <div style="display: flex;justify-content: space-between;">
                <h5 class="mb-3 al"><i class="fa fa-user-plus" style="color: #ea1f27;" aria-hidden="true"></i> Employee Assets
                    Details</h5>
                <a href="/addassets" class="btn btn-success mb-3"
                    style="width:300px;background: #192a9f; border: none;margin-right:10px;">
                    &nbsp;&nbsp;Add Assets</a>
            </div>
            <table class="" id="employeeassetTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        {{-- <th>Asset Name</th>
														<th>Qty</th>
														<th>Status</th> --}}
                        <th>Assets</th>
                        <th>Edit</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($assets->groupBy('employee_id') as $emp_id => $group)
                        @php
                            $employee = $group->first()->employee;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->emp_id ?? 'N/A' }}</td>
                            <td>{{ $employee->name ?? 'N/A' }}</td>
                            <td>
                                <a class="btn assetdetail" data-emp="{{ $emp_id }}" data-bs-toggle="modal"
                                    data-bs-target="#assetmodal" style="border-radius: 5px; background:#e7368d;">
                                    <i class="fa fa-file" style="color: #fff;" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{ url('/employee-assets/edit/' . $emp_id) }}" class="btn btn-primary btn-sm"
                                    style="border-radius: 5px;">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    <div class="modal" id="assetmodal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Asset Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body popcon">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table id="datable_1" class="table table-hover display pb-30">
                                <thead>
                                    <tr>
                                        {{-- <th>EMP ID</th>
                                        <th>Name</th> --}}
                                        <th>Asset Name</th>
                                        <th>Quantity</th>
                                        <!--<th>Request Date</th>-->
                                        <th>Status</th>
                                        <!--<th>E-mail</th>-->
                                        <!-- <th>Remove</th> -->

                                    </tr>
                                </thead>

                                <tbody id="assetdetail1">


                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>


@endsection
