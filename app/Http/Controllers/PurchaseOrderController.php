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
use App\Models\Term;
use App\Models\QuotationInstallationScope;
use App\Support\PurchaseOrderTotals;
use App\Models\PoStatusHistory;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                $orderItems = QuotationProduct::with(['productDetails.productStock', 'productDetails.specifications', 'accessories'])->where('quotationno', $orf->quono)->get();

                if ($orderItems->isEmpty() && $quotation) {
                    $orderItems = QuotationProduct::with(['productDetails.productStock', 'productDetails.specifications', 'accessories'])->where('quotationno', $quotation->quota_id)->get();
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

        $allVendors = Vendor::withTrashed()->with(['stateData', 'cityData'])
            ->where('vendor_status', 'active')
            ->orderBy('company_name')
            ->get();

        $categories = ProductCategory::orderBy('category_name')->get();
        $mercuriisBranches = config('mercuriis_branches.branches', []);
        $paymentTermsMaster = Term::where('termtype', 1)->get();
        $warrantyTermsMaster = Term::where('termtype', 2)->get();
        $installationScopesMaster = QuotationInstallationScope::where('is_active', true)->orderBy('sort_order')->get();

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
            'allVendors',
            'categories',
            'mercuriisBranches',
            'paymentTermsMaster',
            'warrantyTermsMaster',
            'installationScopesMaster'
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
        $canonicalPoNo = rawurldecode((string) $invoiceId);
        $poDetails = PurchaseOrder::with([
            'allVendorDetails',
            'productDetails.specifications',
            'categoryDetails',
            'termsConditionDetails',
            'accessories'
        ])->where('po_no', $canonicalPoNo)->orderBy('id')->get();

        if ($poDetails->isEmpty()) {
            abort(404, 'Purchase Order not found');
        }

        return view('pages.vendor.purchase_order.po_invoice', compact('poDetails'));
    }

    public function savePo(StorePurchaseOrderRequest $request)
    {
        $catId = $request->catId ?? [];
        $proId = $request->proId ?? [];
        $vendorId = $request->vendorId ?? [];
        $unit_price = $request->unit_price ?? [];
        $qty = $request->qty ?? [];
        $gst = $request->gst ?? [];
        $product_description = $request->pro_des ?? [];
        $termsConditionId = $request->terms_condition_id ?: Term::where("whichterm", 2)->value('id');
        $hsn = $request->hsn ?? [];
        $partNo = $request->part_no ?? [];
        $discount = $request->discount ?? [];

        // Mercuriis Issuing Branch
        $mercuriisBranch = $request->mercuriis_branch ?: config('mercuriis_branches.default', 'Chennai');

        // Supplier Billing & Branches
        $supplierBillingAddress = $request->supplier_billing_address;
        $supplierGst = $request->supplier_gst;
        $primaryVendorId = $request->primary_vendor_id ?: ($vendorId[0] ?? null);
        $supplierAddressId = $request->filled('supplier_address_id') ? (int)$request->supplier_address_id : null;

        // Commercial Terms
        $leadTime = ($request->lead_time === 'custom' || $request->lead_time === 'new')
            ? ($request->custom_lead_time ?: '4 Weeks')
            : ($request->lead_time ?: '4 Weeks');

        $paymentTerms = ($request->payment_terms === 'custom' || $request->payment_terms === 'new')
            ? ($request->custom_payment_terms ?: '50% Advance & 50% Against Proforma')
            : ($request->payment_terms ?: '50% Advance & 50% Against Proforma');

        $warrantyTerms = ($request->warranty_terms === 'custom' || $request->warranty_terms === 'new')
            ? ($request->custom_warranty_terms ?: '12 Months Standard Manufacturer Warranty')
            : ($request->warranty_terms ?: '12 Months Standard Manufacturer Warranty');

        $installationScope = ($request->installation_scope === 'custom' || $request->installation_scope === 'new')
            ? ($request->custom_installation_scope ?: 'In Supplier Scope (Included in Unit Rate)')
            : ($request->installation_scope ?: 'In Supplier Scope (Included in Unit Rate)');

        $specialInstructions = $request->special_instructions;
        $hideBrandName = $request->boolean('hide_brand_name') ? 1 : 0;

        $poDate = now()->startOfDay();

        // Advance percentage & due date calculation
        $advancePercentage = (float)($request->advance_percentage ?? 0);
        if ($advancePercentage <= 0 && preg_match('/(\d+)%\s*advance/i', $paymentTerms, $m)) {
            $advancePercentage = (float)$m[1];
        }

        $calculatedDueDate = null;
        if (preg_match('/(\d+)\s*week/i', $leadTime, $lm)) {
            $calculatedDueDate = $poDate->copy()->addWeeks((int)$lm[1])->format('Y-m-d');
        } elseif (preg_match('/(\d+)\s*day/i', $leadTime, $lm)) {
            $calculatedDueDate = $poDate->copy()->addDays((int)$lm[1])->format('Y-m-d');
        } else {
            $calculatedDueDate = $poDate->copy()->addWeeks(4)->format('Y-m-d');
        }

        $advanceStatus = ($advancePercentage > 0) ? 'pending_request' : 'not_required';
        $productionMilestone = ($advancePercentage > 0) ? 'awaiting_advance' : 'po_issued';

        // Charges and Discounts
        $discountPercentage = max(0, min(100, (float)($request->discount_percentage ?? 0)));
        $freightCharges = max(0, (float)($request->freight_charges ?? 0));
        $unloadingCharges = max(0, (float)($request->unloading_charges ?? 0));

        // Shipping Details
        $shippingType = $request->shipping_type ?? 'warehouse';
        $shippingAddress = $request->shipping_address;
        $shippingGst = $request->shipping_gst;
        $customerSiteId = $shippingType === 'customer_site' && $request->filled('customer_site_id')
            ? (int) $request->customer_site_id
            : null;

        $createdUploadPaths = [];
        try {
            // Attachments
            $drawingAttachment = $request->filled('orf_id')
                ? Orf::whereKey($request->orf_id)->value('sigdraw')
                : null;
            if ($request->hasFile('drawing_attachment')) {
                $drawFile = $request->file('drawing_attachment');
                if ($drawFile->isValid()) {
                    $drawingAttachment = uniqid('draw_', true) . '.' . $drawFile->getClientOriginalExtension();
                    $drawFile->move(public_path('uploads/po_files'), $drawingAttachment);
                    $createdUploadPaths[] = public_path('uploads/po_files/' . $drawingAttachment);
                }
            }

            $quoteAttachments = [];
            if ($request->hasFile('quote_attachments')) {
                foreach ($request->file('quote_attachments') as $qFile) {
                    if ($qFile && $qFile->isValid()) {
                        $qFilename = uniqid('quote_', true) . '.' . $qFile->getClientOriginalExtension();
                        $qFile->move(public_path('uploads/po_files'), $qFilename);
                        $createdUploadPaths[] = public_path('uploads/po_files/' . $qFilename);
                        $quoteAttachments[] = $qFilename;
                    }
                }
            }
            $quoteAttachmentsJson = !empty($quoteAttachments) ? json_encode($quoteAttachments) : null;

            // Legacy attachment fallback
            $fileAttach = $request->file("po_attachment");
            $filename = null;
            if (!empty($fileAttach)) {
                $filename = uniqid('po_', true) . '.' . $fileAttach->getClientOriginalExtension();
                $fileAttach->move(public_path('uploads/po_files'), $filename);
                $createdUploadPaths[] = public_path('uploads/po_files/' . $filename);
            }

            $orfId = $request->filled('orf_id') ? $request->orf_id : null;
            $quotationId = $request->filled('quotation_id') ? $request->quotation_id : null;

            return DB::transaction(function () use (
            $request, $catId, $proId, $vendorId, $primaryVendorId, $unit_price, $qty, $gst,
            $product_description, $termsConditionId, $hsn, $partNo, $discount,
            $mercuriisBranch, $supplierBillingAddress, $supplierGst, $supplierAddressId,
            $leadTime, $paymentTerms, $warrantyTerms, $installationScope, $specialInstructions,
            $hideBrandName, $advancePercentage, $calculatedDueDate, $advanceStatus,
            $productionMilestone, $discountPercentage, $freightCharges, $unloadingCharges,
            $shippingType, $shippingAddress, $shippingGst, $customerSiteId, $drawingAttachment,
            $quoteAttachmentsJson, $filename, $orfId, $quotationId
        ) {
            $lastInserted = PurchaseOrder::withTrashed()->lockForUpdate()->orderByDesc('id')->first();
            if ($lastInserted == null) {
                $newPONumber = $this->generatePONumber("MES/PO/26-27/000");
            } else {
                $newPONumber = $this->generatePONumber($lastInserted->po_no);
            }

            // Strictly recompute all line items and totals server-side
            $totals = PurchaseOrderTotals::calculate($unit_price, $qty, $discount, $gst, $discountPercentage);
            $grossSubtotal = $totals['gross_subtotal'];
            $rawNetTotal = $totals['taxable_total'] + $totals['gst_total'] + $freightCharges + $unloadingCharges;
            $netPoTotal = round($rawNetTotal);
            $overallRoundoff = round($netPoTotal - $rawNetTotal, 2);
            $advanceAmount = round(($netPoTotal * $advancePercentage) / 100, 2);

            foreach ($catId as $key => $value) {
                $line = $totals['lines'][$key];
                $purchaseUnitPrice = $line['unit_price'];
                $productQty = $line['quantity'];
                $lineSubTotal = $line['gross'];
                $prodObj = Product::find($proId[$key] ?? 0);

                $lineVendorId = !empty($vendorId[$key]) ? $vendorId[$key] : $primaryVendorId;
                $itemHsn = !empty($hsn[$key]) ? trim($hsn[$key]) : ($prodObj?->hsn ?: ($prodObj?->hsncode ?: '8428'));
                $itemPartNo = !empty($partNo[$key]) ? trim($partNo[$key]) : ($prodObj?->partno ?? '');

                $createdLine = PurchaseOrder::create([
                    "cat_id" => $value,
                    "po_no" => $newPONumber,
                    "po_date" => now()->format('Y-m-d'),
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
                    "discount_percentage" => $line['discount_percentage'],
                    "discount_amount" => $line['discount_amount'],
                    "overall_discount_percentage" => $discountPercentage,
                    "overall_discount_amount" => $line['overall_discount_amount'],
                    "gst_percentage" => $line['gst_percentage'],
                    "taxable_amount" => $line['taxable'],
                    "freight_charges" => round($freightCharges, 2),
                    "unloading_charges" => round($unloadingCharges, 2),
                    "installation_scope" => $installationScope,
                    "hide_brand_name" => $hideBrandName,
                    "supplier_billing_address" => $supplierBillingAddress,
                    "supplier_gst" => $supplierGst,
                    "supplier_address_id" => $supplierAddressId,
                    "gst_amount" => $line['gst_amount'],
                    "sub_total" => round($lineSubTotal, 2),
                    "total_amount" => $line['total_amount'],
                    "product_description" => $product_description[$key] ?? '',
                    "roundoff" => $overallRoundoff,
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
                    "customer_site_id" => $customerSiteId,
                    "mercuriis_branch" => $mercuriisBranch,
                    "hsn_code" => $itemHsn,
                    "part_no" => $itemPartNo,
                    "advance_percentage" => $advancePercentage,
                    "advance_amount" => $advanceAmount,
                    "advance_status" => $advanceStatus,
                    "calculated_due_date" => $calculatedDueDate,
                    "supplier_dispatch_date" => $request->committed_dispatch_date ?: $request->supplier_dispatch_date,
                    "committed_dispatch_date" => $request->committed_dispatch_date ?: $request->supplier_dispatch_date,
                    "actual_dispatch_date" => $request->actual_dispatch_date,
                    "technical_specification" => $request->technical_specification,
                    "additional_file" => $filename,
                    "production_milestone" => $productionMilestone,
                    "lifecycle_status" => 'active',
                ]);
                $this->syncLineAccessories($createdLine, $request, $key);
            }

            // Log status history
            PoStatusHistory::create([
                'po_no' => $newPONumber,
                'from_status' => null,
                'to_status' => $advanceStatus,
                'action' => 'po_created',
                'user_id' => auth()->id(),
                'remarks' => "Purchase Order created with Net Value ₹" . number_format($netPoTotal, 2) . " and {$advancePercentage}% Advance."
            ]);

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

            return redirect()->route('purchase-order-details-page')->with('success', "Purchase Order {$newPONumber} created successfully!");
            });
        } catch (\Throwable $e) {
            foreach ($createdUploadPaths as $path) {
                if (is_file($path) && !@unlink($path)) {
                    \Illuminate\Support\Facades\Log::warning('Failed to clean up a PO upload after a failed save.', ['path' => $path]);
                }
            }
            throw $e;
        }
    }

    private function syncLineAccessories(PurchaseOrder $line, Request $request, int|string $index): void
    {
        $line->accessories()->delete();
        $selected = $request->input("accessories.$index", []);
        $quantities = $request->input("accessory_quantity.$index", []);
        $product = \App\Models\Product::find($line->product_id);
        $catId = $product?->Productcategory ?: (int)($line->cat_id ?? 0);
        foreach ($selected as $accessoryId) {
            $accessory = \App\Models\Accessory::where('status', 'active')->findOrFail($accessoryId);
            $mapped = \App\Models\Accessory::where('status', 'active')
                ->where('id', $accessory->id)
                ->where(function ($q) use ($line, $catId) {
                    $q->where('product_id', $line->product_id);
                    if ($catId) {
                        $q->orWhere('product_category_id', $catId);
                    }
                })->exists();
            if (!$mapped) {
                abort(422, 'An accessory is not available for the selected product.');
            }

            $quantity = max(1, (int) ($quantities[$accessory->id] ?? 1));
            $line->accessories()->create([
                'accessory_id' => $accessory->id,
                'name' => $accessory->name,
                'part_no' => $accessory->part_no,
                'hsn' => $accessory->hsn,
                'unit_price' => $accessory->price,
                'quantity' => $quantity,
                'total' => round((float) $accessory->price * $quantity, 2),
            ]);
        }
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
        $vendor = Vendor::with(['stateData', 'cityData'])
            ->where('vendor_status', 'active')
            ->find($vendorId);
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
            'id' => 'vendor:' . $vendor->id,
            'address_id' => null,
            'branch_name' => $city1 ?: 'Registered Office',
            'gst' => $vendor->gst_number,
            'gst_number' => $vendor->gst_number,
            'address' => $fullAddr1,
            'label' => $fullAddr1,
            'is_primary' => true
        ];

        // 2. Additional Plant / Factory / Branch Addresses for this vendor
        if (\Illuminate\Support\Facades\Schema::hasTable('vendor_addresses')) {
            $plantAddresses = \App\Models\VendorAddress::with(['state', 'city'])->where('vendor_id', $vendor->id)->get();
            foreach ($plantAddresses as $pa) {
                $pCity = $pa->city?->city_name ?: ($pa->city?->city ?: '');
                $pState = $pa->state?->state ?: '';
                $pAddr = trim($pa->address);
                if ($pCity && !str_contains($pAddr, $pCity)) $pAddr .= ', ' . $pCity;
                if ($pState && !str_contains($pAddr, $pState)) $pAddr .= ', ' . $pState;
                if ($pa->postal_code && !str_contains($pAddr, (string)$pa->postal_code)) $pAddr .= ' - ' . $pa->postal_code;

                $branches[] = [
                    'id' => 'addr_' . $pa->id,
                    'address_id' => $pa->id,
                    'branch_name' => $pa->address_type ?: 'Branch Address',
                    'gst' => $pa->gst_number ?: $vendor->gst_number,
                    'gst_number' => $pa->gst_number ?: $vendor->gst_number,
                    'address' => $pAddr,
                    'label' => $pAddr
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'branches' => $branches
        ]);
    }

    public function getSegmentTermsJson($catId, $productId = null)
    {
        $catId = (int) $catId;
        $productId = $productId ? (int) $productId : null;

        $category = ProductCategory::find($catId);
        $product = $productId ? Product::with('specifications')->find($productId) : null;

        // 1. Payment Terms (termtype = 1)
        $paymentTerms = Term::where('termtype', 1)
            ->where(function ($q) use ($catId) {
                if ($catId > 0) {
                    $q->where('product_category_id', $catId)
                      ->orWhereNull('product_category_id');
                }
            })
            ->get();

        $defaultPaymentTerm = null;
        if ($catId > 0) {
            $defaultPaymentTerm = $paymentTerms->firstWhere('product_category_id', $catId)?->content;
        }
        if (!$defaultPaymentTerm) {
            $defaultPaymentTerm = $paymentTerms->first()?->content ?? '50% advance along with purchase order and balance 50% against materials readiness before dispatch.';
        }

        // 2. Warranty Terms (termtype = 2)
        $warrantyTerms = Term::where('termtype', 2)
            ->where(function ($q) use ($catId, $productId) {
                if ($productId) {
                    $q->where('product_id', $productId);
                }
                if ($catId > 0) {
                    $productId ? $q->orWhere('product_category_id', $catId) : $q->where('product_category_id', $catId);
                }
                $q->orWhereNull('product_category_id');
            })
            ->get();

        $defaultWarrantyTerm = null;
        if ($productId) {
            $defaultWarrantyTerm = $warrantyTerms->firstWhere('product_id', $productId)?->content;
        }
        if (!$defaultWarrantyTerm && $catId > 0) {
            $defaultWarrantyTerm = $warrantyTerms->firstWhere('product_category_id', $catId)?->content;
        }
        if (!$defaultWarrantyTerm) {
            $defaultWarrantyTerm = $warrantyTerms->first()?->content ?? '12 Months / 2000 hours from the date of invoice, whichever is earlier.';
        }

        // 3. Lead Time / Dispatch Time
        $leadTime = null;
        if ($product && !empty($product->dispatch_time)) {
            $leadTime = $product->dispatch_time;
        } elseif ($category && !empty($category->dispatch_time)) {
            $leadTime = $category->dispatch_time;
        } else {
            $leadTime = '4 Weeks';
        }

        // 4. Technical Specifications
        $techSpecs = '';
        if ($product) {
            if ($product->specifications && $product->specifications->isNotEmpty()) {
                $specsList = [];
                foreach ($product->specifications as $spec) {
                    $desc = trim($spec->description ?? '');
                    $val = trim($spec->specification ?? '');
                    $unit = trim($spec->unit ?? '');
                    if ($desc || $val) {
                        $specsList[] = ($desc ? $desc . ': ' : '') . $val . ($unit ? ' ' . $unit : '');
                    }
                }
                $techSpecs = implode("\n", $specsList);
            }
            if (empty($techSpecs) && !empty($product->productdes)) {
                $techSpecs = strip_tags($product->productdes);
            }
        }

        return response()->json([
            'status' => 'success',
            'category_id' => $catId,
            'product_id' => $productId,
            'default_payment_term' => $defaultPaymentTerm,
            'payment_terms' => $paymentTerms->pluck('content')->filter()->unique()->values(),
            'default_warranty_term' => $defaultWarrantyTerm,
            'warranty_terms' => $warrantyTerms->pluck('content')->filter()->unique()->values(),
            'lead_time' => $leadTime,
            'technical_specification' => $techSpecs,
        ]);
    }

    public function downloadMergedPo($poNo)
    {
        $cleanPoNo = urldecode($poNo);
        $poDetails = PurchaseOrder::with([
            'allVendorDetails.stateData',
            'allVendorDetails.cityData',
            'productDetails.specifications',
            'productDetails.category',
            'categoryDetails',
            'termsConditionDetails',
            'orf.lead'
        ])->where('po_no', $poNo)
          ->orWhere('po_no', $cleanPoNo)
          ->orWhere('id', $poNo)
          ->get();

        if ($poDetails->isEmpty()) {
            abort(404, 'Purchase Order not found');
        }

        $firstPo = $poDetails->first();
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
        $poDetails = PurchaseOrder::with(['categoryDetails', 'productDetails.specifications', 'vendorDetails', 'termsConditionDetails', 'productStock', 'accessories'])->where('po_no', $poNo)->get();
        if ($poDetails->isEmpty()) {
            abort(404, 'Purchase Order not found');
        }
        $poVendorIds = $poDetails->pluck('vendor_id')->filter()->unique();
        $allVendors = Vendor::with(['stateData', 'cityData'])
            ->where(function ($query) use ($poVendorIds) {
                $query->where('vendor_status', 'active')->orWhereIn('id', $poVendorIds);
            })
            ->orderBy('company_name')
            ->get();
        $allCustomers = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->where('gstno', '!=', '')
            ->select('id', 'customername', 'gstno', 'address', 'city', 'state', 'postalcode')
            ->orderBy('customername')
            ->get();
        $categories = ProductCategory::orderBy('category_name')->get();
        $mercuriisBranches = config('mercuriis_branches.branches', []);
        $paymentTermsMaster = Term::where('termtype', 1)->get();
        $warrantyTermsMaster = Term::where('termtype', 2)->get();
        $installationScopesMaster = QuotationInstallationScope::where('is_active', true)->orderBy('sort_order')->get();
        $firstPo = $poDetails->first();
        $selectedCustomerId = $firstPo->customer_site_id
            ? \App\Models\CustomerAddress::whereKey($firstPo->customer_site_id)->value('customer_id')
            : null;
        return view('pages.vendor.purchase_order.editPurchaseOrder', compact(
            'poDetails', 'allVendors', 'allCustomers', 'firstPo', 'categories', 'mercuriisBranches', 'selectedCustomerId',
            'paymentTermsMaster', 'warrantyTermsMaster', 'installationScopesMaster'
        ));
    }

    public function editPo(UpdatePurchaseOrderRequest $request)
    {
        $poNumbers = array_values(array_unique($request->input('po_number', [])));
        if (count($poNumbers) !== 1) {
            throw ValidationException::withMessages(['po_number' => 'All edited lines must belong to one purchase order.']);
        }
        $targetPoNo = $poNumbers[0];
        $existingLines = PurchaseOrder::where('po_no', $targetPoNo)->orderBy('id')->get();
        $firstPoRecord = $existingLines->first();
        if (!$firstPoRecord) {
            abort(404, 'Purchase Order not found');
        }

        $catId = $request->input('catId', []);
        $proId = $request->input('proId', []);
        $unitPrices = $request->input('unit_price', []);
        $quantities = $request->input('qty', []);
        $gstRates = $request->input('gst', []);
        $lineDiscounts = $request->input('discount', []);
        $globalDiscount = (float) $request->input('discount_percentage', 0);
        $freight = (float) $request->input('freight_charges', 0);
        $unloading = (float) $request->input('unloading_charges', 0);
        $paymentTerms = ($request->input('payment_terms') === 'custom' || $request->input('payment_terms') === 'new')
            ? ($request->input('custom_payment_terms') ?: '50% Advance & 50% Against Proforma')
            : ($request->input('payment_terms') ?: '50% Advance & 50% Against Proforma');
        $leadTime = ($request->input('lead_time') === 'custom' || $request->input('lead_time') === 'new')
            ? ($request->input('custom_lead_time') ?: '4 Weeks')
            : ($request->input('lead_time') ?: '4 Weeks');
        $warrantyTerms = ($request->input('warranty_terms') === 'custom' || $request->input('warranty_terms') === 'new')
            ? ($request->input('custom_warranty_terms') ?: '12 Months Standard Manufacturer Warranty')
            : $request->input('warranty_terms', '12 Months Standard Manufacturer Warranty');
        $installationScope = ($request->input('installation_scope') === 'custom' || $request->input('installation_scope') === 'new')
            ? ($request->input('custom_installation_scope') ?: 'In Supplier Scope (Included in Unit Rate)')
            : $request->input('installation_scope', 'In Supplier Scope (Included in Unit Rate)');
        $advancePercentage = (float) $request->input('advance_percentage', 0);
        if ($advancePercentage <= 0 && preg_match('/(\d+)%\s*advance/i', $paymentTerms, $m)) {
            $advancePercentage = (float) $m[1];
        }
        $totals = PurchaseOrderTotals::calculate($unitPrices, $quantities, $lineDiscounts, $gstRates, $globalDiscount);
        $rawTotal = $totals['taxable_total'] + $totals['gst_total'] + $freight + $unloading;
        $netTotal = round($rawTotal);
        $roundoff = round($netTotal - $rawTotal, 2);
        $advanceAmount = round($netTotal * $advancePercentage / 100, 2);

        $postedIds = array_values(array_filter($request->input('po_id', $request->input('poId', []))));
        $invalidIds = PurchaseOrder::whereIn('id', $postedIds)->where('po_no', '!=', $targetPoNo)->exists();
        $invalidDeletedIds = PurchaseOrder::whereIn('id', $request->input('deleted_po_ids', []))
            ->where('po_no', '!=', $targetPoNo)->exists();
        if ($invalidIds || $invalidDeletedIds) {
            throw ValidationException::withMessages(['po_id' => 'Only line items belonging to this purchase order can be changed.']);
        }

        $lineSignatures = [];
        foreach ($catId as $index => $categoryId) {
            $accessories = [];
            foreach ($request->input("accessories.$index", []) as $accessoryId) {
                $accessories[] = (int) $accessoryId . ':' . (int) $request->input("accessory_quantity.$index.$accessoryId", 1);
            }
            sort($accessories);
            $lineSignatures[] = implode('|', [
                (int) $categoryId,
                (int) $proId[$index],
                (int) ($request->input("vendorId.$index") ?: $request->primary_vendor_id),
                (int) $quantities[$index],
                number_format((float) $unitPrices[$index], 2, '.', ''),
                number_format((float) ($gstRates[$index] ?? 0), 2, '.', ''),
                number_format((float) ($lineDiscounts[$index] ?? 0), 2, '.', ''),
                implode(',', $accessories),
            ]);
        }
        sort($lineSignatures);
        $oldLineSignatures = $existingLines->map(function ($line) {
            $accessories = $line->accessories()->orderBy('accessory_id')->get()
                ->map(fn ($accessory) => (int) $accessory->accessory_id . ':' . (int) $accessory->quantity)
                ->all();
            return implode('|', [
                (int) $line->cat_id,
                (int) $line->product_id,
                (int) $line->vendor_id,
                (int) $line->product_qty,
                number_format((float) $line->unit_price, 2, '.', ''),
                number_format((float) ($line->gst_percentage ?? $line->productDetails?->gst ?? 18), 2, '.', ''),
                number_format((float) ($line->discount_percentage ?? 0), 2, '.', ''),
                implode(',', $accessories),
            ]);
        })->sort()->values()->all();
        $financialChanged = $lineSignatures !== $oldLineSignatures
            || round($globalDiscount, 2) !== round((float) ($firstPoRecord->overall_discount_percentage ?? 0), 2)
            || round($freight, 2) !== round((float) ($firstPoRecord->freight_charges ?? 0), 2)
            || round($unloading, 2) !== round((float) ($firstPoRecord->unloading_charges ?? 0), 2)
            || round($advancePercentage, 2) !== round((float) ($firstPoRecord->advance_percentage ?? 0), 2);

        if ($firstPoRecord->advance_status === 'advance_released' && $financialChanged) {
            throw ValidationException::withMessages([
                'po_number' => 'Financial edits after advance release require a separately approved amendment. The released PO amount and UTR were not changed.',
            ]);
        }

        $newAdvanceStatus = $firstPoRecord->advance_status;
        $resetApproval = $financialChanged && in_array($newAdvanceStatus, [
            'pending_sir_approval',
            'approved_by_sir',
            'rejected_by_sir',
        ], true);
        if ($resetApproval) {
            $newAdvanceStatus = $advancePercentage > 0 ? 'pending_request' : 'not_required';
        } elseif ($financialChanged && in_array($newAdvanceStatus, [null, '', 'not_required'], true)) {
            $newAdvanceStatus = $advancePercentage > 0 ? 'pending_request' : 'not_required';
        }

        $createdUploadPaths = [];
        try {
            return DB::transaction(function () use (
            $request, $targetPoNo, $firstPoRecord, $catId, $proId, $unitPrices, $quantities,
            $gstRates, $lineDiscounts, $totals, $globalDiscount, $freight, $unloading, $netTotal,
            $roundoff, $advancePercentage, $advanceAmount, $newAdvanceStatus, $resetApproval,
            $postedIds, $paymentTerms, $leadTime, $warrantyTerms, $installationScope, &$createdUploadPaths
        ) {
            $poIds = $request->input('po_id', $request->input('poId', []));
            $activeIds = array_values(array_filter($poIds));
            PurchaseOrder::where('po_no', $targetPoNo)->whereNotIn('id', $activeIds)->delete();

            $poDate = \Illuminate\Support\Carbon::parse($firstPoRecord->po_date ?: $firstPoRecord->created_at ?: now())->startOfDay();
            $calculatedDueDate = preg_match('/(\d+)\s*week/i', $leadTime, $lm)
                ? $poDate->copy()->addWeeks((int) $lm[1])->format('Y-m-d')
                : (preg_match('/(\d+)\s*day/i', $leadTime, $lm) ? $poDate->copy()->addDays((int) $lm[1])->format('Y-m-d') : $poDate->copy()->addWeeks(4)->format('Y-m-d'));
            $newDispatchDate = $request->input('committed_dispatch_date', $request->input('supplier_dispatch_date', $firstPoRecord->supplier_dispatch_date));
            $dispatchDateChanged = $newDispatchDate && ($newDispatchDate != $firstPoRecord->supplier_dispatch_date);
            $supplierAddressId = $request->filled('supplier_address_id')
                ? (int) $request->supplier_address_id
                : ($request->exists('supplier_address_id') ? null : $firstPoRecord->supplier_address_id);
            $customerSiteId = $request->shipping_type === 'customer_site'
                ? ($request->filled('customer_site_id')
                    ? (int) $request->customer_site_id
                    : ($request->exists('customer_site_id') ? null : $firstPoRecord->customer_site_id))
                : null;
            $filename = $firstPoRecord->files;
            if ($request->hasFile('po_attachment')) {
                $file = $request->file('po_attachment');
                $filename = uniqid('po_', true) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/po_files'), $filename);
                $createdUploadPaths[] = public_path('uploads/po_files/' . $filename);
            }
            $drawing = $firstPoRecord->drawing_attachment;
            if ($request->hasFile('drawing_attachment')) {
                $file = $request->file('drawing_attachment');
                $drawing = uniqid('drawing_', true) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/po_files'), $drawing);
                $createdUploadPaths[] = public_path('uploads/po_files/' . $drawing);
            }
            $quoteFiles = json_decode($firstPoRecord->quote_attachments ?: '[]', true) ?: [];
            foreach ($request->file('quote_attachments', []) as $file) {
                if ($file && $file->isValid()) {
                    $name = uniqid('quote_', true) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/po_files'), $name);
                    $createdUploadPaths[] = public_path('uploads/po_files/' . $name);
                    $quoteFiles[] = $name;
                }
            }
            $quoteJson = $quoteFiles ? json_encode($quoteFiles) : null;

            foreach ($catId as $index => $categoryId) {
                $line = $totals['lines'][$index];
                $lineId = $poIds[$index] ?? null;
                $poData = $lineId
                    ? PurchaseOrder::where('po_no', $targetPoNo)->whereKey($lineId)->firstOrFail()
                    : new PurchaseOrder();
                $product = Product::findOrFail($proId[$index]);
                $poData->fill([
                    'po_no' => $targetPoNo,
                    'po_date' => $firstPoRecord->po_date ?: $firstPoRecord->created_at?->format('Y-m-d'),
                    'orf_id' => $firstPoRecord->orf_id,
                    'quotation_id' => $firstPoRecord->quotation_id,
                    'cat_id' => $categoryId,
                    'product_id' => $proId[$index],
                    'vendor_id' => $request->input("vendorId.$index") ?: $request->primary_vendor_id,
                    'product_qty' => $quantities[$index],
                    'unit_price' => $unitPrices[$index],
                    'product_total_price' => $line['gross'],
                    'discount_percentage' => $line['discount_percentage'],
                    'discount_amount' => $line['discount_amount'],
                    'overall_discount_percentage' => $globalDiscount,
                    'overall_discount_amount' => $line['overall_discount_amount'],
                    'gst_percentage' => $line['gst_percentage'],
                    'taxable_amount' => $line['taxable'],
                    'gst_amount' => $line['gst_amount'],
                    'sub_total' => $line['gross'],
                    'total_amount' => $line['total_amount'],
                    'freight_charges' => $freight,
                    'unloading_charges' => $unloading,
                    'supplier_billing_address' => $request->supplier_billing_address,
                    'supplier_gst' => strtoupper($request->supplier_gst),
                    'supplier_address_id' => $supplierAddressId,
                    'shipping_type' => $request->shipping_type,
                    'shipping_address' => $request->shipping_address,
                    'shipping_gst' => strtoupper($request->shipping_gst),
                    'customer_site_id' => $customerSiteId,
                    'lead_time' => $leadTime,
                    'payment_terms' => $paymentTerms,
                    'warranty_terms' => $warrantyTerms,
                    'installation_scope' => $installationScope,
                    'special_instructions' => $request->special_instructions,
                    'product_description' => $request->input("pro_des.$index", ''),
                    'terms_condition_id' => $request->terms_condition_id ?: $firstPoRecord->terms_condition_id,
                    'mercuriis_branch' => $request->mercuriis_branch ?: config('mercuriis_branches.default', 'Chennai'),
                    'hsn_code' => $request->input("hsn.$index", $product->hsn ?: ($product->hsncode ?: '8428')),
                    'part_no' => $request->input("part_no.$index", $product->partno),
                    'roundoff' => $roundoff,
                    'files' => $filename,
                    'drawing_attachment' => $drawing,
                    'quote_attachments' => $quoteJson,
                    'advance_percentage' => $advancePercentage,
                    'advance_amount' => $advanceAmount,
                    'advance_status' => $newAdvanceStatus,
                    'production_milestone' => $firstPoRecord->production_milestone,
                    'calculated_due_date' => $calculatedDueDate,
                    'supplier_dispatch_date' => $request->input('committed_dispatch_date', $request->input('supplier_dispatch_date', $firstPoRecord->supplier_dispatch_date)),
                    'committed_dispatch_date' => $request->input('committed_dispatch_date', $request->input('supplier_dispatch_date', $firstPoRecord->committed_dispatch_date)),
                    'actual_dispatch_date' => $request->input('actual_dispatch_date', $firstPoRecord->actual_dispatch_date),
                    'technical_specification' => $request->input('technical_specification', $firstPoRecord->technical_specification),
                    'hide_brand_name' => $request->boolean('hide_brand_name') ? 1 : 0,
                    'lifecycle_status' => $firstPoRecord->lifecycle_status ?: 'active',
                    'dispatch_reminder_sent_at' => $dispatchDateChanged ? null : $firstPoRecord->dispatch_reminder_sent_at,
                ]);
                if ($resetApproval) {
                    $poData->sir_approved_by = null;
                    $poData->sir_approved_at = null;
                    $poData->sir_remark = null;
                } else {
                    $poData->sir_approved_by = $firstPoRecord->sir_approved_by;
                    $poData->sir_approved_at = $firstPoRecord->sir_approved_at;
                    $poData->sir_remark = $firstPoRecord->sir_remark;
                }
                $poData->finance_released_by = $firstPoRecord->finance_released_by;
                $poData->finance_released_at = $firstPoRecord->finance_released_at;
                $poData->finance_utr_no = $firstPoRecord->finance_utr_no;
                $poData->finance_payment_mode = $firstPoRecord->finance_payment_mode;
                $poData->finance_remark = $firstPoRecord->finance_remark;
                $poData->save();
                $this->syncLineAccessories($poData, $request, $index);
            }

            PoStatusHistory::create([
                'po_no' => $targetPoNo,
                'from_status' => $firstPoRecord->advance_status,
                'to_status' => $newAdvanceStatus,
                'action' => $resetApproval ? 'financial_amendment_requires_reapproval' : 'po_updated',
                'user_id' => auth()->id(),
                'remarks' => "Purchase Order {$targetPoNo} updated with Net Value ₹" . number_format($netTotal, 2) . '.',
            ]);

            return redirect()->route('purchase-order-details-page')->with('success', "Purchase order {$targetPoNo} updated successfully!");
            });
        } catch (\Throwable $e) {
            foreach ($createdUploadPaths as $path) {
                if (is_file($path) && !@unlink($path)) {
                    \Illuminate\Support\Facades\Log::warning('Failed to clean up a PO upload after a failed update.', ['path' => $path]);
                }
            }
            throw $e;
        }
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
        $po = PurchaseOrder::find($poId);
        if (!$po) {
            return back()->with('delete-error', 'Purchase order not found');
        }

        return DB::transaction(function () use ($po) {
            $lines = PurchaseOrder::where('po_no', $po->po_no)->lockForUpdate()->get();
            $first = $lines->first();
            $draftStatuses = [null, '', 'pending_request', 'not_required'];
            $isDraft = in_array($first->advance_status, $draftStatuses, true)
                && in_array($first->production_milestone, [null, '', 'awaiting_advance', 'po_issued'], true);

            if ($isDraft) {
                PoStatusHistory::create([
                    'po_no' => $po->po_no,
                    'from_status' => $first->advance_status,
                    'to_status' => 'soft_deleted',
                    'action' => 'po_draft_deleted',
                    'user_id' => auth()->id(),
                    'remarks' => "Draft PO {$po->po_no} soft-deleted; line and supplier history retained.",
                ]);
                $lines->each->delete();
                if ($first->orf_id) {
                    $orf = Orf::whereKey($first->orf_id)
                        ->where('supplier_po_no', $po->po_no)
                        ->lockForUpdate()
                        ->first();
                    if ($orf) {
                        $orf->po_status = 'pending';
                        $orf->supplier_po_no = null;
                        $orf->po_released_at = null;
                        $orf->save();
                    }
                }
            } else {
                PurchaseOrder::where('po_no', $po->po_no)->update([
                    'lifecycle_status' => 'cancelled',
                    'production_milestone' => 'cancelled',
                ]);
                if ($first->orf_id) {
                    $orf = Orf::whereKey($first->orf_id)
                        ->where('supplier_po_no', $po->po_no)
                        ->lockForUpdate()
                        ->first();
                    if ($orf) {
                        $orf->po_status = 'cancelled';
                        $orf->save();
                    }
                }
                PoStatusHistory::create([
                    'po_no' => $po->po_no,
                    'from_status' => $first->advance_status,
                    'to_status' => 'cancelled',
                    'action' => 'po_cancelled',
                    'user_id' => auth()->id(),
                    'remarks' => "Progressed PO {$po->po_no} cancelled; payment and supplier history retained.",
                ]);
            }

            return back()->with('delete-success', $isDraft ? 'Draft purchase order soft-deleted' : 'Purchase order cancelled');
        });
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
        } elseif ($statusFilter === 'pending_sir') {
            $query->whereHas('purchaseOrders', function ($q) {
                $q->where('lifecycle_status', 'active')->where('advance_status', 'pending_sir_approval');
            });
        } elseif ($statusFilter === 'pending_finance') {
            $query->whereHas('purchaseOrders', function ($q) {
                $q->where('lifecycle_status', 'active')->where('advance_status', 'approved_by_sir');
            });
        } elseif ($statusFilter === 'advance_released') {
            $query->whereHas('purchaseOrders', function ($q) {
                $q->where('lifecycle_status', 'active')->where('advance_status', 'advance_released');
            });
        } elseif ($statusFilter === 'rejected_sir') {
            $query->whereHas('purchaseOrders', function ($q) {
                $q->where('lifecycle_status', 'active')->where('advance_status', 'rejected_by_sir');
            });
        }

        $orders = $query->get();
        $directQuery = PurchaseOrder::with(['allVendorDetails', 'productDetails'])
            ->whereNull('orf_id')
            ->where('lifecycle_status', 'active')
            ->orderByDesc('po_date');
        if ($statusFilter === 'pending_sir') {
            $directQuery->where('advance_status', 'pending_sir_approval');
        } elseif ($statusFilter === 'pending_finance') {
            $directQuery->where('advance_status', 'approved_by_sir');
        } elseif ($statusFilter === 'advance_released') {
            $directQuery->where('advance_status', 'advance_released');
        } elseif ($statusFilter === 'rejected_sir') {
            $directQuery->where('advance_status', 'rejected_by_sir');
        } elseif ($statusFilter === 'pending') {
            $directQuery->whereRaw('1 = 0');
        } elseif (!in_array($statusFilter, ['all', 'released'], true)) {
            $directQuery->whereRaw('1 = 0');
        }
        $directPurchaseOrders = $directQuery->get()->groupBy('po_no');

        foreach ($orders as $order) {
            $order->quotationItems = QuotationProduct::where('quotationno', $order->quono)->get();
            $order->customerTotal = $order->quotationItems->sum('grandtotal') ?: $order->quotationItems->sum('subtotal');
        }

        foreach ($directPurchaseOrders as $poNo => $poLines) {
            $direct = new \stdClass();
            $direct->id = $poLines->first()->id;
            $direct->purchaseOrders = $poLines;
            $direct->lead = null;
            $direct->po_status = 'released';
            $direct->supplier_po_no = $poNo;
            $direct->quoproid = null;
            $direct->quono = 'Direct procurement';
            $direct->orfref = 'Direct PO';
            $direct->created_at = $poLines->first()->created_at;
            $direct->customerTotal = 0;
            $direct->is_direct_po = true;
            $orders->push($direct);
        }

        $allOrders = Orf::where('approval_status', 1)->where('cs_status', 1)->get();
        $directPoCount = PurchaseOrder::whereNull('orf_id')
            ->where('lifecycle_status', 'active')
            ->distinct('po_no')
            ->count('po_no');
        $totalApproved = $allOrders->count() + $directPoCount;
        $totalReleased = $allOrders->where('po_status', 'released')->count() + $directPoCount;
        $totalPending = $allOrders->filter(function ($order) {
            return empty($order->po_status) || $order->po_status === 'pending';
        })->count();

        $totalMargin = PurchaseOrder::where('lifecycle_status', 'active')->sum('price_variance');
        $totalProcurement = PurchaseOrder::where('lifecycle_status', 'active')
            ->selectRaw('po_no, SUM(total_amount) + MAX(freight_charges) + MAX(unloading_charges) + MAX(roundoff) AS po_total')
            ->groupBy('po_no')
            ->get()
            ->sum('po_total');

        $pendingSirCount = PurchaseOrder::where('lifecycle_status', 'active')->where('advance_status', 'pending_sir_approval')->distinct('po_no')->count('po_no');
        $pendingFinanceCount = PurchaseOrder::where('lifecycle_status', 'active')->where('advance_status', 'approved_by_sir')->distinct('po_no')->count('po_no');
        $advanceReleasedCount = PurchaseOrder::where('lifecycle_status', 'active')->where('advance_status', 'advance_released')->distinct('po_no')->count('po_no');
        $rejectedSirCount = PurchaseOrder::where('lifecycle_status', 'active')->where('advance_status', 'rejected_by_sir')->distinct('po_no')->count('po_no');

        return view('pages.vendor.purchase_order.orderPoTracking', compact(
            'orders',
            'statusFilter',
            'totalApproved',
            'totalReleased',
            'totalPending',
            'totalMargin',
            'totalProcurement',
            'pendingSirCount',
            'pendingFinanceCount',
            'advanceReleasedCount',
            'rejectedSirCount'
        ));
    }

    public function requestAdvance(Request $request)
    {
        $request->validate(['po_no' => 'required|string|max:100']);
        if (!$this->workflowRoleAllowed(['Super Admin', 'Purchase Manager', 'Service Coordinator', 'CS', 'Customer Support', 'Sales'], ['purchase-order-create', 'purchase-order-edit', 'purchase-order-display'])) {
            return $this->workflowFailure($request, 'You are not authorized to request an advance.', 403);
        }
        $poNo = $request->po_no;
        return DB::transaction(function () use ($request, $poNo) {
            $lines = PurchaseOrder::where('po_no', $poNo)->where('lifecycle_status', 'active')->lockForUpdate()->get();
            $po = $lines->first();
            if (!$po) return $this->workflowFailure($request, 'Purchase Order not found.', 404);
            if (!in_array($po->advance_status, ['pending_request', 'rejected_by_sir', null, ''], true) ||
                (float) $po->advance_percentage <= 0 || (float) $po->advance_amount <= 0) {
                return $this->workflowFailure($request, 'An advance request is only allowed for a positive advance in a requestable state.', 409);
            }

            PurchaseOrder::where('po_no', $poNo)->update([
                'advance_status' => 'pending_sir_approval',
                'sir_approved_by' => null,
                'sir_approved_at' => null,
                'sir_remark' => null,
            ]);
            PoStatusHistory::create([
                'po_no' => $poNo,
                'from_status' => $po->advance_status,
                'to_status' => 'pending_sir_approval',
                'action' => 'requested_advance',
                'user_id' => auth()->id(),
                'remarks' => 'Advance approval request submitted by ' . (auth()->user()?->name ?? 'User'),
            ]);
            return $this->workflowSuccess($request, 'Advance approval request submitted to Sir / Management.');
        });
    }

    public function sirApproveAdvance(Request $request)
    {
        $request->validate([
            'po_no' => 'required|string|max:100',
            'action' => 'required|in:approve,reject',
            'remark' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:500',
        ]);

        if (!$this->workflowRoleAllowed(['Super Admin', 'Admin', 'Director', 'Management'], ['po-sir-approval', 'purchase-order-display'])) {
            return $this->workflowFailure($request, 'Only authorized management can approve or reject advances.', 403);
        }

        $poNo = $request->po_no;
        $isApprove = $request->action === 'approve';
        $newStatus = $isApprove ? 'approved_by_sir' : 'rejected_by_sir';
        $remarkText = $request->remark ?: $request->remarks;
        return DB::transaction(function () use ($request, $poNo, $isApprove, $newStatus, $remarkText) {
            $lines = PurchaseOrder::where('po_no', $poNo)->where('lifecycle_status', 'active')->lockForUpdate()->get();
            $po = $lines->first();
            if (!$po) return $this->workflowFailure($request, 'Purchase Order not found.', 404);
            if ($po->advance_status !== 'pending_sir_approval') {
                return $this->workflowFailure($request, 'This advance is not awaiting a management decision.', 409);
            }
            PurchaseOrder::where('po_no', $poNo)->update([
                'advance_status' => $newStatus,
                'sir_approved_by' => auth()->id(),
                'sir_approved_at' => now(),
                'sir_remark' => $remarkText,
            ]);
            PoStatusHistory::create([
                'po_no' => $poNo,
                'from_status' => $po->advance_status,
                'to_status' => $newStatus,
                'action' => $isApprove ? 'sir_approved' : 'sir_rejected',
                'user_id' => auth()->id(),
                'remarks' => $isApprove ? 'Advance approved by management.' : 'Advance rejected: ' . ($remarkText ?: 'No remarks specified'),
            ]);
            return $this->workflowSuccess($request, $isApprove ? 'Advance approved and sent to Finance.' : 'Advance request rejected.');
        });
    }

    public function financeReleaseAdvance(Request $request)
    {
        $request->validate([
            'po_no' => 'required|string|max:100',
            'finance_utr_no' => 'nullable|string|min:4|max:50',
            'utr_number' => 'nullable|string|min:4|max:50',
            'payment_mode' => 'nullable|in:NEFT,RTGS,IMPS,Cheque,UPI',
            'payment_date' => 'nullable|date|before_or_equal:today',
            'remark' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:500',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        if (!$this->workflowRoleAllowed(['Super Admin', 'Finance Manager', 'Finance', 'Accountant', 'Accounts'], ['po-finance-release', 'purchase-order-display'])) {
            return $this->workflowFailure($request, 'Only Finance / Accounts can release an advance.', 403);
        }

        $poNo = $request->po_no;
        $utr = trim($request->input('finance_utr_no') ?: $request->input('utr_number', ''));
        if (empty($utr)) {
            return $this->workflowFailure($request, 'The UTR / Transaction reference number is required.', 422);
        }
        $paymentMode = $request->input('payment_mode') ?: 'NEFT';
        $paymentDate = $request->input('payment_date') ?: now()->format('Y-m-d');
        $remark = $request->input('remark') ?: $request->input('remarks');

        return DB::transaction(function () use ($request, $poNo, $utr, $paymentMode, $paymentDate, $remark) {
            $lines = PurchaseOrder::where('po_no', $poNo)->where('lifecycle_status', 'active')->lockForUpdate()->get();
            $po = $lines->first();
            if (!$po) return $this->workflowFailure($request, 'Purchase Order not found.', 404);
            if ($po->advance_status !== 'approved_by_sir' || (float) $po->advance_amount <= 0) {
                return $this->workflowFailure($request, 'Finance can release only a positive advance approved by management.', 409);
            }
            $data = [
                'advance_status' => 'advance_released',
                'finance_released_by' => auth()->id(),
                'finance_released_at' => $paymentDate,
                'finance_utr_no' => $utr,
                'finance_payment_mode' => $paymentMode,
                'finance_remark' => $remark,
                'production_milestone' => 'in_fabrication',
            ];
            PurchaseOrder::where('po_no', $poNo)->update($data);
            PoStatusHistory::create([
                'po_no' => $poNo,
                'from_status' => $po->advance_status,
                'to_status' => 'advance_released',
                'action' => 'finance_released',
                'user_id' => auth()->id(),
                'remarks' => "Advance released via {$data['finance_payment_mode']} with UTR recorded.",
            ]);
            return $this->workflowSuccess($request, 'Advance release marked successfully with UTR reference.');
        });
    }

    public function updatePoMilestone(Request $request)
    {
        $request->validate([
            'po_no' => 'required|string|max:100',
            'supplier_work_order_no' => 'nullable|string|max:100',
            'committed_dispatch_date' => 'nullable|date_format:Y-m-d',
            'supplier_dispatch_date' => 'nullable|date_format:Y-m-d',
            'actual_dispatch_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'production_milestone' => 'nullable|in:po_issued,advance_pending,in_fabrication,ready_for_dispatch,dispatched,delivered',
        ]);
        if (!$this->workflowRoleAllowed(['Super Admin', 'Dispatch Manager', 'Purchase Manager'], ['purchase-order-edit'])) {
            return $this->workflowFailure($request, 'You are not authorized to update PO milestones.', 403);
        }
        $poNo = $request->po_no;
        return DB::transaction(function () use ($request, $poNo) {
            $lines = PurchaseOrder::where('po_no', $poNo)->where('lifecycle_status', 'active')->lockForUpdate()->get();
            $po = $lines->first();
            if (!$po) return $this->workflowFailure($request, 'Purchase Order not found.', 404);

            $oldMilestone = $po->production_milestone ?: 'po_issued';
            $newMilestone = $request->input('production_milestone') ?: $oldMilestone;
            $allowed = [
                'awaiting_po' => ['po_issued'],
                'awaiting_advance' => ['advance_pending', 'in_fabrication'],
                'po_issued' => ['advance_pending', 'in_fabrication'],
                'advance_pending' => ['in_fabrication'],
                'in_fabrication' => ['ready_for_dispatch'],
                'ready_for_dispatch' => ['dispatched'],
                'dispatched' => ['delivered'],
                'delivered' => [],
            ];
            if ($newMilestone !== $oldMilestone && !in_array($newMilestone, $allowed[$oldMilestone] ?? [], true)) {
                return $this->workflowFailure($request, 'The requested production milestone is out of sequence.', 409);
            }
            if ($newMilestone === 'in_fabrication' && $po->advance_percentage > 0 && $po->advance_status !== 'advance_released') {
                return $this->workflowFailure($request, 'Production cannot start until the approved advance is released.', 409);
            }
            if ($newMilestone === 'dispatched' && !$request->filled('actual_dispatch_date') && !$po->actual_dispatch_date) {
                return $this->workflowFailure($request, 'Record the actual dispatch date when marking the PO dispatched.', 422);
            }
            $committedDate = $request->input('committed_dispatch_date', $request->input('supplier_dispatch_date', $po->committed_dispatch_date ?: $po->supplier_dispatch_date));
            $actualDate = $request->input('actual_dispatch_date', $po->actual_dispatch_date);
            if ($actualDate && $po->po_date && $actualDate < $po->po_date) {
                return $this->workflowFailure($request, 'Actual dispatch date cannot precede the PO date.', 422);
            }
            $data = [
                'supplier_work_order_no' => $request->input('supplier_work_order_no', $po->supplier_work_order_no),
                'supplier_dispatch_date' => $committedDate,
                'committed_dispatch_date' => $committedDate,
                'actual_dispatch_date' => $actualDate,
                'production_milestone' => $newMilestone,
            ];
            if ($committedDate !== $po->committed_dispatch_date) {
                $data['dispatch_reminder_sent_at'] = null;
            }
            PurchaseOrder::where('po_no', $poNo)->update($data);
            PoStatusHistory::create([
                'po_no' => $poNo,
                'from_status' => $oldMilestone,
                'to_status' => $newMilestone,
                'action' => 'milestone_updated',
                'user_id' => auth()->id(),
                'remarks' => 'PO milestone and dispatch dates updated.',
            ]);
            return $this->workflowSuccess($request, 'Production milestones updated successfully.');
        });
    }

    private function workflowRoleAllowed(array $roles, array $permissions = []): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if (in_array($user->role, $roles, true)) return true;
        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) return true;
        }
        foreach ($permissions as $permission) {
            if ($user->can($permission)) return true;
        }
        return false;
    }

    private function workflowFailure(Request $request, string $message, int $status)
    {
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->is('po/*')) {
            return response()->json(['status' => 'error', 'message' => $message], $status);
        }
        return back()->with('error', $message);
    }

    private function workflowSuccess(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->is('po/*')) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }
        return back()->with('success', $message);
    }
}
