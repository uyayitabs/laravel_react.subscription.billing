<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;
use Carbon\Carbon;

class PaymentListResource extends JsonResource
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
            'date' => dateFormat($this->date),
            'account_holder' => $this->account_name,
            'desc' => $this->descr,
            'amount' => $this->amount,
            'account_iban' => $this->account_iban,
            'payment_type' => $this->type,
            'batch_id' => $this->batch_id,
            'batch_trx' => $this->batch_trx,
            'bank_code' => $this->bank_code,
            'return_code' => $this->return_code,
            'return_reason' => $this->return_reason,
            'relation_id' => $this->relation_id,
        ];
    }
}
