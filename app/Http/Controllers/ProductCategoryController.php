<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    private function processKeywordPayload($keywordsInput, $dispatchesInput)
    {
        if (is_array($keywordsInput) || is_array($dispatchesInput)) {
            $keywords = is_array($keywordsInput) ? $keywordsInput : [$keywordsInput];
            $dispatches = is_array($dispatchesInput) ? $dispatchesInput : [$dispatchesInput];

            $filteredKeywords = [];
            $filteredDispatches = [];

            foreach ($keywords as $idx => $kw) {
                $kwVal = trim((string)$kw);
                $dispVal = trim((string)($dispatches[$idx] ?? ''));

                if (!empty($kwVal) || !empty($dispVal)) {
                    $filteredKeywords[] = $kwVal;
                    $filteredDispatches[] = $dispVal;
                }
            }

            return [
                'product_keyword' => !empty($filteredKeywords) ? json_encode($filteredKeywords) : null,
                'keyword_dispatch_time' => !empty($filteredDispatches) ? json_encode($filteredDispatches) : null,
            ];
        }

        return [
            'product_keyword' => $keywordsInput ? trim((string)$keywordsInput) : null,
            'keyword_dispatch_time' => $dispatchesInput ? trim((string)$dispatchesInput) : null,
        ];
    }

    public function addCategoryFunction(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(ProductCategory::SEGMENT_TYPES)),
            'sub_category' => 'required|in:' . implode(',', array_keys(ProductCategory::WORKFLOW_TYPES)),
            'dispatch_time' => 'required|string|max:255',
            'product_keyword' => 'nullable',
            'keyword_dispatch_time' => 'nullable',
        ]);

        $category = new ProductCategory;

        $category->category_name = $validated['category_name'];
        $category->sub_category = $validated['sub_category'];
        $category->type = $validated['type'];
        $category->dispatch_time = $validated['dispatch_time'];

        $payload = $this->processKeywordPayload($request->product_keyword, $request->keyword_dispatch_time);
        $category->product_keyword = $payload['product_keyword'];
        $category->keyword_dispatch_time = $payload['keyword_dispatch_time'];

        if ($category->save()) {
            return redirect()->route('product-categories')->with('success', 'Category Added Successfully!');
        } else {
            return redirect()->route('product-categories')->with('error', 'Something went wrong, Please try again');
        }

    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'category_name'  => 'required|string|max:255',
            'type'           => 'required|in:' . implode(',', array_keys(ProductCategory::SEGMENT_TYPES)),
            'sub_category'   => 'required|in:' . implode(',', array_keys(ProductCategory::WORKFLOW_TYPES)),
            'dispatch_time'  => 'required|string|max:255',
            'product_keyword' => 'nullable',
            'keyword_dispatch_time' => 'nullable',
        ]);

        $payload = $this->processKeywordPayload($request->product_keyword, $request->keyword_dispatch_time);

        $segment = ProductCategory::create([
            'category_name' => $request->category_name,
            'type'          => $request->type,
            'sub_category'  => $request->sub_category,
            'dispatch_time' => $request->dispatch_time,
            'product_keyword' => $payload['product_keyword'],
            'keyword_dispatch_time' => $payload['keyword_dispatch_time'],
        ]);

        return response()->json([
            'status' => true,
            'data'   => $segment
        ]);
    }

    public function editCategoryFunction(Request $request)
    {
        $validated = $request->validate([
            'catid' => 'required|exists:product_categories_tb,id',
            'edit_category_name' => 'required|string|max:255',
            'edit_type' => 'required|in:' . implode(',', array_keys(ProductCategory::SEGMENT_TYPES)),
            'edit_sub_category_name' => 'required|in:' . implode(',', array_keys(ProductCategory::WORKFLOW_TYPES)),
            'edit_dispatch_time' => 'required|string|max:255',
            'edit_product_keyword' => 'nullable',
            'edit_keyword_dispatch_time' => 'nullable',
        ]);

        $category = ProductCategory::find($validated['catid']);

        $category->category_name = $validated['edit_category_name'];
        $category->sub_category = $validated['edit_sub_category_name'];
        $category->type = $validated['edit_type'];
        $category->dispatch_time = $validated['edit_dispatch_time'];

        $payload = $this->processKeywordPayload($request->edit_product_keyword, $request->edit_keyword_dispatch_time);
        $category->product_keyword = $payload['product_keyword'];
        $category->keyword_dispatch_time = $payload['keyword_dispatch_time'];

        if ($category->update()) {
            return redirect()->route('product-categories')->with('success', 'Category Edited Successfully!');
        } else {
            return redirect()->route('product-categories')->with('error', 'Something went wrong, Please try again');
        }

    }

    public function deleteCategoryFunction($catId)
    {
        $category = ProductCategory::find($catId);

        if (!$category) {
            return redirect()->route('product-categories')->with('error', 'Segment not found.');
        }

        if (ProductSubCategory::where('cat_id', $catId)->exists()) {
            return redirect()->route('product-categories')->with('error', 'Delete the related groups first.');
        }

        if ($category->delete()) {
            return redirect()->route('product-categories')->with('success', 'Category Deleted Successfully!');
        } else {
            return redirect()->route('product-categories')->with('error', 'Something went wrong, Please try again');
        }

    }

}
