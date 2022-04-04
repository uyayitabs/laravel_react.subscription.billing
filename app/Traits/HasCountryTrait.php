<?php

namespace App\Traits;

use App\Models\Country;

trait HasCountryTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getCountryNameAttribute()
    {
        $country = $this->country;
        return $country ? $country->getAttribute('official_name') : '';
    }
}
