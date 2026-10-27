<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AlertDetail;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseStockController extends Controller
{

    public function productStockUpdation(Request $request)
    {
        $vendorId = $request->edit_prostock_vendor;
        $productId = $request->edit_prostock_product;
        $proStockQty = $request->pro_stock_qty;
        $proStockType = $request->pro_stock_type;


        $product = Product::findOrFail($productId);

        if ($proStockType == "purchase") {
            $product->addStock($productId, $vendorId, $proStockQty, $request->pro_stock_remarks);
        } else if ($proStockType == "sale") {
            $product->reduceStock($productId, $proStockQty, $request->pro_stock_remarks);
        } else if ($proStockType == "adjustment") {
            $product->adjustStock($productId, $proStockQty, $request->pro_stock_remarks);
        }

        return back()->with("success", "Product Stock Updated Successfully");
    }

    public function getStockDetails($productId)
    {
        $proStockDetails = StockMovement::with(["vendors"])->where('product_id', $productId)->orderBy('id', 'desc')->get();

        return response()->json(['status' => 'success', 'proStockDetails' => $proStockDetails], 200);
    }

    public function productStockAlertEdit(Request $request)
    {
        $isAlready = AlertDetail::where("type", "product-stock")->first();

        if ($isAlready) {
            $proStockDetails = AlertDetail::where('type', "product-stock")->update([
                "alert_detail" => $request->pro_stock_alert_count,
            ]);
        } else {
            $proStockDetails = AlertDetail::create([
                "alert_detail" => $request->pro_stock_alert_count,
                "type" => "product-stock",
            ]);
        }

        if ($proStockDetails) {
            return back()->with("alert-success", "Product alert count updated successfully");
        } else {
            return back()->with("alert-error", "Something went wrong");
        }
    }

    public function getProductStocksCount()
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error']);
        }

        $userRole = Auth::user()->role ?? '';

        // Exclude all Sales roles from stock alert popups
        if (str_contains(strtolower($userRole), 'sales')) {
            return response()->json(['status' => 'success', 'data' => [], 'role' => $userRole, 'is_sales' => true]);
        }

        $settingsDetails = AdminSetting::first();
        if ($settingsDetails) {
            $counts = ProductStock::with('product')->where("available_stock", '<=', $settingsDetails->product_stock_alert_count)->get();

            if ($counts) {
                return response()->json(['status' => 'success', 'data' => $counts, 'role' => $userRole, 'is_sales' => false]);
            } else {
                return response()->json(['status' => 'error']);
            }
        } else {
            return response()->json(['status' => 'error']);
        }

    }
}
