<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LeadDesign;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class LeadDesignController extends Controller
{

    public function edit($id)
{
    $design = LeadDesign::findOrFail($id);

    return response()->json([
        'leadiid' => $design->leadiid,
        'category_name' => ProductCategory::find($design->catename)?->category_name ?? '',
        'product_name' => Product::find($design->proname)?->productname ?? '',
        'product_id'     => $design->proname,
        'design' => $design->design,
        'drawing' => $design->drawing,
        'offer' => $design->offer,
        'pricevalue' => $design->pricevalue,
        'freightvalue' => $design->freightvalue,
        'installvalue' => $design->installvalue,
        'basicsupply' => $design->basicsupply,
    ]);
}

public function update(Request $request, $id)
{
    $design = LeadDesign::findOrFail($id);

    $design->design = $request->design;
    $design->pricevalue = $request->pricevaluess;
    $design->freightvalue = $request->freightvaluess;
    $design->installvalue = $request->installvaluess;
    $design->basicsupply = $request->basicsupplyss;

    // Handle Drawing File
    if ($request->hasFile('drawing')) {
        $drawingFile = $request->file('drawing')[0];
        $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
        $drawingFile->move(public_path('assets/images/'), $drawingFilename);
        $design->drawing = 'assets/images/' . $drawingFilename;
    }

    // Handle Offer File
    if ($request->hasFile('offer')) {
        $offerFile = $request->file('offer')[0];
        $offerFilename = time() . '_offer_' . $offerFile->getClientOriginalName();
        $offerFile->move(public_path('assets/images/'), $offerFilename);
        $design->offer = 'assets/images/' . $offerFilename;
    }

    $design->save();

    // Return a success message as JSON
    return response()->json(['message' => 'Design updated successfully!']);
}


public function softDeletedesign($id)
{
    $design = LeadDesign::find($id);

    if ($design) {
        $design->softde = 1;  // Set the softde column to 1
        $design->save();

        return response()->json(['success' => true, 'message' => 'Design soft deleted successfully']);
    }

    return response()->json(['success' => false, 'message' => 'Design not found'], 404);
}

}
