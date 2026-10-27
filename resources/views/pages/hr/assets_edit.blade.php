@extends('layouts.app')
@section('title', 'Edit Assets')
@section('main-content')

<div class="container">
    <div class="row mt-3">
    <div class="col-lg-6">
    <h4>Assets for {{ $employee->name }} ({{ $employee->emp_id }})</h4>
    </div>
    <div class="col-lg-6 mb-3" style="text-align: end;">
     <a class="btn btn-primary" href="javascript:history.back()"><i
                                            class="fa fa-hand-o-left mr-2" aria-hidden="true"
                                            style="color:#ffff;"></i>&nbsp;&nbsp;Back</a>
    </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Asset Name</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Return</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $asset->asset->asset_name }}@if($asset->asset->serial_number) | {{ $asset->asset->serial_number }}@endif</td>
                    <td>{{ $asset->quantity }}</td>
                    <td>{{ $asset->status }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm returnBtn"
                            data-bs-toggle="modal"
                            data-bs-target="#returnModal"
                            data-id="{{ $asset->id }}"
                            data-name="{{ $asset->asset->asset_name }}"
                            data-qty="{{ $asset->quantity }}">
                            Return
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('employee-assets.return') }}">
        @csrf
        <input type="hidden" name="asset_id" id="modal_asset_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Asset:</strong> <span id="modal_asset_name"></span></p>
                <p><strong>Assigned Quantity:</strong> <span id="modal_qty"></span></p>

                <div class="mb-3">
                    <label for="return_qty" class="form-label">Return Quantity</label>
                    <input type="number" name="return_qty" id="return_qty" class="form-control" required min="1">
                </div>
                <div class="mb-3">
                    <label for="return_status" class="form-label">Return Status</label>
                    <select name="return_status" id="return_status" class="form-control" required>
                        <option value="Returned">Returned</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Lost">Lost</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit Return</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection
