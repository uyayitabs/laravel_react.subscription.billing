<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Relation;
use App\Models\TenantBankAccount;
use Illuminate\Console\Command;
use Exception;
use Logging;

class LinkPayments extends Command
{
    protected $signature = 'payments:link_new_payments';

    protected $description = 'For all payments with status 0 (new), try linking to relation and sales_invoice.';

    public function handle(): void
    {
        $payments = Payment::where('status_id', 0)
            ->where('tenant_bank_account_id', '>', 0)
            ->get();
        foreach ($payments as $payment) {
            $iban = trim($payment->account_iban);
            $description = trim($payment->description);

            if (empty($iban) || empty($description)) {
                Logging::warning(
                    'Payment iban or description is empty',
                    [
                        'data' => $payment->id,
                        'error' => 'Payment iban:' . $iban . ' or description:'
                            . $description . ' is empty for ' . $payment->id
                    ],
                    9,
                    0
                );

                $payment->status_id = 10;
            }

            $relation = Relation::where('iban', $iban)->first();
            if (
                !$relation || $relation->tenant_id !== $payment
                ->tenantBankAccount()->
                first()
                ->tenant_id
            ) {
                continue;
            }
            $payment->relation_id = $relation->id;

            $invoices = $relation->salesInvoices()->where('invoice_status', 20)->get();
            foreach ($invoices as $invoice) {
                $pattern = '/(?<![a-zA-Z0-9])(' . $invoice->invoice_no . ')(?![a-zA-Z0-9])|^' . $invoice->invoice_no . '$/';
                if (preg_match($pattern, $description)) {
                    $payment->sales_invoice = $invoice;
                    $payment->status_id = 100;
                }
            }

            $payment->save();
        }
    }
}
