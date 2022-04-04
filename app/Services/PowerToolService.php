<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\JsonData;
use App\Services\M7Service;

class PowerToolService
{
    protected $m7Service, $subscription;

    public function __construct()
    {
        $this->m7Service = new M7Service();
    }

    public function validateSubscription($subscriptionId = null)
    {
        if (!$subscriptionId) {
            $subscriptionId = request('subscription_id');
        }
        $subscription = Subscription::find($subscriptionId);
        $this->subscription = $subscription;
        if (!$subscription) {
            return 21;
        }
        $hasJsonData = $subscription->jsonDatas()->exists();
        if (!$hasJsonData) {
            return 22;
        }
        return 20;
    }

    public function resetM7($subscriptionId = null)
    {
        if (!$subscriptionId) {
            $subscriptionId = request('subscription_id');
        }

        $validateSubscription = $this->validateSubscription($subscriptionId);
        if ($validateSubscription != 20) {
            return $validateSubscription;
        }

        $subscription = $this->subscription;
        JsonData::where([['subscription_id', $subscription->id], ['backend_api', '=', 'm7']])->delete();
        return 20;
    }

    public function closeAccount($customerNumber = null, $smartcardnumber = '')
    {
        if (!$customerNumber) {
            $customerNumber = request('customer_number');
        }
        if ($smartcardnumber == '' && request()->has('smartcard')) {
            $smartcardnumber = request('smartcard');
        }
        $m7Response = $this->m7Service->manualDeprovisionSmartcard($smartcardnumber, $customerNumber, false);
        return $m7Response;
    }

    public function fixSubscription($subscriptionId = null, $customerNumber = null, $email = null, $password = null)
    {
        if (!$subscriptionId) {
            $subscriptionId = request('subscription_id');
        }
        $validateSubscription = $this->validateSubscription($subscriptionId);
        if ($validateSubscription != 20) {
            return $validateSubscription;
        }
        if (!$customerNumber) {
            $customerNumber = request('customer_number');
        }
        if (!$email) {
            $email = request('email');
        }
        if (!$password) {
            $password = request('password');
        }
        if (!$customerNumber) {
            return 31;
        }
        if (!$email) {
            return 32;
        }
        if (!$password) {
            return 33;
        }
        $subscription = $this->subscription;
        $json_data = $subscription->json_data_m7;
        $jsonDatas = $json_data->json_data;
        $json_data_m7  = $jsonDatas['m7'];
        $json_data_m7['CustomerNumber'] = $customerNumber;
        $json_data_m7['status'] = 'Provisioned';
        $json_data_m7['account'] = [
            'Email' => $email,
            'Password' => $password
        ];
        $jsonDatas['m7'] = $json_data_m7;
        $json_data->json_data = $jsonDatas;
        $json_data->save();
        return 20;
    }
}
