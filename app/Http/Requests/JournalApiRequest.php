<?php

namespace App\Http\Requests;

class JournalApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            "tenant_id" => $this->requiredOrNullable . "|integer|exists:tenants,id",
            "invoice_id" => $this->requiredOrNullable . "|integer|exists:invoices,id",
            "journal_no" => "nullable|string|min:1|max:191",
            "description" =>  "nullable|string|min:1|max:190",
            "date" => "nullable|date_format:Y-m-d",
        ];
    }
}
