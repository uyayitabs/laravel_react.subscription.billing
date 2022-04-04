<?php

namespace App\Http\Requests;

class BrandApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => $this->requiredOrNullable . '|string|min:1|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required!',
        ];
    }
}
