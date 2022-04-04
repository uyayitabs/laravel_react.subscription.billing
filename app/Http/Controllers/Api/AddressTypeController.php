<?php

namespace App\Http\Controllers\Api;

use App\Models\AddressType;
use App\Services\AddressTypeService;

class AddressTypeController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new AddressTypeService();
    }

    /**
     * Return a paginated list of address types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Address type listing retrieved successfully'
        );
    }

    /**
     * Return the specified address type
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Address type retrieved successfully.'
        );
    }

    /**
     * Store a newly created address type
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(AddressType::$fields)),
            'Address type created successfully.'
        );
    }

    /**
     * Update the specified address type
     *
     * @param \App\Models\AddressType $addressType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(AddressType $addressType)
    {
        return $this->sendSingleResult(
            $this->service->update(request(AddressType::$fields), $addressType),
            'Address type updated successfully.'
        );
    }

    /**
     * Remove the specified address type
     *
     * @param  App\AddressType $addressType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddressType $addressType)
    {
        return $this->sendResponse(
            $addressType->delete(),
            'Address type deleted successfully.'
        );
    }

    /**
     * Return the list address type with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            AddressType::select('id', 'type as name'),
            'Address type lists retrieved successfully.'
        );
    }
}
