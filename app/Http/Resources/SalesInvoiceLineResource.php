<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesInvoiceLineResource extends JsonResource
{
    protected $message = "Successfully received sales invoice line(s)";
    protected $status;

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
            'sales_invoice_id' => $this->sales_invoice_id,
            'product_id' => $this->product_id,
            'order_line_id' => $this->order_line_id,
            'description' => $this->description,
            'description_long' => $this->description_long,
            'price_per_piece' => $this->price_per_piece,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'vat_code' => $this->vat_code,
            'vat_percentage' => $this->vat_percentage,
            'price_vat' => $this->price_vat,
            'price_total' => $this->price_total,
            'subscription_line_id' => $this->subscription_line_id,
            'sales_invoice_line_type' => $this->sales_invoice_line_type,
            'plan_line_id' => $this->plan_line_id,
            'invoice_start' => dateFormat($this->invoice_start),
            'invoice_stop' => dateFormat($this->invoice_stop),

            'line_start_date' => dateFormat($this->line_start_date),
            'line_stop_date' => dateFormat($this->line_stop_date),
            'invoice_status' => $this->invoice_status,
            'has_gadget' => $this->has_gadget,
        ];
    }
}
