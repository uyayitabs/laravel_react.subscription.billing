<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VatCodeApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $tenant = Tenant::find(request('tenant_id'));
        $requiredOrNullableAccount = $tenant->use_accounting ? "required" : "nullable";

        Validator::extend('greater_than_equal', function ($attribute, $value, $otherValue) {
            return floatval($otherValue[0]) >= floatval($value);
        });

        $id = request('id');

        Validator::extend('existing', function ($attribute, $value, $otherValue) use ($tenant, $id) {
            $query = $tenant->vatCodes()->where($otherValue[0], $value);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            return !$query->exists();
        });

        // Validator::extend('datediff', function ($attribute, $value, $otherValue) {
        //     $from  = Carbon::parse($otherValue[0]);
        //     $to  = Carbon::parse($value);
        //     $diff = $to->diffInDays($from);
        //     return false;
        // });

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $activeFromParam = request('active_from');
        $activeToParam = request('active_to');

        $startIsNullable = "nullable";
        if (!empty($activeFromParam) && !empty($activeToParam)) {
            $startIsNullable = "required";
        }

        $activeFromValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $activeToValidation = 'nullable|date_format:Y-m-d';

        if ($activeFromParam && $activeToParam) {
            $activeToValidation .= "|afterOrEqual:$activeFromParam";
        }

        // active_to >= active_from validation
        Validator::extend('afterOrEqual', function ($attribute, $value, $otherValue) {
            return Carbon::createFromFormat("Y-m-d", $value)->gt(Carbon::createFromFormat("Y-m-d", $otherValue[0]));
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
            "description" => "required|string|existing:description",
            "active_from" => $activeFromValidation,
            "active_to" => $activeToValidation,
            "vat_percentage" => [
                "required",
                "numeric",
                "greater_than_equal:30",
                "existing:vat_percentage"
            ],
            "account_id" => $this->requiredOrNullable . "|integer"
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
            'active_from.required' => 'Invalid period (active from is required)',
            'active_from.greater_than' => 'Invalid period (active from must not be more than 3 years in the future)',
            'active_from.less_than' => 'Invalid period (active from must not be less than 30 years in the past)',
            'active_to.afterOrEqual' => 'Invalid period (active to must occur after active from)',
            'vat_percentage.gte' => 'Must not be higher than 30%!',
            'description.existing' => 'VAT code description already exists for this tenant',
            'vat_percentage.existing' => 'VAT code percentage already exists for this tenant',
        ];
    }
}
