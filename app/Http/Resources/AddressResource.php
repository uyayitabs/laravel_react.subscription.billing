<?php

namespace App\Http\Resources;

use App\Traits\ApiResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    use ApiResourceTrait;

    protected $list;

    public function __construct($resource, $list = false)
    {
        parent::__construct($resource);
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
        $city = null;
        if ($this->city()->count()) {
            $city = [
                'id' => $this->city->id,
                'name' => $this->city_name,
                'state_id' => $this->city->state_id,
                'municipality' => $this->city->municipality,
            ];
        }

        $addressType = null;
        if ($this->addressType()->count()) {
            $addressType = [
                'id' => $this->addressType->id,
                'type' => $this->addressType->type
            ];
        }

        $country = null;
        if ($this->country()->count()) {
            $country = [
                'id'  => $this->country->id,
                'name' => $this->country->name,
            ];
        }

        if ($this->list) {
            return [
                'id' => $this->id,
                'primary' => $this->primary,
                'street1' => $this->street1,
                'house_number' => $this->house_number,
                'house_number_suffix' => $this->house_number_suffix,
                'room' => $this->room,
                'zipcode' => $this->zipcode,
                'zipcode_id' => $this->zipcode_id,

                // city
                'city_id' => $this->city_id,
                'city' => $city,

                // address_type
                'address_type_id' => $this->address_type_id,
                'address_type' => $addressType,

                // country
                'country_id' => $this->country_id,
                'country' => $country,

                // attributes
                'full_address' => $this->full_address,
            ];
        }

        return [
            'id' => $this->id,
            'relation_id' => $this->relation_id,
            'street1' => $this->street1,
            'street2' => $this->street2,
            'house_number' => $this->house_number,
            'house_number_suffix' => $this->house_number_suffix,
            'room' => $this->room,
            'zipcode' => $this->zipcode,
            'zipcode_id' => $this->zipcode_id,
            'state_id' => $this->state_id,
            'primary' => $this->primary,

            // city
            'city_id' => $this->city_id,
            'city' => $city,

            // address_type
            'address_type_id' => $this->address_type_id,
            'address_type' => $addressType,

            // country
            'country_id' => $this->country_id,
            'country' => $country,

            // attributes
            'full_address' => $this->full_address,
        ];
    }
}
