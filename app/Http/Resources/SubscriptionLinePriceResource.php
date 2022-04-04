<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class SubscriptionLinePriceResource extends JsonResource
{
    use ApiResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subscription_line_id' => $this->subscription_line_id,
            'parent_plan_line_id' => $this->parent_plan_line_id,
            'fixed_price' => $this->fixed_price,
            'margin' => $this->margin,
            'price_valid_from' => dateFormat($this->price_valid_from),
            'price_excl_vat' => $this->price_excl_vat,
            'price_incl_vat' => $this->price_incl_vat,
        ];
    }
}
