@extends('layouts.app')
@section('main-content')
    <section class="section">
        <div class="container bg-white shadow pt-2 pb-2" style="border-radius:5px">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="mt-2">Create Form</h6>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <a href="{{ url('/setup/viewform') }}" class="btn btn-warning ">Go to form details</a>
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
        <div class="container bg-white shadow mt-2 p-3 mt-4" style="border-radius:5px">
            <div class="alert alert-warning alert-dismissible fade show d-none myalert" role="alert" id="myalert">
                <span class="error-text"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="row">
                <div class="col-lg-6 offset-lg-3 ">
                    <form method="POST" action="/savedetails">
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group mt-3">
                                <label for="exampleFormControlInput1" class="form-label">Choose Form <span
                                        style="color:red">*</span></label>
                                <select class="form-select" name="category" required id="chooseform">
                                    <option value="">-- Select Form --</option>
                                    {{-- <option value="1">Leads</option> --}}
                                    @if ($category)
                                        @foreach ($category as $c)
                                            <option value="{{ $c->id }}">{{ $c->categoryname }}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mt-3 d-none">
                                <label for="exampleFormControlInput1" class="form-label">Choose Group <span
                                        style="color:red">*</span></label>
                                <select class="form-select" name="subcategory" required id="subgroup" readonly>
                                    <option value="">-- Select Group --</option>
                                    @if ($subcategory)
                                        @foreach ($subcategory as $c)
                                            <option value="{{ $c->id }}">{{ $c->subcategory }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                {{-- <input type="text" id="subgroup" class="form-control" name="subcategory" readonly> --}}
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Field Name <span
                                            style="color:red">*</span></label>
                                    <input type="text" class="form-control" onblur="namevalidation(this)"
                                        name="fieldname" required maxlength="100" minlength="3">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Field Type <span
                                            style="color:red">*</span></label>
                                    <select class="form-select" name="fieldtype" id="fieldtype" required>
                                        <option value="">-- Choose Field Type --</option>
                                        <option value="1">Text</option>
                                        <option value="2">Number</option>
                                        <option value="3">Decimal</option>
                                        <option value="4">E-mail ID</option>
                                        <option value="5">Mobile Number</option>
                                        <option value="6">Date</option>
                                        <option value="7">Dropdown</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12" id="dropdowndata">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Order number</th>
                                            <th>Option name</th>
                                            <th><button class="btn btn-primary addfields" type="button">Add</button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="optionfielddetails">

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">Is Mandatory <span
                                            style="color:red">*</span></label>
                                    <select class="form-select" name="mandatory" required>
                                        <option value="">-- Choose Field Type --</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning mt-3" style="padding:5px 25px">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
