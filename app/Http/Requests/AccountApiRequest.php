<?php

namespace App\Http\Requests;

class AccountApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'description' => $this->requiredOrNullable . '|string|max:120',
            'type' => $this->requiredOrNullable . '|string|max:45',
            'code' => $this->requiredOrNullable . '|string|max:45',
            'parent_id' => 'nullable|integer',
            'export_code' => 'nullable|string|max:45',
        ];
    }
}
