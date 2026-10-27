@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
@php
$countries = DB::table('countries')->where('id',76)->get(); // Fetching all countries from the database
@endphp
<section class="bg-white">
    <div class="container-fluid bg-white pt-4 pb-5">
        <div class="alert alert-warning alert-dismissible fade show d-none myalert" role="alert" id="myalert">
            <span class="error-text"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <form action="{{ route('orf.update', $orf->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off" data-form-type="other" data-lpignore="true">
            @csrf
            @method('PUT')
        <div class="row">
            <div class="col-lg-12 ">
                <h6 class="mb-3">Update Order Review</h6>
            </div>
            <input type="hidden" name="quoproid" value="{{ $orf->id }}" class="form-control mt-2" readonly>
            <input type="hidden" name="leano" value="{{ $orf->leano }}" class="form-control mt-2" readonly>
            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>Transaction Reference</label>

                    <input type="text" name="tranref" placeholder="Transaction Reference" class="form-control mt-2" readonly style="background-color: #c6cbd0;" value="{{ $orf->tranref ?? 'N/A' }}">

                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>Quotation Number </label>
                    <input type="text" name="quono" onblur="namevalidation(this)" style="background-color: #c6cbd0;" placeholder="Quotation Number" class="form-control mt-2" value="{{ $orf->quono }}" readonly>
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>Customer </label>
                    <input type="text" name="custo" onblur="namevalidation(this)" style="background-color: #c6cbd0;" placeholder="Customer" class="form-control mt-2" value="{{ $orf->custo ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>Quotation Date</label>
                    <input type="text" name="quotadate" style="text-transform:uppercase;background-color: #c6cbd0;"   placeholder="Quotation Date" value="{{ $orf->created_at->format('d-m-Y') }}" class="form-control mt-2" readonly>
                </div>
            </div>
            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>ORF Reference </label>

                    <input type="text" name="orfref" class="form-control" value="{{ $orf->orfref }}" placeholder="" style="background-color: #c6cbd0;" readonly  >

                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>ORF Date </label>
                    <input type="date" name="orfdate" class="form-control" style="" value="{{ old('orfdate', \Carbon\Carbon::now()->format('Y-m-d')) }}" placeholder=""    >


                </div>
            </div>
            <div class="col-lg-5 mt-2">
                <div class="form-group">
                    <label>Billing Address</label>

                    <textarea class="form-control" style="background-color: #c6cbd0;" name="billingad" id="billingAddress" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">{{ $orf->lead?->billaddress }}</textarea>

                </div>
            </div>
            <div class="col-lg-2 mt-2">
            </div>
            <div class="col-lg-5 mt-2">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea class="form-control" name="placeofsupply" id="shippingAddress" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">{{ $orf->quopro?->placeofsupply }}</textarea>
                </div>
            </div>


            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Billing Country <span
                        style="color:red">*</span></label>
                        <select class="form-select " style="background-color: #c6cbd0;" name="billingcountry" id="country" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->id == 76 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach

                        </select>
                </div>
            </div>
            <div class="col-lg-2 mt-2">
            </div>
            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Shipping Country <span
                        style="color:red">*</span></label>
                        <select class="form-select " name="shippingcountry" id="shipcountry" autocomplete="off" data-lpignore="true" data-form-type="other">
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->id == 76 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach

                        </select>
                </div>
            </div>

            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Billing State <span
                        style="color:red">*</span></label>
                        <select class="form-select " name="state_id" id="state_list" style="background-color: #c6cbd0;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose State -- </option>
                            @if($product = App\Models\Statelist::get())
                            @foreach ($product as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $orf->lead?->state_id ? 'selected' : '' }}>
                                    {{ $p->state }}
                                </option>
                            @endforeach
                        @endif

                        </select>
                </div>
            </div>
            {{-- <div class="col-lg-2 mt-2" style="text-align: center;">
                <button type="button" class="btn btn-primary mt-2" style="font-size: 12px;" onclick="copyAddress()">Same as Billing Address</button>
            </div> --}}
            <div class="col-lg-2 mt-2" style="text-align: center;">
                <input type="checkbox" id="sameAsBilling" onclick="toggleAddress()"> Same as Billing Address
            </div>
            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Shipping State <span
                        style="color:red">*</span></label>
                        <select class="form-select " name="shistate_id" id="state_listsh" autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose State -- </option>
                            @if($product = App\Models\Statelist::get())
                            @foreach ($product as $p)

                            <option value="{{ $p->id }}"
                                {{ $p->id == $orf->quopro?->shistate_id ? 'selected' : '' }}>
                                {{ $p->state }}
                            </option>
                            @endforeach
                        @endif

                        </select>
                </div>
            </div>

            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Billing City <span
                        style="color:red">*</span></label>
                        <select class="form-select " name="citylist" id="city_list" style="background-color: #c6cbd0;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose City -- </option>
                            @if($product = App\Models\Citylist::get())
                            @foreach ($product as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $orf->lead?->citylist ? 'selected' : '' }}>
                                    {{ $p->city_name }}
                                </option>
                            @endforeach
                        @endif

                        </select>
                </div>
            </div>
            <div class="col-lg-2 mt-2">
            </div>
            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Shipping City <span
                        style="color:red">*</span></label>
                        <select class="form-select " name="shcitylist" id="shicity_list" autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose City -- </option>
                            @if($product = App\Models\Citylist::get())
                            @foreach ($product as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $orf->quopro?->shcitylist ? 'selected' : '' }}>
                                    {{ $p->city_name }}
                                </option>
                            @endforeach
                        @endif
                        </select>
                </div>
            </div>

            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Billing Postal Code <span
                        style="color:red">*</span></label>
                        <input type="decimal" name="postalcode" value="{{ $orf->lead?->postalcode }}" id="postalcode" class="form-control" style="background-color: #c6cbd0;" placeholder="Postal Code" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>
            <div class="col-lg-2 mt-2">
            </div>
            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Shipping Postal Code <span
                        style="color:red">*</span></label>
                        <input type="decimal" name="shippostalcode" id="shippostalcode" value="{{ $orf->quopro?->shippostalcode }}" class="form-control" placeholder="Postal Code" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>
            <div class="col-lg-5 mt-2">
                <div class="form-group">
                    <label>Billing GST No </label>
                    <input type="text" name="billgs" id="billgs" style="background-color: #c6cbd0;" value="{{ $orf->lead?->gst }}" class="form-control mt-2" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>
            <div class="col-lg-2 mt-2">
            </div>
            <div class="col-lg-5 mt-2">
                <div class="form-group">
                    <label>Shipping GST No </label>
                    <input type="text" name="gstnum" id="gstnum" value="{{ $orf->quopro?->gstnum }}" class="form-control mt-2" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label> Customer PO ref </label>

                    <input type="text" name="cusporef" class="form-control" value="{{ $orf->cusporef }}" placeholder="" style=""    >

                </div>
            </div>
            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>PO Date </label>
                    <input type="date" name="podate" style="text-transform:uppercase;" value="{{ $orf->podate }}"  class="form-control " >
                </div>
            </div>
            {{-- Attach Customer PO --}}
        {{-- <div class="col-lg-6 mt-4">
            <div class="form-group">
                <label>Attach Customer PO</label>
                <input type="file" name="attcuspo" class="form-control">
                @if($orf->attcuspo)
                    <small>Current File: <a href="{{ asset($orf->attcuspo) }}" target="_blank">View</a></small>
                @endif
            </div>
        </div> --}}
        <div class="col-lg-6 mt-4">
    <label class="fw-bold">Customer PO Attachments</label>

    <div class="attachment-list">
        @foreach($orf->attachments->where('type','attcuspo') as $file)
            <div class="d-flex align-items-center mb-2">
                <a href="{{ asset($file->file_path) }}" target="_blank" class="me-2">
                    {{ basename($file->file_path) }}
                </a>

                {{-- Remove --}}
                <button type="button"
                        class="btn btn-sm btn-danger remove-attachment"
                        data-id="{{ $file->id }}">
                    Remove
                </button>

                {{-- Replace --}}
                <input type="file"
                       name="replace_attcuspo[{{ $file->id }}]"
                       class="form-control form-control-sm ms-2">
            </div>
        @endforeach
    </div>

    {{-- Add new --}}
    <input type="file" name="attcuspo[]" class="form-control mt-2" multiple>
</div>

<div class="col-lg-6 mt-4">
    <label class="fw-bold">Signed Drawing Attachments</label>

    <div class="attachment-list">
        @foreach($orf->attachments->where('type','sigdraw') as $file)
            <div class="d-flex align-items-center mb-2">
                <a href="{{ asset($file->file_path) }}" target="_blank" class="me-2">
                    {{ basename($file->file_path) }}
                </a>

                <button type="button"
                        class="btn btn-sm btn-danger remove-attachment"
                        data-id="{{ $file->id }}">
                    Remove
                </button>

                <input type="file"
                       name="replace_sigdraw[{{ $file->id }}]"
                       class="form-control form-control-sm ms-2">
            </div>
        @endforeach
    </div>

    <input type="file" name="sigdraw[]" class="form-control mt-2" multiple>
</div>

        {{-- Signed Drawing --}}
        {{-- <div class="col-lg-6 mt-4">
            <div class="form-group">
                <label>Signed Drawing </label>
                <input type="file" name="sigdraw" class="form-control">
                @if($orf->sigdraw)
                    <small>Current File: <a href="{{ asset($orf->sigdraw) }}" target="_blank">View</a></small>
                @endif
            </div>
        </div> --}}
            <div class="col-lg-6 mt-2">
                <div class="form-group">
                    <label>CRD Date</label>



                    <input type="date" name="crddate" style="text-transform:uppercase;"  value="{{ $orf->crddate }}"  class="form-control mt-2" >

                </div>
            </div>

            <div class="col-lg-12 mt-2">
                <div class="form-group">
                    <label>Special Instructions</label>
                   <textarea name="specialins" class="form-control">{{ $orf->specialins }}</textarea>
                </div>
            </div>

        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-warning float-end">Update ORF</button>
        </div>
    </form>
    </div>
</section>
@endsection
