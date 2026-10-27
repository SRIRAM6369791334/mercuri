@extends('layouts.app')
@section('title', 'Lead designation & department')
@section('main-content')
<style>
.nav-pills .nav-link.active {
    background-color: #4f46e5;
}
.table-hover tbody tr:hover {
    background-color: #f9fafb;
}
.nav-pills .nav-link {
    border: 1px solid #1b5684;
    border-radius: 6px;
}
</style>

@php
    $quotationMasters = [
        'freight_category' => [
            'label' => 'Freight/Transportation',
            'tab' => 'freight-category',
            'items' => $freightCategories,
            'name_field' => 'categoty',
        ],
        'freight_sub_category' => [
            'label' => 'Included or Extra',
            'tab' => 'freight-sub-category',
            'items' => $freightSubCategories,
            'name_field' => 'sub_category',
            'parent_field' => 'freight_category_name',
        ],
        'unloading' => [
            'label' => 'Unloading at site',
            'tab' => 'unloading',
            'items' => $unloadingOptions,
            'name_field' => 'name',
        ],
        'installation' => [
            'label' => 'Installation Scope',
            'tab' => 'installation',
            'items' => $installationScopes,
            'name_field' => 'name',
        ],
        'orc' => [
            'label' => 'ORC',
            'tab' => 'orc',
            'items' => $orcOptions,
            'name_field' => 'name',
        ],
        'quotation_status' => [
            'label' => 'Quotation Status',
            'tab' => 'quotation-status',
            'items' => $quotationStatuses,
            'name_field' => 'name',
        ],
    ];
@endphp

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3">
        {{ $errors->first() }}
    </div>
@endif

<ul class="nav nav-pills mb-4 gap-2" role="tablist">
    <li class="nav-item">
        <button class="nav-link active px-4" data-bs-toggle="tab" data-bs-target="#designation" type="button">
            <i class="fas fa-user-tie me-1"></i> Designation
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link px-4" data-bs-toggle="tab" data-bs-target="#department" type="button">
            <i class="fas fa-building me-1"></i> Department
        </button>
    </li>
    @foreach($quotationMasters as $type => $master)
        <li class="nav-item">
            <button class="nav-link px-4" data-bs-toggle="tab" data-bs-target="#{{ $master['tab'] }}" type="button">
                {{ $master['label'] }}
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="designation">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Designation List</h6>
            <button class="btn btn-primary btn-sm" onclick="openAdd(80)">
                <i class="fas fa-plus"></i> Add Designation
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80">Order</th>
                        <th>Designation</th>
                        <th width="140" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($designations as $d)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $d->orderno }}</span></td>
                        <td>{{ $d->dropdowndata }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="editData({{ $d->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('master.delete', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">No designations found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="department">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Department List</h6>
            <button class="btn btn-primary btn-sm" onclick="openAdd(81)">
                <i class="fas fa-plus"></i> Add Department
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80">Order</th>
                        <th>Department</th>
                        <th width="140" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($departments as $d)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $d->orderno }}</span></td>
                        <td>{{ $d->dropdowndata }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="editData({{ $d->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('master.delete', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">No departments found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($quotationMasters as $type => $master)
        <div class="tab-pane fade" id="{{ $master['tab'] }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">{{ $master['label'] }} List</h6>
                <button class="btn btn-primary btn-sm" onclick="openQuotationAdd('{{ $type }}', '{{ $master['label'] }}')">
                    <i class="fas fa-plus"></i> Add {{ $master['label'] }}
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Order</th>
                            @if(isset($master['parent_field']))
                                <th>Freight/Transportation</th>
                            @endif
                            <th>{{ $master['label'] }}</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($master['items'] as $index => $item)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $index + 1 }}</span></td>
                            @if(isset($master['parent_field']))
                                <td>{{ $item->{$master['parent_field']} }}</td>
                            @endif
                            <td>{{ $item->{$master['name_field']} }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning me-1" onclick="editQuotationData('{{ $type }}', {{ $item->id }}, '{{ $master['label'] }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('master.quotation-field.delete', [$type, $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ isset($master['parent_field']) ? 4 : 3 }}" class="text-center text-muted">
                                No {{ strtolower($master['label']) }} found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="masterModal">
    <div class="modal-dialog">
        <form method="POST" id="masterForm">
            @csrf
            <input type="hidden" name="formid" id="formid">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="name" id="masterName" maxlength="30" class="form-control mb-2" placeholder="Name" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="quotationMasterModal">
    <div class="modal-dialog">
        <form method="POST" id="quotationMasterForm">
            @csrf
            <input type="hidden" name="type" id="quotationType">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="quotationModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3 d-none" id="freightCategoryWrapper">
                        <label class="form-label">Freight/Transportation</label>
                        <select name="freight_cat_id" id="quotationFreightCategory" class="form-select">
                            <option value="">-- Choose --</option>
                            @foreach($freightCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->categoty }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="quotationName" maxlength="100" class="form-control" placeholder="Name" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openAdd(formid) {
    $('#modalTitle').text(formid == 80 ? 'Add Designation' : 'Add Department');
    $('#formid').val(formid);
    $('#masterName').val('');
    $('#masterForm').attr('action', '{{ route("master.store") }}');
    $('#masterModal').modal('show');
}

function editData(id) {
    fetch('/master/edit/' + id)
        .then(res => res.json())
        .then(data => {
            $('#masterName').val(data.dropdowndata);
            $('#formid').val(data.formid);
            $('#modalTitle').text(data.formid == 80 ? 'Edit Designation' : 'Edit Department');
            $('#masterForm').attr('action', '/master/update/' + id);
            $('#masterModal').modal('show');
        });
}

function setQuotationParentVisibility(type) {
    const needsParent = type === 'freight_sub_category';
    $('#freightCategoryWrapper').toggleClass('d-none', !needsParent);
    $('#quotationFreightCategory').prop('required', needsParent);
}

function openQuotationAdd(type, label) {
    $('#quotationModalTitle').text('Add ' + label);
    $('#quotationType').val(type);
    $('#quotationName').val('');
    $('#quotationFreightCategory').val('');
    setQuotationParentVisibility(type);
    $('#quotationMasterForm').attr('action', '{{ route("master.quotation-field.store") }}');
    $('#quotationMasterModal').modal('show');
}

function editQuotationData(type, id, label) {
    fetch('/master/quotation-field/' + type + '/' + id + '/edit')
        .then(res => res.json())
        .then(data => {
            $('#quotationModalTitle').text('Edit ' + label);
            $('#quotationType').val(type);
            $('#quotationName').val(data.name);
            $('#quotationFreightCategory').val(data.freight_cat_id || '');
            setQuotationParentVisibility(type);
            $('#quotationMasterForm').attr('action', '/master/quotation-field/' + type + '/' + id + '/update');
            $('#quotationMasterModal').modal('show');
        });
}
</script>
@endsection
