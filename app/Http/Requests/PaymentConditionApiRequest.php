<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentConditionApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'description' => $this->requiredOrNullable . '|string',
            'net_days' => $this->requiredOrNullable . '|integer',
        ];
    }
}
