@extends('layouts.app')
@section('main-content')
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Form Details</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <a href="{{ url('/setup/fields') }}" class="btn btn-primary">Create Form</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="container mt-3">
            <div class="row">
                @if ($formcategorie)
                    @foreach ($formcategorie as $c)
                        <div class="col-lg-6 ">
                            <ul class="list-group mt-4">
                                <li class="list-group-item active" aria-current="true">{{ $c->categoryname }} Details</li>

                                @if ($formdetails = App\Models\Field::where('categoryid', $c->id)->get())
                                    @foreach ($formdetails as $form)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                {{ $form->fieldname }} <span style="color:red">
                                                    @if ($form->mandatory == 1)
                                                        *
                                                    @else
                                                    @endif
                                                </span>
                                            </div>
                                            <a href="/editform/{{ $form->id }}"><span
                                                    class="badge me-2 bg-info rounded-pill "
                                                    style="width:50px;font-size:15px"><i class="fa fa-plus"
                                                        aria-hidden="true"></i></span></a>
                                            @if ($form->visibility == 0)
                                                <span class="badge bg-danger rounded-pill formupdate me-2"
                                                    data-formid="{{ $form->id }}" style="width:50px;font-size:15px"><i
                                                        class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                            @else
                                                <span class="badge bg-success rounded-pill formupdate me-2"
                                                    data-formid="{{ $form->id }}" style="width:50px;font-size:15px"><i
                                                        class="fa fa-eye" aria-hidden="true"></i></span>
                                            @endif
                                            {{-- <span class="badge me-2 bg-danger rounded-pill deletefield"
                                                data-formid="{{ $form->id }}"
                                                data-subcategoryid="{{ $form->subcategoryid }}"
                                                data-formtext="{{ $form->fieldnamewithoutspace }}"
                                                data-catid="{{ $form->categoryid }}"
                                                style="cursor:pointer;width:50px;font-size:15px"><i class="fa fa-trash"
                                                    aria-hidden="true"></i></span> --}}
                                        </li>
                                    @endforeach
                                @endif


                            </ul>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </section>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="javascript:;" id="formupdate">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Status Updation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Choose Form Status</label>
                            <select class="form-select" name="formstatus">
                                <option value="">Select Form Status</option>
                                <option value="1">Show</option>
                                <option value="0">Hide</option>
                            </select>
                        </div>

                        <input type="hidden" name="formid" id="formid">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
