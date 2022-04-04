<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionLinePriceApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'subscription_line_id' => 'nullable|integer|exists:subscription_lines,id',
            'parent_plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            'fixed_price' => 'nullable|between:0,99999.99999',
            'margin' => 'nullable|between:00.00,99.99',
            'price_valid_from' => 'nullable|date_format:Y-m-d',
        ];
    }
}
