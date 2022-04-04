<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProvisioningSubscriptionCountResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'total_new' => $this->total_new,
            'total_provisioning' => $this->total_provisioning,
            'total_failed' => $this->total_failed,
            'total_active' => $this->total_active,
        ];
    }
}
