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
        $productStock = ProductStock::where("product_id", $request->productId)->first();
        $catId = $request->Productcategory;
        if ((empty($catId) || $catId == 1) && preg_match('/(forklift|reach|walkie)/i', $request->productname)) {
            $catId = 4; // MHE High End
        }

        $cleanRate = is_string($request->rate) ? preg_replace('/[^\d.]/', '', $request->rate) : $request->rate;

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

        if ($productStock->total_stock != $productStock->available_stock) {
            $productStock->update([
                "total_stock" => $request->quantity,
                "available_stock" => $request->quantity
            ]);
        }



        if ($product) {
            return redirect()->route('products')->with('success', 'Product Updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Product update error, Please try again.');
        }

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

    $products = $query->select('id', 'productname', 'partno', 'rate', 'gst', 'dispatch_time', 'allo_dis', 'sub_cat_id')
        ->get();

    return response()->json($products);
}
}
