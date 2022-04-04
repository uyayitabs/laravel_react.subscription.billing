<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait HasSubscriptionLineTypesTrait
{
    /**
     * Check if line type is free
     *
     * @return boolean
     */
    public function isLineTypeFree()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.free");
    }

    /**
     * Check if line type is for non-recurring cost
     *
     * @return boolean
     */
    public function isLineTypeNRC()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.nrc");
    }

    /**
     * Check if line type is for monthly-recurring cost
     *
     * @return boolean
     */
    public function isLineTypeMRC()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.mrc");
    }

    /**
     * Check if line type is for quarterly-recurring cost
     *
     * @return boolean
     */
    public function isLineTypeQRC()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.qrc");
    }

    /**
     * Check if line type is for yearly-recurring cost
     *
     * @return boolean
     */
    public function isLineTypeYRC()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.yrc");
    }

    /**
     * Check if line type is for deposit
     *
     * @return boolean
     */
    public function isLineTypeDeposit()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.deposit");
    }

    /**
     * Check if line type is for discount
     *
     * @return boolean
     */
    public function isLineTypeDiscount()
    {
        return $this->subscription_line_type == Config::get("constants.subscription_line_types.discount");
    }
}
