<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PlanLinePriceApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $priceValidFromParam = request('price_valid_from');

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        // date >= 3 years (36 months)
        Validator::extend('greater_than', function ($attribute, $value, $otherValue) {
            $valueDate = Carbon::createFromFormat("Y-m-d", $value);
            $otherValueDate = Carbon::createFromFormat("Y-m-d", $otherValue[0]);
            return $otherValueDate->gt($valueDate);
        });

        // date < 30 years
        Validator::extend('less_than', function ($attribute, $value, $otherValue) {
            $valueDate = Carbon::createFromFormat("Y-m-d", $value);
            $otherValueDate = Carbon::createFromFormat("Y-m-d", $otherValue[0]);
            return $otherValueDate->lt($valueDate);
        });

        return [
            'plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            'parent_plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            'fixed_price' => 'nullable|between:0,99999.99999',
            'margin' => 'nullable|between:00.00,99.99',
            'price_valid_from' => "nullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs",
            '*.plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            '*.parent_plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            '*.fixed_price' => 'nullable|between:0,99999.99999',
            '*.margin' => 'nullable|between:00.00,99.99',
            '*.price_valid_from' => "nullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs",
        ];
    }

    public function messages(): array
    {
        return [
            'price_valid_from.greater_than' => 'Invalid period (valid from must not be more than 3 years in the future)',
            'price_valid_from.less_than' => 'Invalid period (valid from must not be less than 30 years in the past)',
            '*.price_valid_from.greater_than' => 'Invalid period (valid from must not be more than 3 years in the future)',
            '*.price_valid_from.less_than' => 'Invalid period (valid from must not be less than 30 years in the past)',
        ];
    }
}
