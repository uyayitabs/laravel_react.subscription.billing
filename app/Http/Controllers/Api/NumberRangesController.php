<?php

namespace App\Http\Controllers\Api;

use App\Models\NumberRange;
use App\Http\Requests\NumberRangeApiRequest;
use App\Services\NumberRangesService;
use App\Models\Tenant;

class NumberRangesController extends BaseController
{
    protected $service;

    public function __construct()
    {

        $this->service = new NumberRangesService();
    }

    /**
     * Get tenant-specific number_ranges
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function my($tenantId)
    {
        $tenant =  Tenant::find($tenantId);
        $this->authorize('view', $tenant);
        return $this->sendNewPaginate(
            $this->service->list(request(), $tenantId),
            'Number range listing retrieved successfully.'
        );
    }

    /**
     * Return a list of number ranges
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendNewPaginate(
            $this->service->list(request(), currentTenant('id')),
            'Number range listing retrieved successfully.'
        );
    }

    /**
     * Store a newly created number range
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NumberRangeApiRequest $request)
    {
        $data = jsonRecode($request->all(NumberRange::$fields));
        return $this->sendSingleResult(
            $this->service->create($data),
            'Number range created successfully.'
        );
    }

    /**
     * Return the specified number range
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->show(
            $id,
            'Number range retrieved successfully.'
        );
    }

    /**
     * Update the specified number range
     *
     * @param \App\Models\NumberRange $numberRange
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(NumberRange $numberRange, NumberRangeApiRequest $request)
    {
        $data = jsonRecode($request->all(NumberRange::$fields));
        return $this->sendResponse(
            $this->service->update($data, $numberRange),
            'NumberRange updated successfully.'
        );
    }

    /**
     * Remove the specified number range
     *
     * @param \App\Models\NumberRange $numberRange
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(NumberRange $numberRange)
    {
        return $this->sendResponse(
            $numberRange->delete(),
            'Number range deleted successfully.'
        );
    }
}
