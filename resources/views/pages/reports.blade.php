@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

@can('quotation-display')

    <div class="container-fluid">
        <div class="row">

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne" style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-tasks"></i>
                                </span>
                                <h5 class="mb-0">Equipment Sales Booking Value</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form id="filter-form">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <table class="table table-bordered display responsive nowrap" id="pendingTasks">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads as $lead)
                                                    <tr>
                                                        <td>{{ $lead->lead_id }}</td>
                                                        <td>{{ $lead->LeadName }}</td>
                                                        <td>{{ $lead->category_name ?? 'N/A' }}</td>
                                                        <td>{{ $lead->productname ?? 'N/A' }}</td>
                                                        <td>{{ $lead->converteddate ? \Carbon\Carbon::parse($lead->converteddate)->format('d-m-Y') : 'N/A' }}
                                                        </td>
                                                        <td>{{ $lead->grandtotal ?? 'N/A' }}</td>
                                                        <td>{{ $lead->stage ?? 'N/A' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No leads available</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"
                            style="background-color: #f2ebe1;">
                            <div class="card-header text-white d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-envelope-open-text"></i>
                                </span>
                                <h5 class="mb-0">Spares & Service Sales Booking Value</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filterspareser">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap" id="pendingEnquiry">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads2 as $lead2)
                                        <tr>
                                            <td>{{ $lead2->lead_id }}</td>
                                            <td>{{ $lead2->LeadName }}</td>
                                            <td>{{ $lead2->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead2->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead2->converteddate ? \Carbon\Carbon::parse($lead2->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead2->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead2->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"
                            style="background-color: #f2ebe1;">
                            <div class="card-header text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-cogs"></i>
                                </span>
                                <h5 class="mb-0">Spares & Service Billing Value</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filterspareserbill">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap"
                                            id="technicalClosure">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads3 as $lead3)
                                        <tr>
                                            <td>{{ $lead3->lead_id }}</td>
                                            <td>{{ $lead3->LeadName }}</td>
                                            <td>{{ $lead3->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead3->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead3->converteddate ? \Carbon\Carbon::parse($lead3->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead3->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead3->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-briefcase"></i>
                                </span>
                                <h5 class="mb-0">Equipment Billing Value</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapsefour" class="accordion-collapse collapse" aria-labelledby="headingfour"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filtereqipbill">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap"
                                            id="businessRequirement">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads4 as $lead4)
                                        <tr>
                                            <td>{{ $lead4->lead_id }}</td>
                                            <td>{{ $lead4->LeadName }}</td>
                                            <td>{{ $lead4->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead4->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead4->converteddate ? \Carbon\Carbon::parse($lead4->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead4->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead4->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsefive" aria-expanded="false" aria-controls="collapsefive"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-box-open"></i>
                                </span>
                                <h5 class="mb-0">Open Order Details</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapsefive" class="accordion-collapse collapse" aria-labelledby="headingfive"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filteropenorder">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap" id="openorder">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads5 as $lead5)
                                        <tr>
                                            <td>{{ $lead5->lead_id }}</td>
                                            <td>{{ $lead5->LeadName }}</td>
                                            <td>{{ $lead5->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead5->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead5->converteddate ? \Carbon\Carbon::parse($lead5->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead5->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead5->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsesix" aria-expanded="false" aria-controls="collapsesix"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <h5 class="mb-0">Monthly Billing Plan</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapsesix" class="accordion-collapse collapse" aria-labelledby="headingsix"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filtermonbill">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap" id="monthlybilling">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads6 as $lead6)
                                        <tr>
                                            <td>{{ $lead6->lead_id }}</td>
                                            <td>{{ $lead6->LeadName }}</td>
                                            <td>{{ $lead6->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead6->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead6->converteddate ? \Carbon\Carbon::parse($lead6->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead6->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead6->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingseve">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseseve" aria-expanded="false" aria-controls="collapseseve"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-calendar-plus"></i>
                                </span>
                                <h5 class="mb-0">Monthly Booking Plan</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseseve" class="accordion-collapse collapse" aria-labelledby="headingseve"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <div class="filter-section mb-4">
                                            <form  id="filtermonbook">
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label>From Date</label>
                                                        <input type="date" name="fromdate" class="form-control"
                                                            value="{{ request('fromdate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>To Date</label>
                                                        <input type="date" name="todate" class="form-control"
                                                            value="{{ request('todate') }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Assigned To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">All Users</option>
                                                            @php
                                                                $users = DB::table('users')->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-control">
                                                            <option value="">All Categories</option>
                                                            @php
                                                                $categories = DB::table('product_categories_tb')->get();
                                                            @endphp
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label>Time Filter</label>
                                                        <select name="time_filter" class="form-control">
                                                            <option value="">No Filter</option>
                                                            <option value="monthly">This Month</option>
                                                            <option value="weekly">This Week</option>
                                                            <option value="quarterly">This Quarter</option>
                                                            <option value="fy">This Financial Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2 align-self-end">
                                                        <button type="submit" class="btn btn-primary">Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <table class="table table-bordered display responsive nowrap" id="monthlybooking">
                                            <thead>
                                                <tr>
                                                    <th>Lead ID</th>
                                                    <th>Lead Name</th>
                                                    <th>Category</th>
                                                    <th>Products</th>
                                                    <th>Converted Date</th>
                                                    <th>Total Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leads7 as $lead7)
                                        <tr>
                                            <td>{{ $lead7->lead_id }}</td>
                                            <td>{{ $lead7->LeadName }}</td>
                                            <td>{{ $lead7->category_name ?? 'N/A' }}</td>
                                            <td>{{ $lead7->productname ?? 'N/A' }}</td>
                                            <td>{{ $lead7->converteddate ? \Carbon\Carbon::parse($lead7->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $lead7->grandtotal ?? 'N/A' }}</td>
                                            <td>{{ $lead7->stage ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No leads available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingeig">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseeig" aria-expanded="false" aria-controls="collapseeig"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </span>
                                <h5 class="mb-0">Monthly Collection Plan</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseeig" class="accordion-collapse collapse" aria-labelledby="headingeig"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap"
                                            id="monthlycollection">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingnine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsenine" aria-expanded="false" aria-controls="collapsenine"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-receipt"></i>
                                </span>
                                <h5 class="mb-0">Payable List</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapsenine" class="accordion-collapse collapse" aria-labelledby="headingnine"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="payablelist">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingten">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseten" aria-expanded="false" aria-controls="collapseten"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                <h5 class="mb-0">Receivable List</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseten" class="accordion-collapse collapse" aria-labelledby="headingten"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="receivablelist">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading11">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse11" aria-expanded="false" aria-controls="collapse11"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-truck-loading"></i>
                                </span>
                                <h5 class="mb-0">Open Advance list of Supplier</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse11" class="accordion-collapse collapse" aria-labelledby="heading11"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="listsupplier">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading12">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse12" aria-expanded="false" aria-controls="collapse12"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-hourglass-half"></i>
                                </span>
                                <h5 class="mb-0">Advance Pending list of Customer</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse12" class="accordion-collapse collapse" aria-labelledby="heading12"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="listcustomer">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading13">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse13" aria-expanded="false" aria-controls="collapse13"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-balance-scale"></i>
                                </span>
                                <h5 class="mb-0">P & L</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse13" class="accordion-collapse collapse" aria-labelledby="heading13"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="pandl">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading14">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse14" aria-expanded="false" aria-controls="collapse14"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #fb6605;">
                                    <i class="fas fa-cubes"></i>
                                </span>
                                <h5 class="mb-0">Stock Details</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse14" class="accordion-collapse collapse" aria-labelledby="heading14"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="stockdetails">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading15">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse15" aria-expanded="false" aria-controls="collapse15"
                            style="background-color: #f2ebe1;">
                            <div class="card-header  text-white  d-flex align-items-center"
                                style="background-color: #f2ebe1;">
                                <span class="icon-box text-white me-2" style="background-color: #205680;">
                                    <i class="fas fa-business-time"></i>
                                </span>
                                <h5 class="mb-0">PO Pending list of Supplier against customer order</h5>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse15" class="accordion-collapse collapse" aria-labelledby="heading15"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="col-md-12">
                                <div class="card">

                                    <div class="card-body">
                                        <table class="table table-bordered display responsive nowrap" id="popending">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>BR Ref No</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Special Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Audi Components India</td>
                                                    <td>CMBM/2024/789</td>
                                                    <td>Vivek Anand</td>
                                                    <td>vivek@audi.com</td>
                                                    <td>9898765432</td>
                                                    <td>Yes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>



        </div>
    </div>

@endcan

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script>
        $(document).on('submit', '#filter-form', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ url('/equipfilter') }}",
                type: 'GET',
                data: $(this).serialize(),
                success: function(response) {

                    // Destroy DataTables instance if exists
                    if ($.fn.DataTable.isDataTable('#pendingTasks')) {
                        $('#pendingTasks').DataTable().clear().destroy();
                    }

                    let tbody = $('#pendingTasks tbody');
                    tbody.empty(); // Clear existing table rows

                    if (response.length > 0) {
                        response.forEach(function(lead) {
                            let convertedDate = lead.converteddate ?
                                moment(lead.converteddate).format('DD-MM-YYYY') :
                                'N/A';

                            let row = `
                        <tr>
                            <td>${lead.lead_id}</td>
                            <td>${lead.LeadName}</td>
                            <td>${lead.category_name ?? 'N/A'}</td>
                            <td>${lead.productname ?? 'N/A'}</td>
                            <td>${convertedDate}</td>
                            <td>${lead.grandtotal ?? 'N/A'}</td>
                            <td>${lead.stage ?? 'N/A'}</td>
                        </tr>`;

                            tbody.append(row); // Append each row
                        });
                    } else {
                        tbody.append(
                            `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                    }

                    // Re-initialize DataTables after appending data
                    $('#pendingTasks').DataTable({
                        responsive: true,
                        autoWidth: false,
                        order: [
                            [6, 'desc']
                        ],
                        dom: 'Bfrtip',
                        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    });

                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

<script>
    $(document).on('submit', '#filterspareser', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/sparserfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#pendingEnquiry')) {
                    $('#pendingEnquiry').DataTable().clear().destroy();
                }

                let tbody = $('#pendingEnquiry tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#pendingEnquiry').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
<script>
    $(document).on('submit', '#filterspareserbill', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/sparserbillfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#technicalClosure')) {
                    $('#technicalClosure').DataTable().clear().destroy();
                }

                let tbody = $('#technicalClosure tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#technicalClosure').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
<script>
    $(document).on('submit', '#filtereqipbill', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/equipbillfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#businessRequirement')) {
                    $('#businessRequirement').DataTable().clear().destroy();
                }

                let tbody = $('#businessRequirement tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#businessRequirement').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
<script>
    $(document).on('submit', '#filteropenorder', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/openorderfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#openorder')) {
                    $('#openorder').DataTable().clear().destroy();
                }

                let tbody = $('#openorder tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#openorder').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>

<script>
    $(document).on('submit', '#filtermonbill', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/monbillfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#monthlybilling')) {
                    $('#monthlybilling').DataTable().clear().destroy();
                }

                let tbody = $('#monthlybilling tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#monthlybilling').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>

<script>
    $(document).on('submit', '#filtermonbook', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/monbookfilter') }}",
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

                // Destroy DataTables instance if exists
                if ($.fn.DataTable.isDataTable('#monthlybooking')) {
                    $('#monthlybooking').DataTable().clear().destroy();
                }

                let tbody = $('#monthlybooking tbody');
                tbody.empty(); // Clear existing table rows

                if (response.length > 0) {
                    response.forEach(function(lead) {
                        let convertedDate = lead.converteddate ?
                            moment(lead.converteddate).format('DD-MM-YYYY') :
                            'N/A';

                        let row = `
                    <tr>
                        <td>${lead.lead_id}</td>
                        <td>${lead.LeadName}</td>
                        <td>${lead.category_name ?? 'N/A'}</td>
                        <td>${lead.productname ?? 'N/A'}</td>
                        <td>${convertedDate}</td>
                        <td>${lead.grandtotal ?? 'N/A'}</td>
                        <td>${lead.stage ?? 'N/A'}</td>
                    </tr>`;

                        tbody.append(row); // Append each row
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="7" class="text-center">No leads available</td></tr>`);
                }

                // Re-initialize DataTables after appending data
                $('#monthlybooking').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [6, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
@endsection
