<?php

namespace App\Console\Commands;

use App\Models\BillingRun;
use App\Jobs\CreatePainDirectDebitFileSendMail;
use App\Models\Tenant;
use App\Jobs\StartInvoicing;
use App\Services\BillingRunService;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Log;

class ProcessInvoiceCreatePainXMLFile extends Command
{
    protected $signature = 'invoice:create_pain_dd_xml {--billing_run_id=} {--user_id=}';

    protected $description = 'Create PAIN direct debit XML file of a billing run';

    public function handle(BillingRunService $service): void
    {
        $billingRunIdParam = $this->option('billing_run_id');
        $billingRun = BillingRun::findOrFail($billingRunIdParam);
        $userIdParam = $this->option('user_id');

        if ($billingRun && $userIdParam) {
            $service->generatePainDirectDebitXML($billingRun, $userIdParam);
        }
    }
}
