<?php

namespace App\Console\Commands;

use App\Jobs\StartFinalizingInvoices;
use Logging;
use App\Models\BillingRun;
use Illuminate\Console\Command;

class FinalizeInvoices extends Command
{
    protected $signature = 'invoice:finalize_invoices {--billing_run_id=} {--user_id=}';
    protected $description = 'Process invoice billing';

    public function handle(): void
    {
        $billingIdParam = $this->option('billing_run_id');
        $userId = $this->option('user_id');
        Logging::information(
            'Executing billingRun ' . $billingIdParam,
            [
                'billing_run_id' => $billingIdParam,
                'user_id' => $userId
            ],
            17,
            0
        );

        $billingRun = BillingRun::find($billingIdParam);

        if ($billingRun) {
            StartFinalizingInvoices::dispatchNow($billingRun->id, $userId);
        } else {
            Logging::warning('BillingRun ' . $billingIdParam . ' does not exist.', [], 17, 0);
        }
    }
}
