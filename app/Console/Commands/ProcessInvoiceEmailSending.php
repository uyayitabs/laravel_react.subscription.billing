<?php

namespace App\Console\Commands;

use Logging;
use App\Jobs\SendInvoiceEmail;
use App\Models\BillingRun;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProcessInvoiceEmailSending extends Command
{
    protected $signature = 'invoice:send_emails
                                {--billing_run_id=}
                                {--date=YYYY-MM-DD}
                                {--tenant_id=}
                                {--subscription_id=*}
                                {--relation_id=*}';

    protected $description = 'Send invoice emails based on a paremeter date YYYY-MM-DD';

    public function handle(): void
    {
        $processingDate = now();
        $invoicingDateParam = $this->option('date');
        $tenantIdParam = $this->option('tenant_id');
        $billingIdParam = $this->option('billing_run_id');

        $subscriptionIdParams = array_map('intval', $this->option('subscription_id'));
        $subscriptionIdParamCount = count($subscriptionIdParams);

        $relationIdParams = array_map('intval', $this->option('relation_id'));
        $relationIdParamCount = count($relationIdParams);

        // billing_run_id param ONLY
        if (!empty($billingIdParam)) {
            $salesInvoices = SalesInvoice::where('billing_run_id', $billingIdParam)->get();
            $billingRun = BillingRun::find($billingIdParam);
        } else {
            if (!empty($invoicingDateParam)) {
                $processingDate = Carbon::parse($invoicingDateParam);
            }

            if ($subscriptionIdParamCount == 0 && $relationIdParamCount == 0) {
                if (empty($tenantIdParam)) {
                    $tenantIdParam = 7; //Default to Fiber NL tenant_id
                }
                $salesInvoices = SalesInvoice::where([
                    ['date', '=', $processingDate->format("Y-m-d")],
                    ['tenant_id', '=', $tenantIdParam]
                ])->get();
            } else {
                $relationIds = [];
                if ($subscriptionIdParamCount > 0) {
                    $relationIds = Subscription::whereIn("id", $subscriptionIdParams)
                        ->pluck("relation_id")
                        ->toArray();
                }

                if ($relationIdParamCount > 0) {
                    $relationIds = $relationIdParams;
                }

                $salesInvoices = SalesInvoice::where([
                    ['date', '=', $processingDate->format("Y-m-d")]
                ])->whereIn('relation_id', $relationIds)->get();
            }

            $billingRun = $salesInvoices->first()
                ->billingRun()->first();
        }

        // UPDATE billing_run status
        if ($billingRun) {
            // invoices_created
            Logging::information(
                'INVOICING - BILLING RUN (invoices_created)',
                [
                    "billing_run_id" => $billingRun->id,
                    "date" => $processingDate->format("Y-m-d"),
                ],
                17,
                1,
                $tenantIdParam
            );

            // sending_invoices
            $billingRun->update([
                'status_id' => 20
            ]);
            Logging::information(
                'INVOICING - BILLING RUN (sending_invoices)',
                [
                    "billing_run_id" => $billingRun->id,
                    "date" => $processingDate->format("Y-m-d"),
                ],
                17,
                1,
                $tenantIdParam
            );
        }

        foreach ($salesInvoices as $salesInvoice) {
            $relation = $salesInvoice->relation()->first();
            if (!empty($relation) && !empty($salesInvoice)) {
                if ($salesInvoice->inv_output_type == "email") {
                    SendInvoiceEmail::dispatchNow($salesInvoice->id);
                }
            }
        }
        if ($billingRun) {
            Logging::information(
                'INVOICING - BILLING RUN (invoices_sent)',
                [
                    "billing_run_id" => $billingRun->id,
                    "date" => $processingDate->format("Y-m-d"),
                ],
                17,
                1,
                $tenantIdParam
            );
            $billingRun->update([
                'status_id' => 22 // invoices_sent
            ]);
        }
    }
}
