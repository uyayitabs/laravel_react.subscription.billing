<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class SubscriptionResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $list = false)
    {
        parent::__construct($resource);
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
        $jsonDatas = null;
        if ($this->jsonDatas()->count()) {
            $jsonDatas = $this->json_datas;
        }

        $relation = null;
        if ($this->relation) {
            $relation = [
                'id' => $this->relation->id,
                'customer_number' => $this->relation->customer_number,
                'customer_name' => $this->person_billing ? $this->person_billing->full_name : null
            ];
        }

        $plan = null;
        if ($this->plan) {
            $plan = [
                'id' => $this->plan ? $this->plan->id : null,
                'description' => $this->plan ? $this->plan->description : null
            ];
        }

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
            'relation' => $relation,
            'plan' => $plan,
            'subscription_start' => dateFormat($this->subscription_start),
            'subscription_stop' => dateFormat($this->subscription_stop),
            'status' => $this->status,
            'price_excl_vat' => $this->price_excl_vat,
            'price_incl_vat' => $this->price_incl_vat,
            'subscription_status' => $subscriptionStatus,
            'wish_date' => dateFormat($this->wish_date),
            'json_datas' => $jsonDatas
        ];

        if ($this->list) {
            return $item;
        }

        $item['subscription_no'] = $this->subscription_no;
        $item['description_long'] = $this->description_long;
        $item['billing_start'] = dateFormat($this->billing_start);
        $item['relation_id'] = $this->relation_id;
        $item['plan_id'] = $this->plan_id;
        $item['billing_person'] = $this->billing_person;
        $item['provisioning_person'] = $this->provisioning_person;
        $item['billing_address'] = $this->billing_address;
        $item['provisioning_address'] = $this->provisioning_address;
        $item['type'] = $this->type;
        $item['contract_period_id'] = $this->contract_period_id;
        $item['nrc_total'] = $this->sum_nrc_excl_vat;
        $item['nrc_total_inc_vat'] = $this->sum_nrc_incl_vat;
        $item['mrc_total'] = $this->sum_mrc_excl_vat;
        $item['mrc_total_inc_vat'] = $this->sum_mrc_incl_vat;
        $item['qrc_total'] = $this->sum_qrc_excl_vat;
        $item['qrc_total_inc_vat'] = $this->sum_qrc_incl_vat;
        $item['yrc_total'] = $this->sum_yrc_excl_vat;
        $item['yrc_total_inc_vat'] = $this->sum_yrc_incl_vat;
        $item['deposit_total'] = $this->sum_deposit_excl_vat;
        $item['deposit_total_inc_vat'] = $this->sum_deposit_incl_vat;
        $item['json_datas'] = $jsonDatas;
        $item['line_count_no_stop'] = $this->line_count_no_subscription_stop;
        $item['line_count_change_start'] = $this->line_count_change_subscription_start;
        $item['relation'] = $relation;
        $item['is_invoiced'] = $this->invoice_count > 0;
        return $item;
    }
}
