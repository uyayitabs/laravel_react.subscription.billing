<?php

namespace App\Traits;

use App\Models\PlanLinePrice;

trait HasPlanLinePriceTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planLinePrice()
    {
        return $this->hasOne(PlanLinePrice::class)
                // ->where("price_valid_from", "<=", now())
                ->orderBy("price_valid_from", "DESC");
    }

    public function getPlanLinePriceMarginAttribute()
    {
        $planLinePrice = $this->planLinePrice;
        return $planLinePrice ? $this->planLinePrice->margin : '';
    }

    public function getPlanLinePriceValidAttribute()
    {
        $planLinePrice = $this->planLinePrice;
        return $planLinePrice ? $planLinePrice->price_valid_from : '';
    }

    public function getPlanLinePriceFixedPriceAttribute()
    {
        $planLinePrice = $this->planLinePrice;
        return $planLinePrice ? $planLinePrice->fixed_price : '';
    }
}
