<?php

namespace App\Http\Requests;

class EntryApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            "entry_no" => "nullable|string|min:1|max:191",
            "date" => $this->requiredOrNullable . "|date_format:Y-m-d",
            "description" =>  "nullable|string|min:1|max:190",
            "journal_id" => $this->requiredOrNullable . "|integer|exists:journals,id",
            "relation_id" => "nullable|integer|exists:relations,id",
            "invoice_id" => "nullable|integer|exists:sales_invoices,id",
            "invoice_line_id" => "nullable|integer|exists:sales_invoice_lines,id",
            "account_id" => "nullable|integer|exists:accounts,id",
            "period_id" => "nullable|integer|exists:accounting_periods,id",
            "credit" => "nullable|regex:/^-?\d+(\.\d{1,})?$/",
            "debit" => "nullable|regex:/^-?\d+(\.\d{1,})?$/",
            "vatcode_id" => "nullable|integer|exists:vat_codes,id",
            "vat_percentage" => "nullable|between:0,99.99",
            "vat_amount" => "nullable|regex:/^-?\d+(\.\d{1,})?$/",
        ];
    }

    public function messages(): array
    {
        return [
            "credit.regex" => "The value is invalid.",
            "debit.regex" => "The value is invalid.",
            "vat_amount.regex" => "The value is invalid.",
        ];
    }
}
