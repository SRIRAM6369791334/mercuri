<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\DataTables\LeadDataTable;
use App\Imports\AccessoryMatrixImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoryController extends Controller
{
    public function fetchTableData(Request $request, $id = null)
    {
        try {
            $productId = $id ?? $request->product_id ?? $request->id;

            if (!$productId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product ID is required',
                    'accessories' => []
                ]);
            }

            $accessories = Accessory::where('product_id', $productId)
                ->where('status', 'active')
                ->select('id', 'name', 'price', 'tax_percent')
                ->get();

            return response()->json([
                'status' => true,
                'accessories' => $accessories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'accessories' => []
            ], 500);
        }
    }

    public function index(LeadDataTable $dataTable = null)
    {
        $accessories = Accessory::with(['product', 'category'])->latest()->get();
        return view('admin.accessories.index', compact('accessories'));
    }

    public function create()
    {
        $products = Product::select('id', 'productname', 'Productcategory')->get();
        $categories = ProductCategory::all();
        $selectedProductIds = [];
        return view('admin.accessories.create', compact('products', 'categories', 'selectedProductIds'));
    }

    public function getProducts($category)
    {
        if (empty($category) || $category === 'all') {
            $products = Product::select('id', 'productname')->get();
        } else {
            $products = Product::where('Productcategory', $category)
                ->orWhere('productcategory', $category)
                ->select('id', 'productname')
                ->get();
        }

        return response()->json($products);
    }

    public function store(Request $request)
    {
        if (empty($request->product_category_id)) {
            $request->merge(['product_category_id' => null]);
        }

        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'product_category_id' => 'nullable|exists:product_categories_tb,id',
            'accessories' => 'required|array|min:1',
            'accessories.*.name' => 'required|string|max:255',
            'accessories.*.price' => 'required|numeric|min:0',
            'accessories.*.tax_percent' => 'required|numeric|min:0',
            'accessories.*.part_no' => 'nullable|string|max:255',
            'accessories.*.hsn' => 'nullable|string|max:255',
            'accessories.*.status' => 'required|in:active,inactive',
        ], [
            'product_ids.required' => 'Please select at least one product.',
            'product_ids.min' => 'Please select at least one product.',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->product_ids as $productId) {
                foreach ($request->accessories as $acc) {
                    Accessory::create([
                        'product_id' => $productId,
                        'product_category_id' => $request->product_category_id,
                        'name' => $acc['name'],
                        'price' => $acc['price'],
                        'tax_percent' => $acc['tax_percent'],
                        'part_no' => $acc['part_no'] ?? null,
                        'hsn' => $acc['hsn'] ?? null,
                        'status' => $acc['status'] ?? 'active',
                    ]);
                }
            }
        });

        return redirect()->route('accessories.index')->with('success', 'Accessories added successfully!');
    }

    public function edit($id)
    {
        $accessory = Accessory::findOrFail($id);
        $products = Product::select('id', 'productname', 'Productcategory')->get();
        $categories = ProductCategory::all();

        // Find all matching accessories to pre-select associated product IDs
        $query = Accessory::where('name', $accessory->name);
        if (!empty($accessory->part_no)) {
            $query->where('part_no', $accessory->part_no);
        } else {
            $query->whereNull('part_no');
        }
        $selectedProductIds = $query->pluck('product_id')->filter()->unique()->toArray();

        if (empty($selectedProductIds) && $accessory->product_id) {
            $selectedProductIds = [$accessory->product_id];
        }

        $productAccessories = Accessory::whereIn('product_id', $selectedProductIds)
            ->where('name', $accessory->name)
            ->get();

        if ($productAccessories->isEmpty()) {
            $productAccessories = collect([$accessory]);
        } else {
            $productAccessories = $productAccessories->unique(function ($item) {
                return $item->name . '-' . $item->part_no . '-' . $item->price;
            });
        }

        return view('admin.accessories.edit', compact(
            'accessory',
            'products',
            'productAccessories',
            'categories',
            'selectedProductIds'
        ));
    }

    public function update(Request $request, $id)
    {
        if (empty($request->product_category_id)) {
            $request->merge(['product_category_id' => null]);
        }

        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'product_category_id' => 'nullable|exists:product_categories_tb,id',
            'accessories' => 'required|array|min:1',
            'accessories.*.name' => 'required|string|max:255',
            'accessories.*.price' => 'required|numeric|min:0',
            'accessories.*.tax_percent' => 'required|numeric|min:0',
            'accessories.*.part_no' => 'nullable|string|max:255',
            'accessories.*.hsn' => 'nullable|string|max:255',
            'accessories.*.status' => 'required|in:active,inactive',
        ], [
            'product_ids.required' => 'Please select at least one product.',
            'product_ids.min' => 'Please select at least one product.',
        ]);

        $currentAccessory = Accessory::findOrFail($id);

        $query = Accessory::where('name', $currentAccessory->name);
        if (!empty($currentAccessory->part_no)) {
            $query->where('part_no', $currentAccessory->part_no);
        } else {
            $query->whereNull('part_no');
        }
        $oldProductIds = $query->pluck('product_id')->filter()->unique()->toArray();
        if (empty($oldProductIds) && $currentAccessory->product_id) {
            $oldProductIds = [$currentAccessory->product_id];
        }

        $newProductIds = array_map('intval', $request->product_ids);

        DB::transaction(function () use ($request, $oldProductIds, $newProductIds, $currentAccessory) {
            $removedProductIds = array_diff($oldProductIds, $newProductIds);
            if (!empty($removedProductIds)) {
                $delQuery = Accessory::whereIn('product_id', $removedProductIds)
                    ->where('name', $currentAccessory->name);
                if (!empty($currentAccessory->part_no)) {
                    $delQuery->where('part_no', $currentAccessory->part_no);
                } else {
                    $delQuery->whereNull('part_no');
                }
                $delQuery->delete();
            }

            foreach ($newProductIds as $productId) {
                foreach ($request->accessories as $accData) {
                    $accQuery = Accessory::where('product_id', $productId)
                        ->where('name', $accData['name']);
                    if (!empty($accData['part_no'])) {
                        $accQuery->where('part_no', $accData['part_no']);
                    }
                    $existing = $accQuery->first();

                    if ($existing) {
                        $existing->update([
                            'product_category_id' => $request->product_category_id,
                            'name' => $accData['name'],
                            'price' => $accData['price'],
                            'tax_percent' => $accData['tax_percent'],
                            'part_no' => $accData['part_no'] ?? null,
                            'hsn' => $accData['hsn'] ?? null,
                            'status' => $accData['status'] ?? 'active',
                        ]);
                    } else {
                        Accessory::create([
                            'product_id' => $productId,
                            'product_category_id' => $request->product_category_id,
                            'name' => $accData['name'],
                            'price' => $accData['price'],
                            'tax_percent' => $accData['tax_percent'],
                            'part_no' => $accData['part_no'] ?? null,
                            'hsn' => $accData['hsn'] ?? null,
                            'status' => $accData['status'] ?? 'active',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('accessories.index')->with('success', 'Accessories updated successfully!');
    }

    public function destroy($id)
    {
        $accessory = Accessory::findOrFail($id);
        $accessory->delete();

        return redirect()->route('accessories.index')->with('success', 'Accessory deleted successfully!');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'accessory_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('accessory_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getSheet(0);

            $reset = $request->boolean('reset_accessories');
            $importer = new AccessoryMatrixImport($reset);

            DB::transaction(function () use ($importer, $sheet) {
                $importer->import($sheet);
            });

            $matchedCount    = $importer->report['products_matched'];
            $mappedCount     = $importer->report['accessories_mapped'];
            $skippedProducts = $importer->report['skipped_products'] ?? [];
            $skippedCount    = count($skippedProducts);

            $redirect = redirect()->route('accessories.index')
                ->with('success', "Bulk upload completed! {$mappedCount} accessories successfully mapped across {$matchedCount} products.");

            if ($skippedCount > 0) {
                $redirect->with('warning', "{$skippedCount} products were skipped because they were not found in the database.")
                         ->with('skipped_products', $skippedProducts);
            }

            return $redirect;

        } catch (\Throwable $e) {
            return redirect()->route('accessories.index')->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }
}