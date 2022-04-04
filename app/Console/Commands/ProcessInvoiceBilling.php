<?php

namespace App\Console\Commands;

use Logging;
use App\Models\BillingRun;
use App\Models\Tenant;
use App\Jobs\StartInvoicing;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ProcessInvoiceBilling extends Command
{
    protected $signature = 'invoice:process_billing {--billing_run_id=} {--user_id=}';
    protected $description = 'Process invoice billing';
    public function handle(): void
    {
        $billingIdParam = $this->option('billing_run_id');
        $userId = $this->option('user_id');
        Logging::debug(
            'Executing billingRun ' . $billingIdParam,
            [
                'billing_run_id' => $billingIdParam,
                'user_id' => $userId
            ],
            17,
            0
        );

        $billingRun = BillingRun::find($billingIdParam);
        $processingDate = $billingRun->date;
        $tenantId = $billingRun->tenant_id;
        if ($tenantId) {
            $tenantIds = [$tenantId];
        } else {
            $tenantIds = Tenant::getIdsInvoicingToday($processingDate->day);
        }
        $subscriptionIds = Subscription::getSubscriptionIdsForTenants($tenantIds, $processingDate);
        Logging::debug(
            'BillingRun: ' . $billingIdParam . ' found ' . count($subscriptionIds),
            [
                'billing_run_id' => $billingRun->id,
                'tenant_ids' => $tenantIds
            ],
            17,
            0
        );

        if (count($subscriptionIds) > 0) {
            StartInvoicing::dispatchNow(
                $subscriptionIds,
                $processingDate->format("Y-m-d"),
                $billingIdParam,
                $userId
            );
        }
    }
}
