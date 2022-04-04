<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PortalCustomerResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;
    protected $user;

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
        $user = $this;
        $person = $user->person;
        $relation = $person->relationsPerson->relation;

        $billingAddress1 = $relation->billing_address_1;
        $billingAddress2 = $relation->billing_address_2;

        $contactAddress1 = $relation->contact_address_1;
        $contactAddress2 = $relation->contact_address_2;

        $bankAccount = $relation->bankAccount()->first();

        return [
            'customer_id' => $relation->id,
            'customer_number' => $relation->customer_number,
            'email' => $person->customer_email,
            'phone' => $person->phone,
            'mobile' => $person->mobile,
            'first_name' => $person->first_name,
            'last_name' => $person->last_name,
            'full_name' => $person->full_name,

            'bank_account' => [
                'iban' => $relation->getIban(),
                'account_name' => $bankAccount->description
            ],

            'billing_address' => [
                'full_address1' => $billingAddress1,
                'full_address2' => $billingAddress2,
            ],
            'contact_address' => [
                'full_address1' => $contactAddress1,
                'full_address2' => $contactAddress2,
            ],
        ];
    }
}
