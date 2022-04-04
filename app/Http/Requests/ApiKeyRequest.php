<?php

namespace App\Http\Requests;

class ApiKeyRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            "user_id" => "required|integer",
            "name" => "required|string|max:64",
            "description" => "nullable|string|max:256",
            "expire_date" => "nullable|date|max:95",
            "ip_address" => "required|string|max:45"
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'name.required' => 'Name is required',
        ];
    }
}
