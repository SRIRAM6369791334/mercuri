@extends('layouts.app')
@section('title', 'Add Assets')
@section('main-content')


    <div class="container" style="">
        @if (session('success'))
            <div class="alert alert-success text-center">
                <h4>{{ session('success') }}</h4>
            </div>
        @endif
        <div class="table table-view shadow col-md-12">
            <div class="row">
                <div class="col-sm-12">

                    <div class="panel panel-default card-view" style="padding-left: 50px;">
                        <form action="assetdetailinsert" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row heading-bg  ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                                    <h5 class="txt-dark"> <i class="fa fa-repeat" style="color: #2d368e;"
                                            aria-hidden="true"></i> Assets</h5>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" style="text-align: center;">
                                    <a class="btn btn-primary" href="javascript:history.back()"><i
                                            class="fa fa-hand-o-left mr-2" aria-hidden="true"
                                            style="color:#ffff;"></i>&nbsp;&nbsp;Back</a>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-lg-4">


                                    <label style="margin-bottom:20px;margin-top:15px;">Choose Employee<span
                                            style="color:red">*</span></label>

                                    <select name="addasset_em" id="addasset_em" class='form-control select2' required>
                                        <option value="">-- Choose Employee --</option>
                                        @if ($employee = App\Models\Employee::get())
                                            @foreach ($employee as $em)
                                                <option value="{{ $em->id }}">{{ $em->emp_id }} |
                                                    {{ $em->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <div class="panel-wrapper ">
                                        <div class="panel-body">
                                            <input type="hidden" name="" class="form-control sid">
                                            <label style="margin-bottom:10px;margin-top:15px;">Select Assets<span
                                                    style="color:red">*</span><span
                                                    style="color:green;margin-left: 10px;"><i
                                                        class="fa fa-plus text-success" data-bs-toggle="modal"
                                                        data-bs-target="#addnewasset"></i></span></label><span
                                                id="aden" class="error-info"
                                                style="margin-left: 10px; color:red;"></span></label>
                                            <table class="table" id="tab_logic">
                                                <tbody id="file_field">
                                                    <tr>
                                                        <td>
                                                            <span id="msg"></span>
                                                            <select required name="assets[]"
                                                                class="form-control common asss">
                                                                <option value="">-- Select Asset --</option>
                                                                @foreach (App\Models\Asset::get() as $as)
                                                                    @php
                                                                        $serial = trim($as->serial_number);
                                                                        $showSerial = $serial && $serial !== '|';
                                                                        $displayText =
                                                                            $as->asset_name .
                                                                            ($showSerial ? ' | ' . $serial : '');
                                                                    @endphp
                                                                    <option value="{{ $as->id }}">{{ $displayText }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" min="1" name="qty[]"
                                                                placeholder="QTY" class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger remove1">X</button>
                                                        </td>
                                                        <td>
                                                            <button type="button" id="add_row"
                                                                class="btn btn-primary">+</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                </div>
                                <div class="col-lg-6" style="text-align: end;">
                                    <input type="submit" name="save-advance"
                                        style="width:150px;background-color:#4aa23c;border-color: #4aa23c;;color:#fff;"
                                        name="" class="form-control btn btn-primary" value="Submit">
                                </div>


                                <br>
                                <br>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addnewasset" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog ">
            <form method="POST" action="addnewassetinsert" id="addasset1">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Asset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlInput1" class="form-label">Asset Name</label>
                            <input type="text" class="form-control" autocomplete="off" minlength="2" maxlength="30"
                                required name="newasset" value="" id="addasset_input">
                        </div>
                        <div class="form-group mt-2">
                            <label for="exampleFormControlInput1" class="form-label">Serial No</label>
                            <input type="text" class="form-control" autocomplete="off" name="serialno"
                                value="" id="addasset_input">
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
