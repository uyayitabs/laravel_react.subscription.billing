<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SalesInvoiceLineApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $invoiceStartParam = request('invoice_start');
        $invoiceStopParam = request('invoice_stop');

        $startIsNullable = "nullable";
        if (!empty($invoiceStartParam) && !empty($invoiceStopParam)) {
            $startIsNullable = "required";
        }

        $invoiceStartValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $invoiceStopValidation = 'nullable|date_format:Y-m-d';

        if ($invoiceStartParam && $invoiceStopParam) {
            $invoiceStopValidation .= "|afterOrEqual:$invoiceStartParam";
        }

        // period end >= period start validation
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
            "sales_invoice_id" => "nullable|integer|exists:sales_invoices,id",
            "product_id" => "nullable|integer|exists:products,id",
            "subscription_line_id" => "nullable|integer|exists:subscription_lines,id",
            "sales_invoice_line_type" => "nullable|integer|exists:plan_subscription_line_types,id",
            "plan_line_id" => "nullable|integer|exists:plan_lines,id",
            "order_line_id" => "nullable|integer",
            "description" => "nullable|string|min:1|max:191",
            "description_long" => "nullable|string|min:1|max:65535",
            "price_per_piece" => "nullable|between:0,99999.99999",
            "price" => "nullable|between:0,99999.99999",
            "quantity" => "nullable|between:0,99999.99999",
            "price_vat" => "nullable|between:0,99999.99999",
            "vat_code" => "nullable|integer|exists:vat_codes,id",
            "vat_percentage" => "nullable|between:0,99.99999",
            "price_total" => "nullable|between:0,99999.99999",
            "invoice_start" => $invoiceStartValidation,
            "invoice_stop" => $invoiceStopValidation,
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_start.required' => 'Invalid period (period start is required)',
            'invoice_start.greater_than' => 'Invalid period (period start must not be more than 3 years in the future)',
            'invoice_start.less_than' => 'Invalid period (period start must not be less than 30 years in the past)',
            'invoice_stop.afterOrEqual' => 'Invalid period (period end must occur after period start)',
        ];
    }
}
