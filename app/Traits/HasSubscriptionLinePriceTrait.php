<?php

namespace App\Traits;

use App\Models\SubscriptionLinePrice;

trait HasSubscriptionLinePriceTrait
{
    /**
     * Get the line type
     *
     * @return \SubscriptionLinePrice
     */
    public function subscriptionLinePrice($date = null)
    {
        if (is_null($date)) {
            $date = now();
        }
        return $this->hasMany(SubscriptionLinePrice::class)
                ->where("price_valid_from", "<=", $date)
                ->orderBy("price_valid_from", "DESC");
    }

    public function getSubscriptionLinePriceMarginAttribute()
    {
        $subscriptionLinePrice = $this->subscriptionLinePrice;
        return count($subscriptionLinePrice) > 0 ? $subscriptionLinePrice[0]->margin : '';
    }

    public function getSubscriptionLinePriceValidAttribute()
    {
        $subscriptionLinePrice = $this->subscriptionLinePrice;
        return count($subscriptionLinePrice) > 0 ? $subscriptionLinePrice[0]->price_valid_from : '';
    }

    public function getSubscriptionLinePriceFixedPriceAttribute()
    {
        $subscriptionLinePrice = $this->subscriptionLinePrice;
        return count($subscriptionLinePrice) > 0 ? $subscriptionLinePrice[0]->fixed_price : '';
    }
}
