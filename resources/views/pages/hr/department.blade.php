@extends('layouts.app')
@section('title', 'Add Department')
@section('main-content')
    <style>
        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-bottom: 1px solid #ccc !important;
        }

        .al {
            /* color: #2d368e; */
            font-weight: bold;
        }
    </style>

    <div class="container" style="">
        <div class="table table-view shadow col-md-12">
            <div style="display: flex;justify-content: space-between;">
                <h5 class="mb-3 al"><i class="fa fa-address-card" style="" aria-hidden="true"></i> Add Department
                </h5>
                <button class="btn btn-success mb-3" style="width:300px; border: none;margin-right:10px;"
                    data-bs-toggle="modal" data-bs-target="#desimodal">
                        &nbsp;&nbsp;Add Department</button>
            </div>
            <table id="example" class="display table table-bordered">
                {{-- table table-bordered table-hover table-striped --}}
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Department</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Edit</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    ?>
                    @foreach ($departments as $c)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $c->department }}</td>
                           <td>{{ $c->logintime->format('h.i A') }}</td>
                           <td>{{ $c->logouttime->format('h.i A') }}</td>
                            <td><button style="border-radius: 5px;"
                                    class="btn btn-primary designedit" data-bs-toggle="modal"
                                    data-bs-target="#edit-desimodal" data-start_time="{{ $c->logintime }}"
                                    data-end_time="{{ $c->logouttime }}"
                                    data-morning_buffer_time="{{ $c->loginbuffer }}"
                                    data-evening_buffer_time="{{ $c->logoutbuffer }}"
                                    data-designatio="{{ $c->department }}" data-id="{{ $c->id }}" >✏️</button></td>
                            <td>
    <button class="btn btn-danger delete-department" data-id="{{ $c->id }}" style="border-radius: 5px;">
        🗑️
    </button>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="desimodal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog ">
            <form method="POST" action="{{ route('department.store') }}" id="adddesignation">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Department Name<span
                                            style="color: #ea1f27;"> *</span></label>
                                    <input type="text" class="form-control" autocomplete="off" minlength="2"
                                        maxlength="30" required name="designation" value="" id="designation">
                                </div>
                            </div>

                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Start Time </label>
                                    <input type="time" class="form-control" name="starttime" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Start Buffer Time </label>
                                    <input type="time" class="form-control" name="startbuftime" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">

                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">End Time </label>
                                    <input type="time" class="form-control" name="endtime" value="">
                                </div>
                            </div>

                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">End Buffer Time </label>
                                    <input type="time" class="form-control" name="endbuftime" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="edit-desimodal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
        tabindex="-1">
        <div class="modal-dialog ">
            <form method="POST" action="{{ route('department.update') }}" id="editdesignation">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" class="editids" name="editid">
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Department<span
                                            style="color: #ea1f27;"> *</span></label>
                                    <input type="text" class="form-control designa" autocomplete="off" minlength="2"
                                        maxlength="30" required name="designation" value="" id="designation">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="starttimeu" name="starttimeu"
                                        value="">
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">Start Buffer Time</label>
                                    <input type="time" class="form-control" id="startbuftimeu" name="startbuftimeu"
                                        value="">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="endtimeu" name="endtimeu"
                                        value="">
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" class="form-label">End Buffer Time</label>
                                    <input type="time" class="form-control" id="endbuftimeu" name="endbuftimeu"
                                        value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('scripts')
<script>
 $(document).ready(function () {
        $('.delete-department').on('click', function () {
            const id = $(this).data('id');
            const row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this department?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('department') }}/" + id,  // ✅ Blade renders this properly
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                row.remove();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
