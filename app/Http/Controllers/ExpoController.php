<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Dropdowndata;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpoController extends Controller
{
    /**
     * Get the Expo source Dropdowndata ID.
     */
    private function getExpoSourceId(): int
    {
        $expo = Dropdowndata::where('formid', 4)
            ->where('dropdowndata', 'Expo')
            ->first();

        if (!$expo) {
            $maxOrder = Dropdowndata::where('formid', 4)->max('orderno') ?? 0;
            $expo = Dropdowndata::create([
                'formid' => 4,
                'dropdowndata' => 'Expo',
                'orderno' => $maxOrder + 1,
            ]);
        }

        return (int) $expo->id;
    }

    /**
     * Display Expo entries list (styled like customer list).
     */
    public function expo()
    {
        $expoSourceId = $this->getExpoSourceId();

        $expoList = Lead::where('leads.Leadsource', $expoSourceId)
            ->where('leads.soft_delete', 0)
            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('state_list', 'state_list.id', '=', 'leads.state_id')
            ->leftJoin('customers', 'customers.id', '=', 'leads.customeriid')
            ->select(
                'leads.*',
                'city_list.city_name',
                'state_list.state as state_name',
                'customers.cusid'
            )
            ->orderBy('leads.id', 'desc')
            ->get();

        return view('pages.expo.index', compact('expoList'));
    }

    /**
     * Show form to add new Expo entry (identical fields as addcustomer).
     */
    public function create()
    {
        $countries = DB::table('countries')->where('id', 76)->get();
        return view('pages.expo.create', compact('countries'));
    }

    /**
     * Store new Expo customer and auto-create Lead with source 'Expo'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customername' => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'contact_name' => 'required|string|max:255',
            'address'      => 'nullable|string',
            'country'      => 'nullable',
            'state'        => 'nullable',
            'city'         => 'nullable',
            'postalcode'   => 'nullable',
        ]);

        // 1. Check duplicate customer
        $customerController = app(CustomerController::class);
        $duplicate = $customerController->findDuplicateCustomer($request);

        $customer = null;
        if ($duplicate['exists'] && !empty($duplicate['customer'])) {
            $customer = $duplicate['customer'];
        } else {
            // Create Customer record
            $customer = Customer::create([
                'customername'          => trim($request->customername),
                'assigned_user'         => $request->assigned_user ?: Auth::id(),
                'address'               => $request->address,
                'communication_address' => $request->communication_address ?? $request->address,
                'country'               => $request->country,
                'contact_name'          => trim($request->contact_name),
                'state'                 => $request->state,
                'cus_email'             => $request->cus_email ? trim($request->cus_email) : null,
                'city'                  => $request->city,
                'country_code'          => $request->country_code ?? '76',
                'contact_no'            => trim($request->contact_no),
                'postalcode'            => trim($request->postalcode),
                'designation'           => $request->designation,
                'department'            => $request->department,
                'gstno'                 => $request->gstno ? strtoupper(trim($request->gstno)) : null,
                'soft_d'                => 0,
            ]);

            $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
            $customer->save();
        }

        // 2. Resolve default Open Status and Expo Source ID
        $expoSourceId = $this->getExpoSourceId();
        $defaultStatus = Dropdowndata::where('formid', 6)
            ->where('dropdowndata', '!=', 'WON')
            ->orderBy('orderno', 'asc')
            ->first();
        $statusId = $defaultStatus ? $defaultStatus->id : 338;
        $assignedTo = $request->assigned_user ?: Auth::id();

        // 3. Create Lead with Source = Expo
        $lead = Lead::create([
            'customeriid'        => $customer->id,
            'LeadName'           => $customer->customername,
            'contactname'        => $customer->contact_name,
            'phonumber'          => $customer->contact_no,
            'email'              => $customer->cus_email,
            'billaddress'        => $customer->address,
            'communicate_address'=> $customer->communication_address,
            'billingcountry'     => $customer->country,
            'state_id'           => $customer->state,
            'citylist'           => $customer->city,
            'postalcode'         => $customer->postalcode,
            'gst'                => $customer->gstno,
            'designation'        => $customer->designation,
            'department'         => $customer->department,
            'countrycode'        => $customer->country_code ?? '76',
            'assigned_to'        => $assignedTo,
            'Entrydate'          => now()->format('Y-m-d'),
            'Leadsource'         => $expoSourceId,
            'Leadstatus'         => $statusId,
            'enquirydetail'      => $request->enquirydetail ?? 'Expo Enquiry',
            'soft_delete'        => 0,
        ]);

        $leadID = "LEAD" . str_pad($lead->id, 4, '0', STR_PAD_LEFT);
        $lead->lead_id = $leadID;
        $lead->save();

        // 4. Insert leadproduct record
        DB::table('leadproducts')->insert([
            'singleproduct' => null,
            'leadid'        => $lead->id,
            'statuslead'    => $statusId,
            'enquirydate'   => $lead->Entrydate,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expo Details and Lead saved successfully!',
                'redirect' => route('pages.expo'),
            ]);
        }

        return redirect()->route('pages.expo')->with('success', 'Expo Details and Lead saved successfully!');
    }

    /**
     * Show form to edit existing Expo entry.
     */
    public function edit($id)
    {
        $lead = Lead::where('leads.id', $id)
            ->leftJoin('customers', 'customers.id', '=', 'leads.customeriid')
            ->select('leads.*', 'customers.cusid')
            ->firstOrFail();

        $countries = DB::table('countries')->where('id', 76)->get();
        return view('pages.expo.edit', compact('lead', 'countries'));
    }

    /**
     * Update existing Expo details.
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $request->validate([
            'customername' => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'contact_name' => 'required|string|max:255',
            'address'      => 'nullable|string',
            'country'      => 'nullable',
            'state'        => 'nullable',
            'city'         => 'nullable',
            'postalcode'   => 'nullable',
        ]);

        // Update Customer if linked
        if ($lead->customeriid) {
            $customer = Customer::find($lead->customeriid);
            if ($customer) {
                $customer->update([
                    'customername'          => trim($request->customername),
                    'assigned_user'         => $request->assigned_user ?: $customer->assigned_user,
                    'address'               => $request->address,
                    'communication_address' => $request->communication_address ?? $request->address,
                    'country'               => $request->country,
                    'contact_name'          => trim($request->contact_name),
                    'state'                 => $request->state,
                    'cus_email'             => $request->cus_email ? trim($request->cus_email) : null,
                    'city'                  => $request->city,
                    'country_code'          => $request->country_code ?? '76',
                    'contact_no'            => trim($request->contact_no),
                    'postalcode'            => trim($request->postalcode),
                    'designation'           => $request->designation,
                    'department'            => $request->department,
                    'gstno'                 => $request->gstno ? strtoupper(trim($request->gstno)) : null,
                ]);
            }
        }

        // Update Lead
        $lead->update([
            'LeadName'           => trim($request->customername),
            'contactname'        => trim($request->contact_name),
            'phonumber'          => trim($request->contact_no),
            'email'              => $request->cus_email ? trim($request->cus_email) : null,
            'billaddress'        => $request->address,
            'communicate_address'=> $request->communication_address ?? $request->address,
            'billingcountry'     => $request->country,
            'state_id'           => $request->state,
            'citylist'           => $request->city,
            'postalcode'         => trim($request->postalcode),
            'gst'                => $request->gstno ? strtoupper(trim($request->gstno)) : null,
            'designation'        => $request->designation,
            'department'         => $request->department,
            'assigned_to'        => $request->assigned_user ?: $lead->assigned_to,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expo details updated successfully!',
                'redirect' => route('pages.expo'),
            ]);
        }

        return redirect()->route('pages.expo')->with('success', 'Expo details updated successfully!');
    }

    /**
     * Soft delete an Expo entry.
     */
    public function softDelete($id)
    {
        $lead = Lead::find($id);
        if ($lead) {
            $lead->soft_delete = 1;
            $lead->save();

            return response()->json(['success' => true, 'message' => 'Expo entry deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Expo entry not found.'], 404);
    }
}