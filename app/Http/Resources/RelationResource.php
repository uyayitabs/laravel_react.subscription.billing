<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class RelationResource extends JsonResource
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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $relation = [];
        $primaryPerson = [];
        if ($this->primaryPerson() && $this->primaryPerson()->count()) {
            $relation = [
                'id' => $this->id,
                'customer_number' => $this->customer_number,
                'customer_name' => $this->primary_person_full_name
            ];
            foreach ($this->primaryPerson()->get() as $person) {
                $primaryPerson[] = [
                    'id' => $person->id,
                    'full_name' => $person->full_name,
                    'primary' => $person->primary
                ];
            }
        }

        $primaryAddress = [];
        if ($this->primaryAddress() && $this->primaryAddress()->count()) {
            foreach ($this->primaryAddress()->get() as $address) {
                $primaryAddress[] = [
                    'id' => $address->id,
                    'address_type_id' => $address->address_type_id,
                    'full_address' => $address->full_address,
                ];
            }
        }

        $subscription = null;
        if ($this->subscriptions()->count()) {
            $relationSubscription = $this->subscriptions()
                ->orderBy('id', 'DESC')
                ->first();

            if ($relationSubscription) {
                $subscription = [
                    'id' => $relationSubscription->id,
                    'status' => !blank($relationSubscription->status) ? (int)$relationSubscription->status : 0,
                    'description' => $relationSubscription->description,
                ];
            }
        }

        $lastInvoice = null;
        if ($this->salesInvoices()->count()) {
            $salesInvoice = $this->salesInvoices()
                ->orderBy('date', 'DESC')
                ->with('status')
                ->first();

            if ($salesInvoice->status->id !== 0) {
                $lastInvoice = [
                    'invoice_no' => $salesInvoice->invoice_no,
                    'date' => dateFormat($salesInvoice->date),
                    'price' => $salesInvoice->price,
                    'price_vat' => $salesInvoice->price_vat,
                    'price_total' => $salesInvoice->price_total
                ];
            }
        }

        if ($this->list) {
            return [
                'id' => $this->id,
                'customer_number' => $this->customer_number,
                'company_name' => $this->company_name,
                'email' => $this->email,

                // primary_person
                'primary_person' => $primaryPerson,

                // primary_address
                'primary_address' => $primaryAddress,

                // subscription
                'subscription' => $subscription,

                // last_invoice
                'last_invoice' => $lastInvoice,
            ];
        }

        $tenant = [];
        if ($this->tenant) {
            $paymentConditions = [];
            foreach ($this->tenant->paymentConditions()->get() as $paymentCondition) {
                $paymentConditions[] = [
                    'id' => $paymentCondition->id,
                    'description' => $paymentCondition->description,
                    'net_days' => $paymentCondition->net_days
                ];
            }

            $tenant = [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
                'payment_conditions' => $paymentConditions
            ];
        }

        $paymentCondition = $this->paymentCondition()->first();
        $pc = [];
        if ($paymentCondition) {
            $pc = [
                'id' => $paymentCondition->id,
                'description' => $paymentCondition->description,
                'net_days' => $paymentCondition->net_days
            ];
        }

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'bank_account' => $this->bank_account,
            'bic' => $this->bic,
            'credit_limit' => $this->credit_limit,
            'customer_number' => $this->customer_number,
            'email' => $this->email,
            'fax' => $this->fax,
            'kvk' => $this->kvk,
            'payment_condition_id' => $this->payment_condition_id,
            'payment_condition' => $pc,
            'phone' => $this->phone,
            'relation_type_id' => $this->relation_type_id,
            'status' => !blank($this->status) ? $this->status : null,
            'vat_no' => $this->vat_no,
            'website' => $this->website,
            'company_name' => $this->company_name,
            'is_business' => $this->is_business,
            'type' => $this->type,
            'inv_output_type' => $this->inv_output_type,


            // attributes
            'customer_email' => $this->customer_email,
            'primary_person_full_name' => $this->primary_person_full_name,
            'billing_address' => $this->billing_address,
            'iban' => $this->iban,

            // primary_person
            'primary_person' => $primaryPerson,

            // primary_address
            'primary_address' => $primaryAddress,

            // tenant
            'tenant' => $tenant,

            // relation
            'relation' => $relation,
        ];
    }
}
