<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $productIds = $this->input('proId', []);
        if (!is_array($productIds) || !collect($productIds)->contains(fn ($productId) => $this->hasLineValue($productId))) {
            return;
        }

        $emptyRows = [];
        foreach (array_keys($productIds) as $index) {
            if ($this->isEmptyProductPlaceholder($index)) {
                $emptyRows[$index] = true;
            }
        }

        if (!$emptyRows) {
            return;
        }

        $lineFields = [
            'catId', 'proId', 'qty', 'unit_price', 'gst', 'discount', 'hsn',
            'part_no', 'pro_des', 'vendorId', 'accessories', 'accessory_quantity',
            'po_id', 'poId',
        ];
        $normalized = [];
        foreach ($lineFields as $field) {
            $values = $this->input($field);
            if (is_array($values)) {
                $normalized[$field] = array_values(array_diff_key($values, $emptyRows));
            }
        }

        $this->merge($normalized);
    }

    private function isEmptyProductPlaceholder(int|string $index): bool
    {
        $selectedAccessories = $this->input("accessories.$index", []);
        if (is_array($selectedAccessories) ? !empty($selectedAccessories) : $this->hasLineValue($selectedAccessories)) {
            return false;
        }

        return !$this->hasLineValue($this->input("proId.$index"))
            && $this->isDefaultOrEmpty($this->input("unit_price.$index"), 0)
            && !$this->hasLineValue($this->input("part_no.$index"))
            && !$this->hasLineValue($this->input("pro_des.$index"));
    }

    private function isDefaultOrEmpty(mixed $value, int|string $default): bool
    {
        if (!$this->hasLineValue($value)) {
            return true;
        }

        if (is_numeric($value) && is_numeric($default)) {
            return (float) $value === (float) $default;
        }

        return (string) $value === (string) $default;
    }

    private function hasLineValue(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    public function rules(): array
    {
        return [
            'primary_vendor_id' => 'required|integer|exists:vendor_details_tb,id',
            'supplier_address_id' => 'nullable|integer|exists:vendor_addresses,id',
            'supplier_billing_address' => 'required|string|min:5|max:5000',
            'supplier_gst' => ['required', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/i'],
            'mercuriis_branch' => ['required', 'string', Rule::in(array_keys(config('mercuriis_branches.branches', [])))],
            'shipping_type' => 'required|in:warehouse,customer_site',
            'shipping_address' => 'required|string|min:5|max:5000',
            'shipping_gst' => ['required', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/i'],
            'customer_site_id' => 'nullable|integer|exists:customer_addresses,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'orf_id' => 'nullable|integer|exists:orf,id',
            'quotation_id' => 'nullable|integer|exists:quotations,id',
            'orf_sigdraw' => 'nullable|string|max:255',

            'catId' => 'required|array|min:1',
            'catId.*' => 'required|integer|exists:product_categories_tb,id',
            'proId' => 'required|array|min:1',
            'proId.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|numeric|min:1',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric|min:0',
            'gst' => 'nullable|array',
            'gst.*' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|array',
            'discount.*' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'hsn' => 'required|array|min:1',
            'hsn.*' => 'required|string|min:2|max:20',
            'part_no' => 'nullable|array',
            'part_no.*' => 'nullable|string|max:100',
            'pro_des' => 'nullable|array',
            'pro_des.*' => 'nullable|string|max:5000',
            'vendorId' => 'nullable|array',
            'vendorId.*' => 'nullable|integer|exists:vendor_details_tb,id',
            'accessories' => 'nullable|array',
            'accessories.*' => 'array',
            'accessories.*.*' => 'integer|distinct|exists:accessories,id',
            'accessory_quantity' => 'nullable|array',
            'accessory_quantity.*' => 'array',
            'accessory_quantity.*.*' => 'numeric|min:1|max:999',

            'terms_condition_id' => 'nullable|integer|exists:terms,id',
            'lead_time' => 'nullable|string|max:255',
            'custom_lead_time' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:5000',
            'custom_payment_terms' => 'nullable|string|max:5000',
            'warranty_terms' => 'nullable|string|max:5000',
            'custom_warranty_terms' => 'nullable|string|max:5000',
            'installation_scope' => 'nullable|string|max:5000',
            'custom_installation_scope' => 'nullable|string|max:5000',
            'special_instructions' => 'nullable|string|max:5000',
            'technical_specification' => 'nullable|string|max:5000',
            'hide_brand_name' => 'nullable|boolean',
            'freight_charges' => 'nullable|numeric|min:0',
            'unloading_charges' => 'nullable|numeric|min:0',
            'advance_percentage' => 'nullable|numeric|min:0|max:100',
            'supplier_dispatch_date' => 'nullable|date_format:Y-m-d',
            'committed_dispatch_date' => 'nullable|date_format:Y-m-d',
            'actual_dispatch_date' => 'nullable|date_format:Y-m-d',
            'po_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'drawing_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'quote_attachments' => 'nullable|array|max:10',
            'quote_attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateLineArrays($validator);
            $vendorId = $this->input('primary_vendor_id');
            $existingPoNo = $this instanceof UpdatePurchaseOrderRequest ? $this->input('po_number.0') : null;
            $existingPrimaryVendor = $existingPoNo && is_numeric($vendorId)
                && \App\Models\PurchaseOrder::where('po_no', $existingPoNo)->where('vendor_id', $vendorId)->exists();
            if (is_numeric($vendorId) &&
                !\App\Models\Vendor::whereKey($vendorId)->where('vendor_status', 'active')->exists() &&
                !$existingPrimaryVendor) {
                $validator->errors()->add('primary_vendor_id', 'Select an active supplier.');
            }
            if (is_numeric($this->input('supplier_address_id')) && is_numeric($vendorId) &&
                !\App\Models\VendorAddress::withTrashed()->whereKey($this->input('supplier_address_id'))->where('vendor_id', $vendorId)->exists()) {
                $validator->errors()->add('supplier_address_id', 'The selected branch does not belong to this supplier.');
            }
            if ($this->filled('customer_site_id')) {
                $site = \App\Models\CustomerAddress::find($this->input('customer_site_id'));
                $customerId = $this->input('customer_id');
                $isExistingSite = $this instanceof UpdatePurchaseOrderRequest
                    && \App\Models\PurchaseOrder::where('po_no', $this->input('po_number.0'))
                        ->where('customer_site_id', $this->input('customer_site_id'))->exists();
                if (!$site || ($customerId && (int) $site->customer_id !== (int) $customerId) || (!$customerId && !$isExistingSite)) {
                    $validator->errors()->add('customer_site_id', 'The selected delivery address does not belong to this customer.');
                }
            }
            if ($this->filled('orf_id') && $this->filled('quotation_id')) {
                $orf = \App\Models\Orf::find($this->input('orf_id'));
                $quotation = \App\Models\Quotation::find($this->input('quotation_id'));
                if (!$orf || !$quotation ||
                    ((int) $orf->quoproid !== (int) $quotation->id && $orf->quono !== $quotation->quota_id)) {
                    $validator->errors()->add('quotation_id', 'The selected quotation does not belong to this order request.');
                }
            }
            $this->validateProductsAndVendors($validator);
        });
    }

    protected function validateLineArrays(Validator $validator): void
    {
        $arrays = ['catId', 'proId', 'qty', 'unit_price', 'gst', 'discount', 'hsn', 'part_no', 'pro_des', 'vendorId'];
        $productIds = $this->input('proId', []);
        if (!is_array($productIds)) {
            return;
        }
        $lineKeys = array_keys($productIds);
        foreach (array_diff($arrays, ['discount', 'hsn', 'part_no', 'pro_des', 'vendorId']) as $field) {
            $values = $this->input($field, []);
            if (!is_array($values) || array_keys($values) !== $lineKeys) {
                $validator->errors()->add($field, 'Line item fields must contain matching rows.');
            }
        }
        foreach (['discount', 'hsn', 'part_no', 'pro_des', 'vendorId'] as $field) {
            $values = $this->input($field, []);
            if ($this->has($field) && (!is_array($values) || array_keys($values) !== $lineKeys)) {
                $validator->errors()->add($field, 'Line item fields must contain matching rows.');
            }
        }
        $gstValues = $this->input('gst', []);
        if ($this->has('gst') && (!is_array($gstValues) || array_keys($gstValues) !== $lineKeys)) {
            $validator->errors()->add('gst', 'Line item fields must contain matching rows.');
        }
    }

    protected function validateProductsAndVendors(Validator $validator): void
    {
        if (!is_array($this->input('proId', []))) return;
        $productsByLine = [];
        foreach ($this->input('proId', []) as $index => $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $productsByLine[$index] = $product;
            }
            if ($product && (int) $product->Productcategory !== (int) ($this->input("catId.$index") ?? 0)) {
                $validator->errors()->add("proId.$index", 'The selected product does not belong to the selected category.');
            }
        }
        foreach (is_array($this->input('vendorId', [])) ? $this->input('vendorId', []) : [] as $index => $vendorId) {
            $existingPoNo = $this instanceof UpdatePurchaseOrderRequest ? $this->input('po_number.0') : null;
            $existingLineVendor = $existingPoNo && $vendorId
                && \App\Models\PurchaseOrder::where('po_no', $existingPoNo)->where('vendor_id', $vendorId)->exists();
            if ($vendorId && !\App\Models\Vendor::whereKey($vendorId)->where('vendor_status', 'active')->exists() && !$existingLineVendor) {
                $validator->errors()->add("vendorId.$index", 'Select an active supplier for this line.');
            }
        }
        foreach (is_array($this->input('accessories', [])) ? $this->input('accessories', []) : [] as $index => $accessoryIds) {
            if (!is_array($accessoryIds)) continue;
            $product = $productsByLine[$index] ?? null;
            if (!$product) continue;
            $availableAccessoryIds = \App\Models\Accessory::availableForProduct($product)->modelKeys();
            foreach ($accessoryIds as $accessoryId) {
                if (!in_array((int) $accessoryId, array_map('intval', $availableAccessoryIds), true)) {
                    $validator->errors()->add("accessories.$index", 'An accessory is not available for the selected product.');
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'primary_vendor_id.required' => 'Please select a Supplier / Vendor.',
            'supplier_billing_address.required' => 'Supplier billing address is required.',
            'supplier_gst.required' => 'Supplier GSTIN is required.',
            'mercuriis_branch.required' => 'Mercuriis Issuing Branch selection is required.',
            'shipping_type.required' => 'Delivery Destination Scope (Warehouse or Customer Site) is required.',
            'shipping_address.required' => 'Delivery destination address is required.',
            'shipping_gst.required' => 'Destination GSTIN is required.',

            'proId.required' => 'At least one product line item is required.',
            'proId.min' => 'At least one product line item is required.',
            'proId.*.required' => 'Row #:position: Product selection is required.',
            'proId.*.exists' => 'Row #:position: Selected product does not exist.',
            'qty.*.required' => 'Row #:position: Quantity is required.',
            'qty.*.min' => 'Row #:position: Quantity must be at least 1.',
            'unit_price.*.required' => 'Row #:position: Supplier Rate is required.',
            'unit_price.*.min' => 'Row #:position: Supplier Rate cannot be negative.',
            'hsn.*.required' => 'Row #:position HSN Code is mandatory.',
            'hsn.*.min' => 'Row #:position: HSN Code must be a valid code.',
            'discount.*.max' => 'Row #:position: Discount % cannot exceed 100%.',
        ];
    }
}
