<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PaymentConditionResource extends JsonResource
{
    use ApiResourceTrait;

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
        $createdUser = null;
        if ($this->createdUser) {
            $createdUser = [
                'id' => $this->createdUser->id,
                'username' => $this->createdUser->username,
            ];
        }

        $updatedUser = null;
        if ($this->updatedUser) {
            $updatedUser = [
                'id' => $this->updatedUser->id,
                'username' => $this->updatedUser->username,
            ];
        }

        if ($this->list) {
            return [
                'id' => $this->id,
                'tenant_id' => $this->tenant_id,
                'direct_debit' => $this->direct_debit,
                'pay_in_advance' => $this->pay_in_advance,
                'status' => $this->status,
                'description' => $this->description,
                'net_days' => $this->net_days,
                'default' => $this->default,
                'created_user' => $createdUser,
                'updated_user' => $updatedUser
            ];
        }

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'direct_debit' => $this->direct_debit,
            'pay_in_advance' => $this->pay_in_advance,
            'status' => $this->status,
            'description' => $this->description,
            'net_days' => $this->net_days,
            'default' => $this->default,
        ];
    }
}
