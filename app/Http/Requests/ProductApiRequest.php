<?php

namespace App\Http\Requests;

class ProductApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'product_type_id' => $this->requiredOrNullable . '|integer|exists:product_types,id',
            'vendor_partcode' => $this->requiredOrNullable . '|string|min:1|max:45',
            'price' => $this->requiredOrNullable . '|numeric',
            'vat_code_id' => $this->requiredOrNullable . '|integer',
            'ean_code' => 'nullable|string|max:45',
            'serialized' => $this->requiredOrNullable,
            'status_id' => $this->requiredOrNullable . '|numeric',
            'weight' => 'nullable|numeric',
            'description' => $this->requiredOrNullable . '|string|min:3|max:191',
        ];
    }
}
