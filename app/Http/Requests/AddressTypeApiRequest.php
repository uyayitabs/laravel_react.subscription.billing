<?php

namespace App\Http\Requests;

class AddressTypeApiRequest extends BaseApiRequest
{
    public function messages(): array
    {
        return [
            'type.required' => 'Type is required!',
        ];
    }
}
