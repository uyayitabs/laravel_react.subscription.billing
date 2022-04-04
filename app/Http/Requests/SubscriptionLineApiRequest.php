<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class SubscriptionLineApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $subscriptionStartParam = request('subscription_start');
        $subscriptionStopParam = request('subscription_stop');

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $startIsNullable = "nullable";
        if (!empty($subscriptionStartParam) && !empty($subscriptionStopParam)) {
            $startIsNullable = "required";
        }

        $subscriptionStartValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $subscriptionStopValidation = 'nullable|date_format:Y-m-d';

        if ($subscriptionStartParam && $subscriptionStopParam) {
            $subscriptionStopValidation .= "|afterOrEqual:subscription_start";
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
            "subscription_id" => "nullable|integer|exists:subscriptions,id",
            "subscription_line_type" => "nullable|integer|exists:plan_subscription_line_types,id",
            "plan_line_id" => "nullable|integer|exists:plan_lines,id",
            "product_id" => "nullable|integer|exists:products,id",
            "serial" => "nullable|string|min:1|max:50",
            "mandatory_line" => "nullable|integer|exists:plan_lines,id",
            "subscription_start" => $subscriptionStartValidation,
            "subscription_stop" => $subscriptionStopValidation,
            "description" =>  "nullable|string|min:1|max:190",
            "description_long" => "nullable|string|min:1|max:65535",
            "mind_id" => "nullable|integer",
            //["subscription_line_prices"] validation
            "subscription_line_prices.*.fixed_price" => "nullable|between:0,99999.99999",
            "subscription_line_prices.*.margin" => "nullable|between:0,99999.99999",
            "subscription_line_prices.*.price_valid_from" => "nullable|date_format:Y-m-d",
            "subscription_line_prices.*.subscription_line_id" => "nullable|integer|exists:subscription_lines,id",
            "subscription_line_prices.*.parent_plan_line_id" => "nullable|integer|exists:plan_lines,id",
        ];
    }


    /**
     * Set custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'subscription_start.required' => 'Invalid period (start date is required)',
            'subscription_start.greater_than' => 'Invalid period (start date must not be more than 3 years in the future)',
            'subscription_start.less_than' => 'Invalid period (start date must not be less than 30 years in the past)',
            'subscription_stop.afterOrEqual' => 'Invalid period (end date must occur after start date)',
        ];
    }
}
