<?php

namespace App\Services;

use App\Models\Country;

class StateService
{
    /**
     * Display a list if cities
     *
     * @return \Illuminate\Http\Response
     */
    public function cities($country)
    {
        $country = Country::find($country);
        return $country->cities()->select('cities.id', 'cities.name')->orderBy('cities.name');
    }
}
