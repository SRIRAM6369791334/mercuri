<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    //
    public function customer()
    {
        $customer = Customer::where('customers.soft_d', '=', 0)
            ->leftJoin('city_list', 'city_list.id', '=', 'customers.city')
            ->select('customers.*', 'city_list.city_name')
            ->orderBy('customers.id', 'desc')
            ->get();

        return view('pages.customer', compact('customer'));
    }

    /**
     * Check if a duplicate customer exists based on GST Number and Pincode (postalcode).
     */
    public function findDuplicateCustomer(Request $request, $excludeId = null): array
    {
        $customerName = trim($request->customername ?? '');
        $gstNo = strtoupper(trim($request->gstno ?? ''));
        $pincode = trim($request->postalcode ?? '');

        // 1. Primary Condition: Same GST Number AND Same Pincode (postalcode)
        if (!empty($gstNo) && !empty($pincode)) {
            $gstPinQuery = Customer::where('soft_d', 0)
                ->whereNotNull('gstno')
                ->where('gstno', '!=', '')
                ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo])
                ->whereNotNull('postalcode')
                ->where('postalcode', '!=', '')
                ->whereRaw('TRIM(postalcode) = ?', [$pincode]);

            if ($excludeId) {
                $gstPinQuery->where('id', '!=', $excludeId);
            }

            $duplicate = $gstPinQuery->first();
            if ($duplicate) {
                return [
                    'exists' => true,
                    'field' => 'gst_pincode',
                    'message' => "Customer with GST Number '{$gstNo}' and Pincode '{$pincode}' already exists ({$duplicate->cusid} - {$duplicate->customername}).",
                    'customer' => $duplicate,
                ];
            }
        }

        // 2. Real-time / partial check: If GST Number is entered and Pincode is not yet given
        if (!empty($gstNo) && empty($pincode)) {
            $gstQuery = Customer::where('soft_d', 0)
                ->whereNotNull('gstno')
                ->where('gstno', '!=', '')
                ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo]);

            if ($excludeId) {
                $gstQuery->where('id', '!=', $excludeId);
            }

            $duplicate = $gstQuery->first();
            if ($duplicate) {
                return [
                    'exists' => true,
                    'field' => 'gstno',
                    'message' => "Customer with GST Number '{$gstNo}' already exists at Pincode '{$duplicate->postalcode}' ({$duplicate->cusid} - {$duplicate->customername}).",
                    'customer' => $duplicate,
                ];
            }
        }

        // 3. If GST is empty, check Customer Name AND Pincode
        if (empty($gstNo) && !empty($customerName) && !empty($pincode)) {
            $namePinQuery = Customer::where('soft_d', 0)
                ->whereRaw('LOWER(TRIM(customername)) = ?', [strtolower($customerName)])
                ->whereNotNull('postalcode')
                ->where('postalcode', '!=', '')
                ->whereRaw('TRIM(postalcode) = ?', [$pincode]);

            if ($excludeId) {
                $namePinQuery->where('id', '!=', $excludeId);
            }

            $duplicate = $namePinQuery->first();
            if ($duplicate) {
                return [
                    'exists' => true,
                    'field' => 'name_pincode',
                    'message' => "Customer with name '{$customerName}' and Pincode '{$pincode}' already exists ({$duplicate->cusid}).",
                    'customer' => $duplicate,
                ];
            }
        }

        return [
            'exists' => false,
            'message' => null,
            'field' => null,
            'customer' => null,
        ];
    }

    /**
     * AJAX endpoint to check if customer duplicate exists in real-time.
     */
    public function checkDuplicate(Request $request)
    {
        $excludeId = $request->input('exclude_id');
        $result = $this->findDuplicateCustomer($request, $excludeId);

        return response()->json($result);
    }

    public function savecustomer(Request $request)
    {
        $request->validate([
            'customername' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address' => 'required|string',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postalcode' => 'required',
        ]);

        $duplicate = $this->findDuplicateCustomer($request);
        if ($duplicate['exists']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $duplicate['message'],
                    'field' => $duplicate['field'],
                    'customer' => $duplicate['customer'],
                ], 422);
            }

            return back()->withInput()->with('error', $duplicate['message']);
        }

        $customer = Customer::create([
            'customername' => trim($request->customername),
            'assigned_user' => $request->assigned_user,
            'address' => $request->address,
            'communication_address' => $request->communication_address ?? $request->address,
            'country' => $request->country,
            'contact_name' => trim($request->contact_name),
            'state' => $request->state,
            'cus_email' => $request->cus_email ? trim($request->cus_email) : null,
            'city' => $request->city,
            'country_code' => $request->country_code ?? '76',
            'contact_no' => trim($request->contact_no),
            'postalcode' => trim($request->postalcode),
            'designation' => $request->designation,
            'department' => $request->department,
            'gstno' => $request->gstno ? strtoupper(trim($request->gstno)) : null,
            'soft_d' => 0,
        ]);

        // Generate cusid based on the ID
        $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
        $customer->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer saved successfully!',
                'redirect' => route('pages.customer'),
            ]);
        }

        return redirect()->route('pages.customer')->with('success', 'Customer saved successfully!');
    }


    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('pages.customeredit', compact('customer'));
    }

    // Update customer details
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'customername' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address' => 'required|string',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postalcode' => 'required',
        ]);

        $duplicate = $this->findDuplicateCustomer($request, $id);
        if ($duplicate['exists']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $duplicate['message'],
                    'field' => $duplicate['field'],
                    'customer' => $duplicate['customer'],
                ], 422);
            }

            return back()->withInput()->with('error', $duplicate['message']);
        }

        $customer->update([
            'customername' => trim($request->customername),
            'assigned_user' => $request->assigned_user,
            'address' => $request->address,
            'communication_address' => $request->communication_address ?? $request->address,
            'country' => $request->country,
            'contact_name' => trim($request->contact_name),
            'state' => $request->state,
            'cus_email' => $request->cus_email ? trim($request->cus_email) : null,
            'city' => $request->city,
            'country_code' => $request->country_code ?? $customer->country_code,
            'contact_no' => trim($request->contact_no),
            'postalcode' => trim($request->postalcode),
            'designation' => $request->designation,
            'department' => $request->department,
            'gstno' => $request->gstno ? strtoupper(trim($request->gstno)) : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.',
                'redirect' => route('pages.customer'),
            ]);
        }

        return redirect()->route('pages.customer')->with('success', 'Customer updated successfully.');
    }

    public function softDelete($id)
    {
        $customer = Customer::find($id);

        if ($customer) {
            $customer->soft_d = 1;
            $customer->save();

            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found.']);
    }

    public function getCustomerData($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            return response()->json([
                'contactname' => $customer->contact_name,
                'contact_no' => $customer->contact_no,
                'address' => $customer->address,
                'postalcode' => $customer->postalcode,
                'country_code' => $customer->country_code,
                'country' => $customer->country,
                'state' => $customer->state,
                'city' => $customer->city,
                'designation' => $customer->designation,
                'department' => $customer->department,
                'communication_address' => $customer->communication_address,
                'gstno' => $customer->gstno,
                'assigned_user' => $customer->assigned_user,
                'cus_email' => $customer->cus_email,// Assuming the customer has a 'contactname' column
            ]);
        }
        return response()->json(null);
    }

    public function getCustomerByGst($gst)
    {
        $gstNo = strtoupper(trim($gst));
        if (strlen($gstNo) < 15) {
            return response()->json(['found' => false]);
        }

        $customer = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo])
            ->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'source' => 'local_db',
                'customer_id' => $customer->id,
                'customername' => $customer->customername,
                'contactname' => $customer->contact_name,
                'contact_no' => $customer->contact_no,
                'address' => $customer->address,
                'postalcode' => $customer->postalcode,
                'country_code' => $customer->country_code,
                'country' => $customer->country,
                'state' => $customer->state,
                'city' => $customer->city,
                'designation' => $customer->designation,
                'department' => $customer->department,
                'communication_address' => $customer->communication_address,
                'gstno' => $customer->gstno,
                'assigned_user' => $customer->assigned_user,
                'cus_email' => $customer->cus_email,
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function getAddressesByGst(Request $request, $gst = null)
    {
        $gstInput = $gst ?: ($request->gst ?: ($request->gst_no ?: $request->gstin));
        $gstNo = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$gstInput)));

        if (strlen($gstNo) < 8) {
            return response()->json([
                'status' => 'error',
                'found' => false,
                'count' => 0,
                'message' => 'GST number must be at least 8 characters.',
                'addresses' => []
            ]);
        }

        $addresses = [];
        $seen = [];

        $addUnique = function($rawAddr, $name, $city = '', $state = '', $postalcode = '', $source = 'Customer', $cityId = null, $stateId = null, $isPrimary = false, $addressId = null, $customerId = null) use (&$addresses, &$seen) {
            $cleanAddr = trim((string)$rawAddr);
            if (empty($cleanAddr) || strlen($cleanAddr) < 5) return;

            $full = $cleanAddr;
            if (!empty($city) && !str_contains(strtolower($full), strtolower($city))) {
                $full .= ', ' . $city;
            }
            if (!empty($state) && !str_contains(strtolower($full), strtolower($state))) {
                $full .= ', ' . $state;
            }
            if (!empty($postalcode) && !str_contains($full, (string)$postalcode)) {
                $full .= ' - ' . $postalcode;
            }

            $normKey = strtolower(preg_replace('/[^a-z0-9]/', '', $full));
            if (isset($seen[$normKey])) return;
            $seen[$normKey] = true;

            $shortLocation = !empty($city) ? $city : (!empty($postalcode) ? "PIN: $postalcode" : '');
            $label = ($name && !str_starts_with($full, $name)) ? ($name . ' - ' . $full) : $full;
            $label = trim(preg_replace('/⭐|\s*\[Primary\]|\s*\(Primary\)|^Primary\s*-\s*/i', '', $label));

            $item = [
                'label' => $label,
                'address' => $full,
                'raw_address' => $cleanAddr,
                'city' => $city,
                'state' => $state,
                'city_id' => $cityId,
                'state_id' => $stateId,
                'postalcode' => $postalcode,
                'company_name' => $name,
                'source' => $source,
                'is_primary' => $isPrimary,
                'address_id' => $addressId,
                'customer_id' => $customerId,
            ];

            if ($isPrimary) {
                array_unshift($addresses, $item);
            } else {
                $addresses[] = $item;
            }
        };

        // 1. Mercuriis Own GST Branches
        if ($gstNo === '33AATCM0824C1ZQ') {
            $addUnique(
                '89, Balaji Nagar, Athanancheri, Padappai',
                'Mercuriis Chennai HQ / Warehouse',
                'Chennai',
                'Tamil Nadu',
                '601301',
                'Mercuriis HQ',
                692,
                31,
                true
            );
            $addUnique(
                'Peelamedu, Coimbatore',
                'Mercuriis Coimbatore Branch',
                'Coimbatore',
                'Tamil Nadu',
                '641004',
                'Mercuriis Branch',
                695,
                31,
                false
            );
        }

        // 2. Customers Table
        $customers = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo])
            ->get();
        foreach ($customers as $c) {
            $cCity = $c->city_name ?: '';
            $cState = $c->state_name ?: '';
            $addUnique($c->address, $c->customername, $cCity, $cState, $c->postalcode, 'Customer Master', $c->city, $c->state, true);
            if ($c->communication_address && $c->communication_address !== $c->address) {
                $addUnique($c->communication_address, $c->customername . ' (Site/Comm)', $cCity, $cState, $c->postalcode, 'Customer Comm', $c->city, $c->state, false);
            }
        }

        // 2.1 Customer Plant / Delivery Addresses Table
        if (\Illuminate\Support\Facades\Schema::hasTable('customer_addresses')) {
            $custAddrs = \App\Models\CustomerAddress::with(['customer', 'city', 'state'])
                ->where(function($q) use ($gstNo) {
                    $q->whereRaw('UPPER(TRIM(gst_number)) = ?', [$gstNo])
                      ->orWhereHas('customer', function($cq) use ($gstNo) {
                          $cq->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo]);
                      });
                })
                ->get();
            foreach ($custAddrs as $ca) {
                $cName = ($ca->customer?->customername ?: 'Customer') . ' (' . ($ca->address_type ?: 'Branch') . ')';
                $cCity = $ca->city?->city_name ?: ($ca->city?->city ?: '');
                $cState = $ca->state?->state ?: '';
                $addUnique($ca->address, $cName, $cCity, $cState, $ca->postal_code, 'Customer ' . ($ca->address_type ?: 'Branch'), $ca->city_id, $ca->state_id, false, $ca->id, $ca->customer_id);
            }
        }

        // 3. Leads Table
        $leads = Lead::where('soft_delete', 0)
            ->whereNotNull('gst')
            ->whereRaw('UPPER(TRIM(gst)) = ?', [$gstNo])
            ->get();
        foreach ($leads as $l) {
            $lCity = $l->city_name ?: '';
            $lState = $l->state_name ?: '';
            $addUnique($l->billaddress, $l->LeadName, $lCity, $lState, $l->postalcode, 'Lead Billing', $l->citylist, $l->state_id, true);
            if ($l->communicate_address && $l->communicate_address !== $l->billaddress) {
                $addUnique($l->communicate_address, $l->LeadName . ' (Site/Comm)', $lCity, $lState, $l->postalcode, 'Lead Comm', $l->citylist, $l->state_id, false);
            }
            if (!empty($l->shipping_address) && $l->shipping_address !== $l->billaddress) {
                $addUnique($l->shipping_address, $l->LeadName . ' (Shipping)', $lCity, $lState, $l->shippostalcode ?: $l->postalcode, 'Lead Shipping', $l->citylist, $l->state_id, false);
            }
        }

        // 4. Vendors Table
        $vendors = Vendor::with(['cityData', 'stateData'])
            ->whereNotNull('gst_number')
            ->whereRaw('UPPER(TRIM(gst_number)) = ?', [$gstNo])
            ->get();
        foreach ($vendors as $v) {
            $cityName = $v->cityData?->city_name ?: '';
            $stateName = $v->stateData?->state ?: '';
            $addUnique($v->address, $v->company_name, $cityName, $stateName, $v->postal_code, 'Vendor Master (Primary)', $v->city_id, $v->state_id, true);
        }

        // 5. Vendor Plant / Branch Addresses Table
        if (\Illuminate\Support\Facades\Schema::hasTable('vendor_addresses')) {
            $vendorAddrs = \App\Models\VendorAddress::with(['vendor', 'city', 'state'])
                ->where(function($q) use ($gstNo) {
                    $q->whereRaw('UPPER(TRIM(gst_number)) = ?', [$gstNo])
                      ->orWhereHas('vendor', function($vq) use ($gstNo) {
                          $vq->whereRaw('UPPER(TRIM(gst_number)) = ?', [$gstNo]);
                      });
                })
                ->get();
            foreach ($vendorAddrs as $va) {
                $vName = ($va->vendor?->company_name ?: 'Vendor') . ' (' . ($va->address_type ?: 'Plant') . ')';
                $vCity = $va->city?->city_name ?: ($va->city?->city ?: '');
                $vState = $va->state?->state ?: '';
                $addUnique($va->address, $vName, $vCity, $vState, $va->postal_code, 'Vendor ' . ($va->address_type ?: 'Plant'), $va->city_id, $va->state_id, false);
            }
        }

        // 6. If not found in local DB, attempt RapidAPI Profile Lookup
        $gstProfileKey = config('services.gst_profile.key');
        if (empty($addresses) && strlen($gstNo) === 15 && filled($gstProfileKey)) {
            try {
                $gstRes = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders([
                        'x-rapidapi-key' => $gstProfileKey,
                        'x-rapidapi-host' => config('services.gst_profile.host')
                    ])
                    ->get('https://gst-return-status.p.rapidapi.com/api/v2/profile/' . $gstNo);
                if ($gstRes->successful()) {
                    $gData = $gstRes->json();
                    if (!empty($gData['data'])) {
                        $d = $gData['data'];
                        $compName = $d['tradeName'] ?: ($d['legalName'] ?: 'GST Registered Business');
                        $rawAddr = $d['principalPlaceOfBusiness'] ?? '';
                        
                        // Extract Pincode
                        $pincode = '';
                        if (preg_match('/\b[1-9][0-9]{5}\b/', $rawAddr, $pm)) {
                            $pincode = $pm[0];
                        }

                        // Map State from 2-digit GST prefix
                        $gstStateCode = substr($gstNo, 0, 2);
                        $gstStateMap = [
                            '01' => 15, '02' => 13, '03' => 27, '04' => 7,  '05' => 33,
                            '06' => 14, '07' => 9,  '08' => 29, '09' => 34, '10' => 5,
                            '11' => 30, '12' => 3,  '13' => 25, '14' => 22, '15' => 24,
                            '16' => 32, '17' => 20, '18' => 4,  '19' => 35, '20' => 16,
                            '21' => 26, '22' => 6,  '23' => 23, '24' => 12, '27' => 21,
                            '28' => 2,  '29' => 17, '30' => 11, '31' => 19, '32' => 18,
                            '33' => 31, '34' => 28, '35' => 1,  '36' => 36, '37' => 2
                        ];
                        $stateId = $gstStateMap[$gstStateCode] ?? null;
                        $stateName = '';
                        if ($stateId) {
                            $sObj = \Illuminate\Support\Facades\DB::table('state_list')->find($stateId);
                            $stateName = $sObj ? $sObj->state : '';
                        }

                        // Find matching city
                        $cityId = null;
                        $cityName = '';
                        if ($stateId) {
                            $cities = \Illuminate\Support\Facades\DB::table('city_list')->where('state_code', $stateId)->get();
                            foreach ($cities as $c) {
                                if (stripos($rawAddr, $c->city_name) !== false) {
                                    $cityId = $c->id;
                                    $cityName = $c->city_name;
                                    break;
                                }
                            }
                        }

                        $addUnique($rawAddr, $compName, $cityName, $stateName, $pincode, 'GST Portal (Primary)', $cityId, $stateId, true);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('GST profile lookup failed.', [
                    'exception' => get_class($e),
                ]);
            }
        }

        // Ensure Primary Address is always at index 0
        usort($addresses, function($a, $b) {
            if (!empty($a['is_primary']) && empty($b['is_primary'])) return -1;
            if (empty($a['is_primary']) && !empty($b['is_primary'])) return 1;
            return 0;
        });

        $primaryAddr = $addresses[0] ?? null;

        return response()->json([
            'status' => 'success',
            'found' => count($addresses) > 0,
            'count' => count($addresses),
            'gst_number' => $gstNo,
            'company_name' => $primaryAddr['company_name'] ?? '',
            'address' => $primaryAddr['raw_address'] ?? ($primaryAddr['address'] ?? ''),
            'state_id' => $primaryAddr['state_id'] ?? null,
            'city_id' => $primaryAddr['city_id'] ?? null,
            'postalcode' => $primaryAddr['postalcode'] ?? '',
            'primary_address' => $primaryAddr,
            'addresses' => $addresses
        ]);
    }

    public function downloadSampleTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));
        $fileName = 'sample_customer_template.' . ($format === 'csv' ? 'csv' : 'xlsx');
        $filePath = public_path($fileName);

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileName);
        }

        return redirect()->back()->with('error', 'Sample template file not found.');
    }

    public function bulkUploadCustomers(Request $request)
    {
        $request->validate([
            'customer_data_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ], [
            'customer_data_file.required' => 'Please select a file to upload.',
            'customer_data_file.file'     => 'The uploaded item must be a valid file.',
            'customer_data_file.mimes'    => 'The upload file must be an Excel (.xlsx, .xls) or CSV (.csv) file.',
            'customer_data_file.max'      => 'The file size must not exceed 20MB.',
        ]);

        try {
            $import = new CustomerImport();
            Excel::import($import, $request->file('customer_data_file'));

            $msg = "Bulk upload completed. Successfully imported {$import->importedCount} customer(s).";
            if ($import->skippedCount > 0) {
                $msg .= " Skipped {$import->skippedCount} row(s).";
            }

            $redirect = redirect()->route('pages.customer')->with('success', $msg);
            if (!empty($import->errors)) {
                $redirect->with('import_errors', $import->errors);
            }

            return $redirect;
        } catch (\Exception $e) {
            return redirect()->route('pages.customer')->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }

}
