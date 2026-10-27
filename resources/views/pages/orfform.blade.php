@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
<style>
    .attachment-list {
    border: 1px dashed #dcdcdc;
    padding: 10px;
    border-radius: 6px;
    min-height: 60px;
}

.attachment-item {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #eee;
    padding: 8px 10px;
    border-radius: 6px;
    margin-bottom: 8px;
    background: #fff;
}

.attachment-item img {
    width: 38px;
    height: 38px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.attachment-name {
    flex: 1;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attachment-actions button,
.attachment-actions a {
    margin-left: 4px;
}

    </style>
@php
$countries = DB::table('countries')->where('id',76)->get(); // Fetching all countries from the database
@endphp
<section class="bg-white">
    <div class="container-fluid bg-white pt-4 pb-5 px-4">
        <div class="alert alert-warning alert-dismissible fade show d-none myalert" role="alert" id="myalert">
            <span class="error-text"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <form method="POST" action="{{ route('orf.store') }}" enctype="multipart/form-data" autocomplete="off" data-form-type="other" data-lpignore="true">
            @csrf

        <div class="row">
            <div class="col-lg-10">
                <h6 class="mb-3">Add Order Review</h6>
            </div>
            <div class="col-lg-2">
                <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
            </div>
            <input type="hidden" name="quoproid" value="{{ $quto->id }}" class="form-control" readonly>
            <input type="hidden" name="leano" value="{{ $quto->id_lead }}" class="form-control" readonly>
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>Transaction Reference</label>

                    <input type="text" name="tranref" placeholder="Transaction Reference" class="form-control" readonly style="background-color: #dedede;" value="{{ $quto->lead->lead_id ?? 'N/A' }}">

                </div>
            </div>

            @php
                $quotationId = $quto['id'];
            @endphp

            <div class="col-lg-6 mt-3">
                <div class="form-group">

                    <div class="row">
                        <div class="col-lg-6">
                            <label>Quotation Number </label>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="/quotation/view/{{ $quotationId }}" target="_blank"><i class="fa fa-eye ms-2" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <input type="text" name="quono" onblur="namevalidation(this)" style="background-color: #dedede;" placeholder="Quotation Number" class="form-control" value="{{ $quto->quota_id }}" readonly>
                </div>
            </div>

            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>Customer </label>
                    <input type="text" name="custo" onblur="namevalidation(this)" style="background-color: #dedede;" placeholder="Customer" class="form-control" value="{{ $quto->lead->LeadName ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>Quotation Date</label>
                    <input type="text" name="quotadate" style="text-transform:uppercase;background-color: #dedede;"   placeholder="Quotation Date" value="{{ $quto->created_at->format('d-m-Y') }}" class="form-control" readonly>
                </div>
            </div>
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>ORF Reference </label>

                    <input type="text" name="orfref" class="form-control" value="{{ 'ORF/' . $quto->quota_id }}" placeholder="" style="background-color: #dedede;" readonly  >

                </div>
            </div>

            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>ORF Date </label>
                    <input type="date" name="orfdate" class="form-control" style="" value="{{ old('orfdate', \Carbon\Carbon::now()->format('Y-m-d')) }}" placeholder=""    >


                </div>
            </div>
            <div class="col-lg-5 mt-3">
                <div class="form-group">
                    <label>Billing Address</label>

                    <textarea class="form-control" style="background-color: #dedede;" name="billingad" id="billingAddress" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">{{ $quto->lead->billaddress }}</textarea>

                </div>
            </div>
            <div class="col-lg-2 mt-3">
            </div>
            <div class="col-lg-5 mt-3">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea class="form-control" name="placeofsupply" id="shippingAddress" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">{{ $quto->shipping_address }}</textarea>
                </div>
            </div>


            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Billing Country <span
                        style="color:red">*</span></label>
                        <select class="form-select " style="background-color: #dedede;" name="billingcountry" id="country" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->id == 76 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach

                        </select>
                </div>
            </div>
            <div class="col-lg-2 mt-3">
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
                        <select class="form-select " name="state_id" id="state_list" style="background-color: #dedede;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose State -- </option>
                            @if($product = App\Models\Statelist::get())
                            @foreach ($product as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $quto->lead->state_id ? 'selected' : '' }}>
                                    {{ $p->state }}
                                </option>
                            @endforeach
                        @endif

                        </select>
                </div>
            </div>
            {{-- <div class="col-lg-2 mt-3" style="text-align: center;">
                <button type="button" class="btn btn-primary mt-3" style="font-size: 12px;" onclick="copyAddress()">Same as Billing Address</button>
            </div> --}}
            <div class="col-lg-2 mt-3" style="text-align: center;">
                <input type="checkbox" id="sameAsBilling" onclick="toggleAddress()" @if($quto->same_as_billing_address == 1) checked @endif  > Same as Billing Address
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
                                {{ $p->id == $quto->shipping_state ? 'selected' : '' }}>
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
                        <select class="form-select " name="citylist" id="city_list" style="background-color: #dedede;" disabled autocomplete="off" data-lpignore="true" data-form-type="other">
                            <option value=""> -- Choose City -- </option>
                            @if($product = App\Models\Citylist::get())
                            @foreach ($product as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $quto->lead->citylist ? 'selected' : '' }}>
                                    {{ $p->city_name }}
                                </option>
                            @endforeach
                        @endif

                        </select>
                </div>
            </div>
            <div class="col-lg-2 mt-3">
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
                                    {{ $p->id == $quto->shipping_city ? 'selected' : '' }}>
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
                        <input type="decimal" name="postalcode" value="{{ $quto->lead->postalcode }}" id="postalcode" class="form-control" style="background-color: #dedede;" placeholder="Postal Code" readonly autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>
            <div class="col-lg-2 mt-3">
            </div>
            <div class="col-lg-5" >
                <div class="form-group mt-4">
                    <label for="exampleFormControlInput1"
                        class="form-label">Shipping Postal Code <span
                        style="color:red">*</span></label>
                        <input type="decimal" name="shippostalcode" id="shippostalcode" value="{{ $quto->shipping_pincode }}" class="form-control" placeholder="Postal Code" autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
                </div>
            </div>
            <div class="col-lg-5 mt-3">
                <div class="form-group">
                    <label>Billing GST No </label>
                    <input type="text" name="billgs" id="billgs" style="background-color: #dedede;"  value="{{ $quto->lead->gst }}" class="form-control" readonly>
                </div>
            </div>
            <div class="col-lg-2 mt-3">
            </div>
            <div class="col-lg-5 mt-3">
                <div class="form-group">
                    <label>Shipping GST No </label>
                    <input type="text" name="gstnum" id="gstnum" value="{{ $quto->shipping_gst }}"  class="form-control" >
                </div>
            </div>
@if($isSpecialCategory)
<div class="col-lg-6 mt-3">
    <div class="form-group">
        <label>
            Special Product <span style="color:red">*</span>
        </label>

        <select name="special_product_id"
                class="form-select special-product-select"
                required>
            <option value="">-- Select Special Product --</option>

            @foreach($specialProducts as $product)
                <option value="{{ $product->id }}">
                    {{ $product->productname }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@endif


            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label> Customer PO ref <span
                        style="color:red">*</span></label>

                    <input type="text" name="cusporef" class="form-control" value="" placeholder="" style="" required>

                </div>
            </div>
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>PO Date <span
                        style="color:red">*</span></label>
                    <input type="date" name="podate" style="text-transform:uppercase;" value=""  class="form-control" required>
                </div>
            </div>
            <!-- CUSTOMER PO -->
<div class="col-lg-6 mt-3">
    <label class="form-label fw-bold">Attachment Customer PO</label>

    <div id="attcuspoPreview" class="attachment-list"></div>

    <button type="button"
            class="btn btn-sm btn-outline-primary select-filee"
            data-input="attcuspoInput">
        Attach
    </button>

    <input type="file"
           id="attcuspoInput"
           name="attcuspo[]"
           class="d-none"
           multiple>
</div>

<!-- SIGNED DRAWING -->
<div class="col-lg-6 mt-3">
    <label class="form-label fw-bold">Attachment Signed Drawing</label>

    <div id="sigdrawPreview" class="attachment-list"></div>

    <button type="button"
            class="btn btn-sm btn-outline-primary select-filee"
            data-input="sigdrawInput">
        Attach
    </button>

    <input type="file"
           id="sigdrawInput"
           name="sigdraw[]"
           class="d-none"
           multiple>
</div>


            {{-- <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>Attachment Customer PO</label>
                    <input type="file" name="attcuspo" style="text-transform:uppercase;" class="form-control">
                </div>
            </div>
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>Attachment Signed Drawing</label>
                    <input type="file" name="sigdraw" style="text-transform:uppercase" class="form-control" >
                </div>
            </div> --}}
            <div class="col-lg-6 mt-3">
                <div class="form-group">
                    <label>CRD Date</label>

                    <input type="date" name="crddate" style="text-transform:uppercase;"  value=""  class="form-control" >

                </div>
            </div>

            <div class="col-lg-12 mt-3">
                <div class="form-group">
                    <label>Special Instructions</label>
                   <textarea name="specialins" class="form-control"></textarea>
                </div>
            </div>

        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-warning float-end">Add ORF</button>
        </div>
    </form>
    </div>
</section>
@endsection



