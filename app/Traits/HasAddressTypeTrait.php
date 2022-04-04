<?php

namespace App\Traits;

use App\Models\AddressType;

trait HasAddressTypeTrait
{
    /**
     * Get AddressType function
     *
     * @return \AddressType
     */
    public function addressType()
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type_id');
    }

    public function getAddressTypeTypeAttribute()
    {
        $addressType = $this->addressType;
        return $addressType ? $addressType->type : '';
    }
}
