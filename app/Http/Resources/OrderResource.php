<?php

namespace App\Http\Resources;

use App\Traits\ApiResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $status, $message, $success, $list = false)
    {
        parent::__construct($resource);
        $this->status = $status;
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
        $order = $this->resource;
        $status = $order->status();

        $addressData = $this->data['address'];
        $address1 = "{$addressData['street']} {$addressData['house_no']}";
        if (!blank($addressData['house_no_suffix'])) {
            $address1 .= "-{$addressData['house_no_suffix']}";
        }
        $address1 .= " {$addressData['room']}";
        $address2 = "{$addressData['postal_code']} {$addressData['city']}";

        $customerData = $this->data['customer'];
        $customerName = $customerData['name']['first'];
        $customerName .= " {$customerData['name']['middle']}";
        $customerName .= " {$customerData['name']['last']}";

        $package = $this->data['product']['package'];

        return [
            'id'   => $order->id,
            'date' => dateFormat($this->date),
            'customer_name' => $customerName,
            'address1' => $address1,
            'address2' => $address2,
            'package'  => $package,
            'status'   => [
                'id'    => $status->id,
                'name'  => $status->label
            ]
        ];
    }
}
