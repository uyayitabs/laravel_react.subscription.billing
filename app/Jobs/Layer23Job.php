<?php

namespace App\Jobs;

use App\Services\Layer23Service;
use App\Services\StatusService;
use App\Services\SubscriptionService;
use App\Models\SubscriptionLine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Layer23Job implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    const CONNECTION = 'connection';

    protected $subscription;
    protected $subscriptionLine;
    protected $layer23Service;
    protected $statusService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscription, $subscriptionLine)
    {
        $this->subscription = $subscription;
        $this->subscriptionLine = $subscriptionLine;
        $this->layer23Service = new Layer23Service();
        $this->statusService = new StatusService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->layer23Service->setSubscription($this->subscription);
        $this->layer23Service->setSubscriptionLine($this->subscriptionLine);

        $isCheckPendingStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'check_pending'));
        $isMigrationConfirmedStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'migration_confirmed'));
        $isMigrationPendingStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'migration_pending'));
        $isOrderPendingStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'order_pending'));
        $isCancelPendingStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'cancel_pending'));
        $isPendingTerminationStatus = ($this->subscriptionLine->status_id == $this->statusService->getStatusId('connection', 'pending_termination'));

        if ($isCheckPendingStatus) {
            $this->layer23Service->processProvisioning($this->subscription, $this->subscriptionLine);
        } elseif ($isMigrationConfirmedStatus) {
            $this->layer23Service->processMigration($this->subscription, $this->subscriptionLine);
        } elseif ($isMigrationPendingStatus) {
            $this->layer23Service->checkMigrationStatus($this->subscription, $this->subscriptionLine);
        } elseif ($isOrderPendingStatus) {
            $this->layer23Service->checkOrderStatus($this->subscription, $this->subscriptionLine);
        } elseif ($isCancelPendingStatus) {
            $this->layer23Service->cancelOrderMigration($this->subscription, $this->subscriptionLine);
        } elseif ($isPendingTerminationStatus) {
            $this->layer23Service->processDeprovisioning($this->subscription, $this->subscriptionLine);
        }
    }
}
