<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Http\Requests\PersonApiRequest;
use App\Models\Relation;
use App\Models\RelationsPerson;
use App\Services\PersonService;

class PersonController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PersonService();
    }

    /**
     * Display a listing of persons
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
         $tenantId = currentTenant('id');
        return $this->sendPaginateOrResult(
            $this->service->list($tenantId),
            'Person listing retrieved successfully',
            function (Person $person) use ($tenantId) {
                return (new PersonResource($person));
            }
        );
    }

    /**
     * Store a newly created person
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonApiRequest $request)
    {
        $result = $this->service->create($request->all());
        if ($result && $result['success']) {
            return $this->sendResult(
                $result,
                'Person created successfully'
            );
        } elseif ($result) {
            return $this->sendError($result['message'], [], 422);
        }

        return $this->sendError('Unexpected error creating Person', [], 500);
    }

    /**
     * Link the existing person to a relation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkperson()
    {
        return $this->sendSingleResult(
            $this->service->linkperson(request([
                'relation_id',
                'person_id',
                'status'
            ])),
            'Person linked successfully.'
        );
    }

    /**
     * @param Person $person
     * @param Relation|null $relation
     * @return ?PersonResource
     */
    public function show(Person $person): ?PersonResource
    {
        return $this->service->show(
            $person->id,
            'Person retrieved successfully.'
        );
    }

    /**
     * Update the specified person
     *
     * @param \App\Models\Person $person
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Person $person, PersonApiRequest $request)
    {
        $result = $this->service->update($request->all(), $person);
        if ($result && $result['success']) {
            return $this->sendResult(
                $result['data'],
                'Person updated successfully.'
            );
        } elseif ($result) {
            return $this->sendError($result['message'], [], 422);
        }
        return $this->sendError('Unexpected error updating Person', [], 500);
    }

    /**
     * Remove the specified person
     *
     * @param \App\Models\Person $person
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Person $person)
    {
        $result = $this->service->delete($person);
        if (isset($result) && $result['success']) {
            return $this->sendResult(
                $result['data'],
                $result['message']
            );
        } elseif (isset($result)) {
            return $this->sendError($result['message'], [], 422);
        }
        return $this->sendError('Unexpected error updating Person', [], 500);
    }

    /**
     * Get person record counts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        return $this->sendResult(
            $this->service->count(),
            'Person record counts retrieved successfully.'
        );
    }

    /**
     * Get existing person record email list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkemail()
    {
        $persons = $this->service->checkemail();

        if ($persons == 'duplicate') {
            return $this->sendError(
                request('email') . ' is already taken for this relation!',
                [],
                500
            );
        }

        return $this->sendResult(
            $persons,
            'Person list retrieved successfully.'
        );
    }
}
