<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orf;
use App\Models\Role;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Term;
use App\Models\Lead;
use Carbon\Carbon;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;

class DashboardControllerApi extends Controller
{
    public function getDashboardData($period = null, $fromdate = null, $todate = null, $assignedto = null)
    {
        $data = [];
        // Common filter logic
        $applyFilters = function ($query) use ($period, $fromdate, $todate, $assignedto) {
            if ($period === 'this_week') {
                $query->whereBetween('orf.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($period === 'this_month') {
                $query->whereMonth('orf.created_at', Carbon::now()->month);
            }

            if ($fromdate && $todate) {
                $from = Carbon::parse($fromdate)->startOfDay();
                $to = Carbon::parse($todate)->endOfDay();
                $query->whereBetween('orf.created_at', [$from, $to]);
            }

            if ($assignedto) {
                $query->where('assigned_to', $assignedto);
            }

            return $query;
        };

        // Card 1: Sales
        $salesQuery = Orf::where('orf.approval_status', 1)
            ->where('orf.cs_status', 1)
            ->leftJoin('quotationproduct', 'orf.quoproid', '=', 'quotationproduct.id')
            ->select('orf.*', 'quotationproduct.catepro');

        $data['sales'] = $applyFilters($salesQuery)->get();

        // Card 2: Spares
        $sparesQuery = Orf::where('orf.approval_status', 1)
            ->where('orf.cs_status', 1)
            ->leftJoin('quotationproduct', 'orf.quoproid', '=', 'quotationproduct.id')
            ->select('orf.*', 'quotationproduct.catepro');

        $data['spares'] = $applyFilters($sparesQuery)->get();

        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }

    public function getLeadSummaryCount()
    {
        $statusCounts = DB::table('dropdowndatas as source')
            ->leftJoin('leads', 'source.id', '=', 'leads.Leadstatus')
            ->select(
                'source.dropdowndata as Leadstatus',
                DB::raw('COUNT(leads.id) as LeadCount')
            )
            ->where('source.formid', 6)
            ->groupBy('source.dropdowndata')
            ->get();
        $sourceCounts = DB::table('dropdowndatas as source')
            ->leftJoin('leads', 'source.id', '=', 'leads.Leadsource')
            ->select(
                'source.dropdowndata as Leadsource',
                DB::raw('COUNT(leads.id) as LeadCount')
            )
            ->where('source.formid', 4)
            ->groupBy('source.dropdowndata')
            ->get();

        $totalLeadCount = DB::table('leads')->count();

        $formattedCounts = [];

        $formattedCounts[] = [
            'totallead' => 'Total Lead',
            'LeadCount' => $totalLeadCount
        ];

        foreach ($statusCounts as $item) {
            $formattedCounts[] = [
                'Leadstatus' => $item->Leadstatus,
                'LeadCount' => $item->LeadCount,
            ];
        }

        return ResponseHelper::successResponseData('Success', 'Lead status count retrieved successfully', [
            'statusCounts' => $formattedCounts,
            'sourceCounts' => $sourceCounts,
        ]);
    }
    public function getUserData($id = null)
    {
        $id = $id;

        if ($id) {
            $user = User::find($id);
            if ($user) {
                return ResponseHelper::successResponseData('Success', 'User retrieved successfully', [$user]);
            } else {
                return ResponseHelper::errorResponse('User not found', 404);
            }
        }

        $data = User::all();
        return ResponseHelper::successResponseData('Success', 'Users retrieved successfully', $data);
    }

    public function getProductCategoriesData($id = null)
    {
        $id = $id;

        if ($id) {
            $user = ProductCategory::find($id);
            if ($user) {
                return ResponseHelper::successResponseData('Success', 'User retrieved successfully', [$user]);
            } else {
                return ResponseHelper::errorResponse('User not found', 404);
            }
        }
        $data = ProductCategory::all();

        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }


    public function getProductData($id = null)
    {
        if ($id) {
            $product = DB::table('products as p')
                ->leftJoin('product_categories_tb as c', 'p.Productcategory', '=', 'c.id')
                ->select(
                    'p.*',
                    'c.category_name'
                )
                ->where('p.id', $id)
                ->first();

            if ($product) {
                return ResponseHelper::successResponseData('Success', 'Product retrieved successfully', [$product]);
            } else {
                return ResponseHelper::errorResponse('Product not found', 404);
            }
        }

        $products = DB::table('products as p')
            ->leftJoin('product_categories_tb as c', 'p.Productcategory', '=', 'c.id')
            ->select(
                'p.*',
                'c.category_name'
            )
            ->get();

        return ResponseHelper::successResponseData('Success', 'Products retrieved successfully', $products);
    }

    public function getCustomerData($id = null)
    {
        $id = $id;

        if ($id) {
            $user = Customer::find($id);
            if ($user) {
                return ResponseHelper::successResponseData('Success', 'User retrieved successfully', [$user]);
            } else {
                return ResponseHelper::errorResponse('User not found', 404);
            }
        }
        $data = Customer::all();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }
    public function getTermData($id = null)
    {
        $id = $id;

        if ($id) {
            $user = Term::find($id);
            if ($user) {
                return ResponseHelper::successResponseData('Success', 'User retrieved successfully', [$user]);
            } else {
                return ResponseHelper::errorResponse('User not found', 404);
            }
        }
        $data = Term::all();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }
    public function getRoles()
    {
        $data = Role::all();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $data);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->has(['name', 'mobile', 'email', 'type', 'userstatus'])) {

            // Validate input for update
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'type' => 'required|string',
                'userstatus' => 'required|string',
            ]);

            // Update user details
            $user->name = $validatedData['name'];
            $user->mobilenumber = $validatedData['mobile'];
            $user->email = $validatedData['email'];
            $user->role = $validatedData['type'];
            $user->userstatus = $validatedData['userstatus'];
            $user->save();

            $user->syncRoles([$validatedData['type']]);

            return ResponseHelper::successResponseData(
                'Success',
                'User updated successfully.',
                $user
            );
        }

        return ResponseHelper::successResponseData(
            'Success',
            'User data retrieved successfully.',
            $user
        );
    }
    public function updateProductCategory(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:product_categories_tb,id',
            'category_name' => 'required|string|max:255',
            'sub_category' => 'nullable|in:special,standard',
            'type' => 'nullable|in:mhe,rack,pallet,spares',
        ]);

        $category = ProductCategory::find($validated['id']);

        if (isset($validated['category_name'])) {
            $category->category_name = $validated['category_name'];
        }

        if (isset($validated['sub_category'])) {
            $category->sub_category = $validated['sub_category'];
        }
        if (isset($validated['type'])) {
            $category->type = $validated['type'];
        }
        $category->save();

        return ResponseHelper::successResponseData('Success', 'Category updated successfully', [$category]);
    }
    public function updateProductData(Request $request,)
    {
        $validated = $request->validate([
            'id' => 'required|exists:products,id',
            'productname' => 'required|string|max:255',
            'rate' => 'required|numeric|between:0,999999999999.99',
            'hsn' => 'nullable|string|max:255',
            'gst' => 'nullable|numeric|between:0,99.99',
            'partno' => 'nullable|string|max:255',
            'productdes' => 'nullable|string',
            'Productcategory' => 'nullable|string|max:255',
            'quantity' => 'required|integer',
        ]);

        $product = Product::find($validated['id']);
        $fields = [
            'productname',
            'rate',
            'hsn',
            'gst',
            'partno',
            'productdes',
            'Productcategory',
            'quantity'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $product->$field = $validated[$field];
            }
        }

        $product->save();

        return ResponseHelper::successResponseData('Success', 'Product updated successfully', [$product]);
    }
    public function updateCustomerData(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:customers,id',
            'customername' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'cus_email' => 'nullable|email|max:255',
            'country_code' => 'nullable|string|max:10',
            'contact_no' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postalcode' => 'nullable|string|max:10',
            'gstno' => 'nullable|string|max:255',
        ]);

        $customer = Customer::find($validated['id']);

        $fields = [
            'customername',
            'contact_name',
            'cus_email',
            'country_code',
            'contact_no',
            'designation',
            'department',
            'address',
            'country',
            'city',
            'state',
            'postalcode',
            'gstno',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $customer->$field = $validated[$field];
            }
        }

        $customer->save();

        return ResponseHelper::successResponseData('Success', 'Customer updated successfully', [$customer]);
    }

    public function updateTermData(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:terms,id',
            'whichterm' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'termtype' => 'nullable|string|max:255',
        ]);

        $term = Term::find($validated['id']);

        $fields = [
            'whichterm',
            'content',
            'termtype',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $term->$field = $validated[$field];
            }
        }

        $term->save();

        return ResponseHelper::successResponseData('Success', 'Customer updated successfully', [$term]);
    }

    public function updateTermApprovalStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:terms,id',
            'term_approve' => 'nullable|in:0,1',
        ]);

        $term = Term::find($validated['id']);

        $fields = [
            'term_approve',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $term->$field = $validated[$field];
            }
        }

        $term->save();

        return ResponseHelper::successResponseData('Success', 'Customer updated successfully', [$term]);
    }
    public function deleteTerm(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:terms,id',
        ]);

        $term = Term::find($validated['id']);

        if ($term) {
            $term->delete();
            return ResponseHelper::successResponseData('Success', 'Term deleted successfully');
        }

        return ResponseHelper::errorResponse('Error', 'Term not found');
    }
    public function deleteCustomer(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:customers,id',
        ]);

        $term = Customer::find($validated['id']);

        if ($term) {
            $term->delete();
            return ResponseHelper::successResponseData('Success', 'Term deleted successfully');
        }

        return ResponseHelper::errorResponse('Error', 'Term not found');
    }
    public function deleteProducts(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $term = Product::find($validated['id']);

        if ($term) {
            $term->delete();
            return ResponseHelper::successResponseData('Success', 'Term deleted successfully');
        }

        return ResponseHelper::errorResponse('Error', 'Term not found');
    }
    public function deleteProductsCategories(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:product_categories_tb,id',
        ]);

        $term = ProductCategory::find($validated['id']);

        if ($term) {
            $term->delete();
            return ResponseHelper::successResponseData('Success', 'Term deleted successfully');
        }

        return ResponseHelper::errorResponse('Error', 'Term not found');
    }
}
