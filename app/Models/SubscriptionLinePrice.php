<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;

class SubscriptionLinePrice extends BaseModel
{
    protected $fillable = [
        'subscription_line_id',
        'parent_plan_line_id',
        'fixed_price',
        'margin',
        'price_valid_from'
    ];

    public static $fields = [
        'id',
        'subscription_line_id',
        'parent_plan_line_id',
        'fixed_price',
        'margin',
        'price_valid_from'
    ];

    public static $scopes = [
        'subscription-line'
    ];

    protected $casts = [
        'price_valid_from' => 'datetime:Y-m-d',
        'price' => 'float',
        'fixed_price' => 'float',
        'price_vat' => 'float',
        'price_total' => 'float',
        'price_per_piece' => 'float',
        'margin' => 'float',
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Get SubscriptionLine function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class);
    }

    /**
     * Get PlanLine function
     *
     * @return \PlanLine
     */
    public function planLine()
    {
        return $this->belongsTo(PlanLine::class, 'parent_plan_line_id', 'id');
    }

    /**
     * Get PlanLine function
     *
     * @return \PlanLine
     */
    public function getPlanLinesAttribute()
    {
        return  PlanLine::ancestorsOf($this->parent_plan_line_id);
    }


    /**
     * Get computed price from margin and fixed_price function
     *
     * @param integer $computedFixedPrice
     * @param integer $computedMargin
     * @param integer $parentPlanLineId
     * @return float
     */
    public function getComputedPrice($parentPlanLineId, &$computedFixedPrice = 0, &$computedMargin = 0)
    {
        if (!empty($parentPlanLineId) && $parentPlanLineId > 0) {
            $planLine = PlanLine::findOrFail($parentPlanLineId);
            if (!empty($planLine)) {
                if ($planLine->planLinePrice()->count() > 0) {
                    $planLinePrice = $planLine->planLinePrice()->first();
                    $computedMargin += $planLinePrice->margin;

                    // Not yet root Plan, loop again
                    if (!empty($planLinePrice->parent_plan_line_id) && $planLinePrice->parent_plan_line_id > 0) {
                        $this->getFixedPriceFromParentPlans($computedMargin, $planLinePrice->parent_plan_line_id);
                    } else { // Last line (possibly the root Plan)
                        $earningFromMargin = 0;
                        $priceAfterMarginEarning = 0;

                        if ($computedFixedPrice == 0) {
                            if ($planLinePrice->fixed_price > 0) {
                                $computedFixedPrice = $planLinePrice->fixed_price;
                            } else {
                                $computedFixedPrice = $planLine->product()->first()->fixed_price;
                            }
                        }

                        $earningFromMargin = $computedFixedPrice * $computedMargin;
                        $priceAfterMarginEarning = $computedFixedPrice + $earningFromMargin;

                        $logMessage = "getComputedPrice() fixed_price={$computedFixedPrice}";
                        $logMessage .= " margin={$computedMargin} earningFromMargin={$earningFromMargin}";
                        $logMessage .= " finalPrice={$priceAfterMarginEarning}";
                        Log::info($logMessage);
                        return floatval($priceAfterMarginEarning);
                    }
                } else {
                    return 0;
                }
            }
            // Check if returning 0 is correct
            return 0;
        } else {
            $earningFromMargin = 0;
            $priceAfterMarginEarning = 0;
            if ($computedMargin > 0) {
                $earningFromMargin = $computedFixedPrice * $computedMargin;
                $priceAfterMarginEarning = $computedFixedPrice + $earningFromMargin;
                return floatval($priceAfterMarginEarning);
            }
            return floatval($computedFixedPrice);
        }
    }

    public function setPriceValidFromAttribute($value)
    {
        $this->attributes['price_valid_from'] = dateFormat($value);
    }

    public function getPriceExclVatAttribute()
    {
        return $this->fixed_price;
    }

    public function getPriceInclVatAttribute()
    {

        $subscriptionLine = SubscriptionLine::find($this->subscription_line_id);
        $subscription = $subscriptionLine->subscription;
        $relation = $subscription->relation;

        $tenantId = $relation->tenant_id;
        $productId = $subscriptionLine->product_id;
        $fixed_price = $this->fixed_price;

        return getPriceIncVat($fixed_price, $tenantId, $productId);
    }
}
