<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PlanResource extends JsonResource
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
        if ($this->list) {
            return [
                'id' => $this->id,
                'tenant_id' => $this->tenant_id,
                'parent_plan' => $this->parent_plan,
                'plan_type' => $this->plan_type,
                'area_code_id' => $this->area_code_id,
                'project_id' => $this->project_id,
                'description' => $this->description,
                'description_long' => $this->description_long,
                'billing_start' => dateFormat($this->billing_start),
                'plan_start' => dateFormat($this->plan_start),
                'plan_stop' => dateFormat($this->plan_stop),
                // attributes
                'costs' => $this->costs
            ];
        }

        $planLines = null;
        if ($this->planLines()->count()) {
            foreach ($this->planLines()->get() as $planLine) {
                $lineType = null;
                if ($planLine->line_type) {
                    $lineType = [
                        'id' => $planLine->line_price->id,
                        'line_type' => $planLine->line_price->line_type,
                        'description' => $planLine->line_price->description,
                    ];
                }

                $planLinePrice = null;
                $planLinePrices = null;
                if ($planLine->planLinePrices()->count()) {
                    $planLinePrice = $planLine->planLinePrices()->first();
                    $planLinePrice = [
                        'id' => $planLinePrice->id,
                        'plan_line_id' => $planLinePrice->plan_line_id,
                        'parent_plan_line_id' => $planLinePrice->parent_plan_line_id,
                        'fixed_price' => $planLinePrice->fixed_price,
                        'margin' => $planLinePrice->margin,
                        'price_valid_from' => dateFormat($planLinePrice->price_valid_from, 'Y-m-d'),
                    ];

                    $planLinePrices = $planLine->planLinePrices->map(function ($planLinePrice) {
                        return [
                            'id' => $planLinePrice->id,
                            'plan_line_id' => $planLinePrice->plan_line_id,
                            'parent_plan_line_id' => $planLinePrice->parent_plan_line_id,
                            'fixed_price' => $planLinePrice->fixed_price,
                            'price_incl_vat' => $planLinePrice->price_incl_vat,
                            'margin' => $planLinePrice->margin,
                            'price_valid_from' => dateFormat($planLinePrice->price_valid_from, 'Y-m-d'),
                            'price_excl_vat' => $planLinePrice->price_excl_vat
                        ];
                    });
                }

                $product = null;
                if ($planLine->product()->count()) {
                    $lineProduct = $planLine->product;
                    $product = [
                        'id' => $lineProduct->id,
                        'product_type_id' => $lineProduct->product_type_id,
                        'serialized' => $lineProduct->serialized,
                        'status_id' => $lineProduct->status_id,
                        'description' => $lineProduct->description,
                        'description_long' => $lineProduct->description_long,
                        'ean_code' => $lineProduct->ean_code,
                        'price' => $lineProduct->price,
                    ];
                }

                $planLines[] = [
                    'id' => $planLine->id,
                    'description' => $planLine->description,
                    'description_long' => $planLine->description_long,
                    'mandatory_line' => $planLine->mandatory_line,
                    'parent_plan_line_id' => $planLine->parent_plan_line_id,
                    'plan_id' => $planLine->plan_id,
                    'plan_line_type_id' => $planLine->lineType->id,
                    'plan_line_type_label' => $planLine->lineType->line_type,
                    'plan_start' => dateFormat($planLine->plan_start),
                    'plan_stop' => dateFormat($planLine->plan_stop),
                    'product_id' => $planLine->product_id,

                    // attributes
                    'active' => $planLine->active,
                    'line_type' => $lineType,
                    'line_price' => $planLine->line_price,
                    'plan_line_price' => $planLinePrice,
                    'plan_line_price_fixed_price' => $planLine->plan_line_price_fixed_price,
                    'plan_line_price_margin' => $planLine->plan_line_price_margin,
                    'plan_line_price_valid' => dateFormat($planLine->plan_line_price_valid),
                    'product' => $product,
                    'plan_line_prices' => $planLinePrices
                ];
            }
        }

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'parent_plan' => $this->parent_plan,
            'plan_type' => $this->plan_type,
            'area_code_id' => $this->area_code_id,
            'project_id' => $this->project_id,
            'description' => $this->description,
            'description_long' => $this->description_long,
            'billing_start' => dateFormat($this->billing_start),
            'plan_start' => dateFormat($this->plan_start),
            'plan_stop' => dateFormat($this->plan_stop),
            'plan_lines' => $planLines,
        ];
    }
}
