<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function settingFunction(Request $request)
    {
        $discountPercentage = $request->discount_percentage;
        $stockCount = $request->pro_stock_alert_count;
        $gstValidation = !empty($request->gst_validation) ? 'active' : 'inactive';
        $discountStatus = !empty($request->discount_status) ? 'active' : 'inactive';
        $stockStatus = !empty($request->stock_status) ? 'active' : 'inactive';

        // Check if settings already exist
        $adminSettings = AdminSetting::first();

        if (!$adminSettings) {
            // Create new settings
            $adminSettings = new AdminSetting();
        }

        // Set values
        $adminSettings->allowed_discount = $discountPercentage;
        $adminSettings->product_stock_alert_count = $stockCount;
        $adminSettings->gst_validation = $gstValidation;
        $adminSettings->allowed_discount_status = $discountStatus;
        $adminSettings->product_stock_alert_count_status = $stockStatus;

        // Save or update
        $adminSettings->save();

        return response()->json(["status" => "success", "message" => "Admin settings updated"]);

    }

    // to get settings details function 
    public function getSettingsDetails(){
        $settings = AdminSetting::first();

        if($settings){
            return response()->json(["status"=>"success","data"=>$settings]);
        }
    }
    
}
