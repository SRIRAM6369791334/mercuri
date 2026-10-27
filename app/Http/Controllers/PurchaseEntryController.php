<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseEntry;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseEntryController extends Controller
{

    public function getPEProductDetails(Request $request)
    {
        $poNo = $request->poNo;
        $catId = $request->catId;
        $productData = PurchaseOrder::with(['productDetails'])->where("po_no", $poNo)->where("cat_id", $catId)->get();

        return response()->json(['productData' => $productData], 200);
    }

    public function savePE(Request $request)
    {

        $catId = $request->catId;
        $proId = $request->proId;
        $poId = $request->po_id;
        $poNumber = $request->po_number;
        $vendorId = $request->vendorId;
        $pe_req_qty = $request->pe_req_qty;
        $pe_received_qty = $request->pe_received_qty;
        $pe_pending_qty = $request->pe_pending_qty;
        $unit_price = $request->unit_price;
        $gst = $request->gst;
        $sub_total = $request->sub_total;
        $product_total = $request->product_total;

        foreach ($catId as $key => $value) {

            $product = Product::findOrFail($proId[$key]);

            
            $sub_total = (int)$pe_received_qty[$key] * (float)$unit_price[$key];
            $gstAmt = ($sub_total / 100) * $product->gst;
            $totalAmt = $sub_total + $gstAmt;
            
            PurchaseEntry::create([
                "cat_id" => $value,
                "po_no" => $poNumber[$key],
                "po_id" => $poId[$key],
                "product_id" => $proId[$key],
                "vendor_id" => $vendorId[$key],
                "requested_qty" => $pe_req_qty[$key],
                "received_qty" => $pe_received_qty[$key],
                "pending_qty" => $pe_pending_qty[$key],
                "unit_price" => $unit_price[$key],
                "product_total_price" => $sub_total,
                "gst_amount" => $gstAmt,
                "sub_total" => $sub_total,
                "total_amount" => $totalAmt,
            ]);

            $lastPeId = PurchaseEntry::latest()->first();

            // to track stock movements

            $product->addStock($proId[$key], $vendorId[$key], $pe_received_qty[$key], 'Stock purchased', $lastPeId->id);
        }

        return redirect()->route('purchase-entry-details')->with('success', 'Purchase entry created successfully');
    }

    public function editPEDetails($peId)
    {
        $peDetails = PurchaseEntry::with(["categoryDetails", "productDetails", "vendorDetails"])->find($peId);
        // dd($peDetails);
        return view("pages.vendor.purchase_entry.editPurchaseEntry", compact("peDetails"));
    }

    public function editPE(Request $request)
    {

        // dd($request->all());

        $peId = $request->pe_id;
        $catId = $request->catId;
        $proId = $request->proId;
        $old_product_id = $request->old_product_id;
        $old_product_qty = $request->old_product_qty;
        $poId = $request->po_id;
        $poNumber = $request->po_number;
        $vendorId = $request->vendorId;
        $pe_req_qty = $request->pe_req_qty;
        $pe_received_qty = $request->pe_received_qty;
        $pe_pending_qty = $request->pe_pending_qty;
        $unit_price = $request->unit_price;
        $gst = $request->gst;
        $sub_total = $request->sub_total;
        $product_total = $request->product_total;

        foreach ($catId as $key => $value) {

            $product = Product::findOrFail($proId[$key]);

            $peUpdate = PurchaseEntry::find($peId[$key]);
            
            $sub_total = (int)$pe_received_qty[$key] * (float)$unit_price[$key];
            $gstAmt = ($sub_total / 100) * $product->gst;
            $totalAmt = $sub_total + $gstAmt;

            $peUpdate->cat_id = $value;
            $peUpdate->po_no = $poNumber[$key];
            $peUpdate->po_id = $poId[$key];
            $peUpdate->product_id = $proId[$key];
            $peUpdate->vendor_id = $vendorId[$key];
            $peUpdate->requested_qty = $pe_req_qty[$key];
            $peUpdate->received_qty = $pe_received_qty[$key];
            $peUpdate->pending_qty = $pe_pending_qty[$key];
            $peUpdate->unit_price = $unit_price[$key];
            $peUpdate->product_total_price = $sub_total;
            $peUpdate->gst_amount = $gstAmt;
            $peUpdate->sub_total = $sub_total;
            $peUpdate->total_amount = $totalAmt;

            // to track stock movements

            if ($old_product_id[$key] != $proId[$key]) {
                $product->updateStock($proId[$key], $vendorId[$key], $pe_received_qty[$key], 'Stock purchased', $peId[$key], $old_product_id[$key], $old_product_qty[$key]);
            }

            $peUpdate->update();


        }

        return redirect()->route('purchase-entry-details')->with('success', 'Purchase entry created successfully');
    }


    public function deletePE($peId, $oldQty, $proId)
    {
        $product = Product::findOrFail($proId);
        $deletePe = PurchaseEntry::find($peId)->delete();
        if ($deletePe) {
            $product->deleteStock($proId, $oldQty);
            return back()->with('delete-success', 'Purchase entry deleted successfully');
        } else {
            return back()->with('delete-error', 'Something went wrong');
        }
    }
}
