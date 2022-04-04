<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionApiRequest extends BaseApiRequest
{
    protected $subscription;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->subscription = $this->route("subscription");
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $bilingStartRequiredNullable = $this->requiredOrNullable;
        $statusParam = request('status');
        $relationIdParam = request("relation_id");
        $billingStartParam = request("billing_start");

        $subscriptionStatus = !blank($this->subscription) ? $this->subscription->status : null;
        $isSubscriptionOngoing = (!blank($subscriptionStatus) && $subscriptionStatus == 1) ||
            (!blank($statusParam) && $statusParam == 1);

        if (blank($relationIdParam) && !blank($this->subscription)) {
            $relationIdParam = $this->subscription->relation_id;
        }

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');
        $subscriptionStartParam = request('subscription_start');
        $subscriptionStopParam = request('subscription_stop');

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
            "subscription_no" => "nullable|string",
            "type" => "nullable|integer",
            "relation_id" => $this->requiredOrNullable . "|integer|exists:relations,id",
            "plan_id" => "nullable|integer|exists:plans,id",
            "billing_start" => "nullable|date_format:Y-m-d",
            "subscription_start" => $subscriptionStartValidation,
            "subscription_stop" => $subscriptionStopValidation,
            "billing_person" => [
                "nullable",
                "integer",
                Rule::exists("relations_persons", "person_id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam]
                            ]);
                        }
                    )
            ],
            "provisioning_person" => [
                "nullable",
                "integer",
                Rule::exists("relations_persons", "person_id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam]
                            ]);
                        }
                    )
            ],
            "billing_address" => [
                "nullable",
                "integer",
                Rule::exists("addresses", "id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam],
                                ["address_type_id", "=", 3] //billing_address
                            ]);
                        }
                    )
            ],
            "provisioning_address" => [
                "nullable",
                "integer",
                Rule::exists("addresses", "id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam],
                                ["address_type_id", "=", 2], // provisioning_address
                            ]);
                        }
                    )
            ],
            "description" =>  "nullable|string|min:1|max:190",
            "description_long" => "nullable|string|min:1|max:65535",
            "status" => $this->requiredOrNullable . "|integer|min:0|max:2"
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
