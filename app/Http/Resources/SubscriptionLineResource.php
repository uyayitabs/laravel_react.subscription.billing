<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class SubscriptionLineResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = "Successfully received subscription line(s)";
    protected $status;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = null;
        $productObject = $this->product;
        if ($productObject) {
            $product = [
                'vendor_partcode' => $productObject->vendor_partcode,
                'description' => $productObject->description,
            ];
        }

        $lineType = null;
        if ($this->line_type_name) {
            $lineType = [
                'line_type' => $this->line_type_name
            ];
        }

        $mySerial = $this->my_serial;

        // $subscriptionLinePrices = [];
        // $subscriptionLinePricesObj = $this->subscriptionLinePrices;
        // if (count($subscriptionLinePricesObj)) {
        //     foreach ($subscriptionLinePricesObj as $subscriptionLinePrice) {
        //         $subscriptionLinePrices[] = [
        //             'id' => $subscriptionLinePrice->id,
        //             'subscription_line_id' => $subscriptionLinePrice->subscription_line_id,
        //             'parent_plan_line_id' => $subscriptionLinePrice->parent_plan_line_id,
        //             'fixed_price' => (float) $subscriptionLinePrice->fixed_price,
        //             'margin' => $subscriptionLinePrice->margin,
        //             'price_valid_from' => dateFormat(
        //                 $subscriptionLinePrice->price_valid_from,
        //                 'Y-m-d'
        //             ),
        //             'price_excl_vat' => (float) $subscriptionLinePrice->price_excl_vat,
        //             'price_incl_vat' => (float) $subscriptionLinePrice->price_incl_vat,
        //         ];
        //     }
        // }

        $Line_status = $this->Line_status;
        $status = [];
        if ($Line_status) {
            $type = 'danger';
            if (0 == $Line_status->id) {
                $type = 'info';
            }
            if (1 == $Line_status->id) {
                $type = 'success';
            }
            $status = [
                'id' => $Line_status->id,
                'label' => $Line_status->label,
                'status' => $Line_status->status,
                'status_type' => $Line_status->status_type->type,
                'type' => $type
            ];
        }

        $currentPrice = $this->subscriptionLinePrice()->first();

        $currentLinePrice = [];
        if ($currentPrice) {
            $currentLinePrice = [
                'price_excl_vat' => $currentPrice->fixed_price,
                'price_incl_vat' => $currentPrice->price_incl_vat,
                'price_valid_from' => dateFormat($currentPrice->price_valid_from)
            ];
        }

        return [
            'id' => $this->id,
            'subscription_id' => $this->subscription_id,
            'subscription_line_type' => $this->subscription_line_type,
            'plan_line_id' => $this->plan_line_id,
            'plan_id' => $this->plan_id,
            'product_id' => $this->product_id,
            'serial' => $this->serial,
            'mandatory_line' => $this->mandatory_line,
            'subscription_start' => dateFormat($this->subscription_start),
            'subscription_stop' => dateFormat($this->subscription_stop),
            'description' => $this->description,
            'description_long' => $this->description_long,
            'mind_id' => $this->mind_id,
            'json_data_product_type' => $this->json_data_product_type,
            'backend_api' => $this->backend_api,
            'has_gadget' => $this->has_gadget,
            'm7_main_stb' => $this->m7_main_stb,
            'my_serial' => $mySerial, // my_serial,
            'product' => $product, // product
            'current_line_price' => $currentLinePrice,
            'line_type' => $lineType,
            'status_id' => $this->status_id,
            'status' => $status,
            'is_invoiced' => $this->invoice_count > 0,
        ];
    }
}
