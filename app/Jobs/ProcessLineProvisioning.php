<?php

namespace App\Jobs;

use Logging;
use App\Services\StatusService;
use App\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessLineProvisioning implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    const BACKEND_API = 'lineProvisioning';
    const PROVISIONING_STATUS_ID = 20;

    protected $statusService;
    protected $subscriptionService;
    protected $subscriptions;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
        $this->statusService = new StatusService();
        $this->subscriptions = $this->subscriptionService->getProviderSubscriptions($this::BACKEND_API, null, 0, null, null);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->subscriptions as $subscription) {
            $subscriptionLine = $subscription->getProvisioningLine($this::BACKEND_API);

            $lineProvisioningPendingStatusIds = [
                $this->statusService->getStatusId('connection', 'check_pending'),
                $this->statusService->getStatusId('connection', 'cancel_pending'),
                $this->statusService->getStatusId('connection', 'order_pending'),
                $this->statusService->getStatusId('connection', 'migration_pending'),
                $this->statusService->getStatusId('connection', 'migration_confirmed'),
                $this->statusService->getStatusId('connection', 'pending_termination'),
            ];
            $statusNeedsProcessing = in_array($subscriptionLine->status_id, $lineProvisioningPendingStatusIds);

            if ($subscriptionLine && $statusNeedsProcessing) {
                $operator = $subscriptionLine->getSubscriptionLineOperator();
                if ($operator) {
                    // E.g.
                    // $operator->provisioning_api = layer23
                    // equivalent job class file  = \App\Jobs\Layer23Job.php
                    // ----------
                    // $operator->provisioning_api = brightblue
                    // equivalent job class file = \App\Jobs\BrightblueJob.php
                    $jobClassName = "\App\Jobs\\" . ucwords($operator->provisioning_api) . "Job"; // no need to add .php
                    if (class_exists($jobClassName)) {
                        dispatch(new $jobClassName($subscription, $subscriptionLine));
                    } else {
                        Logging::error(
                            'Job class not found',
                            [
                                'job_class' => $jobClassName,
                                'operator_id' => $operator->id,
                                'subscription_id' => $subscription->id,
                                'subscription_line_id' => $subscriptionLine->id,
                            ],
                            19,
                            1,
                            $subscription->relation->tenant_id
                        );
                    }
                }
            }
        }
    }
}
