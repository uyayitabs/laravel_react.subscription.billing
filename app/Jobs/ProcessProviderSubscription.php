<?php

namespace App\Jobs;

use Logging;
use App\Services\BrightBlueService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\SubscriptionService;
use App\Services\M7Service;
use Illuminate\Support\Str;

class ProcessProviderSubscription implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $m7Service;
    protected $brightBlueService;
    protected $subscriptions;
    protected $backend_api;
    protected $transaction;
    protected $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($backend_api, $limit, $transaction = null, $status = 'all')
    {
        $this->backend_api = $backend_api;
        $this->transaction = $transaction;
        $subscriptionService = new SubscriptionService();

        $this->m7Service = new M7Service();
        $this->brightBlueService = new BrightBlueService();
        $this->subscriptions = ('deprovisioning' == $this->transaction ?
            $subscriptionService->getProviderSubscriptions($backend_api) :
            $subscriptionService->getProviderSubscriptions(
                $backend_api,
                $transaction,
                $status,
                $limit
            ));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscriptions = $this->subscriptions;
        foreach ($subscriptions as $index => $subscription) {
            if ('m7' == $this->backend_api) {
                $enable_cron_m7_provisioning = config('app.enable_cron_m7_provisioning');
                if (!$enable_cron_m7_provisioning) {
                    break;
                }
                if ('failed' == strtolower($subscription->m7_provisioning_status)) {
                    break;
                }

                $m7Service = $this->m7Service;
                $m7_has_provisioning = $subscription->m7_has_provisioning;
                $m7_has_deprovisioning = $subscription->m7_has_deprovisioning;
                $m7_provisionable = $subscription->m7_provisionable;

                if (
                    !$m7_has_provisioning['hasNewStb'] &&
                    !$m7_has_provisioning['hasNewBasis'] &&
                    !$m7_has_deprovisioning['hasDeProStb'] &&
                    !$m7_has_deprovisioning['hasDeProBas'] &&
                    !$m7_provisionable['completeSerial']
                ) {
                    continue;
                }

                $isValid = false;
                $method = '';

                if ($subscription->m7_provisioned) {
                    if (
                        $m7_has_deprovisioning['hasDeProStb'] &&
                        !$m7_has_deprovisioning['hasDeProStbMain']
                    ) {
                        $method = 'CloseAccount';
                        $isValid = true;
                    } elseif (
                        ($m7_has_deprovisioning['hasDeProProd'] && !$m7_has_deprovisioning['hasDeProBas']) ||
                        ($m7_has_deprovisioning['hasDeProBas'] && $m7_has_provisioning['hasNewBasis']) ||
                        $m7_has_provisioning['hasNewProd']
                    ) {
                        $method = 'ChangePackage';
                        $isValid = true;
                    } elseif ($m7_has_deprovisioning['hasDeProBas'] && $m7_has_provisioning['hasNewBasis']) {
                        $method = 'ChangePackage';
                        $isValid = true;
                    } elseif ($m7_has_deprovisioning['hasDeProBas'] && !$m7_has_provisioning['hasNewBasis']) {
                        $method = 'CloseAccount';
                        $isValid = true;
                    } elseif ($m7_has_provisioning['hasNewStb']) {
                        $stbLines = $subscription->m7_stb_lines;
                        foreach ($stbLines as $stbLine) {
                            $jsonDataM7 = $stbLine->json_data_m7;
                            if ($jsonDataM7 || !$stbLine->serial || $stbLine->is_stoped || !$stbLine->is_started) {
                                continue;
                            }
                            $m7Service->processSingleProvision($stbLine);
                        }

                        continue;
                    }

                    if ($isValid) {
                        Logging::information(
                            'M7 ' . $method,
                            $subscription,
                            16,
                            1,
                            $subscription->relation->tenant_id,
                            'subscription',
                            $subscription->id
                        );
                        $m7Service->setSubscription($subscription);
                        $m7Service->manager($method);

                        if ($m7_has_provisioning['hasNewStb']) {
                            Logging::information(
                                'M7 CaptureSubscriber',
                                $subscription,
                                16,
                                1,
                                $subscription->relation->tenant_id,
                                'subscription',
                                $subscription->id
                            );
                            $m7Service->manager('CaptureSubscriber');
                        }
                    }
                } else {
                    if ($m7_has_provisioning['hasNewBasis'] && $m7_has_provisioning['hasNewStb']) {
                        $method = 'CaptureSubscriber';
                        $isValid = true;
                    }

                    if ($isValid) {
                        Logging::information(
                            'M7 ' . $method,
                            $subscription,
                            16,
                            1,
                            $subscription->relation->tenant_id,
                            'subscription',
                            $subscription->id
                        );
                        $m7Service->setSubscription($subscription);
                        $m7Service->manager($method);
                    }
                }
            }

            if ('brightblue' === $this->backend_api) {
                $relation = $subscription->relation;
                $tenant = $relation->tenant;
                $provisioningPerson = $subscription->person_provisioning;
                $sluggedTenantName = $tenant->slugged_name;

                $subscriptionBrightblueJsonData = $subscription->jsonDatas()
                    ->where('backend_api', 'brightblue')
                    ->first();

                $subscriptionLine = $subscription->subscription_line_brightblue;
                $subscriptionLineJsonData = $subscriptionLine->line_json_data_brightblue;
                $provisionThis = false;

                if (!empty($subscriptionLineJsonData)) {
                    $json = $subscriptionLineJsonData->json_data;
                    if (!empty($json)) {
                        $brightBlueData = $json["brightblue"][$sluggedTenantName];
                        // If $provisioningPerson->email is not yet provisioned
                        if (Str::contains($brightBlueData["user"]["name"], [$provisioningPerson->email]) === false) {
                            $provisionThis = true;
                        } else { // Otherwise
                            $provisionThis = false;
                        }
                    }
                } else { // Otherwise, new provisioning
                    $provisionThis = true;
                }

                if ($provisionThis) {
                    $this->brightBlueService->setSubscription($subscription);
                    $this->brightBlueService->setSubscriptionBrightblueJsonData($subscriptionBrightblueJsonData);
                    $this->brightBlueService->setSubscriptionLine($subscriptionLine);
                    $this->brightBlueService->setSubscriptionLineJsonData($subscriptionLineJsonData);

                    $this->brightBlueService->setParams([
                        'description' => "[{$relation->customer_number}] {$provisioningPerson->full_name}",
                        'primaryUserName' => "{$provisioningPerson->email}",
                        'primaryUserPin' => 1234, // rand(1000,9999)
                    ]);
                    $this->brightBlueService->manager('CreateAccount');
                }
            }
        }
    }
}
