<?php

namespace App\Http\Requests;

class ProductHierarchyApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'relation_type' => $this->requiredOrNullable . "|integer",
        ];
    }
}
