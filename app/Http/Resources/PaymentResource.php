<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PaymentResource extends JsonResource
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
        $invoiceNo = '';
        if (!blank($this->salesInvoice)) {
            $invoiceNo = $this->salesInvoice->invoice_no;
        }

        return [
            'id' => $this->id,
            'date' => dateFormat($this->date),
            'desc' => $this->descr,
            'amount' => $this->amount,
            'account_iban' => $this->account_iban,
            'invoice_no' => $invoiceNo,
            'type' => str_replace("_", " ", ucwords($this->type, "_")),
        ];
    }
}
