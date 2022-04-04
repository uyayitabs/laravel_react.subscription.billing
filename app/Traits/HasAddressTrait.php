<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAddressTrait
{
    /**
     * Get address[] function
     *
     * @return HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class)->orderBy('primary', 'DESC');
    }

    /**
     * Get contact address[] function
     *
     * @return HasMany
     */
    public function contactAddresses()
    {
        return $this->addresses()->where("address_type_id", 1);
    }

    /**
     * Get contact address function
     *
     * @return HasMany
     */
    public function contactAddress()
    {
        return $this->contactAddresses()->first();
    }

    /**
     * Get provisioning address[] function

     * @return HasMany
     */
    public function provisioningAddresses()
    {
        return $this->addresses()->where("address_type_id", 2);
    }

    /**
     * Get provisioning address function
     *
     * @return HasMany
     */
    public function provisioningAddress()
    {
        $primary = $this->provisioningAddresses()->where('primary', 1)->first();
        if ($primary) {
            return $primary;
        }
        return $this->provisioningAddresses()->first();
    }

    /**
     * Get billing address[]
     *
     * @return HasMany
     */
    public function billingAddresses()
    {
        return $this->addresses()->where("address_type_id", 3);
    }

    /**
     * Get billing address function
     *
     * @return HasMany
     */
    public function billingAddress()
    {
        $primary = $this->billingAddresses()->where('primary', 1)->first();
        if ($primary) {
            return $primary;
        }
        return $this->billingAddresses()->first();
    }

    /**
     * Get shipping address[] function
     *
     * @return HasMany
     */
    public function shippingAddresses()
    {
        return $this->addresses()->where("address_type_id", 4);
    }

    /**
     * Get shipping address function
     *
     * @return HasMany
     */
    public function shippingAddress()
    {
        $primary = $this->shippingAddresses()->where('primary', 1)->first();
        if ($primary) {
            return $primary;
        }
        return $this->shippingAddresses()->first();
    }


    /**
     * Get primary address
     *
     * @return \Address[]
     */
    public function primaryAddress()
    {
        return $this->addresses();
    }
}
