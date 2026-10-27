@extends('layouts.app')
@section('main-content')
    <div class="container dashboard-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="dasboard-welcome-card">
                    <div class="dashboard">
                        <h3><b>Hi, {{ Auth::user()->name }} </b></h3>
                        <p><b>Ready to start your day with some pitch decks?</b></p>
                    </div>
                </div>
            </div>
        </div>

        @canany(['order-booking-value-sales', 'order-booking-value-spares', 'total-billing-value', 'total-outstanding-value', 'total-payable-value'])
            <div class="row mt-5" style="justify-content: center;">
                <div style="position: relative;">
                    <button class="btn btn-light" id="filterToggle" style="position: absolute; top: -10px; right: -30px;">
                        <i class="mdi mdi-filter-plus-outline"></i> <!-- Bootstrap icon -->
                    </button>
                </div>
                <h4 class="mb-4">Total Order Booking</h4>
                @can('order-booking-value-sales')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70 dashboard-card" data-type="sales"
                            style="background:#fc6704;border-radius:15px;height: 110px; cursor: pointer;">
                            <div class="card-body">
                                <span style="color:#fff;">Order Booking Value - Sales</span>
                                <h4 class="text-white"><span class="dashboard-count" data-type="sales">0</span></h4>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('order-booking-value-spares')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70 dashboard-card" data-type="spares"
                            style="background:#643378;border-radius:15px;height: 110px; cursor: pointer;">
                            <div class="card-body">
                                <span style="color:#fff;">Order Booking Value - Spares & Services</span>
                                <h4 class="text-white"><span class="dashboard-count" data-type="spares">0</span></h4>
                            </div>
                        </div>
                    </div>
                @endcan

                {{-- <div class="col-xl-3 col-md-6">

                    <div class="card card-h-70" id="orderCard"
                        style="background:#fc6704;border-radius:15px;height: 110px; cursor: pointer;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6" style="width: 100%;">
                                    <span class=" mb-3 d-block " style="color:#fff;">Order Booking Value - Sales</span>
                                    <h4 class="text-white">
                                        <span id="orderCount">0</span>
                                    </h4>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-70" style="background:#643378;border-radius:15px;height: 110px;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6" style="width: 100%;">
                                    <span class=" mb-3 d-block " style="color:#fff;">Order Booking Value - Spares &
                                        Services</span>
                                    <h4 class="mb-3">
                                        <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                    </h4>
                                </div>

                            </div>

                        </div>
                    </div>
                </div> --}}
                @can('total-billing-value')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70" style="background:#366a7d;border-radius:15px;height: 110px;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6" style="width: 100%;">
                                        <span class=" mb-3 d-block " style="color:#fff;">Total Billing Value</span>
                                        <h4 class="mb-3">
                                            <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                        </h4>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @endcan
                @can('total-outstanding-value')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70" style="background:#225885;border-radius:15px;height: 110px;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6" style="width: 100%;">
                                        <span class=" mb-3 d-block " style="color:#fff;">Total Outstanding Value</span>
                                        <h4 class="mb-3">
                                            <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('total-payable-value')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70" style="background:#d84744;border-radius:15px;height: 110px;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6" style="width: 100%;">
                                        <span class=" mb-3  d-block " style="color:#fff;">Total Payable Value</span>
                                        <h4 class="mb-3">
                                            <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        @endcanany
        @canany(['stock-value-equipment', 'stock-value-spares'])
            <div class="row mt-5" style="justify-content: center;">
                <h4 class="me-2 mb-3">Stock Values</h4>
                @can('stock-value-equipment')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70" style="background:#fc6704;border-radius:15px;height: 90px;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6" style="width: 100%;">
                                        <span class=" mb-3 d-block text-truncate" style="color:#fff;">Stock Value Equipment</span>
                                        <h4 class="mb-3">
                                            <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                        </h4>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @endcan
                @can('stock-value-spares')
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-70" style="background:#225885;border-radius:15px;height: 90px;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6" style="width: 100%;">
                                        <span class=" mb-3 d-block text-truncate" style="color:#fff;">Stock Value Spares</span>
                                        <h4 class="mb-3">
                                            <span class="counter-value" data-target="0" style="color:#fff;">0</span>
                                        </h4>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        @endcanany
        <div class="row mt-5">
            <h4 class="me-2 mb-3">Leads Status Summary</h4>
            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100 lead-card" data-bs-toggle="modal" data-bs-target="#leadsModal">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <span class="mb-3  d-block text-truncate" style="color:#000;">Total Leads</span>
                                <h4 class="mb-3">
                                    <span class="" style="color:#000;">{{ $totalLeads }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach($leadsstatus as $leadsta)
                @php
                    $modalId = 'Modal' . str_replace(' ', '_', $leadsta->dropdowndata);
                @endphp
                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100 lead-card" data-bs-toggle="modal" data-bs-target="#Modal{{ $modalId }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="mb-3  d-block text-truncate"
                                        style="color:#000;">{{ $leadsta->dropdowndata }}</span>
                                    <h4 class="mb-3">
                                        <span class="" style="color:#000;">{{ $leadsta->lead_count }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
        <div class="row mt-4">
            <h4 class="me-2 mb-3">Leads Source Summary</h4>
            @foreach($leads as $lead)
                @php
                    $modalId = 'Modal' . str_replace(' ', '_', $lead->dropdowndata);
                @endphp
                <div class="col-xl-3 col-md-6">

                    <div class="card card-h-100 lead-card" data-bs-toggle="modal" data-bs-target="#Modal{{ $modalId }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <span class="mb-3 d-block text-truncate"
                                        style="color:#000;">{{ $lead->dropdowndata }}</span>
                                    <h4 class="mb-3">
                                        <span class="" style="color:#000;">{{ $lead->lead_count }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>



        <div class="row mt-4">

            <!-- quotation hidden details -->
            <input type="hidden" id="quotation_total_count" value="{{ $quotationSummary['quotationTotalCount'] }}">
            <input type="hidden" id="quotation_total_value" value="{{ $quotationSummary['quotationAmountTotal'] }}">
            <input type="hidden" id="quotation_approved_count" value="{{ $quotationSummary['quotationApprovedCount'] }}">
            <input type="hidden" id="quotation_approved_total" value="{{ $quotationSummary['quotationApprovedTotal'] }}">
            <input type="hidden" id="quotation_pending_count" value="{{ $quotationSummary['quotationPendingCount'] }}">
            <input type="hidden" id="quotation_pending_Total" value="{{ $quotationSummary['quotationPendingTotal'] }}">

            <!-- orf hidden details -->
            <input type="hidden" id="orf_total_count" value="{{ $orfSummary['orfCount'] }}">
            <input type="hidden" id="orf_total_value" value="{{ $orfSummary['orfAmountTotal'] }}">
            <input type="hidden" id="orf_approved_count" value="{{ $orfSummary['orfApprovedCount'] }}">
            <input type="hidden" id="orf_approved_total" value="{{ $orfSummary['orfApprovedAmountTotal'] }}">
            <input type="hidden" id="orf_pending_count" value="{{ $orfSummary['orfPendingCount'] }}">
            <input type="hidden" id="orf_pending_Total" value="{{ $orfSummary['orfPendingTotal'] }}">

            <!-- Quotation summary -->
            <div class="col-xl-6 col-md-6">
                <h4 class="me-2 mb-3">Quotation Summary</h4>
                <div class="card card-h-100 lead-card">

                    <div class="card-body d-flex align-items-center">
                        <a href="/viewquota"></a>
                        <div class="quota-orf-summary">
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $quotationSummary['quotationTotalCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#000;font-size:15px;">₹{{  $quotationSummary['quotationAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            {{-- <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Approved count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $quotationSummary['quotationApprovedCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#28a745;">₹{{ $quotationSummary['quotationApprovedTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Pending count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $quotationSummary['quotationPendingCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#dc3545;">₹{{ $quotationSummary['quotationPendingTotal'] }}</span>
                                    </h4>
                                </div>
                            </div> --}}
                        </div>
                        </a>
                    </div>

                </div>
            </div>
<div class="col-xl-6 col-md-6">
                <h4 class="me-2 mb-3">PO Summary</h4>
                <div class="card card-h-100 lead-card">

                    <div class="card-body d-flex align-items-center">
                        <a href="/vieworfapp"></a>
                        <div class="quota-orf-summary">
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total PO count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $poSummary['poCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span style="color:#000;font-size:15px;">₹{{ $poSummary['poAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Approved count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $poSummary['poCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#28a745;font-size:15px;">₹{{ $poSummary['poAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Pending count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $poSummary['poCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#dc3545;font-size:15px;">₹{{ $poSummary['poAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>

            <!-- ORF summary -->
            <div class="col-xl-6 col-md-6 mt-5">
                <h4 class="me-2 mb-3">ORF Summary</h4>
                <div class="card card-h-100 lead-card">

                    <div class="card-body d-flex align-items-center">
                        <a href="/vieworfapp"></a>
                        <div class="quota-orf-summary">
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $orfSummary['orfCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span style="color:#000;font-size:15px;">₹{{ $orfSummary['orfAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Approved count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $orfSummary['orfApprovedCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#28a745;font-size:15px;">₹{{ $orfSummary['orfApprovedAmountTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Pending count</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">{{ $orfSummary['orfPendingCount'] }}</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#dc3545;font-size:15px;">₹{{ $orfSummary['orfPendingTotal'] }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mt-5">
                <h4 class="me-2 mb-3">Outstanding Summary</h4>
                <div class="card card-h-100 lead-card">

                    <div class="card-body d-flex align-items-center">
                        <a href="/vieworfapp"></a>
                        <div class="quota-orf-summary">
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total Outstanding</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">0</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span style="color:#000;font-size:15px;">₹0</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total Invoiced</span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">0</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#28a745;font-size:15px;">₹0</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="border-line"></div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <span class=" d-block text-truncate" style="color:#000;">Total Received </span>
                                </div>
                                <div class="col-3">
                                    <h4 class="">
                                        <span style="color:#000;">0</span>
                                    </h4>
                                </div>
                                <div class="col-4">
                                    <h4>
                                        <span
                                            style="color:#dc3545;font-size:15px;">₹0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>

        </div>

        <div class="row mt-5">
            <div class="col-xl-6">
                <div class="quotation_chart_div">
                    <canvas id="quotation_chart"></canvas>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="orf_chart_div">
                    <canvas id="orf_chart"></canvas>
                </div>
            </div>
        </div>


    </div>



    </div>




    <div class="modal fade" id="leadsModal" tabindex="-1" aria-labelledby="leadsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadsModalLabel">All Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="leadsTable">
                        <thead>
                            <tr>
                                <th>Entry Date</th>
                                <th>Lead Name</th>
                                <th>Lead Source</th>
                                <th>Lead Status</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Leads Data Will be Loaded Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@php
    $userss = auth()->user();
    $isSuperAdmin = $userss->role === 'Super Admin';
@endphp

    @foreach($leadsstatus as $leadsta)
        @php
            $modalId = 'Modal' . str_replace(' ', '_', $leadsta->dropdowndata);
        @endphp
        <div class="modal fade" id="Modal{{ $modalId }}" tabindex="-1" aria-labelledby="ModalLabel{{ $leadsta->id }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel{{ $leadsta->id }}">{{ $leadsta->dropdowndata }} Leads</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Entry Date</th>
                                    <th>Lead Name</th>
                                    <th>Lead Source</th>
                                    <th>Lead Status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @php
$filteredLeads = DB::table('leads')
    ->where('leads.Leadstatus', $leadsta->id)
    ->when(!$isSuperAdmin, function ($q) use ($userss) {
        $q->where('leads.assigned_to', $userss->id);
    })
    ->leftJoin('dropdowndatas as source', 'leads.Leadsource', '=', 'source.id')
    ->leftJoin('dropdowndatas as status', 'leads.Leadstatus', '=', 'status.id')
    ->select(
        'leads.id',
        'leads.LeadName',
        'source.dropdowndata as Leadsource',
        'leads.Entrydate',
        'status.dropdowndata as Leadstatus'
    )
    ->get();
@endphp

                                @foreach($filteredLeads as $lead)
                                    <tr>
                                        <td>{{ $lead->Entrydate }}</td>
                                        <td>{{ $lead->LeadName }}</td>
                                        <td>{{ $lead->Leadsource }}</td>
                                        <td>{{ $lead->Leadstatus }}</td>
                                        {{-- <td> <a href="singlelead/{{ $lead->id }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    @foreach($leads as $lead)
        @php
            $modalId = 'Modal' . str_replace(' ', '_', $lead->dropdowndata);
        @endphp
        <div class="modal fade" id="Modal{{ $modalId }}" tabindex="-1" aria-labelledby="ModalLabel{{ $lead->id }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel{{ $lead->id }}">{{ $lead->dropdowndata }} Leads</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Entry Date</th>
                                    <th>Lead Name</th>
                                    <th>Lead Source</th>
                                    <th>Lead Status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                               @php
$filteredLeads = DB::table('leads')
    ->where('leads.Leadsource', $lead->id)
    ->when(!$isSuperAdmin, function ($q) use ($userss) {
        $q->where('leads.assigned_to', $userss->id);
    })
    ->leftJoin('dropdowndatas as source', 'leads.Leadsource', '=', 'source.id')
    ->leftJoin('dropdowndatas as status', 'leads.Leadstatus', '=', 'status.id')
    ->select(
        'leads.id',
        'leads.LeadName',
        'source.dropdowndata as Leadsource',
        'leads.Entrydate',
        'status.dropdowndata as Leadstatus'
    )
    ->get();
@endphp

                                @foreach($filteredLeads as $lead)
                                    <tr>
                                        <td>{{ $lead->Entrydate }}</td>
                                        <td>{{ $lead->LeadName }}</td>
                                        <td>{{ $lead->Leadsource }}</td>
                                        <td>{{ $lead->Leadstatus }}</td>
                                        {{-- <td> <a href="singlelead/{{ $lead->id }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div id="filterBox" class="card p-3"
        style="position: fixed; top: 80px; right: 20px; z-index: 1000; width: 300px; display: none;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
        <div class="text-end mb-2">
            <button type="button" id="closeFilterBox" class="btn btn-sm btn-outline-danger">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        <form id="dashboardFilter">
            <div class="mb-2">
                <label>From Date</label>
                <input type="date" name="fromdate" class="form-control">
            </div>
            <div class="mb-2">
                <label>To Date</label>
                <input type="date" name="todate" class="form-control">
            </div>
            <div class="mb-2">
                <label>Assigned To</label>
                <select name="assignedto" class="form-control">
                    <option value="">All</option>
                    {{-- @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="mb-2">
                <label>Quick Filter</label>
                <select name="period" class="form-control">
                    <option value="">Custom</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                </select>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
            </div>
        </form>
    </div>

    {{-- <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="orderTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Product Category</th>
                                <th>ORF No </th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table" id="orderTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Segment</th>
                                <th>ORF Ref</th>
                                <th>ORF Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
