<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class RelationCsResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $subscription;

    public function __construct($resource, $message, $success, $subscription = null)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->success = $success;
        $this->subscription = $subscription;
    }

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
            'tenant_id' => $this->tenant_id,
            'bank_account' => $this->bank_account,
            'bic' => $this->bic,
            'credit_limit' => $this->credit_limit,
            'customer_number' => $this->customer_number,
            'email' => $this->email,
            'fax' => $this->fax,
            'info' => $this->info,
            'kvk' => $this->kvk,
            'payment_condition_id' => $this->payment_condition_id,
            'phone' => $this->phone,
            'relation_type_id' => $this->relation_type_id,
            'status' => $this->status,
            'vat_no' => $this->vat_no,
            'website' => $this->website,
            'company_name' => $this->company_name,
            'is_business' => $this->is_business,
            'type' => $this->type,
            'inv_output_type' => $this->inv_output_type,
            'iban' => $this->iban,
            'subscription_id' => $this->subscription ? $this->subscription->id : null
        ];
    }
}
