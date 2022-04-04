<?php

namespace App\Http\Controllers\Api;

use App\Models\PersonType;
use App\Services\PersonTypeService;

class PersonTypeController extends BaseController
{
    protected $fields = ['type'];
    protected $service;

    public function __construct()
    {
        $this->service = new PersonTypeService();
    }

    /**
     * Return a paginated list of person types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Person type listing retrieved successfully'
        );
    }

    /**
     * Store a newly created person type
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(PersonType::$fields)),
            'Person type created successfully.'
        );
    }

    /**
     * Return the specified person type
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Person type retrieved successfully.'
        );
    }

    /**
     * Update the specified person type.
     *
     * @param \App\Models\PersonType $personType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PersonType $personType)
    {
        return $this->sendResponse(
            $this->service->update(request(PersonType::$fields), $personType),
            'Person type updated successfully.'
        );
    }

    /**
     * Remove the specified person type
     *
     * @param \App\Models\PersonType $personType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonType $personType)
    {
        return $this->sendResponse(
            $personType->delete(),
            'Person type deleted successfully.'
        );
    }

    /**
     * Return the list person types with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Person type lists retrieved successfully.'
        );
    }
}
