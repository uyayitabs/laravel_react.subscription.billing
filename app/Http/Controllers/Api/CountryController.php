<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Logging;
use App\Services\CountryService;

class CountryController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new CountryService();
    }

    /**
     * Return a paginated list of countries
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Country listing retrieved successfully.'
        );
    }

    /**
     * Store a newly created country
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(Country::$fields)),
            'Country created successfully.'
        );
    }

    /**
     * Return the specified country
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Country retrieved successfully.'
        );
    }

    /**
     * Update the specified country
     *
     * @param \App\Models\Country $country
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Country $country)
    {
        return $this->sendSingleResult(
            $this->service->update(request(Country::$fields), $country),
            'Country updated successfully.'
        );
    }

    /**
     * Remove the specified country
     *
     * @param \App\Models\Country $country
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        Logging::information('Delete Country', $country, 1, 1);
        return $this->sendResponse(
            $country->delete(),
            'Country deleted successfully.'
        );
    }

    /**
     * Return the list products with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Country lists retrieved successfully.'
        );
    }

    /**
     * Return the states for the specified country
     *
     * @return \Illuminate\Http\Response
     */
    public function states(Country $country)
    {
        return $this->sendResults(
            $this->service->states($country),
            'Country states retrieved successfully.'
        );
    }
}
