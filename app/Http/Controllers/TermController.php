<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TermController extends Controller
{
    //
    public function viewterms()
    {
        $terms = Term::with([
            'category',
            'productSubCategory',
            'product',
            'frieghtDetails',
            'frieghtDetails.quotationDetails',
            'frieghtWarrantyDetails',
            'frieghtWarrantyDetails.quotationDetails'
        ])->get();
        // dd($terms);
        return view('pages.viewterms', compact('terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'whichterm' => 'required',
            'termtype' => 'required',
            'content' => 'required',
            'product_id' => 'nullable|exists:products,id',
            'product_sub_category_id' => 'nullable|exists:product_sub_categories,id',
            'product_category_id' => 'nullable|exists:product_categories_tb,id',
        ]);

        $isWarranty = ((int) $request->termtype === 2);

        $normalizedSearch = strtolower(str_replace([' ', ';'], ['', ''], $request->content));
        $exists = Term::where('whichterm', 1)
            ->where('termtype', $request->termtype)
            ->where(function ($q) use ($request) {
                if ($request->filled('product_category_id')) {
                    $q->where('product_category_id', $request->product_category_id);
                } else {
                    $q->whereNull('product_category_id');
                }
            })
            ->whereRaw('TRIM(LOWER(REPLACE(REPLACE(content, " ", ""), ";", ""))) = ?', [$normalizedSearch])
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'A term with this content already exists for the selected segment.');
        }

        Term::create([
            'whichterm' => 1,
            'termtype' => $request->termtype,
            'term_approve' => 0,
            'content' => trim($request->content),
            'product_id' => $isWarranty ? $request->product_id : null,
            'product_sub_category_id' => $isWarranty ? $request->product_sub_category_id : null,
            'product_category_id' => $request->product_category_id,
        ]);

        return redirect()->route('terms.view')->with('success', 'Term added successfully!');
    }

    public function edit($id)
    {
        $term = Term::findOrFail($id);
        return view('pages.editterms', compact('term'));
    }

    // Update the term
    public function update(Request $request, $id)
    {
        $request->validate([
            'termtype' => 'required',
            'content' => 'required',
            // 'whichterm' => 'required',
            'product_id' => 'nullable|exists:products,id',
            'product_sub_category_id' => 'nullable|exists:product_sub_categories,id',
            'product_category_id' => 'nullable|exists:product_categories_tb,id',
        ]);

        $isWarranty = ((int) $request->termtype === 2);

        $term = Term::findOrFail($id);
        $term->termtype = $request->termtype;
        $term->content = $request->content;
        $term->whichterm = 1;
        $term->product_id = $isWarranty ? $request->product_id : null;
        $term->product_sub_category_id = $isWarranty ? $request->product_sub_category_id : null;
        $term->product_category_id = $request->product_category_id;
        $term->save();

        return redirect()->route('terms.view')->with('success', 'Term updated successfully!');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'term_approve' => 'required|in:0,1,2',
            'term_remark' => 'nullable|string|max:1000',
        ]);

        $term = Term::findOrFail($id);
        $term->term_approve = (int) $request->input('term_approve');

        // Save remark if rejected
        if ($term->term_approve == 2) {
            $term->term_remark = $request->input('term_remark');
        } else {
            $term->term_remark = null;
        }

        $term->save();

        return response()->json(['success' => true]);

    }

    public function destroy($id)
    {
        $term = Term::findOrFail($id);
        $term->delete();
        return redirect()->route('terms.view')->with('success', 'Term deleted successfully!');
    }

    public function getSubCategoriesBySegment($id)
    {
        $subcategories = \App\Models\ProductSubCategory::where('cat_id', $id)
            ->orderBy('subcategory_name')
            ->get(['id', 'subcategory_name']);

        return response()->json($subcategories);
    }

    public function getProductsBySegment($id)
    {
        $category = \App\Models\ProductCategory::find($id);

        $query = Product::where('Productcategory', $id);

        // Only for MHE Standard
        if ($category && $category->category_name == 'MHE Standard') {

            $query->where('productname', 'LIKE', '%Hydraulic Hand Pallet Truck%');
        }

        $products = $query
            ->orderBy('productname')
            ->get(['id', 'productname']);

        return response()->json($products);
    }
}
