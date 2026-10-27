<?php

namespace App\Http\Controllers;

use App\Models\ProductSubCategory;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductSubCategoryController extends Controller
{
    public function productSubCategoryList()
    {
        $productCategory = ProductSubCategory::with('segmentDetails')
            ->orderBy('id', 'desc')
            ->get();
        return view('pages.masters.productSubCategory', compact('productCategory'));
    }

    public function getProductsByKeyword(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));
        $segmentId = $request->get('segment_id', '');

        if (empty($segmentId)) {
            return response()->json([
                'status' => true,
                'products' => []
            ]);
        }

        $query = Product::where('Productcategory', $segmentId);

        if (!empty($keyword)) {
            $words = array_filter(explode(' ', $keyword));
            $query->where(function ($q) use ($keyword, $words) {
                $q->where('productname', 'LIKE', '%' . $keyword . '%');
                foreach ($words as $word) {
                    $w = trim($word);
                    if (strlen($w) >= 2) {
                        $q->orWhere('productname', 'LIKE', '%' . $w . '%');
                    }
                }
            });
        }

        $products = $query->orderBy('productname', 'asc')->get(['id', 'productname', 'partno']);

        // Fallback: If keyword filter returned no products, get all products for the segment
        if ($products->isEmpty()) {
            $products = Product::where('Productcategory', $segmentId)
                ->orderBy('productname', 'asc')
                ->get(['id', 'productname', 'partno']);
        }

        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }

    public function addSubCategoryFunction(Request $request)
    {
        $validated = $request->validate([
            'product_segment_id' => 'required|exists:product_categories_tb,id',
            'sub_category' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_sub_categories', 'subcategory_name')
                    ->where('cat_id', $request->product_segment_id),
            ],
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'nullable|exists:products,id',
        ], [
            'sub_category.unique' => 'A group with this name already exists in the selected segment.',
        ]);

        $tb = new ProductSubCategory;

        $tb->cat_id = $validated['product_segment_id'];
        $tb->subcategory_name = trim($validated['sub_category']);
        $tb->product_id = !empty($validated['product_ids']) ? $validated['product_ids'][0] : null;

        if ($tb->save()) {
            if (!empty($validated['product_ids']) && is_array($validated['product_ids'])) {
                Product::whereIn('id', array_filter($validated['product_ids']))->update(['sub_cat_id' => $tb->id]);
            }
            return back()->with('success', 'Group Added Successfully!');
        } else {
            return back()->with('error', 'Something went wrong, Please try again');
        }
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'product_segment_id' => 'required|exists:product_categories_tb,id',
            'sub_category'       => 'required|string|max:255',
            'product_id'         => 'nullable|exists:products,id',
        ]);

        $sub = ProductSubCategory::where('cat_id', $request->product_segment_id)
            ->whereRaw("LOWER(TRIM(subcategory_name)) = ?", [strtolower(trim($request->sub_category))])
            ->first();

        if (!$sub) {
            $sub = ProductSubCategory::create([
                'cat_id'           => $request->product_segment_id,
                'subcategory_name' => trim($request->sub_category),
                'product_id'       => $request->product_id,
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $sub
        ]);
    }

    public function editSubCategoryFunction(Request $request)
    {
        $validated = $request->validate([
            'sub_hidden_id' => 'required|exists:product_sub_categories,id',
            'product_segment_id' => 'required|exists:product_categories_tb,id',
            'edit_sub_category_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_sub_categories', 'subcategory_name')
                    ->where('cat_id', $request->product_segment_id)
                    ->ignore($request->sub_hidden_id),
            ],
            'edit_product_ids' => 'nullable|array',
            'edit_product_ids.*' => 'nullable|exists:products,id',
        ], [
            'edit_sub_category_name.unique' => 'A group with this name already exists in the selected segment.',
        ]);

        $tb = ProductSubCategory::find($validated['sub_hidden_id']);

        $tb->subcategory_name = trim($validated['edit_sub_category_name']);
        $tb->cat_id = $validated['product_segment_id'];
        $tb->product_id = !empty($validated['edit_product_ids']) ? $validated['edit_product_ids'][0] : null;

        if ($tb->update()) {
            // Unlink old products for this group
            Product::where('sub_cat_id', $tb->id)->update(['sub_cat_id' => null]);
            // Link new selected products
            if (!empty($validated['edit_product_ids']) && is_array($validated['edit_product_ids'])) {
                Product::whereIn('id', array_filter($validated['edit_product_ids']))->update(['sub_cat_id' => $tb->id]);
            }
            return back()->with('success', 'Group Edited Successfully!');
        } else {
            return back()->with('error', 'Something went wrong, Please try again');
        }
    }

    public function deleteSubCategoryFunction($Id)
    {
        $category = ProductSubCategory::find($Id);

        if (!$category) {
            return back()->with('error', 'Group not found.');
        }

        if ($category->delete()) {
            return back()->with('success', 'Group Deleted Successfully!');
        } else {
            return back()->with('error', 'Something went wrong, Please try again');
        }
    }

    public function getSubCategory($categoryId)
    {
        $query = ProductSubCategory::query();

        if ($categoryId && $categoryId !== 'all') {
            $query->where('cat_id', $categoryId);
        }

        $data = $query->orderBy('subcategory_name', 'asc')->get(['id', 'subcategory_name', 'cat_id']);

        if ($data->isEmpty()) {
            $data = ProductSubCategory::orderBy('subcategory_name', 'asc')->get(['id', 'subcategory_name', 'cat_id']);
        }

        return response()->json(['subcategory_details' => $data]);
    }
}
