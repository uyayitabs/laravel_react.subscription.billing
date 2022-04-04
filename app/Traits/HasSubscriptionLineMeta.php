<?php

namespace App\Traits;

use App\Models\SubscriptionLineMeta;
use Illuminate\Support\Facades\Config;

trait HasSubscriptionLineMeta
{
    /**
     * Get binding SubscriptionLineMeta
     *
     * @return \SubscriptionLineMeta
     */
    public function subscriptionLineMeta()
    {
        return $this->hasOne(SubscriptionLineMeta::class, 'subscription_line_id', 'id');
    }

    /**
     * Get NetworkOperator of a SubscriptionLine
     *
     * @return mixed
     */
    public function getSubscriptionLineNetworkOperator()
    {
        if ($this->subscriptionLineMeta) {
            $lineNetworkOperator = $this->subscriptionLineMeta->networkOperator;
            return $lineNetworkOperator;
        }
        return;
    }

    /**
     * Get Operator of a NetworkOperator
     *
     * @return mixed
     */
    public function getSubscriptionLineOperator()
    {
        if ($this->subscriptionLineMeta) {
            $lineNetworkOperator = $this->getSubscriptionLineNetworkOperator();
            if ($lineNetworkOperator) {
                return $lineNetworkOperator->operator;
            }
            return;
        }
        return;
    }
}
