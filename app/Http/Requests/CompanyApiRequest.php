<?php

namespace App\Http\Requests;

class CompanyApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255|unique:companies,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tenant name is required!',
        ];
    }
}
