@extends('layouts.app')
@section('title', 'Enquiry/Lead Entry')
@section('main-content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4">
                    <h4>{{ $lead->LeadName }}</h4>
                </div>
                <div class="col-lg-8 d-flex justify-content-end ">
                    <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4 ">
            <div class="leadinfo mt-3 p-3 sticky-top"
                style="top:13%;border-radius:5px;z-index:0;box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;">

                <table class="table  table-hover  ">

                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Enquiry Info</h6>
                        </div>
                    </div>
                    <tr>
                        <th>Lead No</th>
                        <td>{{ $lead->lead_id }}</td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>{{ $lead->LeadName }}</td>
                    </tr>
                    @php
                        $dropdownValue = App\Models\Dropdowndata::where('id', $lead->Leadsource)->value(
                            'dropdowndata',
                        );
                    @endphp
                    <tr>
                        <th>Source of Enquiry</th>
                        <td>{{ $dropdownValue ?? '-' }}</td>
                    </tr>
                    @php
                        $dropdownValue = App\Models\Dropdowndata::where('id', $lead->Leadstatus)->value(
                            'dropdowndata',
                        );
                    @endphp
                    <tr>
                        <th>Enquiry Status</th>
                        <td>{{ $dropdownValue ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Enquiry Date</th>
                        <td>{{ $lead->Entrydate }}</td>
                    </tr>
                    <tr>
                        <th>Enquiry Details</th>
                        <td>{{ $lead->enquirydetail }}</td>
                    </tr>
                    <tr>
                        <th>E-mail</th>
                        <td>
                            <a href="mailto:{{ $lead->email }}" target="_blank">
                                {{ $lead->email }} <i class="fa fa-envelope" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Contact Number</th>
                        <td><a href="tel:{{ $lead->phonumber }}" style="color:#000"
                                target="_blank">{{ $lead->phonumber }}</a>&nbsp;&nbsp;<a target="_blank"
                                style="color:green"
                                href="https://api.whatsapp.com/send?phone=91{{ $lead->phonumber }}"><i
                                    class="mdi mdi-whatsapp" aria-hidden="true"></i></a></td>
                    </tr>
                    <tr>
                        <th>Sales Manager</th>
                        @if ($username = App\Models\User::where('id', $lead->assigned_to)->first())
                            <td>{{ $username->name }}</td>
                        @endif
                    </tr>

                </table>
            </div>
        </div>


        <div class="col-lg-8">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @if ($lead->quota_proceed != 1)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-contact-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
                            aria-selected="false">Design</button>
                    </li>
                @endif
                @if ($lead->quota_proceed == 1)
                    @can('quotation-create')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="pills-payhis-tab" data-bs-toggle="pill" data-bs-target="#pills-payhis"
                                type="button" role="tab" aria-controls="pills-payhis" aria-selected="false">Quotations</button>
                        </li>
                    @endcan
                @endif
                @if (Auth::user()->role == 'Super Admin' && (isset($lead) && $lead->quota_proceed != 1))
                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="pills-quota-tab" data-bs-toggle="pill" data-bs-target="#pills-quota"
                            type="button" role="tab" aria-controls="pills-quota" aria-selected="false">Move To
                            Quotation</button>
                    </li>
                @endif
            </ul>
            <div class="tab-content" id="pills-tabContent">
                @if ($lead->quota_proceed != 1)
                @canany('design-display')
                <div class="tab-pane fade show active" id="pills-contact" role="tabpanel"
                    aria-labelledby="pills-contact-tab">

                    <form action="{{ route('leaddesigns.storedesign') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white container-fluid"
                            style="padding:10px;box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;">
                            <div class="row">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1" class="form-label">Category Name
                                        </label>
                                        <input type="hidden" name="catename" class="form-control"
                                            value="{{ $lead->category }}" placeholder="">
                                        <input type="hidden" name="leadiid" class="form-control" value="{{ $lead->id }}"
                                            placeholder="">
                                        @if ($drop = App\Models\ProductCategory::where('id', $lead->category)->first())
                                            <input type="text" name="" class="form-control"
                                                value="{{ $drop->category_name }}" placeholder="" disabled>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1" class="form-label">Product Name
                                        </label>
                                         <select name="proname" class="form-select">
            <option value="">-- Select Product --</option>

            @foreach (
                App\Models\Product::where('Productcategory', $lead->category)->get()
                as $product
            )
                <option value="{{ $product->id }}"
                    {{ $product->id == $lead->products ? 'selected' : '' }}>
                    {{ $product->productname }}
                </option>
            @endforeach
        </select>
                                        {{-- <input type="hidden" name="proname" class="form-control"
                                            value="{{ $lead->products }}" placeholder="">
                                        @if ($prod = App\Models\Product::where('id', $lead->products)->first())
                                            <textarea class="form-control" rows="1" style="height: 70px;resize: none;"
                                                disabled>{{ $prod->productname }}</textarea>

                                        @endif --}}

                                    </div>
                                </div>
                                <div class="col-lg-12 mt-2 justify-content-center">
                                    <div class="form-group">
                                        <textarea name="design" id="summernote" class="summernote"></textarea>


                                    </div>
                                </div>
                                @php

                                    $breakdown = App\Models\ProductCategory::where('id', $lead->category)
                                        ->where('type', 'rack')
                                        ->first();
                                @endphp
                                <div id="dynamic-fields">
                                    <div class="row field-group">
                                        @if (Auth::user()->role == 'Design Engineer' && $breakdown)
                                            <div class="col-lg-10 mt-2">
                                            </div>

                                            <div class="col-lg-1 mt-2">

                                            </div>
                                            <div class="col-lg-1 mt-2">
                                                <button type="button" class="btn btn-success add-more">+</button>
                                            </div>
                                        @endif
                                        <div class="col-lg-3 mt-2">
                                            <label for="document" class="form-label">GA Drawing</label>
                                            <input type="file" class="form-control" name="drawing[]">
                                        </div>

                                        @if (Auth::user()->role == 'Design Engineer' && $breakdown)
                                            <div class="col-lg-3 mt-2">
                                                <label for="document" class="form-label">Offer</label>
                                                <input type="file" class="form-control" name="offer[]">
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <label for="document" class="form-label">Basic Supply</label>
                                                <input type="text" class="form-control basicsupply" name="basicsupply[]"
                                                    placeholder="" min="0" step="any" required>
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <label for="document" class="form-label">Freight/Transportation</label>
                                                <input type="text" class="form-control freightvalue" name="freightvalue[]"
                                                    placeholder="" min="0" step="any" required>
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <label for="document" class="form-label">Installation</label>
                                                <input type="text" class="form-control installvalue" name="installvalue[]"
                                                    placeholder="" min="0" step="any" required>
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <label for="document" class="form-label">Price Value</label>
                                                <input type="text" class="form-control pricevalue" name="pricevalue[]"
                                                    placeholder="">
                                            </div>
                                            <div class="col-lg-11 mt-2">
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-lg-10 mt-2">
                                </div>
                                <div class="col-lg-2 mt-2 justify-content-center">
                                    <div class="form-group">
                                        <input type="hidden" value="">
                                        @can('design-create')
                                            <button type="submit" class="mt-2 btn btn-success btn-sm w-100"
                                                style="color:#fff;">Add</button>
                                        @endcan
                                    </div>
                                </div>


                            </div>
                        </div>
                    </form>

                    @if ($designs = App\Models\LeadDesign::where('leadiid', '=', $lead->id)->where('softde', 0)->orderBy('id', 'desc')->get())
                        @foreach ($designs as $de)
                            <div class="notes-form-details">
                                <div class="notes-form-box "
                                    style="border-left: 8px solid {{ $de->reverse == 1 ? '#ff6501' : '#1b5683' }};">
                                    <div class="button-box">
                                        {{-- Offer Download --}}
                                        @if ($de->offer)
                                            <a class="btn btn-primary p-2" target="_blank" href="{{ asset($de->offer) }}" download>
                                                <i class="fa fa-download"></i> Offer
                                            </a>
                                        @endif

                                        {{-- Drawing Download --}}
                                        @if ($de->drawing)
                                            <a class="btn btn-warning p-2" target="_blank" href="{{ asset($de->drawing) }}"
                                                download>
                                                <i class="fa fa-download"></i> Drawing
                                            </a>
                                        @endif

                                        {{-- View Price Value --}}
                                        @if ($de->pricevalue)
                                            <a class="btn btn-info p-2">
                                                {{ $de->pricevalue }}
                                            </a>
                                        @endif
                                        @if ($de->pricevalue)
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-info p-2 dropdown-toggle" type="button"
                                                    id="priceDropdown{{ $de->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    View Breakdown
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="priceDropdown{{ $de->id }}">
                                                    <li><a class="dropdown-item" href="#">Basic Supply:
                                                            {{ $de->basicsupply ?? 'N/A' }}</a></li>
                                                    <li><a class="dropdown-item" href="#">Freight Value:
                                                            {{ $de->freightvalue ?? 'N/A' }}</a></li>
                                                    <li><a class="dropdown-item" href="#">Installation
                                                            Value: {{ $de->installvalue ?? 'N/A' }}</a></li>
                                                </ul>
                                            </div>
                                        @endif
                                        @if (auth()->id() == $de->assignee || auth()->user()->can('design-edit') || auth()->user()->can('design-delete'))
                                            @can('design-edit')
                                                <button class="btn text-primary notesedit p-2" data-id="{{ $de->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#leadeddesiModal"><i
                                                        class="mdi mdi-lead-pencil" aria-hidden="true"></i></button>
                                            @endcan
                                            @can('design-delete')
                                                <button class="btn text-danger notesdelete p-2" data-id="{{ $de->id }}"><i
                                                        class="fa fa-trash" aria-hidden="true"></i></button>
                                            @endcan
                                        @endif
                                    </div>
                                    <p>{!! $de->design !!}</p>
                                    <span
                                        style="background-color:{{ $de->reverse == 1 ? '#ff6501' : '#1b5683' }};">{{ \DB::table('users')->where('id', $de->assignee)->value('name') ?? 'Unknown' }}</span>

                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
                @endcan
                @endif
                @if ($lead->quota_proceed == 1)
                    @can('quotation-display')
                        <div class="tab-pane fade active show" id="pills-payhis" role="tabpanel"
                            aria-labelledby="pills-payhis-tab">
                            <div class="container">
                                <div class="row">
                                    @php
                                        $estimates = App\Models\Quotation::where('id_lead', $lead->id)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                        $i = count($estimates);
                                        $lastQuotationId = $estimates->first()->id ?? null;
                                    @endphp
                                    @if ($estimates->isEmpty())
                                        @can('quotation-create')
                                            <div class="col-lg-3 mt-3">
                                                <a href="/createquota/{{ $lead->id }}">
                                                    <div class="bg-white estimatecreate">
                                                        <div class="es">
                                                            <i style="font-size:75px" class="fa fa-plus" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif
                                    @php
                                        // $estimates = App\Models\QuotationProduct::where('leadno', $lead->id)
                                        $estimates = App\Models\Quotation::where('id_lead', $lead->id)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                        $i = count($estimates);
                                        $lastQuotationId = $estimates->first()->id ?? null;
                                    @endphp



                                    @if ($estimates->isNotEmpty())
                                        @foreach ($estimates as $index => $e)
                                            @php
                                                // Fetch the related freight record
                                                $freight = App\Models\Freight::where(
                                                    'quotationno',
                                                    $e->id,
                                                )->first();
                                                // Check for term approval
                                                $isApproved = false;
                                                if ($freight) {
                                                    $isApproved = App\Models\Term::where(function ($query) use ($freight, ) {
                                                        $query
                                                            ->where('id', $freight->termscondition)
                                                            ->orWhere('id', $freight->warranty);
                                                    })
                                                        ->where('term_approve', 1)
                                                        ->exists();
                                                }

                                                $isRejected = false;
                                                $termRemark = null;

                                                if ($freight) {
                                                    $rejectedTerm = App\Models\Term::where(function ($query) use ($freight, ) {
                                                        $query
                                                            ->where('id', $freight->termscondition)
                                                            ->orWhere('id', $freight->warranty);
                                                    })
                                                        ->where('term_approve', 2)
                                                        ->first();

                                                    if ($rejectedTerm) {
                                                        $isRejected = true;
                                                        $termRemark = $rejectedTerm->term_remark;
                                                    }
                                                }

                                                $quotationProduct = App\Models\Quotation::find($e->id);
                                                $disApproved = $quotationProduct && $quotationProduct->dis_app == 1;
                                                $disnotApproved = $quotationProduct && $quotationProduct->dis_app == 0;
                                                $frieghtPending = $quotationProduct && $quotationProduct->freight_approval == 1;
                                                $frieghtRejected = $quotationProduct && $quotationProduct->freight_approval == 2;

                                                $chargePending = in_array($e->charge_approval_status, ['Pending Approval', 'Pending']);
                                                $chargeRejected = $e->charge_approval_status === 'Rejected';

                                                $termChangePending = App\Models\QuotationTermChangeRequest::where('quotation_id', $e->id)->where('status', 'Pending Approval')->exists();
                                                $termChangeRejected = App\Models\QuotationTermChangeRequest::where('quotation_id', $e->id)->where('status', 'Rejected')->exists();

                                                $termsPending = $isApproved || $termChangePending;
                                                $termsRejectedFinal = $isRejected || $termChangeRejected;

                                                $allApprovalsDone = ($e->quto_app == 2)
                                                    && !$disApproved
                                                    && !$disnotApproved
                                                    && !$frieghtPending
                                                    && !$frieghtRejected
                                                    && !$termsPending
                                                    && !$termsRejectedFinal
                                                    && !$chargePending
                                                    && !$chargeRejected;

                                                $isSuperAdmin = Auth::check() && Auth::user()->role === 'Super Admin';
                                                $hasQuotationDisplay = Auth::check() && Auth::user()->can('quotation-display');
                                                $canAccessQuotation = $isSuperAdmin || $hasQuotationDisplay || $allApprovalsDone;
                                            @endphp
                                            <div class="col-lg-3 mt-3">
                                                <div class="bg-white estimatecreate">
                                                    @if ($index == 0)
                                                        <div class="d-flex justify-content-end" style="padding: 10px;">
                                                            @if ($e->sent_to_customer)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Sent to Client"
                                                                    class="badge bg-info ms-1"><i class="fa fa-paper-plane"></i> Sent</span>
                                                            @endif
                                                            @if ($e->revision_minor > 0 || preg_match('/\(R\d+-\d+\)$/', $e->quota_id))
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Internal Draft Revision"
                                                                    class="badge bg-secondary ms-1">Draft</span>
                                                            @endif
                                                            @if ($allApprovalsDone)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Quotation Approved"
                                                                    class="badge bg-success ms-1">Approved</span>
                                                            @endif
                                                            @if ($termsPending)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Terms Approval Pending"
                                                                    class="badge bg-info ms-1">TAP</span>
                                                            @endif
                                                            @if ($termsRejectedFinal)
                                                                <span data-bs-toggle="modal" data-bs-target="#termRemarkModal"
                                                                    data-remark="{{ $termRemark }}" class="badge bg-danger ms-1 term-reject"
                                                                    style="cursor:pointer;">TR</span>
                                                            @endif
                                                            @if ($chargePending)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Charge Approval Pending"
                                                                    class="badge bg-secondary ms-1">CAP</span>
                                                            @endif
                                                            @if ($chargeRejected)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Charge Approval Rejected"
                                                                    class="badge bg-danger ms-1">CR</span>
                                                            @endif
                                                            @if ($frieghtPending)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Freight Approval Pending"
                                                                    class="badge bg-primary ms-1">FAP</span>
                                                            @endif
                                                            @if ($frieghtRejected)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Freight Approval Rejected"
                                                                    class="badge bg-danger ms-1">FR</span>
                                                            @endif
                                                            @if ($disApproved)
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Discount Approval Pending"
                                                                    class="badge bg-warning ms-1">DAP</span>
                                                            @endif
                                                            @if ($disnotApproved)
                                                                <span class="badge bg-danger ms-1 dis-rejected"
                                                                    data-id="{{ $quotationProduct->id }}"
                                                                    data-remark="{{ $quotationProduct->disremark }}" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-original-title="Discount Approval Rejected"
                                                                    style="cursor: pointer;">
                                                                    DR
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <div class="es text-center">
                                                        <i class="mdi mdi-file-document" style="color:#fe6600;font-size:45px"
                                                            aria-hidden="true"></i>
                                                        <p class=" text-dark mb-0">
                                                            {{ $e->quota_id }}
                                                        </p>
                                                        <p class="text-dark">₹ {{ $e->grand_total }}</p>
                                                    </div>

                                                    <ul class="estimatelist">
                                                        @if ($canAccessQuotation)
                                                            <li style="margin:0px 5px">
                                                                <a style="color:#000;" target="_blank" href="/quotation/view/{{ $e->id }}" title="View Quotation">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if ($index == 0)
                                                            <li style="margin:0px 11px">
                                                                <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuButton"
                                                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                    @can('quotation-edit')
                                                                        <li>
                                                                            <a class="dropdown-item" href="/editquota/{{ $e->id }}">
                                                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                                                                Edit
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @if ($canAccessQuotation)
                                                                        <li>
                                                                            <a class="dropdown-item" href="/quotation/download/{{ $e->id }}">
                                                                                <i class="fa fa-download" aria-hidden="true"></i>
                                                                                Download PDF
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item share-quotation" href="javascript:void(0);" data-id="{{ $e->id }}">
                                                                                <i class="fa fa-envelope" aria-hidden="true"></i> Send Mail
                                                                            </a>
                                                                        </li>
                                                                        @if(!$e->sent_to_customer)
                                                                            <li>
                                                                                <a class="dropdown-item mark-sent-quotation" href="javascript:void(0);" data-id="{{ $e->id }}">
                                                                                    <i class="fa fa-check-circle" aria-hidden="true"></i> Mark as Sent to Client
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                        @can('orf-create')
                                                                            <li>
                                                                                <a class="dropdown-item" href="/orf/{{ $e->id }}">
                                                                                    <i class="fa fa-arrow-right" aria-hidden="true"></i> Move to ORF
                                                                                </a>
                                                                            </li>
                                                                        @endcan
                                                                    @endif
                                                                </ul>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif
                <div class="tab-pane fade " id="pills-quota" role="tabpanel" aria-labelledby="pills-quota-tab">

                    <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white container-fluid"
                            style="padding:10px;box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;">
                            <div class="row">
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group">
                                        <label for="document" class="form-label">Document / File</label>
                                        <input type="file" class="form-control" name="quota_doc" id="">
                                    </div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <div class="form-group">
                                        <label for="document" class="form-label">Proceed</label>
                                        <select id="" name="quota_proceed" class="form-control" required>
                                            <option value="0">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 mt-2 justify-content-center">
                                    <div class="form-group">
                                        <input type="hidden" name="idle" value="{{ $lead->id }}">
                                        <button type="submit" class="mt-2 btn btn-success btn-sm w-100"
                                            style="color:#fff;">Add</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>


                <div class="tab-pane fade" id="pills-Products" role="tabpanel" aria-labelledby="pills-Products-tab">
                    <div class="container bg-white pt-3 " style="box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Status</th>
                                            <th>Enquiry Date</th>
                                            <th>Converted Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $leadproduct = App\Models\LeadProduct::where(
                                                'leadid',
                                                $lead->id,
                                            )->get();
                                        @endphp

                                        @if ($leadproduct->count() > 0)
                                            @foreach ($leadproduct as $product)
                                                <tr>
                                                    <td>
                                                        @if ($prod = App\Models\Product::where('id', $product->singleproduct)->first())
                                                            {{ $prod->productname }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($drop = App\Models\Dropdowndata::where('id', $product->statuslead)->first())
                                                            {{ $drop->dropdowndata }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $product->enquirydate ?? '-' }}</td>
                                                    <td>{{ $product->coverteddate ?? '-' }}</td>
                                                    <td>
                                                        <div class="float-btn top">
                                                            <a href="" class="btn btn-danger btn-sm"> + </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No Products Found</td>
                                            </tr>
                                        @endif


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

<div class="modal fade" id="statusedit" tabindex="-1" wire:ignore aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/statusupdate" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Status Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Status <span
                                style="color:red">*</span></label>
                        <select class="form-select" name="statuslead" id="statuslead" required>
                            <option value="">-- Choose Status --</option>
                            @foreach (App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get() as $data)
                                <option value="{{ $data->id }}">{{ $data->dropdowndata }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="mt-3 form-group">
                        <label>Converted Date <span class="paymentrequiredalert13" style="color:red">*</span></label>
                        <input type="date" class="mt-2 bg-white form-control" name="converteddate" id="converteddate">
                    </div>

                    <input type="hidden" name="leadid" id="leadid">
                    <input type="hidden" name="productid" id="productid">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addleadproduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="javascript:;" id="saveleadsproduct">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Lead Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Status <span
                                style="color:red">*</span></label>
                        <select class="form-select " name="statuslead" required>
                            <option value="">-- Choose Status --</option>
                            @if ($status = App\Models\Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get())
                                @foreach ($status as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->dropdowndata }}
                                    </option>
                                @endforeach
                            @endif

                        </select>

                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Product <span
                                style="color:red">*</span></label>
                        <select class="form-select " name="singleproduct" required>
                            <option value="">-- Choose Product --</option>
                            @if ($product = App\Models\Product::get())
                                @foreach ($product as $dataproduct)
                                    <option value="{{ $dataproduct->id }}">
                                        {{ $dataproduct->productname }}
                                    </option>
                                @endforeach
                            @endif

                        </select>

                    </div>

                    <div class="mt-3 form-group">
                        <label>Enquiry Date <span class="paymentrequiredalert13" style="color:red">*</span></label>
                        <input type="date" class="mt-2 bg-white  form-control " name="enquirydate">
                    </div>

                    <div class="mt-3 form-group">
                        <label>Converted Date <span class="paymentrequiredalert13" style="color:red">*</span></label>
                        <input type="date" class="mt-2 bg-white  form-control " name="coverteddate">
                    </div>
                    <input type="hidden" class="form-control " name="leadid" value="{{ $lead->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="leadeddesiModal" tabindex="-1" aria-labelledby="leadeditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadeditModalLabel">Edit Design</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editForm" action="/leaddesigns/update" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" id="editId" name="leadiid">
                    <input type="hidden" name="edit_action_set" id="edit_action_set">


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" id="editCategory" name="catename" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                {{-- <input type="text" id="editProduct" name="proname" class="form-control" readonly> --}}
                                <select name="proname" id="editProduct" class="form-select">
            <option value="">-- Select Product --</option>

            @foreach (
                App\Models\Product::where('Productcategory', $lead->category)->get()
                as $product
            )
                <option value="{{ $product->id }}"
                    {{ $product->id == $lead->products ? 'selected' : '' }}>
                    {{ $product->productname }}
                </option>
            @endforeach
        </select>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2">
                            <div class="form-group">
                                <label>Design</label>
                                <textarea id="editDesign" name="design" class="form-control summernote"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2">
                            <div class="form-group">
                                <label>GA Drawing</label>
                                <input type="file" class="form-control" name="drawing[]">
                                <div id="currentDrawing"></div>
                            </div>
                        </div>
                        @if (Auth::user()->role == 'Design Engineer' && $breakdown)
                            <div class="col-lg-12 mt-2">
                                <div class="form-group">
                                    <label>Offer</label>
                                    <input type="file" class="form-control" name="offer[]">
                                    <div id="currentOffer"></div>
                                </div>
                            </div>
                            <div class="col-lg-3 mt-2">
                                <label for="document" class="form-label">Basic Supply</label>
                                <input type="text" class="form-control basicsupplyss" name="basicsupplyss" placeholder=""
                                    min="0" step="any" required>
                            </div>
                            <div class="col-lg-3 mt-2">
                                <label for="document" class="form-label">Freight/Transportation</label>
                                <input type="text" class="form-control freightvaluess" name="freightvaluess" placeholder=""
                                    min="0" step="any" required>
                            </div>
                            <div class="col-lg-3 mt-2">
                                <label for="document" class="form-label">Installation</label>
                                <input type="text" class="form-control installvaluess" name="installvaluess" placeholder=""
                                    min="0" step="any" required>
                            </div>
                            <div class="col-lg-3 mt-2">
                                <label for="document" class="form-label">Price Value</label>
                                <input type="text" class="form-control pricevaluess" name="pricevaluess" placeholder="">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="qutoRemarkModal" tabindex="-1" aria-labelledby="qutoRemarkLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quotation Remark</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="quto-remark-text"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disRemarkModal" tabindex="-1" aria-labelledby="disRemarkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discount Rejection Remark</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="dis-remark-text"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termRemarkModal" tabindex="-1" aria-labelledby="termRemarkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Term Rejection Remark</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="term-remark-text"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="designDrawingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Design Drawings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row" id="designDrawingsContainer">
                    <div class="col-12 text-center text-muted">
                        Loading drawings...
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection


@section('scripts')

    <script>
        $(document).on('click', '.notesedit', function () {
            let id = $(this).data('id');

            $('#edit_action_set').val(id);
            $.ajax({
                url: `/leaddesigns/${id}/edit`,
                method: 'GET',
                success: function (response) {
                    console.log(response);

                    $('#editId').val(response.leadiid);
                    $('#editCategory').val(response.category_name);
                    // $('#editProduct').val(response.product_name);
                    $('#editProduct').val(response.product_id);
                    $('.basicsupplyss').val(response.basicsupply);
                    $('.freightvaluess').val(response.freightvalue);
                    $('.installvaluess').val(response.installvalue);
                    $('.pricevaluess').val(response.pricevalue);
                    let plainText = response.design.replace(/<\/?p>/g, "");
                    $('#editDesign').val(plainText);

                    if (response.drawing) {
                        let drawingPath = response.drawing.startsWith('/') ? response.drawing :
                            `/${response.drawing}`;
                        $('#currentDrawing').html(
                            `<a href="${drawingPath}" target="_blank">View Current Drawing</a>`);
                    } else {
                        $('#currentDrawing').html('No Drawing Available');
                    }

                    if (response.offer) {
                        let offerPath = response.offer.startsWith('/') ? response.offer :
                            `/${response.offer}`;
                        $('#currentOffer').html(
                            `<a href="${offerPath}" target="_blank">View Current Offer</a>`);
                    } else {
                        $('#currentOffer').html('No Offer Available');
                    }

                    $('#leadeddesiModal').modal('show');
                },
                error: function (err) {
                    console.log(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to load data',
                        text: 'The selected design record could not be found.'
                    });
                }
            });
        });

        $('#editForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: '/leaddesigns/update/' + $('#edit_action_set').val(),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Design updated successfully!'
                    }).then(() => {
                        $('#leadeddesiModal').modal('hide');
                        // Optionally reload page or refresh table
                        // location.reload();
                    });
                },
                error: function (err) {
                    console.log(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update the design. Please try again.'
                    });
                }
            });
        });
    </script>



    <script>
        $(document).on('input', '.basicsupplyss, .freightvaluess, .installvaluess', function () {
            const $container = $(this).closest('.row, .modal'); // adapt to your structure
            calculatePriceValueScoped($container);
        });

        function calculatePriceValueScoped($container) {
            const basic = parseFloat($container.find('.basicsupplyss').val()) || 0;
            const freight = parseFloat($container.find('.freightvaluess').val()) || 0;
            const install = parseFloat($container.find('.installvaluess').val()) || 0;
            const total = basic + freight + install;
            $container.find('.pricevaluess').val(total.toFixed(2));
        }
    </script>



    <script>
        function calculatePriceValue(fieldGroup) {
            const basic = parseFloat(fieldGroup.querySelector('input[name="basicsupply[]"]').value) || 0;
            const freight = parseFloat(fieldGroup.querySelector('input[name="freightvalue[]"]').value) || 0;
            const install = parseFloat(fieldGroup.querySelector('input[name="installvalue[]"]').value) || 0;

            const total = basic + freight + install;
            fieldGroup.querySelector('input[name="pricevalue[]"]').value = total.toFixed(2);
        }

        // Add more field groups dynamically
        document.querySelector('.add-more').addEventListener('click', function () {
            const fieldGroup = document.querySelector('.field-group');
            const newFieldGroup = fieldGroup.cloneNode(true);

            // Clear values for cloned inputs
            newFieldGroup.querySelectorAll('input').forEach(input => input.value = '');

            // Remove the add button if it exists
            const addButton = newFieldGroup.querySelector('.add-more');
            if (addButton) addButton.remove();

            // Create the remove button
            const removeBtnDiv = document.createElement('div');
            removeBtnDiv.classList.add('col-lg-1', 'mt-2');
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.classList.add('remove-field', 'btn', 'btn-danger', 'w-100');
            removeBtn.textContent = '-';
            removeBtnDiv.appendChild(removeBtn);
            newFieldGroup.appendChild(removeBtnDiv);

            // Append the new field group
            document.getElementById('dynamic-fields').appendChild(newFieldGroup);
        });

        // Handle remove button click
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-field')) {
                if (document.querySelectorAll('.field-group').length > 1) {
                    e.target.closest('.field-group').remove();
                }
            }
        });

        // Listen for input changes to recalculate the price value
        document.addEventListener('input', function (e) {
            const target = e.target;
            if (['basicsupply[]', 'freightvalue[]', 'installvalue[]'].includes(target.getAttribute('name'))) {
                const fieldGroup = target.closest('.field-group');
                calculatePriceValue(fieldGroup);
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.quto-revision', function () {
                let remark = $(this).data('remark');
                $('#quto-remark-text').text(remark || 'No remark available.');
                $('#qutoRemarkModal').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.dis-rejected', function () {
                let remark = $(this).data('remark');
                $('#dis-remark-text').text(remark || 'No remark available.');
                $('#disRemarkModal').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.term-reject', function () {
                let remark = $(this).data('remark');
                $('#term-remark-text').text(remark || 'No remark available.');
                $('#termRemarkModal').modal('show');
            });
        });
    </script>
<script>
$(document).on('click', '.share-quotation', function () {

    let quotationId = $(this).data('id');

    Swal.fire({
        title: 'Send Quotation?',
        text: 'Do you want to send this quotation via mail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Sending...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/quotation/send-mail/${quotationId}`,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mail Sent',
                        text: res.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to send mail. Please try again.'
                    });
                }
            });
        }
    });
});

$(document).on('click', '.mark-sent-quotation', function () {
    let quotationId = $(this).data('id');

    Swal.fire({
        title: 'Mark as Sent?',
        text: 'Mark this quotation as sent to client? (Subsequent edits will become formal customer revisions).',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Mark as Sent',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/quotation/mark-sent/${quotationId}`,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: res.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to update quotation.'
                    });
                }
            });
        }
    });
});
</script>


@endsection