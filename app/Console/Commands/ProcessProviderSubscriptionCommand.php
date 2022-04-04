<?php

namespace App\Console\Commands;

use Logging;
use Illuminate\Console\Command;
use App\Jobs\ProcessProviderSubscription;

class ProcessProviderSubscriptionCommand extends Command
{
    protected $signature = 'provider:process_subscription {backend_api} {transaction} {status} {limit}';

    protected $description = 'Process provider subscription @backend_api @transaction @status @limit';

    public function handle(): void
    {
        $backend_api = $this->argument('backend_api');
        $transaction = $this->argument('transaction');
        $status = $this->argument('status');
        $limit = $this->argument('limit');
        Logging::information(
            'ProcessProviderSubscriptionCommand',
            [
                'backend_api' => $backend_api,
                'transaction' => $transaction,
                'status' => $status,
                'limit' => $limit
            ],
            16,
            1
        );
        ProcessProviderSubscription::dispatchNow($backend_api, $limit, $transaction, $status);
    }
}
