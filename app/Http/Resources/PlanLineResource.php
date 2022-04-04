<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PlanLineResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $message = '', $success = true, $list = false)
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
        $planLinePrices = null;
        $planLinePrice = null;
        if ($this->planLinePrices()->count()) {
            $planLinePrices = $this->planLinePrices->map(function ($planLinePrice) {
                return [
                    'id' => $planLinePrice->id,
                    'plan_line_id' => $planLinePrice->plan_line_id,
                    'parent_plan_line_id' => $planLinePrice->parent_plan_line_id,
                    'fixed_price' => $planLinePrice->fixed_price,
                    'margin' => $planLinePrice->margin,
                    'price_valid_from' => dateFormat($planLinePrice->price_valid_from),
                    'price_incl_vat' => $planLinePrice->price_incl_vat,
                    'price_excl_vat' => $planLinePrice->price_excl_vat
                ];
            });

            $planLinePrice = $this->planLinePrices()->first();
            $planLinePrice = [
                'id' => $planLinePrice->id,
                'plan_line_id' => $planLinePrice->plan_line_id,
                'parent_plan_line_id' => $planLinePrice->parent_plan_line_id,
                'fixed_price' => $planLinePrice->fixed_price,
                'margin' => $planLinePrice->margin,
                'price_valid_from' => dateFormat($planLinePrice->price_valid_from),
                'price_incl_vat' => $planLinePrice->price_incl_vat,
                'price_excl_vat' => $planLinePrice->price_excl_vat
            ];
        }

        $product = null;
        if ($this->product()->count()) {
            $lineProduct = $this->product;
            $product = [
                'id' => $lineProduct->id,
                'product_type_id' => $lineProduct->product_type_id,
                'serialized' => $lineProduct->serialized,
                'status_id' => $lineProduct->status_id,
                'description' => $lineProduct->description,
                'description_long' => $lineProduct->description_long,
                'ean_code' => $lineProduct->ean_code,
                'price' => $lineProduct->price,
                'vat_code' => $lineProduct->vat_code ? [
                    "vat_percentage" => $lineProduct->vat_code->vat_percentage,
                    "description" => $lineProduct->vat_code->description,
                    "active_from" => dateFormat($lineProduct->vat_code->active_from),
                ] : null
            ];
        }
        $lineType = $this->lineType;
        $plan_subscription_line_type = null;
        if ($this->lineType) {
            $plan_subscription_line_type = [
                'id' => $lineType->id,
                'line_type' => $lineType->line_type
            ];
        }

        if ($this->list) {
            return [
                'id' => $this->id,
                'plan_id' => $this->plan_id,
                'product_id' => $this->product_id,
                'plan_line_type' => $this->plan_line_type,
                'parent_plan_line_id' => $this->parent_plan_line_id,
                'mandatory_line' => $this->mandatory_line,
                'plan_start' => dateFormat($this->plan_start),
                'plan_stop' => dateFormat($this->plan_stop),
                'description' => $this->description,
                'description_long' => $this->description_long,

                // attributes
                'active' => $this->active,
                'line_type' => $lineType,
                'line_price' => $this->line_price,
                'plan_line_prices' => $planLinePrices,
                'plan_line_price' => $planLinePrice,
                'plan_line_price_fixed_price' => $this->plan_line_price_fixed_price,
                'plan_line_price_margin' => $this->plan_line_price_margin,
                'plan_line_price_valid' => dateFormat($this->plan_line_price_valid),
                'product' => $product,

                'plan_subscription_line_type' => $plan_subscription_line_type
            ];
        }

        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'product_id' => $this->product_id,
            'plan_line_type' => $this->plan_line_type,
            'parent_plan_line_id' => $this->parent_plan_line_id,
            'mandatory_line' => $this->mandatory_line,
            'plan_start' => dateFormat($this->plan_start),
            'plan_stop' => dateFormat($this->plan_stop),
            'description' => $this->description,
            'description_long' => $this->description_long,

            // attributes
            'active' => $this->active,
            'line_type' => $lineType,
            'line_price' => $this->line_price,
            'plan_line_prices' => $planLinePrices,
            'plan_line_price' => $planLinePrice,
            'plan_line_price_fixed_price' => $this->plan_line_price_fixed_price,
            'plan_line_price_margin' => $this->plan_line_price_margin,
            'plan_line_price_valid' => dateFormat($this->plan_line_price_valid),
            'product' => $product,
        ];
    }
}
