<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Lead;
use App\Models\Estimate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Leadproduct;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Design;
use App\Models\Quotation;
use App\Models\ProductCategory;

class LeadControllerApi extends Controller
{

    public function sourceEnquiryDropdown()
    {
        $data = DB::table('dropdowndatas')->where('formid', '=', '4')->get();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }

    public function customersList($cusId = null)
    {
        if ($cusId) {
            $data = DB::table('customers')->where('id', '=', $cusId)->get();

        } else {
            $data = DB::table('customers')->get();
        }
        return ResponseHelper::successResponseData('Success', 'Customers data successfully', $data);
    }

    public function leadData($userId)
    {
        // dd($userId);
        $leads = Lead::where('assigned_to', '=', $userId)->where('Leadstatus', '!=', "343")->orWhereNull('Leadstatus')
            ->leftJoin('users', 'leads.assigned_to', '=', 'users.id')
            ->leftJoin('dropdowndatas', 'leads.Leadsource', '=', 'dropdowndatas.id')
            ->select('leads.*', 'users.name as assignedto', 'dropdowndatas.dropdowndata')
            ->orderBy('leads.id', 'desc')
            ->get();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $leads);

    }
    // public function leadData($userId)
    // {

    //     $leads = Lead::where('assigned_to', '=',$userId)->where('Leadstatus', '!=' ,"343")
    //     ->leftJoin('users', 'leads.assigned_to', '=', 'users.id')
    //     ->leftJoin('dropdowndatas', 'leads.Leadsource', '=', 'dropdowndatas.id')
    //     ->select('leads.*', 'users.name as assignedto','dropdowndatas.dropdowndata') 
    //     ->orderBy('leads.id', 'desc')
    //     ->get();
    //     return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $leads);

    // }
    public function addLeads(Request $request)
    {

        $leaddata = $request->renewal_id;
        $meetingsdata = $request->meeting_data;
        $activitydate = $request->meeting_date;

        if ($activitydate == null || !$activitydate) {
            $activitydate = date("Y-m-d");
        }
        DB::table('meetings')
            ->insert([
                "notes_data" => $meetingsdata,
                "purposeid_2" => $leaddata,
                "notification_date" => $activitydate,
                "notification_time" => "12:00",
                "status" => 0,
                "userid" => $userId,
                "created_date" => $activitydate
            ]);

        return successResponse("Renewal Meeting added Successfully");
    }



    public function saveleads(Request $request)
    {

        $data = $request->except('_token', 'newCustomerName');

        if ($request->LeadName === 'new' && $request->newCustomerName) {
            // Store the entered new customer name
            $data['LeadName'] = $request->newCustomerName;
        } else {
            // Store the selected existing customer name
            $data['LeadName'] = $request->LeadName;
        }

        if ($request->assigned_to != '') {
            $assignto = $request->assigned_to;
        } else {
            $assignto = Auth::user()->id;
        }

        // Insert the data into the leads table
        // Lead::create($data);
        $lead = Lead::create($data);
        $lastInsertId = $lead->id;

        $leadproduct = DB::table('leadproducts')
            ->insertGetId([
            "singleproduct" => $request->products,
            "leadid" => $lastInsertId,
            "statuslead" => $request->Leadstatus,
            "enquirydate" => $request->Entrydate,
        ]);
        $maxValue = $lastInsertId;
        $invID = $maxValue;
        $invID = str_pad($invID, 4, '0', STR_PAD_LEFT);
        $leadID = "LEAD" . $invID;

        DB::table('leads')
            ->where('id', $lastInsertId)
            ->update([
                    "lead_id" => $leadID,
                    "assigned_to" => $assignto,

                ]);

        $leadrt = Lead::with(['assignedTo', 'leadstatus'])->find($lastInsertId);

        $adminEmail = 'nvikram.sts@gmail.com'; // Replace with your admin email
        $assignedUserEmail = User::find($assignto)->email ?? null;

        // Mail::to($adminEmail)->send(new LeadCreatedMail($leadrt));

        // if ($assignedUserEmail) {
        //     Mail::to($assignedUserEmail)->send(new LeadCreatedMail($leadrt));
        // }

        return ResponseHelper::successResponse("Renewal Meeting added Successfully");


    }


    public function updateStatuslead(Request $request)
    {
        // Find the lead
        $lead = Lead::find($request->lead_id);

        if ($lead) {
            // Update lead details
            $lead->Leadstatus = $request->status;
            $lead->category = $request->category;
            $lead->products = $request->products;
            $lead->save();

            $customerId = null;
            // dd($request);
            // Check if status is 'CONVERTED'
            if ($request->status == '343') {
                // Check if the mobile number already exists in the customer table
                $existingCustomer = Customer::where('contact_no', $lead->phonumber)->first();

                if (!$existingCustomer) {
                    // Insert data into customer table only if the customer does not exist
                    $customer = new Customer();
                    $customer->customername = $lead->LeadName;
                    $customer->contact_name = $lead->contactname;
                    $customer->cus_email = $lead->email;
                    $customer->country_code = $lead->countrycode;
                    $customer->contact_no = $lead->phonumber;
                    $customer->department = $lead->department;
                    $customer->designation = $lead->designation;
                    $customer->address = $lead->billaddress;
                    $customer->country = $lead->billingcountry;
                    $customer->city = $lead->citylist;
                    $customer->state = $lead->state_id;
                    $customer->postalcode = $lead->postalcode;
                    $customer->gstno = $lead->gst;
                    $customer->save();

                    // Generate cusid after saving customer
                    $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
                    $customer->save();

                    $customerId = $customer->cusid;
                } else {
                    $customerId = $existingCustomer->cusid;
                }

                // Update the 'customeriid' in the leads table
                $lead->customeriid = $customerId;
                $lead->save();
            }

            // Determine the table for insertion based on product segment workflow.
            $selectedCategory = ProductCategory::find($request->category);
            $categoriesForDesign = $selectedCategory && $selectedCategory->sub_category === 'special';

            // if (in_array($request->category, $categoriesForDesign)) {
            if ($categoriesForDesign) {
                // Insert into `designs` table
                Design::create([
                    'idlead' => $lead->id,
                    'idcustomer' => $customerId,
                    'product' => $request->products,
                    'category' => $request->category,
                    'ganumber' => $request->ga_number,
                    'quantity' => $request->quantity
                ]);

                $lead->quota_proceed = null;
                $lead->save();
            } else {
                // Insert into `quotations` table
                Quotation::create([
                    'id_lead' => $lead->id,
                    'id_customer' => $customerId,
                    'products' => $request->products,
                    'categorys' => $request->category,
                    'ga_number' => $request->ga_number,
                    'quantitys' => $request->quantity,
                    'quota_id' => 'QUTO/' . date('Y') . '/' . str_pad(Quotation::max('id') + 1, 4, '0', STR_PAD_LEFT)
                ]);
                $lead->quota_proceed = 1;
                $lead->save();
            }
            return ResponseHelper::successResponse("Lead and products updated successfully");
        }
        return ResponseHelper::successResponse("Lead not found");
    }

}
