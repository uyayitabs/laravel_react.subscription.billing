<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PaymentInvoiceApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $paymentIdParam = request("payment_id");
        $invoiceIdParam = request("invoice_id");

        return [
            'payment_id' => [
                "required",
                'integer',
                Rule::exists("payments", "id")
                    ->where(
                        function ($query) use ($paymentIdParam) {
                            $query->where("id", $paymentIdParam);
                        }
                    )
            ],
            'invoice_id' => [
                "required",
                'integer',
                Rule::exists("sales_invoices", "id")
                    ->where(
                        function ($query) use ($invoiceIdParam) {
                            $query->where("id", $invoiceIdParam);
                        }
                    )
            ],
        ];
    }
}
