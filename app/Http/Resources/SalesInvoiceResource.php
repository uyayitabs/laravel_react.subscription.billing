<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class SalesInvoiceResource extends JsonResource
{
    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $message = null, $success = null, $list = false)
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
        $salesInvoiceMeta = $this->salesInvoiceMetas()->where('key', 'reminder_status')->first();
        $reminder = null;
        if ($salesInvoiceMeta) {
            $reminder = [
                'reminder_status' => $salesInvoiceMeta->value
            ];
        }

        $relation = null;
        if ($this->relation) {
            $relation['id'] = $this->relation->id;
            $relation['customer_number'] = $this->relation->customer_number;
            $relation['customer_name'] = $this->relation_primary_person;
            $relation['vat_no'] = $this->relation->vat_no;
        }

        if ($this->list) {
            return [
                'id' => $this->id,
                'invoice_no' => $this->invoice_no,
                'date' => dateFormat($this->date),
                'relation_company_name' => $this->relation_company_name,
                'relation_customer_number' => $this->relation_customer_number,
                'relation_primary_person' => $this->relation_primary_person,
                'relation_primary_address' => $this->relation_primary_address,
                'relation_primary_person_email' => $this->relation_primary_person_email,
                'price' => $this->price,
                'price_vat' => $this->price_vat,
                'price_total' => $this->price_total,
                'status_invoice' => $this->status,
                'reminder' => $reminder,
                'has_gadget' => $this->has_gadget,
                'relation' => $relation,
            ];
        }

        $tenant = null;
        if ($this->tenant) {
            foreach ($this->tenant->paymentConditions()->get() as $paymentCondition) {
                $tenant['payment_conditions'][] = [
                    'id' => $paymentCondition->id,
                    'description' => $paymentCondition->description,
                    'net_days' => $paymentCondition->net_days
                ];
            }
        }

        $invoiceStatus = null;
        if ($this->status) {
            $invoiceStatus['id'] = $this->status->id;
            $invoiceStatus['status'] = $this->status->status;
        }

        if ($salesInvoiceMeta) {
            $reminder['date'] = dateFormat($salesInvoiceMeta->updated_at);
        }

        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'date' => dateFormat($this->date),
            'description' => $this->description,
            'tenant_id' => $this->tenant_id,
            'relation_id' => $this->relation_id,
            'price' => $this->price,
            'price_vat' => $this->price_vat,
            'price_total' => $this->price_total,
            'due_date' => dateFormat($this->due_date),
            'billing_person_id' => $this->invoice_person_id,
            'billing_person' => $this->invoicePerson,
            'shipping_person_id' => $this->shipping_person_id,
            'shipping_person' => $this->shippingPerson,
            'billing_address_id' => $this->invoice_address_id,
            'billing_address' => $this->invoiceAddress,
            'shipping_address_id' => $this->shipping_address_id,
            'shipping_address' => $this->shippingAddress,
            'invoice_status' => $this->invoice_status,
            'payment_status' => $this->payment_status,
            'payment_condition_id' => $this->payment_condition_id,
            'inv_output_type' => $this->inv_output_type,
            'billing_run_id' => $this->billing_run_id,

            // attributes
            'relation_company_name' => $this->relation_company_name,
            'relation_customer_number' => $this->relation_customer_number,
            'relation_primary_person' => $this->relation_primary_person,
            'relation_primary_address' => $this->relation_primary_address,
            'relation_primary_person_email' => $this->relation_primary_person_email,
            'is_updatable' => $this->is_updatable,
            'vat_percentage' => $this->vat_percentage,
            'invoice_filename' => $this->invoice_filename,
            'invoice_file_exists' => $this->invoice_file_exists,

            // relation
            'relation' => $relation,

            // statusInvoice
            'status_invoice' => $invoiceStatus,

            // tenant
            'tenant' => $tenant,
            'reminder' => $reminder
        ];
    }
}
