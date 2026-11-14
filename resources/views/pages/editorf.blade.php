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

            @php
                $poAttachmentsCount = $orf->attachments->where('type','attcuspo')->count();
                $isPoChecked = old('check_po', $orf->check_po) || $poAttachmentsCount > 0 || !empty($orf->attcuspo);

                $drawingAttachmentsCount = $orf->attachments->where('type','sigdraw')->count();
                $isDrawingChecked = old('check_drawing', $orf->check_drawing) || $drawingAttachmentsCount > 0 || !empty($orf->sigdraw);
            @endphp

            <!-- Verification Checklist -->
            <div class="col-lg-12 mt-4">
                <div class="p-3 border rounded bg-light">
                    <label class="form-label fw-bold mb-3 text-dark fs-6">Verification Checklist</label>
                    <div class="row g-3">
                        <div class="col-md-6 col-6">
                            <div class="form-check">
                                <input class="form-check-input orf-verify-check" type="checkbox" name="check_po" id="check_po" value="1" {{ $isPoChecked ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="check_po">PO verified</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="form-check">
                                <input class="form-check-input orf-verify-check" type="checkbox" name="check_drawing" id="check_drawing" value="1" {{ $isDrawingChecked ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="check_drawing">Drawing verified</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer PO Attachments Container -->
            <div class="col-lg-6 mt-4" id="po_attachments_container" style="{{ $isPoChecked ? '' : 'display: none;' }}">
                <label class="fw-bold mb-2">Customer PO Attachments</label>

                <div class="attachment-list mb-2">
                    @foreach($orf->attachments->where('type','attcuspo') as $file)
                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-light">
                            <a href="{{ asset($file->file_path) }}" target="_blank" class="text-truncate me-2 text-primary" style="max-width: 80%;">
                                <i class="fas fa-file-pdf text-danger me-1"></i> {{ basename($file->file_path) }}
                            </a>

                            {{-- Remove --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-attachment"
                                    data-id="{{ $file->id }}">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Add new --}}
                <input type="file" name="attcuspo[]" class="form-control" multiple>
            </div>

            <!-- Signed Drawing Attachments Container -->
            <div class="col-lg-6 mt-4" id="drawing_attachments_container" style="{{ $isDrawingChecked ? '' : 'display: none;' }}">
                <label class="fw-bold mb-2">Signed Drawing Attachments</label>

                <div class="attachment-list mb-2">
                    @foreach($orf->attachments->where('type','sigdraw') as $file)
                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-light">
                            <a href="{{ asset($file->file_path) }}" target="_blank" class="text-truncate me-2 text-primary" style="max-width: 80%;">
                                <i class="fas fa-file-pdf text-danger me-1"></i> {{ basename($file->file_path) }}
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-attachment"
                                    data-id="{{ $file->id }}">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>

                <input type="file" name="sigdraw[]" class="form-control" multiple>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkPo = document.getElementById('check_po');
        var checkDrawing = document.getElementById('check_drawing');
        var poContainer = document.getElementById('po_attachments_container');
        var drawingContainer = document.getElementById('drawing_attachments_container');

        function toggleContainer(container, isVisible, animate) {
            if (!container) return;
            if (window.jQuery && animate) {
                if (isVisible) {
                    window.jQuery(container).stop(true, true).slideDown(200);
                } else {
                    window.jQuery(container).stop(true, true).slideUp(200);
                }
            } else {
                container.style.display = isVisible ? '' : 'none';
            }
        }

        function updateAttachmentVisibility(animate) {
            toggleContainer(poContainer, checkPo && checkPo.checked, animate);
            toggleContainer(drawingContainer, checkDrawing && checkDrawing.checked, animate);
        }

        if (checkPo) {
            checkPo.addEventListener('change', function () {
                updateAttachmentVisibility(true);
            });
        }

        if (checkDrawing) {
            checkDrawing.addEventListener('change', function () {
                updateAttachmentVisibility(true);
            });
        }

        updateAttachmentVisibility(false);
    });
</script>
@endsection
