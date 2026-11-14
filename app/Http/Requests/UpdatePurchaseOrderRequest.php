<?php

namespace App\Http\Requests;

class UpdatePurchaseOrderRequest extends StorePurchaseOrderRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'po_number' => 'required|array|min:1',
            'po_number.*' => 'required|string|max:100',
            'po_id' => 'nullable|array',
            'po_id.*' => 'nullable|integer|distinct|exists:purchase_order_tb,id',
            'poId' => 'nullable|array',
            'poId.*' => 'nullable|integer|distinct|exists:purchase_order_tb,id',
            'deleted_po_ids' => 'nullable|array',
            'deleted_po_ids.*' => 'integer|exists:purchase_order_tb,id',
        ]);
    }
}
