<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PaymentInvoiceResource extends JsonResource
{
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

        if ($this->list) {
            return [
                'id' => $this->id,
                'invoice_no' => $this->invoice_no,
                'date' => dateFormat($this->date),
                'price_total' => $this->price_total
            ];
        }

        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'date' => dateFormat($this->date),
            'price_total' => $this->price_total
        ];
    }
}
