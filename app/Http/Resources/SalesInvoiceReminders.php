<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;
use Carbon\Carbon;

class SalesInvoiceReminders extends JsonResource
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
            'id' => $this->id,
            'relation_id' => $this->relation_id,
            'sales_invoice_id' => $this->sales_invoice_id,
            'customer_no' => $this->customer_number,
            'customer' => $this->full_name,
            'invoice' => $this->invoice_no,
            'date_time' => dateFormat($this->date),
            'status' => $this->value,
            'city' => $this->name,
            'price_total' => $this->price_total
        ];
    }
}
