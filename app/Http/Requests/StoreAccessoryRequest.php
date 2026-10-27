<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccessoryRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id', 'product_category_id' => 'nullable|exists:product_categories_tb,id',
            'accessories' => 'required|array|min:1', 'accessories.*.name' => 'required|string|max:255',
            'accessories.*.part_no' => 'nullable|string|max:255', 'accessories.*.hsn' => 'nullable|string|max:30',
            'accessories.*.price' => 'required|numeric|min:0', 'accessories.*.tax_percent' => 'required|numeric|min:0|max:100',
            'accessories.*.status' => 'required|in:active,inactive',
        ];
    }
}
