<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseEntry;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\DataTables\VendorDataTable;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function viewVendors(VendorDataTable $dataTable)
    {
        return $dataTable->render('pages.vendor.vendorList');
    }

    public function saveVendors(Request $request)
    {
        $vendor = new Vendor();

        $vendor->company_name = $request->input('company_name');
        $vendor->alternate_name = $request->input('alternate_name');
        $vendor->point_of_contact = $request->input('poc_name');
        $vendor->contact_number = $request->input('contact_number');
        $vendor->email = $request->input('email');
        $vendor->gst_number = $request->input('gst_number');
        $vendor->address = $request->input('address');
        $vendor->country_id = $request->input('country_id');
        $vendor->state_id = $request->input('state_id');
        $vendor->city_id = $request->input('city_id');
        $vendor->postal_code = $request->input('postal_code');
        $vendor->vendor_status = $request->input('vendor_status');
        $vendor->product_cat_id = json_encode($request->input('pro_cat_id'), true);



        // to generate vendorId
        function incrementCode($code)
        {
            // Split the code into parts
            $parts = explode('-', $code);

            // Get the numeric part and increment it
            $number = (int) $parts[2] + 1;

            // Format back with leading zeros
            return sprintf('%s-%s-%03d', $parts[0], $parts[1], $number);
        }

        $lastInserted = Vendor::latest()->first();

        if ($lastInserted == null) {
            $vendorId = incrementCode("MIS-VEN-000");
        } else {
            $vendorId = incrementCode($lastInserted->vendor_id);
        }


        $vendor->vendor_id = $vendorId;

        $isVendorInsert = $vendor->save();
        $lastInserted = Vendor::latest()->first();



        // to find existing category to insert vendorid

        foreach ($request->input('pro_cat_id') as $key => $value) {
            $category = ProductCategory::find($value);
            $vendorIdArray = [];
            if ($isVendorInsert) {
                if ($category->vendor_id == null) {
                    array_push($vendorIdArray, (int) $lastInserted->id);
                } else {
                    $vendorIdArray = json_decode($category->vendor_id);
                    array_push($vendorIdArray, $lastInserted->id);
                }
                $category->vendor_id = json_encode($vendorIdArray);
                $category->update();


            } else {

            }
        }

        if ($isVendorInsert) {
            return redirect()->route('pages.vendor.vendorList')->with('success', 'Vendor Details successfully!');
        } else {
            return redirect()->route('pages.vendor.addVendors')->with('error', 'Something went wrong, Please try again');
        }

    }

    public function vendorEditDetails($vendorId)
    {
        $vendorDetails = Vendor::where('id', $vendorId)->first();

        // dd($vendorDetails);

        return view('pages.vendor.editVendors', compact('vendorDetails'));
    }

    public function editVendors(Request $request)
    {
        $vendorId = $request->vendorId;
        $vendor = Vendor::findOrFail($request->vendorId);

        if ($vendor) {
            $vendor->company_name = $request->input('company_name');
            $vendor->alternate_name = $request->input('alternate_name');
            $vendor->point_of_contact = $request->input('poc_name');
            $vendor->contact_number = $request->input('contact_number');
            $vendor->email = $request->input('email');
            $vendor->gst_number = $request->input('gst_number');
            $vendor->address = $request->input('address');
            $vendor->country_id = $request->input('address');
            $vendor->state_id = $request->input('state_id');
            $vendor->city_id = $request->input('city_id');
            $vendor->postal_code = $request->input('postal_code');
            $vendor->vendor_status = $request->input('vendor_status');
            $vendor->product_cat_id = json_encode($request->input('pro_cat_id'), true);

            $isVendorInsert = $vendor->update();

            // to find existing category to insert vendorid

            // dd($request->input('pro_cat_id'));

            foreach ($request->input('pro_cat_id') as $key => $value) {
                $category = ProductCategory::find($value);

                if ($category) {
                    $vendorIdArray = [];

                    if ($isVendorInsert) {
                        // Decode existing vendor_id JSON into an array if not null
                        if (!empty($category->vendor_id)) {
                            $vendorIdArray = json_decode($category->vendor_id, true);
                        }

                        // Ensure $vendorId is not already in the array before adding it
                        if (!in_array((int) $vendorId, $vendorIdArray)) {
                            $vendorIdArray[] = (int) $vendorId; // Add only if it does not exist
                        }

                        // dd($vendorIdArray);

                        // Update the vendor_id field with the new array
                        $category->vendor_id = json_encode($vendorIdArray);
                        $category->update();
                    }
                }
            }


            if ($isVendorInsert) {
                return redirect()->route('pages.vendor.vendorList')->with('success', 'Vendor Details successfully!');
            } else {
                return redirect()->route('pages.vendor.addVendors')->with('error', 'Something went wrong, Please try again');
            }
        } else {
            return back()->with('error', "Vendor not found");
        }

    }

    public function deleteVendor($vendorId)
    {
        $deleteData = Vendor::find($vendorId)->delete();
        $vendorIds = json_decode($vendorId, true);
        $categories = ProductCategory::whereJsonContains('vendor_id', $vendorIds)->get();

        foreach ($categories as $category) {
            $vendorIds = json_decode($category->vendor_id, true); // Decode JSON

            // Remove the vendor ID from the array
            $vendorIds = array_filter($vendorIds, fn($id) => $id != $vendorId);

            if (empty($vendorIds)) {
                // If no vendors left, delete the row
                $category->vendor_id = null;
                $category->save();
            } else {
                // Otherwise, update the column with the new JSON array
                $category->vendor_id = json_encode(array_values($vendorIds)); // Reindex array
                $category->save();
            }
        }
        if ($deleteData) {
            return back()->with('delete-success', 'Vendor details deleted successfully');
        } else {
            return back()->with('delete-error', 'Something went wrong');
        }
    }

    public function getVendorDetails($catId)
    {
        $vendors = Vendor::whereJsonContains('product_cat_id', $catId)->get();

        return response()->json(['vendors' => $vendors], 200);
    }

    public function getVendorListAjax($type)
    {


        switch ($type) {
            case 'active':
                $vendors = Vendor::with(["stateData", "cityData"])->where('vendor_status', $type)->get();
                break;

            case 'inactive':
                $vendors = Vendor::with(["stateData", "cityData"])->where('vendor_status', $type)->get();
                break;

            default:
                $vendors = Vendor::with(["stateData", "cityData"])->get();
                break;
        }

        if ($vendors) {
            return response()->json([
                'status' => 'success',
                'vendors' => $vendors,
                'permissions' => [
                    'vendors-edit' => Auth::user()->can('vendors-edit'),
                    'vendors-delete' => Auth::user()->can('vendors-delete'),
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'vendors' => $vendors,
                'permissions' => [
                    'vendors-edit' => Auth::user()->can('vendors-edit'),
                    'vendors-delete' => Auth::user()->can('vendors-delete'),
                ]
            ], 200);
        }


    }


}
