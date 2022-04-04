<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class PlanLineApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $planStartParam = request('plan_start');
        $planStopParam = request('plan_stop');

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $startIsNullable = "nullable";
        if (!empty($planStartParam) && !empty($planStopParam)) {
            $startIsNullable = "required";
        }

        $planStartValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $planStopValidation = 'nullable|date_format:Y-m-d';

        if ($planStartParam && $planStopParam) {
            $planStopValidation .= "|afterOrEqual:$planStartParam";
        }

        // end date >= start date validation
        Validator::extend('afterOrEqual', function ($attribute, $value, $otherValue) {
            return Carbon::createFromFormat("Y-m-d", $value)->gte(Carbon::createFromFormat("Y-m-d", $otherValue[0]));
        });

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
            'plan_id' => 'nullable|integer|exists:plans,id',
            'product_id' => 'required|integer|exists:products,id',
            "plan_line_type" => 'nullable|integer|exists:plan_subscription_line_types,id',
            'parent_plan_line_id' => 'nullable|integer|exists:plan_lines,id',
            'mandatory_line' => 'nullable|boolean|min:0|max:1',
            'description' =>  'required|string|min:1|max:190',
            'description_long' => 'nullable|string|min:1|max:65535',
            'plan_start' => $planStartValidation,
            'plan_stop' => $planStopValidation,
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'The product is required',
            'plan_start.required' => 'Invalid period (start date is required)',
            'plan_start.greater_than' => 'Invalid period (start date must not be more than 3 years in the future)',
            'plan_start.less_than' => 'Invalid period (start date must not be less than 30 years in the past)',
            'plan_start.afterOrEqual' => 'Invalid period (date to must occur after date from)',
        ];
    }
}
