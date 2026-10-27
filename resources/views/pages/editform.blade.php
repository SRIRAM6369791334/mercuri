@extends('layouts.app')
@section('main-content')
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Edit Form</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <a href="{{ url('/setup/viewform') }}" class="btn btn-primary">Go to Form details</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="container ">
            <div class="row">
                <div class="col-lg-8 offset-lg-3 shadow bg-white p-3" style="border-radius:10px">
                    <form method="POST" action="/updateformdetails">
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group mt-3">
                                <label for="exampleFormControlInput1" class="form-label">Choose Form <span
                                        style="color:red">*</span></label>
                                <select class="form-select" name="category" required>
                                    <option value="">-- Select Form --</option>
                                    @if ($category)
                                        @foreach ($category as $c)
                                            <option value="{{ $c->id }}"
                                                @if ($row->categoryid == $c->id) selected @endif>{{ $c->categoryname }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mt-3 d-none">
                                <label for="exampleFormControlInput1" class="form-label">Choose Group <span
                                        style="color:red">*</span></label>
                                <select class="form-select" name="subcategory">
                                    <option value="">-- Select Group --</option>
                                    @if ($subcategory)
                                        @foreach ($subcategory as $c)
                                            <option value="{{ $c->id }}"
                                                @if ($row->subcategoryid == $c->id) selected @endif>{{ $c->subcategory }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Field Name </label>
                                    <input type="text" class="form-control" name="fieldname"
                                        value="{{ $row->fieldname }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Field Type <span
                                            style="color:red">*</span></label>
                                    <select class="form-select" name="fieldtype" id="fieldtype" required>
                                        <option value="">-- Choose Field Type --</option>
                                        <option value="1" @if ($row->fieldtype == 1) selected @endif>Text
                                        </option>
                                        <option value="2" @if ($row->fieldtype == 2) selected @endif>Number
                                        </option>
                                        <option value="3" @if ($row->fieldtype == 3) selected @endif>Decimal
                                        </option>
                                        <option value="4" @if ($row->fieldtype == 4) selected @endif>E-mail ID
                                        </option>
                                        <option value="5" @if ($row->fieldtype == 5) selected @endif>Mobile
                                            Number</option>
                                        <option value="6" @if ($row->fieldtype == 6) selected @endif>Date
                                        </option>
                                        <option value="7" @if ($row->fieldtype == 7) selected @endif>Dropdown
                                        </option>
                                    </select>
                                </div>
                            </div>
                            @if ($row->fieldtype == 7)
                                <div class="col-lg-12" id="">
                                    <table class="table table-bordered mt-3">
                                        <thead>
                                            <tr>
                                                <th>Order number</th>
                                                <th>Option name</th>
                                                <th>MHE/Rack</th>
                                                <th>Special/Standard</th>
                                                <th><button class="btn btn-primary addfields w-100 btn-sm"
                                                        type="button">Add</button></th>
                                            </tr>
                                        </thead>
                                        <tbody id="optionfielddetails">
                                            @php
                                              $mheRackOptions = ['MHE', 'Rack'];
                                              $proCatOptions = ['Special', 'Standard'];

                                            @endphp
                                            @if ($dropdownfields = App\Models\Dropdowndata::where('formid', $row->id)->get())
                                                @foreach ($dropdownfields as $d)
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="orderno[]"
                                                                value="{{ $d->orderno }}" required></td>
                                                        <td><input type="text" class="form-control" name="optionname[]"
                                                                value="{{ $d->dropdowndata }}" required></td>
                                                                <td>
                                                                    <select class="form-control" name="mhe_rack[]">
                                                                        @foreach ($mheRackOptions as $option)
                                                                            <option value="{{ $option }}" {{ $d->mhe_rack == $option ? 'selected' : '' }}>
                                                                                {{ $option }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="pro_catid[]">
                                                                        @foreach ($proCatOptions as $option)
                                                                            <option value="{{ $option }}" {{ $d->pro_catid == $option ? 'selected' : '' }}>
                                                                                {{ $option }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                        <td><button class="btn btn-danger remove btn-sm"
                                                                type="button">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Is Mandatory<span
                                            style="color:red">*</span></label>
                                    <select class="form-select" name="mandatory" required>
                                        <option value="">-- Choose Field Type --</option>
                                        <option value="1" @if ($row->mandatory == 1) selected @endif>Yes
                                        </option>
                                        <option value="0" @if ($row->mandatory == 0) selected @endif>No
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{ $row->id }}">
                            <button type="submit" class="btn btn-primary mt-3">Update Form</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
