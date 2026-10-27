@extends('layouts.app')

@section('main-content')

<div class="container">

    <h5 class="mb-3">Date of Validity</h5>

    <!-- Add Form -->
    <form action="{{ route('dov.store') }}" method="POST" class="mb-3 d-flex gap-2">
        @csrf
        <input type="text" name="value" class="form-control" placeholder="Enter DOV (e.g. 30 Days)" required>
        <button class="btn btn-primary">Add</button>
    </form>

    <!-- Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                {{-- <th>ID</th> --}}
                <th>S.No</th>
                <th>Value</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dovs as $dov)
            <tr>
                {{-- <td>{{ $dov->id }}</td> --}}
                <td>{{ $loop->iteration }}</td>
                <td>{{ $dov->value }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editDov({{ $dov->id }})">Edit</button>

                    <form action="/date-of-validity/delete/{{ $dov->id }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
<div class="modal-dialog">
<form method="POST" id="editForm">
@csrf

<div class="modal-content">
<div class="modal-header"><h5>Edit DOV</h5></div>

<div class="modal-body">
    <input type="text" name="value" id="editValue" class="form-control" required>
</div>

<div class="modal-footer">
    <button class="btn btn-success">Update</button>
</div>
</div>

</form>
</div>
</div>

<script>
function editDov(id){
    fetch('/date-of-validity/edit/'+id)
    .then(res=>res.json())
    .then(data=>{
        document.getElementById('editValue').value = data.value;
        document.getElementById('editForm').action = '/date-of-validity/update/'+id;
        $('#editModal').modal('show');
    });
}
</script>

@endsection
