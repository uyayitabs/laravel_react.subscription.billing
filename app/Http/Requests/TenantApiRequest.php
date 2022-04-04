<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => $this->requiredOrNullable . '|string|min:3|max:191',
            'parent_id' => $this->requiredOrNullable . '|integer',
            'billing_day' => $this->requiredOrNullable . '|integer|min:1|max:31',
            'billing_schedule' => 'nullable|integer|min:1|max:31',
            'invoice_start_calculation' => 'nullable|date',
        ];
    }
}
