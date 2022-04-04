<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SalesInvoiceApiRequest extends BaseApiRequest
{
    protected $salesInvoice;

    public function authorize()
    {
        $this->salesInvoice = $this->route('salesInvoice');
        return true;
    }
    public function rules(): array
    {
        $invoiceLines = "sales_invoice_lines";
        $relationIdParam = request('relation_id');
        if (empty($relationIdParam) && !empty($this->salesInvoice)) {
            $relationIdParam = $this->salesInvoice->relation_id;
        }

        $nowafter3yrs = now()->addYearsNoOverflow(3)->format('Y-m-d');
        $nowbefore30yrs = now()->subYearsNoOverflow(30)->format('Y-m-d');

        $dateParam = request('date');
        $duateDateParam = request('due_date');

        $startIsNullable = 'nullable';
        if (!empty($dateParam) && !empty($duateDateParam)) {
            $startIsNullable =  "required";
        }

        $dateValidation = "$startIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs";
        $dueDateValidation = 'nullable|date_format:Y-m-d';
        if ($dateParam && $duateDateParam) {
            $dueDateValidation .= "|afterOrEqual:$dateParam";
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

        // Invoice line validation
        $lineInvoiceStartParam = request("$invoiceLines.*.invoice_start");
        $lineInvoiceStopParam = request("$invoiceLines.*.invoice_stop");
        $lineInvoiceStartIsNullable = $lineInvoiceStartParam && $lineInvoiceStopParam ? "nullable" : "required";
        $lineInvoiceStopValidation = 'nullable|date_format:Y-m-d';
        if ($lineInvoiceStartParam && $lineInvoiceStopParam) {
            $lineInvoiceStopValidation .= "|after:$lineInvoiceStartParam";
        }

        return [
            'invoice_no' => 'nullable|string|min:1|max:191',
            'date' => $dateValidation,
            'due_date' => $dueDateValidation,
            'description' => 'nullable|string|min:1|max:191',
            'tenant_id' => 'nullable|integer|exists:tenants,id',
            'relation_id' => 'integer|exists:relations,id',
            'invoice_address_id' => [
                'nullable',
                'integer',
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
            'shipping_address_id' => [
                'nullable',
                'integer',
                Rule::exists("addresses", "id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam],
                                ["address_type_id", "=", 2], // provisioning_address
                            ])->orWhere([
                                ["relation_id", "=", $relationIdParam],
                                ["address_type_id", "=", 4], // shipping_address
                            ]);
                        }
                    )
            ],
            'invoice_person_id' => [
                'nullable',
                'integer',
                Rule::exists("relations_persons", "person_id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam]
                            ]);
                        }
                    )
            ],
            'shipping_person_id' => [
                'nullable',
                'integer',
                Rule::exists("relations_persons", "person_id")
                    ->where(
                        function ($query) use ($relationIdParam) {
                            $query->where([
                                ["relation_id", "=", $relationIdParam]
                            ]);
                        }
                    )
            ],
            'payment_condition_id' => 'nullable|integer|exists:payment_conditions,id',
            'invoice_status' => 'nullable|integer|min:0|max:2',
            'price' => 'nullable|between:0,99999.99999',
            'price_vat' => 'nullable|between:0,99999.99999',
            'price_total' => 'nullable|between:0,99999.99999',

            // [sales_invoice_lines]
            "{$invoiceLines}.*.sales_invoice_id" => 'nullable|integer|exists:sales_invoices,id',
            "{$invoiceLines}.*.product_id" => 'nullable|integer|exists:products,id',
            "{$invoiceLines}.*.subscription_line_id" => 'nullable|integer|exists:subscription_lines,id',
            "{$invoiceLines}.*.sales_invoice_line_type" => 'required|integer|exists:plan_subscription_line_types,id',
            "{$invoiceLines}.*.plan_line_id" => 'nullable|integer|exists:plan_lines,id',
            "{$invoiceLines}.*.order_line_id" => 'nullable|integer',
            "{$invoiceLines}.*.description" => 'nullable|string|min:1|max:191',
            "{$invoiceLines}.*.description_long" => 'nullable|text',
            "{$invoiceLines}.*.price_per_piece" => 'nullable|between:0,99999.99999',
            "{$invoiceLines}.*.price" => 'nullable|between:0,99999.99999',
            "{$invoiceLines}.*.quantity" => 'nullable|between:0,99999.99999',
            "{$invoiceLines}.*.price_vat" => 'nullable|between:0,99999.99999',
            "{$invoiceLines}.*.vat_code" => 'nullable|integer|exists:vat_codes,id',
            "{$invoiceLines}.*.vat_percentage" => 'nullable|between:0,99.99999',
            "{$invoiceLines}.*.price_total" => 'nullable|between:0,99999.99999',
            "{$invoiceLines}.*.invoice_start" => "$lineInvoiceStartIsNullable|date_format:Y-m-d|greater_than:$nowafter3yrs|less_than:$nowbefore30yrs",
            "{$invoiceLines}.*.invoice_stop" => $lineInvoiceStopValidation,
        ];
    }

    public function messages(): array
    {
        $invoiceLines = "sales_invoice_lines";
        return [
            'date.required' => 'Invalid period (invoice date is required)',
            'date.greater_than' => 'Invalid period (invoice date must not be more than 3 years in the future)',
            'date.less_than' => 'Invalid period (invoice date must not be less than 30 years in the past)',
            'due_date.afterOrEqual' => 'Invalid period (due date must occur after invoice date)',
            "$invoiceLines.*.invoice_start.required" => 'Invalid period (invoice start is required)',
            "$invoiceLines.*.invoice_start.greater_than" => 'Invalid period (invoice start must not be more than 3 years in the future)',
            "$invoiceLines.*.invoice_start.less_than" => 'Invalid period (invoice start must not be less than 30 years in the past)',
        ];
    }
}
