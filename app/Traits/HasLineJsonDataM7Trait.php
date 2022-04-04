<?php

namespace App\Traits;

use App\Models\City;
use Carbon\Carbon;

trait HasLineJsonDataM7Trait
{
    public function getJsonDataM7Attribute()
    {
        return $this->jsonDatas()->where('backend_api', 'm7')->first();
    }

    public function getMacAddressAttribute()
    {
        $serial = $this->serialItem;
        return $serial ? $serial->json_data['serial']['mac'] : '';
    }

    public function getIsStopedAttribute()
    {
        $subscription_stop = $this->getOriginal('subscription_stop');
        $now = Carbon::now();
        if (
            ($subscription_stop && $subscription_stop->format('Y-m-d') < $now->format('Y-m-d')) ||
            Carbon::parse($subscription_stop)->format('Y-m-d 23:45:00') <= $now->format('Y-m-d H:m:s')
        ) {
            return true;
        }
        if (
            $subscription_stop &&
            'Provisioning' == $this->m7_provisioning_status &&
            $subscription_stop->format('Y-m-d') <= $now->format('Y-m-d')
        ) {
            return true;
        }
        $jsonData = $this->json_data_m7;
        if ($jsonData) {
            $json_data = $jsonData->json_data;
            $json_dataM7 = isset($json_data['m7']) ?  $json_data['m7'] : null;
            if ($json_dataM7 && isset($json_dataM7['state']) && 'Deprovisioned' == $json_dataM7['state']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get m7 provisioning status
     * @return string
     */
    public function getM7ProvisioningStatusAttribute()
    {
        $jsonData = $this->json_data_m7;
        if (!$jsonData) {
            return 'Provisioning';
        }
        if (
            array_key_exists('m7', $jsonData->json_data) &&
            array_key_exists('status', $jsonData->json_data['m7'])
        ) {
            return $jsonData->json_data['m7']['status'];
        }
        return 'Provisioned';
    }

    /**
     * Get m7 if deprovisioned
     * @return boolean
     */
    public function getM7DeprovisionedAttribute()
    {
        $jsonData = $this->json_data_m7;
        return $jsonData &&
            array_key_exists('m7', $jsonData->json_data) &&
            array_key_exists('status', $jsonData->json_data['m7']) &&
            'Deprovisioned' == $jsonData->json_data['m7']['status'];
    }
}
