<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PlanLinePriceResource extends JsonResource
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
                'plan_line_id' => $this->plan_line_id,
                'parent_plan_line_id' => $this->parent_plan_line_id,
                'fixed_price' => $this->fixed_price,
                'margin' => $this->margin,
                'price_valid_from' => dateFormat($this->price_valid_from),

                // attributes
                'price_incl_vat' => $this->price_incl_vat,
                'price_excl_vat' => $this->price_excl_vat
            ];
        }

        return [
            'id' => $this->id,
            'plan_line_id' => $this->plan_line_id,
            'parent_plan_line_id' => $this->parent_plan_line_id,
            'fixed_price' => $this->fixed_price,
            'margin' => $this->margin,
            'price_valid_from' => dateFormat($this->price_valid_from),

            // attributes
            'price_incl_vat' => $this->price_incl_vat,
            'price_excl_vat' => $this->price_excl_vat
        ];
    }
}
