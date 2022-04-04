<?php

namespace App\Http\Resources;

use App\Services\StatusService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;
use Carbon\Carbon;

class ProvisioningSubscriptionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $statusService = new StatusService();
        $relation = $this;
        $subscription = $relation->subscriptions()->first();

        $provisioningAddress = $subscription->address_provisioning;
        $customerAddress1 = "{$provisioningAddress->street1} {$provisioningAddress->house_number}";
        if (!blank($provisioningAddress->house_number_suffix)) {
            $customerAddress1 .= "-{$provisioningAddress->house_number_suffix}";
        }
        $customerAddress1 .= " {$provisioningAddress->room}";
        $customerAddress2 = "{$provisioningAddress->zipcode} {$provisioningAddress->city_name}";

        $subscriptionLine = $subscription->getProvisioningLine('lineProvisioning');
        $dateStarted = $dateFinished = $lineStatus = $failedErrorMessage = '';
        $operator = $subscriptionLineId = $provisioningGadget = null;
        $status = "NEW";
        if ($subscriptionLine) {
            $provisioningGadget = $subscriptionLine->provisioning_gadget;
            $subscriptionLineId = $subscriptionLine->id;
            $lineNetworkOperator = $subscriptionLine->getSubscriptionLineOperator();
            $operator = $lineNetworkOperator->name;
            $dateStarted = dateFormat($subscriptionLine->created_at);


            if (!blank($subscriptionLine->status_id)) {
                $statusObj = $statusService->getStatusByTypeAndId('connection', $subscriptionLine->status_id);
                $status = $statusObj->label;
                $lineStatus = $statusObj->status;

                if ($subscriptionLine->status_id == $statusService->getStatusId('connection', 'active')) {
                    $dateFinished = dateFormat($subscriptionLine->updated_at);
                } elseif ($subscriptionLine->status_id == $statusService->getStatusId('connection', 'failed')) {
                    $failureReasonData = $subscriptionLine->subscriptionLineMeta()
                        ->where('key', 'eos_failure_reason')
                        ->first();

                    if ($failureReasonData && $failureReasonData->value) {
                        $failedErrorMessage = $failureReasonData->value;
                    }
                }
            }
        }

        return [
            'subscription_id' => $subscription->id,
            'subscription_line_id' => $subscriptionLineId,
            'subscription_line_status' => $lineStatus,
            'relation_id' => $relation->id,
            'provisioning_address' => ['address1' => $customerAddress1, 'address2' => $customerAddress2],
            'customer_number' => $relation->customer_number,
            'customer' => $relation->primary_person_full_name,
            'network_operator' => $operator,
            'status' => strtoupper($status),
            'gadget' => $provisioningGadget,
            'date_started' => $dateStarted,
            'date_finished' => $dateFinished,
            'failed_error_message' => $failedErrorMessage,
        ];
    }
}
