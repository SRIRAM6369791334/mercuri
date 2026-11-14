<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Imports\ProductMasterImport;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lead;
use App\Models\Design;
use App\Models\ProductSubCategory;
use App\Mail\ProductPriceUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $catId = $request->Productcategory;
        if ((empty($catId) || $catId == 1) && preg_match('/(forklift|reach|walkie)/i', $request->productname)) {
            $catId = 4; // MHE High End
        }

        $cleanRate = is_string($request->rate) ? preg_replace('/[^\d.]/', '', $request->rate) : $request->rate;

        // Directly create the product
        $product = Product::create([
            'productname' => $request->productname,
            'productmodel' => $request->productmodel,
            'hsn' => $request->hsn,
            'gst' => $request->gst,
            'allo_dis' => $request->all_dis,
            'rate' => $cleanRate,
            'partno' => $request->partno,
            'dispatch_time' => $request->dispatch_time,
            'Productcategory' => $catId,
            'sub_cat_id' => $request->product_sub_category_id,
            'productdes' => $request->productdes,
            'quantity' => $request->quantity,
        ]);

        $lastId = Product::latest()->first();

        ProductStock::create([
            "product_id" => $lastId->id,
            "total_stock" => $request->quantity,
            "available_stock" => $request->quantity
        ]);

        if ($product) {
            return redirect()->route('products')->with('success', 'Product added successfully!');
        } else {
            return redirect()->back()->with('error', 'Product insert error, Please try again.');
        }

    }

    public function updateProductData($productId)
    {
        $productData = Product::with(['category', 'stock'])->where('id', $productId)->first();
        return view('pages.masters.editProducts', compact('productData'));
    }

    public function updateProduct(Request $request)
    {
        $product = Product::find($request->productId);
        $oldRate = $product ? (float) $product->rate : null;

        $productStock = ProductStock::where("product_id", $request->productId)->first();
        $catId = $request->Productcategory;
        if ((empty($catId) || $catId == 1) && preg_match('/(forklift|reach|walkie)/i', $request->productname)) {
            $catId = 4; // MHE High End
        }

        $cleanRate = is_string($request->rate) ? preg_replace('/[^\d.]/', '', $request->rate) : $request->rate;
        $newRate = (float) $cleanRate;

        $product->update([
            'productname' => $request->productname,
            'hsn' => $request->hsn,
            'gst' => $request->gst,
            'rate' => $cleanRate,
            'partno' => $request->partno,
            'dispatch_time' => $request->dispatch_time,
            'allo_dis' => $request->all_dis,
            'Productcategory' => $catId,
            'sub_cat_id' => $request->product_sub_category_id,
            'productdes' => $request->productdes,
            'quantity' => $request->quantity,
            'productmodel' => $request->productmodel,
        ]);

        if ($productStock && $productStock->total_stock != $productStock->available_stock) {
            $productStock->update([
                "total_stock" => $request->quantity,
                "available_stock" => $request->quantity
            ]);
        }

        $isSpecialCat = ((int) $catId === 2) || (optional(ProductCategory::find($catId))->sub_category === 'special');
        $rateChanged = ($oldRate !== null && abs($oldRate - $newRate) > 0.001);

        if ($isSpecialCat || $rateChanged) {
            try {
                $this->sendProductPriceUpdatedMail($product, $oldRate, $newRate);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send product price update mail: ' . $e->getMessage());
            }
        }

        if ($product) {
            return redirect()->route('products')->with('success', 'Product Updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Product update error, Please try again.');
        }

    }

    public function sendProductPriceUpdatedMail($product, $oldRate, $newRate)
    {
        $productId = $product->id;

        // 1. Find all leads / designs associated with this product
        $designs = Design::where(function ($q) use ($productId) {
            $q->where('product', $productId)
                ->orWhere('product', 'LIKE', "%,{$productId},%")
                ->orWhere('product', 'LIKE', "{$productId},%")
                ->orWhere('product', 'LIKE', "%,{$productId}");
        })->get();

        $leadIds = $designs->pluck('idlead')->filter()->unique()->toArray();

        // Also check leadproducts table if it exists
        $leadProductIds = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('leadproducts')) {
            $leadProductIds = DB::table('leadproducts')
                ->where('singleproduct', $productId)
                ->pluck('leadid')
                ->filter()
                ->unique()
                ->toArray();
        }

        $allLeadIds = array_unique(array_merge($leadIds, $leadProductIds));

        $relatedLeads = [];
        $recipientEmails = [];

        foreach ($allLeadIds as $lId) {
            $lead = Lead::find($lId);
            if ($lead) {
                $salesRepName = 'Unassigned';
                if ($lead->assigned_to) {
                    $salesUser = User::find($lead->assigned_to);
                    if ($salesUser) {
                        $salesRepName = $salesUser->name;
                        if (!empty($salesUser->email)) {
                            $recipientEmails[] = $salesUser->email;
                        }
                    }
                }

                $relatedLeads[] = [
                    'lead_id' => $lead->lead_id ?: ('LEAD' . str_pad($lead->id, 4, '0', STR_PAD_LEFT)),
                    'customer_name' => $lead->LeadName ?: 'N/A',
                    'sales_rep' => $salesRepName,
                    'quota_url' => url('/createquota/' . $lead->id),
                ];
            }
        }

        // 2. Add all Sales Managers and Managers
        $salesManagers = User::where(function ($q) {
            $q->where('role', 'Sales Manager')
                ->orWhere('role', 'Manager')
                ->orWhere('role', 'Sales Executive');
        })->orWhereHas('roles', function ($q) {
            $q->whereIn('name', ['Sales Manager', 'Manager', 'Sales Executive']);
        })->get();

        foreach ($salesManagers as $sm) {
            if (!empty($sm->email)) {
                $recipientEmails[] = $sm->email;
            }
        }

        // 3. Add the updating user (e.g. Design Engineer / Admin)
        $authUser = auth()->user();
        if ($authUser && !empty($authUser->email)) {
            $recipientEmails[] = $authUser->email;
        }

        $recipientEmails = array_values(array_unique(array_filter($recipientEmails)));

        if (empty($recipientEmails)) {
            return;
        }

        $category = ProductCategory::find($product->Productcategory);
        $subCategory = ProductSubCategory::find($product->sub_cat_id);

        $mailData = [
            'product_name' => $product->productname,
            'product_model' => $product->productmodel,
            'part_no' => $product->partno,
            'category_name' => $category?->category_name ?? 'MHE Special',
            'sub_category_name' => $subCategory?->subcategory_name,
            'old_rate' => $oldRate,
            'new_rate' => $newRate,
            'gst_percent' => $product->gst,
            'updated_by' => $authUser?->name ?? 'Design Team',
            'updated_at' => now()->format('d M Y, h:i A'),
            'related_leads' => $relatedLeads,
        ];

        $subject = '[MHE Special] Product Price & Details Updated: ' . $product->productname;
        if (!empty($product->productmodel)) {
            $subject .= ' (' . $product->productmodel . ')';
        }

        Mail::to($recipientEmails)->send(new ProductPriceUpdatedMail($mailData, $subject));
    }

    public function productDelete($productId)
    {
        $productDelete = Product::where("id", $productId)->delete();
        if ($productDelete) {
            return back()->with("delete-success", "Product Deleted Successfully");
        } else {
            return back()->with("delete-error", "Something went wrong, Please try again");
        }
    }



    public function productCategoryList()
    {
        $productCategory = ProductCategory::get();
        return view('pages.masters.productCategory', compact('productCategory'));

    }

    public function products()
    {
        $products = Product::with(['category', 'subcategory','stock'])->get();
        // dd($products);
        return view('pages.product', compact('products'));

    }

    public function getProductData($catId)
    {
        $productData = Product::with('stock')->where('Productcategory', $catId)->get();

        $category = ProductCategory::find($catId);

        $vendors = [];

        if ($category->vendor_id != null) {
            $vendorIds = json_decode($category->vendor_id, true);
            $vendors = Vendor::whereIn('id', $vendorIds)->where("vendor_status", "active")->get();
        }



        return response()->json(['productData' => $productData, 'vendors' => $vendors], 200);
    }

    public function bulkUploadProduct(Request $request)
    {
        $products = Product::with(['category', 'stock'])->get();
        Excel::import(new ProductImport, $request->file('product_data_file'));
        return view('pages.product',compact('products'))->with('success', 'Bulk data uploaded successfully.');
    }
    public function bulkUploadMasterView()
{
    return view('pages.bulk-upload-master');
}

public function bulkUploadMaster(Request $request)
{
    $request->validate([
        'master_file' => 'required|mimes:xlsx,xls|max:10240',
    ]);

    try {
        $file = $request->file('master_file');
        
        // Auto-detect heading row (Row 1 vs Row 2)
        $headingRow = 1;
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $firstSheet = $spreadsheet->getSheet(0);
            $row1A = strtoupper(trim((string) $firstSheet->getCell('A1')->getValue()));
            $row2A = strtoupper(trim((string) $firstSheet->getCell('A2')->getValue()));

            if (str_contains($row2A, 'PRODUCT') || str_contains($row2A, 'SEGNMENT') || str_contains($row2A, 'SEGMENT') || str_contains($row2A, 'MODEL')) {
                $headingRow = 2;
            } elseif (str_contains($row1A, 'PRODUCT') || str_contains($row1A, 'SEGNMENT') || str_contains($row1A, 'SEGMENT') || str_contains($row1A, 'MODEL')) {
                $headingRow = 1;
            }
        } catch (\Exception $e) {
            $headingRow = 1;
        }

        $importer = new ProductMasterImport($headingRow);

        DB::transaction(function () use ($importer, $file) {
            Excel::import($importer, $file);
        });

        $createdCount = count($importer->report['created']);
        $updatedCount = count($importer->report['updated']);
        $msg = "Import completed successfully! {$createdCount} products created, {$updatedCount} products updated.";

        return back()
            ->with('success', $msg)
            ->with('bulk_report', $importer->report);

    } catch (\Throwable $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

public function getProductsByCategory(Request $request, $categoryId)
{
    $subCategoryId = $request->get('sub_category_id') ?? $request->get('subCategoryId');

    $query = Product::where('productcategory', $categoryId);
    if (!empty($subCategoryId)) {
        $query->where('sub_cat_id', $subCategoryId);
    }

    $products = $query->select('id', 'productname', 'productmodel', 'partno', 'rate', 'hsn', 'gst', 'dispatch_time', 'allo_dis', 'sub_cat_id', 'productdes')
        ->get();

    return response()->json($products);
}
}
