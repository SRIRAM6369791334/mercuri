@extends('layouts.app')
@section('main-content')

    @php
        $settingsDetails = App\Models\AdminSetting::first();
        $allowedDiscountStatus = $settingsDetails->allowed_discount_status == 'active' ? true : false;
        $stockStatus = $settingsDetails->product_stock_alert_count_status == 'active' ? true : false;
        $gstStatus = $settingsDetails->gst_validation == 'active' ? true : false;
    @endphp

    <form action="" id="settingForm">
        @csrf
        <div class="container">

            <div class="card">
                <div class="card-header">
                    Profile Details
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle header-profile-user" src="/assets/images/people.png" alt="User">
                        <h4>{{ Auth::user()->name }}</h4>
                    </div>
                    <h5 class="mt-3">Role: {{ Auth::user()->role }}</h5>
                    <h5 class="mt-3">Mobile number: {{ Auth::user()->mobilenumber }}</h5>
                    <h5 class="mt-3">Email: {{ Auth::user()->email }}</h5>
                </div>
            </div>

            @if(Auth::user()->role == 'Super Admin' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Director')

            <div class="card">
                <div class="card-header">
                    Default settings
                </div>
                <div class="card-body">
                    <div class="settings-inner">
                        <div class="settings-box discount-allowed">
                            <div class="d-flex align-items-center">
                                <h5>Allowed Discount(%)</h5>
                                <div class="ms-3 checkbox-wrapper-22">
                                    <label class="switch" for="discount_checkbox">
                                        <input type="checkbox" name="discount_status" id="discount_checkbox" @if($allowedDiscountStatus) checked @endif />
                                        <div class="slider round"></div>
                                    </label>
                                </div>
                            </div>
                            <input type="number" name="discount_percentage" class="form-control" placeholder="Type" required value="{{ $settingsDetails->allowed_discount ?? ''}}">
                        </div>
                        <div class="settings-box discount-allowed">
                            <div class="d-flex align-items-center">
                                <h5>Product Stock Alert Count</h5>
                                <div class="ms-3 checkbox-wrapper-22">
                                    <label class="switch" for="stock">
                                        <input type="checkbox" id="stock" name="stock_status" @if($stockStatus) checked @endif />
                                        <div class="slider round"></div>
                                    </label>
                                </div>
                            </div>
                            <input type="number" name="pro_stock_alert_count" class="form-control" placeholder="Type"
                                required value="{{ $settingsDetails->product_stock_alert_count ?? '' }}">
                        </div>
                        <div class="settings-box discount-allowed">
                            <div class="d-flex align-items-center">
                                <h5>GST Validation</h5>
                                <div class="ms-3 checkbox-wrapper-22">
                                    <label class="switch" for="gst_checkbox">
                                        <input type="checkbox" name="gst_validation" id="gst_checkbox" @if($gstStatus) checked @endif  />
                                        <div class="slider round"></div>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="text-center mt-5">
                            <button type="button" class="btn btn-primary w-50" id="setting-submit">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            @endif



            <div class="card">
                <div class="card-body">
                    <a class="dropdown-item" href="/logout"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i>
                        Logout</a>
                </div>
            </div>

        </div>
    </form>

    

@endsection
