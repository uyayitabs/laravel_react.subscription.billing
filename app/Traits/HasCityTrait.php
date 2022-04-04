<?php

namespace App\Traits;

use App\Models\City;

trait HasCityTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getCityNameAttribute()
    {
        $city = $this->city;
        return $city ? $city->name : '';
    }

    public function getCityMunicipalityAttribute()
    {
        $city = $this->city;
        return $city ? $city->municipality : '';
    }
}
