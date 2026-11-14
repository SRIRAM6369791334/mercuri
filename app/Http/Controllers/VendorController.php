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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function viewVendors(VendorDataTable $dataTable)
    {
        return $dataTable->render('pages.vendor.vendorList');
    }

    public function saveVendors(Request $request)
    {
        $data = $request->validate($this->vendorRules());
        $this->validateVendorLocations($request);
        $vendor = DB::transaction(function () use ($data, $request) {
            $nextNumber = ((int) Vendor::withTrashed()->lockForUpdate()->max('id')) + 1;
            do {
                $vendorCode = sprintf('MIS-VEN-%03d', $nextNumber++);
            } while (Vendor::withTrashed()->where('vendor_id', $vendorCode)->exists());

            $vendor = Vendor::create($this->vendorAttributes($data, $vendorCode));
            $this->syncBranches($vendor, $request);
            $this->syncCategories($vendor, $data['pro_cat_id'] ?? []);

            return $vendor;
        });

        return redirect()->route('pages.vendor.vendorList')->with('success', 'Vendor Details successfully!');
    }

    public function vendorEditDetails($vendorId)
    {
        $vendorDetails = Vendor::findOrFail($vendorId);
        $vendorAddresses = \App\Models\VendorAddress::with('city')->where('vendor_id', $vendorId)->get();

        return view('pages.vendor.editVendors', compact('vendorDetails', 'vendorAddresses'));
    }

    public function editVendors(Request $request)
    {
        $vendor = Vendor::findOrFail($request->input('vendorId'));
        $data = $request->validate($this->vendorRules($vendor->id));
        $this->validateVendorLocations($request);

        DB::transaction(function () use ($vendor, $request, $data) {
            $vendor->fill($this->vendorAttributes($data, $vendor->vendor_id))->save();
            $this->syncBranches($vendor, $request);
            $this->syncCategories($vendor, $data['pro_cat_id'] ?? []);
        });

        return redirect()->route('pages.vendor.vendorList')->with('success', 'Vendor Details successfully!');
    }

    public function deleteVendor($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        DB::transaction(function () use ($vendor) {
            $vendor->delete();
            $vendor->addresses()->delete();
            $this->syncCategories($vendor, []);
        });

        return back()->with('delete-success', 'Vendor details deleted successfully');
    }

    private function vendorRules(?int $vendorId = null): array
    {
        return [
            'vendorId' => $vendorId ? 'required|integer|exists:vendor_details_tb,id' : 'prohibited',
            'company_name' => 'required|string|min:2|max:255',
            'alternate_name' => 'nullable|string|max:255',
            'poc_name' => 'nullable|string|max:255',
            'contact_number' => ['nullable', 'regex:/^[0-9+()\\s.-]{7,20}$/'],
            'email' => 'nullable|email|max:255',
            'gst_number' => ['required', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/i'],
            'address' => 'required|string|min:5|max:5000',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:state_list,id',
            'city_id' => 'nullable|integer|exists:city_list,id',
            'postal_code' => 'nullable|string|max:20',
            'vendor_status' => 'required|in:active,inactive',
            'pro_cat_id' => 'required|array|min:1',
            'pro_cat_id.*' => 'required|integer|distinct|exists:product_categories_tb,id',
            'branch_id' => 'nullable|array',
            'branch_id.*' => 'nullable|integer|exists:vendor_addresses,id',
            'branch_address' => 'nullable|array',
            'branch_address.*' => 'nullable|string|max:5000',
            'branch_type' => 'nullable|array',
            'branch_type.*' => 'nullable|string|max:50',
            'branch_gst' => 'nullable|array',
            'branch_gst.*' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/i'],
            'branch_state_id' => 'nullable|array',
            'branch_state_id.*' => 'nullable|integer|exists:state_list,id',
            'branch_city_id' => 'nullable|array',
            'branch_city_id.*' => 'nullable|integer|exists:city_list,id',
            'branch_pincode' => 'nullable|array',
            'branch_pincode.*' => 'nullable|string|max:20',
            'branch_contact_person' => 'nullable|array',
            'branch_contact_person.*' => 'nullable|string|max:100',
            'branch_contact_phone' => 'nullable|array',
            'branch_contact_phone.*' => ['nullable', 'regex:/^[0-9+()\\s.-]{7,20}$/'],
        ];
    }

    private function vendorAttributes(array $data, string $vendorCode): array
    {
        return [
            'vendor_id' => $vendorCode,
            'company_name' => $data['company_name'],
            'alternate_name' => $data['alternate_name'] ?? substr($data['company_name'], 0, 15),
            'point_of_contact' => $data['poc_name'] ?? $data['company_name'],
            'contact_number' => $data['contact_number'] ?? '0000000000',
            'email' => $data['email'] ?? 'vendor@mercuriis.com',
            'gst_number' => strtoupper($data['gst_number']),
            'address' => $data['address'],
            'country_id' => $data['country_id'],
            'state_id' => $data['state_id'],
            'city_id' => $data['city_id'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'vendor_status' => $data['vendor_status'],
            'product_cat_id' => json_encode(array_values($data['pro_cat_id'] ?? [])),
        ];
    }

    private function validateVendorLocations(Request $request): void
    {
        $errors = [];
        if ($request->filled('city_id') &&
            !DB::table('city_list')->where('id', $request->input('city_id'))
                ->where('state_code', $request->input('state_id'))->exists()) {
            $errors['city_id'] = 'Choose a city that belongs to the selected state.';
        }

        foreach (is_array($request->input('branch_city_id', [])) ? $request->input('branch_city_id', []) : [] as $index => $cityId) {
            if (!$cityId) {
                continue;
            }
            $stateId = $request->input("branch_state_id.$index");
            if (!$stateId || !DB::table('city_list')->where('id', $cityId)->where('state_code', $stateId)->exists()) {
                $errors["branch_city_id.$index"] = 'Choose a branch city that belongs to the selected branch state.';
            }
        }

        $addressKeys = array_keys(is_array($request->input('branch_address')) ? $request->input('branch_address') : []);
        foreach (['branch_id', 'branch_type', 'branch_gst', 'branch_state_id', 'branch_city_id', 'branch_pincode', 'branch_contact_person', 'branch_contact_phone'] as $field) {
            if ($request->exists($field) && (!is_array($request->input($field)) || array_keys($request->input($field)) !== $addressKeys)) {
                $errors[$field] = 'Branch fields must contain matching rows.';
            }
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    private function syncBranches(Vendor $vendor, Request $request): void
    {
        $ids = [];
        foreach (($request->input('branch_address') ?? []) as $index => $address) {
            $branchId = $request->input("branch_id.$index");
            $branch = null;
            if ($branchId) {
                $branch = $vendor->addresses()->whereKey($branchId)->first();
                if (!$branch) {
                    abort(422, 'A submitted supplier branch does not belong to this supplier.');
                }
            }

            if (!trim((string) $address)) {
                if ($branch) {
                    $branch->delete();
                }
                continue;
            }

            $attributes = [
                'address_type' => $request->input("branch_type.$index") ?: 'Branch #' . ($index + 1),
                'address' => trim($address),
                'gst_number' => strtoupper($request->input("branch_gst.$index") ?: $vendor->gst_number),
                'country_id' => $request->input('country_id', 76),
                'state_id' => $request->input("branch_state_id.$index") ?: $vendor->state_id,
                'city_id' => $request->input("branch_city_id.$index") ?: $vendor->city_id,
                'postal_code' => $request->input("branch_pincode.$index") ?: $vendor->postal_code,
                'contact_person' => $request->input("branch_contact_person.$index"),
                'contact_number' => $request->input("branch_contact_phone.$index"),
            ];
            if ($branch) {
                $branch->update($attributes);
            } else {
                $branch = $vendor->addresses()->create($attributes);
            }
            $ids[] = $branch->id;
        }

        $vendor->addresses()->whereNotIn('id', $ids)->delete();
    }

    private function syncCategories(Vendor $vendor, array $selectedIds): void
    {
        $selectedIds = array_map('intval', $selectedIds);
        foreach (ProductCategory::all() as $category) {
            $vendorIds = json_decode($category->vendor_id, true) ?: [];
            $vendorIds = array_map('intval', $vendorIds);
            $vendorIds = array_values(array_filter($vendorIds, fn ($id) => $id !== (int) $vendor->id));
            if (in_array((int) $category->id, $selectedIds, true)) {
                $vendorIds[] = (int) $vendor->id;
            }
            $category->vendor_id = $vendorIds ? json_encode(array_values(array_unique($vendorIds))) : null;
            $category->save();
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
