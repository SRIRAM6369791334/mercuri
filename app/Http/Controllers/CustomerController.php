<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Models\Customer;
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
