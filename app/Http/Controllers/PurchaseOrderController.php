<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseEntry;
use App\Models\PurchaseOrder;
use App\Models\Orf;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Models\Vendor;
use App\Models\ProductCategory;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Citylist;
use App\Models\Statelist;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{

    public function viewPurchaseOrder()
    {
        $poDetails = PurchaseOrder::with(['allVendorDetails', 'productDetails', 'categoryDetails','productStock', 'orf.lead'])->orderBy('id', 'desc')->get();
        // dd($poDetails);
        return view('pages.vendor.purchase_order.purchaseOrder', compact('poDetails'));
    }

    public function createPoView(Request $request)
    {
        $orf = null;
        $orderItems = collect();
        $isOrderPo = false;
        $quotation = null;
        $defaultCustomerGst = null;
        $defaultCustomerAddress = null;
        $orfSpecialInstructions = null;
        $orfDrawingAttachment = null;

        if ($request->filled('orf_id')) {
            $orf = Orf::with(['lead'])->find($request->orf_id);
            if ($orf) {
                $isOrderPo = true;
                $quotation = Quotation::find($orf->quoproid) ?? Quotation::where('quota_id', $orf->quono)->first();
                $orderItems = QuotationProduct::with(['productDetails.productStock', 'productDetails.specifications'])->where('quotationno', $orf->quono)->get();

                if ($orderItems->isEmpty() && $quotation) {
                    $orderItems = QuotationProduct::with(['productDetails.productStock', 'productDetails.specifications'])->where('quotationno', $quotation->quota_id)->get();
                }

                $defaultCustomerGst = $orf->lead?->gst;
                $defaultCustomerAddress = $quotation?->shipping_address ?: ($quotation?->billing_address ?: ($orf->lead?->billaddress ?: $orf->lead?->communicate_address));
                $orfSpecialInstructions = $orf->specialins;
                $orfDrawingAttachment = $orf->sigdraw;
            }
        }

        $allCustomers = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->where('gstno', '!=', '')
            ->select('id', 'customername', 'gstno', 'address', 'city', 'state', 'postalcode')
            ->orderBy('customername')
            ->get();

        $allVendors = Vendor::with(['stateData', 'cityData'])
            ->where('vendor_status', 'active')
            ->orderBy('company_name')
            ->get();

        return view('pages.vendor.purchase_order.addPurchaseOrder', compact(
            'orf',
            'orderItems',
            'isOrderPo',
            'quotation',
            'defaultCustomerGst',
            'defaultCustomerAddress',
            'orfSpecialInstructions',
            'orfDrawingAttachment',
            'allCustomers',
            'allVendors'
        ));
    }

    public function generatePONumber($lastPONumber = null)
    {
        // Get current date
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Calculate financial year
        if ($currentMonth >= 4) { // From April to December
            $startYear = $currentYear % 100;  // Last two digits of year
            $endYear = ($currentYear + 1) % 100;
        } else { // From January to March (Still in previous financial year)
            $startYear = ($currentYear - 1) % 100;
            $endYear = $currentYear % 100;
        }

        // Format financial year as "24-25", "26-27"
        $financialYear = sprintf("%02d-%02d", $startYear, $endYear);

        // Determine next PO number
        if ($lastPONumber) {
            $lastNumber = (int) substr($lastPONumber, strrpos($lastPONumber, '/') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Generate PO number with MES/PO/ prefix
        return "MES/PO/$financialYear/" . str_pad($nextNumber, 3, "0", STR_PAD_LEFT);
    }

    public function viewPOInvoice($invoiceId)
    {
        $poDetails = PurchaseOrder::with([
            'allVendorDetails',
            'productDetails.specifications',
            'categoryDetails',
            'termsConditionDetails'
        ])->where('po_no', $invoiceId)->get();

        return view('pages.vendor.purchase_order.po_invoice', compact('poDetails'));
    }

    public function savePo(Request $request)
    {
        $catId = $request->catId ?? [];
        $proId = $request->proId ?? [];
        $vendorId = $request->vendorId ?? [];
        $unit_price = $request->unit_price ?? [];
        $qty = $request->qty ?? [];
        $gst = $request->gst ?? [];
        $product_description = $request->pro_des ?? [];
        $roundoff = $request->roundoff ?? [];
        $termsConditionId = $request->terms_condition_id;

        // Supplier Billing & Branches (Section 1.3)
        $supplierBillingAddress = $request->supplier_billing_address;
        $supplierGst = $request->supplier_gst;
        $primaryVendorId = $request->primary_vendor_id ?: ($vendorId[0] ?? null);

        // Commercial Terms (Section 1.5, 1.6, 2.2)
        $leadTime = $request->lead_time ?: '4 Weeks';
        $paymentTerms = $request->payment_terms ?: '50% Advance & 50% Against Proforma';
        $warrantyTerms = $request->warranty_terms ?: '12 Months Standard Manufacturer Warranty';
        $installationScope = $request->installation_scope ?: 'In Supplier Scope';
        $specialInstructions = $request->special_instructions;
        $hideBrandName = $request->boolean('hide_brand_name') ? 1 : 0;

        // Charges and Discounts (Section 2.1, 2.4)
        $discountPercentage = max(0, min(100, (float)($request->discount_percentage ?? 0)));
        $freightCharges = max(0, (float)($request->freight_charges ?? 0));
        $unloadingCharges = max(0, (float)($request->unloading_charges ?? 0));

        // Shipping Details (Section 1.4, 3.4)
        $shippingType = $request->shipping_type ?? 'warehouse';
        $shippingAddress = $request->shipping_address;
        $shippingGst = $request->shipping_gst;

        // Attachments (Section 4.2, 4.3)
        $drawingAttachment = $request->orf_sigdraw ?? null;
        if ($request->hasFile('drawing_attachment')) {
            $drawFile = $request->file('drawing_attachment');
            if ($drawFile->isValid()) {
                $drawingAttachment = time() . '_draw_' . uniqid() . '.' . $drawFile->getClientOriginalExtension();
                $drawFile->move(public_path('uploads/po_files'), $drawingAttachment);
            }
        }

        $quoteAttachments = [];
        if ($request->hasFile('quote_attachments')) {
            foreach ($request->file('quote_attachments') as $qFile) {
                if ($qFile && $qFile->isValid()) {
                    $qFilename = time() . '_quote_' . uniqid() . '.' . $qFile->getClientOriginalExtension();
                    $qFile->move(public_path('uploads/po_files'), $qFilename);
                    $quoteAttachments[] = $qFilename;
                }
            }
        }
        $quoteAttachmentsJson = !empty($quoteAttachments) ? json_encode($quoteAttachments) : null;

        // Legacy attachment fallback
        $fileAttach = $request->file("po_attachment");
        $filename = null;
        if (!empty($fileAttach)) {
            $filename = time() . '_po_' . $fileAttach->getClientOriginalName();
            $fileAttach->move(public_path('uploads/po_files'), $filename);
        }

        $orfId = $request->filled('orf_id') ? $request->orf_id : null;
        $quotationId = $request->filled('quotation_id') ? $request->quotation_id : null;

        $lastInserted = PurchaseOrder::latest()->first();
        if ($lastInserted == null) {
            $newPONumber = $this->generatePONumber("MES/PO/26-27/000");
        } else {
            $newPONumber = $this->generatePONumber($lastInserted->po_no);
        }

        foreach ($catId as $key => $value) {
            $purchaseUnitPrice = (float)($unit_price[$key] ?? 0);
            $productQty = (int)($qty[$key] ?? 0);
            $lineSubTotal = $productQty * $purchaseUnitPrice;

            // Proportional line discount
            $lineDiscount = ($lineSubTotal * $discountPercentage) / 100;
            $lineTaxable = $lineSubTotal - $lineDiscount;

            $productGst = (float)($gst[$key] ?? 0);
            $gstAmt = ($lineTaxable / 100) * $productGst;
            $totalAmt = $lineTaxable + $gstAmt;

            $lineVendorId = !empty($vendorId[$key]) ? $vendorId[$key] : $primaryVendorId;

            PurchaseOrder::create([
                "cat_id" => $value,
                "po_no" => $newPONumber,
                "orf_id" => $orfId,
                "quotation_id" => $quotationId,
                "product_id" => $proId[$key],
                "vendor_id" => $lineVendorId,
                "product_qty" => $productQty,
                "unit_price" => $purchaseUnitPrice,
                "original_order_price" => 0,
                "product_total_price" => round($lineSubTotal, 2),
                "original_total_price" => 0,
                "price_variance" => 0,
                "variance_percentage" => 0,
                "discount_percentage" => $discountPercentage,
                "discount_amount" => round($lineDiscount, 2),
                "freight_charges" => round($freightCharges, 2),
                "unloading_charges" => round($unloadingCharges, 2),
                "installation_scope" => $installationScope,
                "hide_brand_name" => $hideBrandName,
                "supplier_billing_address" => $supplierBillingAddress,
                "supplier_gst" => $supplierGst,
                "gst_amount" => round($gstAmt, 2),
                "sub_total" => round($lineSubTotal, 2),
                "total_amount" => round($totalAmt, 2),
                "product_description" => $product_description[$key] ?? '',
                "roundoff" => $roundoff[$key] ?? 0,
                "files" => $filename,
                "drawing_attachment" => $drawingAttachment,
                "quote_attachments" => $quoteAttachmentsJson,
                "terms_condition_id" => $termsConditionId,
                "lead_time" => $leadTime,
                "payment_terms" => $paymentTerms,
                "warranty_terms" => $warrantyTerms,
                "special_instructions" => $specialInstructions,
                "shipping_type" => $shippingType,
                "shipping_address" => $shippingAddress,
                "shipping_gst" => $shippingGst,
            ]);
        }

        // If created against an Order (ORF), mark PO released
        if ($orfId) {
            $orf = Orf::find($orfId);
            if ($orf) {
                $orf->po_status = 'released';
                $orf->supplier_po_no = $newPONumber;
                $orf->po_released_at = now();
                $orf->save();
            }
        }

        return redirect()->route('purchase-order-details-page')->with('success', 'Purchase order created successfully');
    }

    public function getCustomerByGst(Request $request)
    {
        $gst = strtoupper(trim($request->gst_no ?? ''));
        $customerId = $request->customer_id ?? null;

        $customer = null;
        if ($customerId) {
            $customer = Customer::where('soft_d', 0)->find($customerId);
        } elseif ($gst) {
            $customer = Customer::where('soft_d', 0)
                ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gst])
                ->first();
            if (!$customer) {
                $lead = Lead::where('soft_delete', 0)
                    ->whereRaw('UPPER(TRIM(gst)) = ?', [$gst])
                    ->first();
                if ($lead) {
                    return response()->json([
                        'status' => 'success',
                        'customer' => [
                            'name' => $lead->LeadName,
                            'gst' => $lead->gst,
                            'address' => $lead->billaddress ?: $lead->communicate_address,
                            'postalcode' => $lead->postalcode,
                        ]
                    ]);
                }
            }
        }

        if ($customer) {
            $fullAddress = trim($customer->address ?: $customer->communication_address);
            if ($customer->city_name && !str_contains($fullAddress, $customer->city_name)) {
                $fullAddress .= ', ' . $customer->city_name;
            }
            if ($customer->state && !str_contains($fullAddress, $customer->state)) {
                $fullAddress .= ', ' . $customer->state;
            }
            if ($customer->postalcode && !str_contains($fullAddress, $customer->postalcode)) {
                $fullAddress .= ' - ' . $customer->postalcode;
            }

            return response()->json([
                'status' => 'success',
                'customer' => [
                    'name' => $customer->customername,
                    'gst' => $customer->gstno,
                    'address' => $fullAddress,
                    'postalcode' => $customer->postalcode,
                ]
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'No customer record found for this GST Number'
        ], 404);
    }

    public function getVendorBranches(Request $request)
    {
        $vendorId = $request->vendor_id;
        $vendor = Vendor::with(['stateData', 'cityData'])->find($vendorId);
        if (!$vendor) {
            return response()->json(['status' => 'error', 'branches' => []], 404);
        }

        $branches = [];

        // 1. Primary registered branch for this vendor
        $city1 = $vendor->cityData?->city_name;
        $state1 = $vendor->stateData?->state;
        $fullAddr1 = trim($vendor->address);
        if ($city1 && !str_contains($fullAddr1, $city1)) $fullAddr1 .= ', ' . $city1;
        if ($state1 && !str_contains($fullAddr1, $state1)) $fullAddr1 .= ', ' . $state1;
        if ($vendor->postal_code && !str_contains($fullAddr1, (string)$vendor->postal_code)) $fullAddr1 .= ' - ' . $vendor->postal_code;

        $branches[] = [
            'vendor_id' => $vendor->id,
            'branch_name' => 'Main / Registered Branch (' . ($city1 ?: 'Primary') . ')',
            'gst' => $vendor->gst_number,
            'address' => $fullAddr1,
            'label' => 'Main Unit - GST: ' . ($vendor->gst_number ?: 'Not specified') . ($city1 ? ' (' . $city1 . ')' : '')
        ];

        // 2. Look for other vendor records sharing similar company name
        $cleanName = trim(preg_replace('/(pvt|ltd|limited|private)\.?/i', '', $vendor->company_name));
        if (strlen($cleanName) > 3) {
            $otherVendors = Vendor::with(['stateData', 'cityData'])
                ->where('id', '!=', $vendor->id)
                ->where('company_name', 'LIKE', '%' . $cleanName . '%')
                ->get();

            foreach ($otherVendors as $ov) {
                $cCity = $ov->cityData?->city_name;
                $cState = $ov->stateData?->state;
                $cAddr = trim($ov->address);
                if ($cCity && !str_contains($cAddr, $cCity)) $cAddr .= ', ' . $cCity;
                if ($cState && !str_contains($cAddr, $cState)) $cAddr .= ', ' . $cState;
                if ($ov->postal_code && !str_contains($cAddr, (string)$ov->postal_code)) $cAddr .= ' - ' . $ov->postal_code;

                $branches[] = [
                    'vendor_id' => $ov->id,
                    'branch_name' => $ov->company_name . ($cCity ? ' - ' . $cCity : ''),
                    'gst' => $ov->gst_number,
                    'address' => $cAddr,
                    'label' => ($cCity ? $cCity . ' Branch' : 'Alternate Branch') . ' - GST: ' . ($ov->gst_number ?: 'N/A')
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'branches' => $branches
        ]);
    }

    public function downloadMergedPo($poNo)
    {
        $poDetails = PurchaseOrder::with([
            'allVendorDetails.stateData',
            'allVendorDetails.cityData',
            'productDetails.specifications',
            'productDetails.category',
            'categoryDetails',
            'termsConditionDetails',
            'orf.lead'
        ])->where('po_no', $poNo)->get();

        if ($poDetails->isEmpty()) {
            abort(404, 'Purchase Order not found');
        }

        $firstPo = $poDetails[0];
        $vendor = $firstPo->allVendorDetails;

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => storage_path('app/temp')
        ]);

        // 1. Purchase Order (PO)
        $poHtml = view('pages.vendor.purchase_order.pdf.po_main_pdf', compact('poDetails', 'firstPo', 'vendor'))->render();
        $mpdf->WriteHTML($poHtml);

        // 2. Technical Specifications
        $specsHtml = view('pages.vendor.purchase_order.pdf.po_specs_pdf', compact('poDetails', 'firstPo'))->render();
        $mpdf->AddPage();
        $mpdf->WriteHTML($specsHtml);

        // 3. Approved Drawing
        $drawingFile = $firstPo->drawing_attachment ?: ($firstPo->orf?->sigdraw ?? null);
        if ($drawingFile) {
            $drawingPath = public_path('uploads/po_files/' . $drawingFile);
            if (!file_exists($drawingPath)) {
                $drawingPath = public_path('assets/images/' . ltrim(str_replace('assets/images/', '', $drawingFile), '/'));
            }
            if (file_exists($drawingPath)) {
                $ext = strtolower(pathinfo($drawingPath, PATHINFO_EXTENSION));
                if ($ext === 'pdf') {
                    try {
                        $pageCount = $mpdf->setSourceFile($drawingPath);
                        for ($p = 1; $p <= $pageCount; $p++) {
                            $mpdf->AddPage();
                            $tpl = $mpdf->importPage($p);
                            $mpdf->useTemplate($tpl);
                        }
                    } catch (\Exception $e) {
                        Log::error("Drawing PDF merge error: " . $e->getMessage());
                    }
                } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                    $mpdf->AddPage();
                    $imgHtml = '<div style="text-align:center; padding-top:10px;">
                        <h3 style="color:#1b5583; margin-bottom:5px; text-transform:uppercase;">APPROVED DRAWING</h3>
                        <p style="font-size:8.5pt; color:#64748b; margin-top:0;">PO: ' . e($firstPo->po_no) . ' | Attachment: ' . e($drawingFile) . '</p>
                        <img src="' . $drawingPath . '" style="max-width:180mm; max-height:235mm; object-fit:contain;" />
                    </div>';
                    $mpdf->WriteHTML($imgHtml);
                }
            }
        }

        // 4. Supplier Quote(s)
        $quoteFiles = [];
        if (!empty($firstPo->quote_attachments)) {
            $decoded = json_decode($firstPo->quote_attachments, true);
            if (is_array($decoded)) {
                $quoteFiles = $decoded;
            }
        }
        if (empty($quoteFiles) && !empty($firstPo->files)) {
            $quoteFiles = [$firstPo->files];
        }

        foreach ($quoteFiles as $qFile) {
            $qPath = public_path('uploads/po_files/' . $qFile);
            if (file_exists($qPath)) {
                $ext = strtolower(pathinfo($qPath, PATHINFO_EXTENSION));
                if ($ext === 'pdf') {
                    try {
                        $pageCount = $mpdf->setSourceFile($qPath);
                        for ($p = 1; $p <= $pageCount; $p++) {
                            $mpdf->AddPage();
                            $tpl = $mpdf->importPage($p);
                            $mpdf->useTemplate($tpl);
                        }
                    } catch (\Exception $e) {
                        Log::error("Quote PDF merge error: " . $e->getMessage());
                    }
                } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                    $mpdf->AddPage();
                    $imgHtml = '<div style="text-align:center; padding-top:10px;">
                        <h3 style="color:#1b5583; margin-bottom:5px; text-transform:uppercase;">SUPPLIER QUOTATION</h3>
                        <p style="font-size:8.5pt; color:#64748b; margin-top:0;">PO: ' . e($firstPo->po_no) . ' | Reference: ' . e($qFile) . '</p>
                        <img src="' . $qPath . '" style="max-width:180mm; max-height:235mm; object-fit:contain;" />
                    </div>';
                    $mpdf->WriteHTML($imgHtml);
                }
            }
        }

        $cleanPoNo = str_replace(['/', '\\'], '_', $firstPo->po_no);
        $downloadName = 'Purchase_Order_' . $cleanPoNo . '_Complete_Package.pdf';

        return response($mpdf->Output($downloadName, 'I'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $downloadName . '"');
    }

    public function poEditDetails($poNo)
    {
        $poDetails = PurchaseOrder::with(['categoryDetails', 'productDetails.specifications', 'vendorDetails', 'termsConditionDetails', 'productStock'])->where('po_no', $poNo)->get();
        if ($poDetails->isEmpty()) {
            abort(404, 'Purchase Order not found');
        }
        $allVendors = Vendor::with(['stateData', 'cityData'])->where('vendor_status', 'active')->orderBy('company_name')->get();
        $allCustomers = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->where('gstno', '!=', '')
            ->select('id', 'customername', 'gstno', 'address', 'city', 'state', 'postalcode')
            ->orderBy('customername')
            ->get();
        $firstPo = $poDetails->first();
        return view('pages.vendor.purchase_order.editPurchaseOrder', compact('poDetails', 'allVendors', 'allCustomers', 'firstPo'));
    }

    public function editPo(Request $request)
    {
        $poId = $request->po_id;
        $poNumber = $request->po_number;
        $catId = $request->catId;
        $proId = $request->proId;
        $vendorId = $request->vendorId;
        $primaryVendorId = $request->primary_vendor_id;
        $existQty = $request->existQty;
        $unit_price = $request->unit_price;
        $qty = $request->qty;
        $product_description = $request->pro_des;
        $roundoff = $request->roundoff;
        $termsConditionId = $request->terms_condition_id;

        $leadTime = $request->lead_time ?: '4 Weeks';
        $paymentTerms = $request->payment_terms ?: '50% Advance & 50% Against Proforma';
        $warrantyTerms = $request->warranty_terms ?: '12 Months Standard Manufacturer Warranty';
        $installationScope = $request->installation_scope ?: 'In Supplier Scope';
        $specialInstructions = $request->special_instructions;
        $hideBrandName = $request->boolean('hide_brand_name') ? 1 : 0;

        $discountPercentage = max(0, min(100, (float)($request->discount_percentage ?? 0)));
        $freightCharges = max(0, (float)($request->freight_charges ?? 0));
        $unloadingCharges = max(0, (float)($request->unloading_charges ?? 0));

        $supplierBillingAddress = $request->supplier_billing_address;
        $supplierGst = $request->supplier_gst;
        $shippingType = $request->shipping_type ?? 'warehouse';
        $shippingAddress = $request->shipping_address;
        $shippingGst = $request->shipping_gst;

        $fileAttach = $request->file("po_attachment");
        $existingFile = $request->input("existing_file_name");
        $filename = null;

        if ($fileAttach) {
            $filename = time() . '_po_' . $fileAttach->getClientOriginalName();
            $fileAttach->move(public_path('uploads/po_files'), $filename);
        } else {
            $filename = $existingFile;
        }

        $drawingAttachment = $request->input('existing_drawing_attachment');
        if ($request->hasFile('drawing_attachment')) {
            $drawingFile = $request->file('drawing_attachment');
            $drawingAttachment = time() . '_drawing_' . $drawingFile->getClientOriginalName();
            $drawingFile->move(public_path('uploads/po_files'), $drawingAttachment);
        }

        $quoteAttachments = [];
        if ($request->filled('existing_quote_attachments')) {
            $quoteAttachments = json_decode($request->input('existing_quote_attachments'), true) ?: [];
        }
        if ($request->hasFile('quote_attachments')) {
            foreach ($request->file('quote_attachments') as $qFile) {
                $qName = time() . '_quote_' . $qFile->getClientOriginalName();
                $qFile->move(public_path('uploads/po_files'), $qName);
                $quoteAttachments[] = $qName;
            }
        }
        $quoteAttachmentsJson = !empty($quoteAttachments) ? json_encode($quoteAttachments) : null;

        foreach ($catId as $key => $value) {
            $poData = PurchaseOrder::find($poId[$key] ?? 0);
            $product = Product::findOrFail($proId[$key]);

            $purchaseUnitPrice = (float)$unit_price[$key];
            $productQty = (int)$qty[$key];
            $sub_total = $productQty * $purchaseUnitPrice;
            $lineDiscount = ($sub_total * $discountPercentage) / 100;
            $lineTaxable = $sub_total - $lineDiscount;

            $gstAmt = ($lineTaxable / 100) * (int)$product->gst;
            $totalAmt = $lineTaxable + $gstAmt;

            $lineVendorId = !empty($vendorId[$key]) ? $vendorId[$key] : $primaryVendorId;

            if ($poData) {
                $poData->cat_id = $value;
                $poData->product_id = $proId[$key];
                $poData->vendor_id = $lineVendorId;
                $poData->product_qty = $productQty;
                $poData->unit_price = $purchaseUnitPrice;
                $poData->product_total_price = round($sub_total, 2);
                $poData->discount_percentage = $discountPercentage;
                $poData->discount_amount = round($lineDiscount, 2);
                $poData->freight_charges = round($freightCharges, 2);
                $poData->unloading_charges = round($unloadingCharges, 2);
                $poData->installation_scope = $installationScope;
                $poData->hide_brand_name = $hideBrandName;
                $poData->supplier_billing_address = $supplierBillingAddress;
                $poData->supplier_gst = $supplierGst;
                $poData->shipping_type = $shippingType;
                $poData->shipping_address = $shippingAddress;
                $poData->shipping_gst = $shippingGst;
                $poData->lead_time = $leadTime;
                $poData->payment_terms = $paymentTerms;
                $poData->warranty_terms = $warrantyTerms;
                $poData->special_instructions = $specialInstructions;
                $poData->gst_amount = round($gstAmt, 2);
                $poData->sub_total = round($sub_total, 2);
                $poData->total_amount = round($totalAmt, 2);
                $poData->product_description = $product_description[$key] ?? '';
                $poData->roundoff = $roundoff[$key] ?? 0;
                $poData->files = $filename;
                $poData->drawing_attachment = $drawingAttachment;
                $poData->quote_attachments = $quoteAttachmentsJson;
                $poData->terms_condition_id = $termsConditionId;
                $poData->update();
            } else {
                $poData = new PurchaseOrder;
                $poData->po_no = $poNumber[0];
                $poData->cat_id = $value;
                $poData->product_id = $proId[$key];
                $poData->vendor_id = $lineVendorId;
                $poData->product_qty = $productQty;
                $poData->unit_price = $purchaseUnitPrice;
                $poData->product_total_price = round($sub_total, 2);
                $poData->discount_percentage = $discountPercentage;
                $poData->discount_amount = round($lineDiscount, 2);
                $poData->freight_charges = round($freightCharges, 2);
                $poData->unloading_charges = round($unloadingCharges, 2);
                $poData->installation_scope = $installationScope;
                $poData->hide_brand_name = $hideBrandName;
                $poData->supplier_billing_address = $supplierBillingAddress;
                $poData->supplier_gst = $supplierGst;
                $poData->shipping_type = $shippingType;
                $poData->shipping_address = $shippingAddress;
                $poData->shipping_gst = $shippingGst;
                $poData->lead_time = $leadTime;
                $poData->payment_terms = $paymentTerms;
                $poData->warranty_terms = $warrantyTerms;
                $poData->special_instructions = $specialInstructions;
                $poData->gst_amount = round($gstAmt, 2);
                $poData->sub_total = round($sub_total, 2);
                $poData->total_amount = round($totalAmt, 2);
                $poData->product_description = $product_description[$key] ?? '';
                $poData->roundoff = $roundoff[$key] ?? 0;
                $poData->files = $filename;
                $poData->drawing_attachment = $drawingAttachment;
                $poData->quote_attachments = $quoteAttachmentsJson;
                $poData->terms_condition_id = $termsConditionId;
                $poData->save();
            }
        }

        return redirect()->route('purchase-order-details-page')->with('success', 'Purchase order updated successfully');
    }

    // get po details

    public function getPoDetails(Request $request)
    {
        $poNo = $request->poNo;
        $poDetails = PurchaseOrder::with(['categoryDetails', 'vendorDetails'])->where("po_no", $poNo)->get();
        if ($poDetails) {
            return response()->json(['status' => 'success', 'poDetails' => $poDetails], 200);
        } else {
            return response()->json(['status' => "error"], 500);
        }
    }

    public function getPoProDetails(Request $request)
    {
        $poNo = $request->poNo;
        $proId = $request->proId;
        $peDetails = PurchaseEntry::with('productDetails')->where("po_no", $poNo)->where('product_id', $proId)->first();
        if ($peDetails) {
            return response()->json(['status' => 'success', 'peDetails' => $peDetails], 200);
        } else {
            $poDetails = PurchaseOrder::with('productDetails')->where("po_no", $poNo)->where('product_id', $proId)->first();
            if ($poDetails) {
                return response()->json(['status' => 'success', 'poDetails' => $poDetails], 200);
            } else {
                return response()->json(['status' => "error"], 500);
            }
        }

    }

    public function deletePO($poId)
    {
        $deletePo = PurchaseOrder::find($poId)->delete();
        if ($deletePo) {
            return back()->with('delete-success', 'Purchase order deleted successfully');
        } else {
            return back()->with('delete-error', 'Something went wrong');
        }
    }

    public function orderPoTracking(Request $request)
    {
        $statusFilter = $request->get('status', 'all');

        $query = Orf::with(['lead', 'purchaseOrders.allVendorDetails', 'purchaseOrders.productDetails', 'quotation'])
            ->where('approval_status', 1)
            ->where('cs_status', 1)
            ->orderBy('id', 'desc');

        if ($statusFilter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('po_status')->orWhere('po_status', 'pending');
            });
        } elseif ($statusFilter === 'released') {
            $query->where('po_status', 'released');
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            $order->quotationItems = QuotationProduct::where('quotationno', $order->quono)->get();
            $order->customerTotal = $order->quotationItems->sum('grandtotal') ?: $order->quotationItems->sum('subtotal');
        }

        $allOrders = Orf::where('approval_status', 1)->where('cs_status', 1)->get();
        $totalApproved = $allOrders->count();
        $totalReleased = $allOrders->where('po_status', 'released')->count();
        $totalPending = $totalApproved - $totalReleased;

        $totalMargin = PurchaseOrder::whereNotNull('orf_id')->sum('price_variance');
        $totalProcurement = PurchaseOrder::whereNotNull('orf_id')->sum('total_amount');

        return view('pages.vendor.purchase_order.orderPoTracking', compact(
            'orders',
            'statusFilter',
            'totalApproved',
            'totalReleased',
            'totalPending',
            'totalMargin',
            'totalProcurement'
        ));
    }
}
