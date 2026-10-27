@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')
<section class="">

    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-3 ">
                <div class="" style="position: sticky;top: 13%;">

                    <div class="sidebar-container bg-white shadow " style="padding:25px 25px;border-radius:10px;">
                        <div class="form-group">
                            <label>Select Activities <span style="color:red">*</span>
                                <span style="font-size:12px;color:red">Please Select Activity..</span>
                            </label>
                            <select class="form-select mt-2 mb-3"  name="selectactivities" id="selectactivities">
                                <option value="">-- Choose Activities --</option>
                                <option value="1">Call Update</option>
                                <option value="3">Reminders</option>
                                <option value="2">Meetings Update</option>
                                <option value="4">Tasks</option>
                            </select>

                        </div>
                        <div class="form-group  disablestatusbox activestatusbox " >
                            {{-- <label>Select Type </label> --}}
                            <select class="form-select mt-2 mb-3">
                                <option value="">-- Choose Type --</option>
                                <option value="1">Leads</option>
                                <option value="2">Renewals</option>
                            </select>
                        </div>

                        <div class="form-group "  id="status" >
                            <label>Status</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox"  id="Activity1" wire:change='checkboxchecked' name="getActivityData[]" value="0" wire:model="getActivityData">
                                <label class="form-check-label" for="Activity1" >
                                Open
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"  id="Activity2" wire:change='checkboxchecked' name="getActivityData[]" value="1" wire:model="getActivityData">
                                <label class="form-check-label" for="Activity2">
                                Closed
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"  id="Activity3" wire:change='checkboxchecked' name="getActivityData[]" value="2" wire:model="getActivityData">
                                <label class="form-check-label" for="Activity3">
                                Overdue
                                </label>
                            </div>
                        </div>

                        {{-- <input type="text" name="" class="picdate form-control mt-4" wire:model='filterdate' placeholder="Select Date Range"> --}}
                        <div class="form-group">
                            {{-- <label>From date</label> --}}
                            <input type="text" placeholder="Select From Date.." wire:model='fromdate' wire:change='fromdatechange' class="form-control mt-2" id="fromdate" style="background:none" autocomplete="off">
                        </div>

                        <div class="form-group mt-3">
                            {{-- <label>To date</label> --}}
                            <input type="text" placeholder="Select ToDate..." wire:model='todate' wire:change='todatechange' class="form-control mt-2" id="todate" style="background:none" autocomplete="off">
                        </div>


                        <div class="form-group mt-3">
                            {{-- <label>Select User </label> --}}
                            <select class="form-select mt-2" name="userdetails" wire:change='useridchange' wire:model='userid' >
                                <option value="">-- Select User --</option>

                            </select>
                        </div>



                        <div class="form-group mt-3">
                            {{-- <label>Select Call Status</label> --}}
                            <div class="accordion mt-2" id="accordionExample">
                                <div class="accordion-item">
                                  <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" style="font-size:12px;padding:7px 10px;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                      Select Call Status
                                    </button>
                                  </h2>
                                  <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{-- <div class="form-check">
                                            <input class="form-check-input callinfocheckbox" type="checkbox" wire:change='selectcallupdate' value="0" id="Speeched" wire:model="getCallstatusData" wire:loading.attr="disabled">
                                            <label class="form-check-label" for="Speeched">
                                                Talked
                                            </label>
                                        </div> --}}

                                                <div class="form-check">
                                                    <input class="form-check-input callinfocheckbox" type="checkbox" wire:change='selectcallupdate' id=""   value=""  >
                                                    <label class="form-check-label" for="">

                                                    </label>
                                                </div>



                                    </div>
                                  </div>
                                </div>

                            </div>
                        </div>



                        <button type="button"  class="activityfilter mt-3 btn btn-warning w-100">Apply Filter</button>
                    </div>
                </div>

            </div>

            <div class="col-lg-9 ">
                <div class="filter-section shadow" style="padding:25px;background-color:#fff;border-radius:10px">





                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <img src="/assets/images/filter.png" class="img-fluid m-auto d-block">
                                <p class="text-center mt-3"><span style="color:#FF1000"><b>Note</b></span> Click <b>Apply Filter</b> Button after fill the form</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-lg-10">
                                <h5>Call Update Details </h5>
                            </div>
                            <div class="col-lg-2 d-flex justify-content-end">
                                <button class="btn btn-primary" wire:click.prevent='showTask()'>Add Task</button>
                            </div>
                        </div>

                                    <table class="table table-stripped table-bordered mt-2">
                                        <thead class="bg-warning text-dark">
                                            <tr>
                                                <th colspan="6" class="text-center">Call Update Details </th>
                                            </tr>
                                            <tr class="text-center">

                                                <th style="width:10%">Type</th>
                                                <th>Client Name</th>
                                                <th>Company Name</th>
                                                <th>Last Call Update</th>
                                                <th>Assigned</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                                    <tr class="text-center" style="vertical-align: middle">
                                                        <td>


                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                    </tr>

                                        </tbody>
                                    </table>




                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <img src="/assets/images/filter.png" class="img-fluid m-auto d-block">
                                <p class="text-center mt-3"><span style="color:#FF1000"><b>Note</b></span> Click <b>Apply Filter</b> Button after fill the form</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-lg-10">
                                <h5>Meeting Update Details </h5>
                            </div>
                            <div class="col-lg-2 d-flex justify-content-end">
                                <button class="btn btn-primary" >Add Task</button>
                            </div>
                        </div>


                            <table class="table table-bordered mt-2">
                                <thead class="bg-warning text-dark">
                                    <tr>
                                        <th colspan="6" class="text-center">Meeting Update Details </th>
                                    </tr>
                                    <tr class="text-center">

                                        <th style="width:10%">Type</th>
                                        <th>Client Name</th>
                                        <th>Company Name</th>
                                        <th>Last Call Update</th>
                                        <th>Assigned</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>


                                            <tr class="text-center" style="vertical-align: middle">
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>


                                                </td>
                                            </tr>

                                </tbody>
                            </table>



                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <img src="/assets/images/filter.png" class="img-fluid m-auto d-block">
                                <p class="text-center mt-3"><span style="color:#FF1000"><b>Note</b></span> Click <b>Apply Filter</b> Button after fill the form</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-lg-10">
                                <h5>Reminder Update Details</h5>
                            </div>
                            <div class="col-lg-2 d-flex justify-content-end">
                                <button class="btn btn-primary">Add Task</button>
                            </div>
                        </div>


                            <table class="table table-bordered mt-2">
                                <thead class="bg-warning text-dark" >
                                    <tr>
                                        <th colspan="7" class="text-center">Reminder Details </th>
                                    </tr>
                                    <tr class="text-center">

                                        <th style="width:10%">Type</th>
                                        <th>Client Name</th>
                                        <th>Company Name</th>
                                        <th>Last Call Update</th>
                                        <th>Reminder Date</th>
                                        <th>Assigned</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>


                                            <tr class="text-center" style="vertical-align: middle">
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>


                                                </td>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>


                                                </td>
                                            </tr>

                                </tbody>
                            </table>

                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <img src="/assets/images/filter.png" class="img-fluid m-auto d-block">
                                                <p class="text-center mt-3"><span style="color:#FF1000"><b>Note</b></span> Click <b>Apply Filter</b> Button after fill the form</p>
                                            </div>
                                        </div>
                                    </div>

                        <div class="row">
                            <div class="col-lg-10">
                                <h5>Task Details </h5>
                            </div>
                            <div class="col-lg-2 d-flex justify-content-end">
                                <button class="btn btn-primary" >Add Task</button>
                            </div>
                        </div>


                            <div class="card shadow mt-3" style="border-left:7px solid green;border-radius:10px">
                                <div class="card-body" style="overflow:hidden;">
                                    <span style="margin-top:10px;margin-right:50px">Date : <span style="background-color: #FFBD00;padding:0px 15px;font-size:12px;border-radius:50px;color:#000"></span></span>
                                    <span style="margin-top:10px;margin-right:50px">Status :  <span style="font-size:12px;color:#fff;border-radius:50px;padding:0px 15px;background-color:#00BE24">Closed</span>  <span style="font-size:12px;color:#fff;border-radius:50px;padding:0px 15px;background-color:#FF1000">Open</span>  </span>
                                    <span style="margin-top:10px">Owner : <span style="background-color: #2290FF;padding:0px 15px;font-size:12px;border-radius:50px;color:#fff"></span>
                                    <div class="mt-3">
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                                    <div class="notes-form-details shadow mt-3 bg-white">
                                        <img src="/images/nodata.png" class="img-fluid m-auto d-block" style="width:250px;padding:50px;opacity:0.3">
                                    </div>

            </div>



            </div>
        </div>
    </div>
</section>

@endsection

