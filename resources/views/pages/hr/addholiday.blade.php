@extends('layouts.app')
@section('title', 'Add Holidays')
@section('main-content')


    @if (session('existingDates'))
        <script>
            var existingDates = @json(session('existingDates'));
            alert('The following dates already exist: ' + existingDates.join(', '));
        </script>
    @endif
    {{-- <div style="display: flex;justify-content: space-between;">
        <h5 class="mb-3 al" ><i class="fa fa-calendar" style="color: #ea1f27;" aria-hidden="true"></i> Add Holiday</h5>
        <a  class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addholi" style="width:300px;background: #192a9f; border: none;margin-right:10px;" ><span class="btn-text"><i class="fa fa-plus" style="color: #fff;" aria-hidden="true"></i>
            &nbsp;&nbsp;Add Holiday</span></a>
</div> --}}
    <div class="text-end px-4 pb-3">
        <a class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addholi"
            style="width:300px;background: #192a9f; border: none;margin-right:10px;"><span class="btn-text"><i
                    class="fa fa-plus" style="color: #fff;" aria-hidden="true"></i>
                &nbsp;&nbsp;Add Holiday</span></a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover" id="holidayTable">
                <thead class="table-light">
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        <th>Holiday</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    ?>
                    @foreach ($addholiday as $a)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $a->holi_date }}</td>
                            <td><span
                                    style="background: #e7368d;color:#fff;padding:4px;border-radius:10px;">{{ $a->holi_name }}</span>
                            </td>

                            <td><a style="border-radius: 5px;background-color: #2d368e;" class="btn btn-primary editholiday"
                                    data-bs-toggle="modal" data-bs-target="#editholi" data-id="{{ $a->id }}"
                                    data-hdate="{{ $a->holi_date }}" data-hday="{{ $a->holi_day }}"
                                    data-hname="{{ $a->holi_name }}">✏️</a></td>
                            <td>
                                <button class="btn btn-danger delete-holiday" data-id="{{ $a->id }}"
                                    style="border-radius: 5px;">
                                    🗑️
                                </button>
                            </td>

                        </tr>
                    {{-- @empty
                        <tr>
                            <td colspan="5" class="text-center">No employees found.</td>
                        </tr> --}}
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Add Employee Button in Bottom Right -->

    </div>



    <div class="modal fade" id="addholi" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="Addholidayinsert" id="adddholid">
                @csrf
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Holiday</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-scroll mt-3" id="productTable">
                            <thead>
                                <tr>
                                    <th scope="col">Date<span style="margin-left: 10px; color:red;">*</span></th>
                                    {{-- <th scope="col">Day<span  style="margin-left: 10px; color:red;">*</span></th> --}}
                                    <th scope="col">Holiday<span style="margin-left: 10px; color:red;">*</span></th>
                                    {{-- <th scope="col"><button type="button" class="btn btn-primary" id="addholiday">+</button></th> --}}
                                </tr>
                            </thead>
                            <tbody id="holi1">
                                <tr>
                                    <td><input class="form-control dateholi" type="date"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" name="dateholi[]" id="dateholi"
                                            placeholder="" required></td>
                                    {{-- <td><input class="form-control dayholi" type="text" name="dayholi[]" id="dayholi" placeholder="" required></td> --}}

                                    <td><input class="form-control holiname" type="text" name="holiname[]" id="holiname"
                                            placeholder="Holiday" required></td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>




    <div class="modal fade" id="editholi" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog ">
            <form method="POST" action="editholidayinsert" id="editholid">
                @csrf
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Holiday</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input class="" type="hidden" name="editholiid" id="edithoid">



                        <div class="form-group">
                            <label for="" class="form-label">Date<span style="color: #ea1f27;"> *</span></label>
                            <input class="form-control dateholied" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                type="date" name="dateholied" id="dateholied" placeholder="" required>

                        </div>
                        {{-- <div class="form-group mt-2">
                    <label for="" class="form-label">Day<span style="color: #ea1f27;"> *</span></label>
                    <input class="form-control dayholied" type="text" name="dayholied" id="dayholied" placeholder="" required>

                </div> --}}
                        <div class="form-group mt-2">
                            <label for="" class="form-label">Holiday<span style="color: #ea1f27;">
                                    *</span></label>
                            <input class="form-control holinameed" type="text" name="holinameed" id="holinameed"
                                placeholder="Holiday" required>

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

@endsection
