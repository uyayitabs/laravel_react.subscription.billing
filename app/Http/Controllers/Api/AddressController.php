<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Models\Relation;
use App\Http\Requests\AddressApiRequest;
use App\Services\AddressService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class AddressController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new AddressService();
    }

    /**
     * Return a paginated list of addresses.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $relationId = request('relation_id');
        return $this->sendPaginateOrResult(
            $this->service->list($relationId),
            'Address listing retrieved successfully',
            function (Address $address) {
                return (new AddressResource(
                    $address
                ));
            }
        );
    }

    /**
     * Store a newly created address
     *
     * @return Response
     */
    public function store(Relation $relation, AddressApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $data['relation_id'] = $relation->id;
        return $this->sendResponse(
            $this->service->create($data),
            'Address created successfully.'
        );
    }

    /**
     * Return the specified address.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(Address $address)
    {
        $result = $this->service->show($address->id);
        if ($result) {
            return $this->sendResult($result, 'Address retrieved successfully.');
        } else {
            return $this->sendResult([], 'Address could not be found.', 404);
        }
    }

    /**
     * Update the specified address
     *
     * @param Address $address
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Address $address, AddressApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $result = $this->service->update($data, $address);
        if ($result['success']) {
            return $this->sendResult($result['data'], $result['message']);
        } else {
            return $this->sendResult([], $result['message'], 422);
        }
    }

    /**
     * Remove the specified address
     *
     * @param Address $address
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        return $this->sendResponse(
            $this->service->delete($address),
            'Address deleted successfully.'
        );
    }
}
