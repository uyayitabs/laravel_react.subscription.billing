<?php

namespace App\Http\Requests;

class ProductTypeApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'type' => $this->requiredOrNullable . '|string|min:1|max:255|unique:product_types,type',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.required' => 'Type is required!',
        ];
    }
}
