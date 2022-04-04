<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PortalSubscriptionResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $message, $success, $list = false)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->success = $success;
        $this->list = $list;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $relation = $this->relation;

        $billingAddress1 = $relation->billing_address_1;
        $billingAddress2 = $relation->billing_address_2;

        $subscriptionStatus = null;
        if ($this->subscription_status) {
            $thisSubscriptionStatus = $this->subscription_status;
            $subscriptionStatus = [
                'id' => $thisSubscriptionStatus->id,
                'label' => $thisSubscriptionStatus->label,
            ];
        }

        $item = [
            'id' => $this->id,
            'description' => $this->description,
            'start_date' => dateFormat($this->subscription_start),
            'end_date' =>  dateFormat($this->subscription_stop),
            'billing_address' => [
                'full_address1' => $billingAddress1,
                'full_address2' => $billingAddress2,
            ],
            'status' => $subscriptionStatus
        ];

        $recurringLines = $oneOffLines = [];
        $recurringTotalExclVat = $recurringTotalIncVat = 0;
        $oneOffTotalExclVat = $oneOffTotalIncVat = 0;

        foreach ($this->recurringLines()->get() as $subscriptionLine) {
            $subscriptionLinePrice = $subscriptionLine->subscriptionLinePrices()->first();
            $priceExclVat = $priceInclVat = 0;
            if ($subscriptionLinePrice) {
                $priceExclVat = $subscriptionLinePrice->price_excl_vat;
                $priceInclVat = $subscriptionLinePrice->price_incl_vat;

                $recurringTotalExclVat += $priceExclVat;
                $recurringTotalIncVat += $priceInclVat;
            }
            $recurringLines[] = [
                'id' => $subscriptionLine->id,
                'description' => $subscriptionLine->description,
                'start_date' => dateFormat($this->subscription_start),
                'end_date' => dateFormat($this->subscription_stop),
                'price_excl_vat' => $priceExclVat,
                'price_incl_vat' => $priceInclVat,
            ];
        }

        $item['total_price_inc_vat'] = $recurringTotalIncVat + $oneOffTotalIncVat;
        $item['total_price_excl_vat'] = $recurringTotalExclVat + $oneOffTotalExclVat;

        foreach ($this->oneOffLines()->get() as $subscriptionLine) {
            $subscriptionLinePrice = $subscriptionLine->subscriptionLinePrices()->first();
            $priceExclVat = $priceInclVat = 0;
            if ($subscriptionLinePrice) {
                $priceExclVat = $subscriptionLinePrice->price_excl_vat;
                $priceInclVat = $subscriptionLinePrice->price_incl_vat;

                $oneOffTotalExclVat += $priceExclVat;
                $oneOffTotalIncVat += $priceInclVat;
            }
            $oneOffLines[] = [
                'id' => $subscriptionLine->id,
                'description' => $subscriptionLine->description,
                'start_date' => dateFormat($this->subscription_start),
                'end_date' => dateFormat($this->subscription_stop),
                'price_excl_vat' => $priceExclVat,
                'price_incl_vat' => $priceInclVat,
            ];
        }
        $item['subscription_lines']['recurring']['items'] = $recurringLines;
        $item['subscription_lines']['recurring']['total_price_excl_vat'] = $recurringTotalExclVat;
        $item['subscription_lines']['recurring']['total_price_inc_vat'] = $recurringTotalIncVat;

        $item['subscription_lines']['one_off']['items'] = $oneOffLines;
        $item['subscription_lines']['one_off']['total_price_excl_vat'] = $oneOffTotalExclVat;
        $item['subscription_lines']['one_off']['total_price_inc_vat'] = $oneOffTotalIncVat;
        return $item;
    }
}
