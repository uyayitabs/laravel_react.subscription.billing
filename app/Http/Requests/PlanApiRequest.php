<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PlanApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');
        $planStartParam = request('plan_start');
        $planStopParam = request('plan_stop');

        $startIsNullable = "nullable";
        if (!empty($planStartParam) && !empty($planStopParam)) {
            $startIsNullable = "required";
        }

        $planStartValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $planStopValidation = 'nullable|date_format:Y-m-d';

        if ($planStartParam && $planStartParam) {
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
            'tenant_id' => 'nullable|integer|exists:tenants,id',
            'parent_plan' => 'nullable|integer|exists:plans,id',
            'project_id' => 'nullable|integer|min:1|max:99',
            "plan_type" => 'nullable|string|min:1|max:45',
            'description' =>  'nullable|string|min:1|max:190',
            'description_long' => 'nullable|string|min:1|max:65535',
            'billing_start' => 'nullable|date_format:Y-m-d',
            'plan_start' => $planStartValidation,
            'plan_stop' => $planStopValidation,
        ];
    }

    public function messages(): array
    {
        return [
            'plan_start.required' => 'Invalid period (start date is required)',
            'plan_start.greater_than' => 'Invalid period (start date must not be more than 3 years in the future)',
            'plan_start.less_than' => 'Invalid period (start date must not be less than 30 years in the past)',
            'plan_stop.afterOrEqual' => 'Invalid period (end date must occur after start date)',
        ];
    }
}
