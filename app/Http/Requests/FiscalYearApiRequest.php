<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class FiscalYearApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $nowAfter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowBefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $dateFromParam = request('date_from');
        $dateToParam = request('date_to');

        $startIsNullable = "nullable";
        if (!empty($dateFromParam) && !empty($dateToParam)) {
            $startIsNullable = "required";
        }

        $dateFromValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowAfter3yrs|less_than:$nowBefore30yrs";
        $dateToValidation = 'nullable|date_format:Y-m-d';

        if ($dateFromParam && $dateToParam) {
            $dateToValidation .= "|afterOrEqual:$dateFromParam";
        }

        // date_to >= date_from validation
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
            'description' => 'required|string|min:1|max:45',
            'date_from' => $dateFromValidation,
            'date_to' => $dateToValidation,
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.required' => 'Invalid period (date from is required)',
            'date_from.greater_than' => 'Invalid period (date from must not be more than 3 years in the future)',
            'date_from.less_than' => 'Invalid period (date from must not be less than 30 years in the past)',
            'date_to.afterOrEqual' => 'Invalid period (date to must occur after date from)',
        ];
    }
}
