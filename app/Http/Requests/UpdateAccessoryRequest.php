<?php

namespace App\Http\Requests;

class UpdateAccessoryRequest extends StoreAccessoryRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), ['accessories.*.id' => 'nullable|exists:accessories,id']);
    }
}
