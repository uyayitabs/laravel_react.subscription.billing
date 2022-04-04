<?php

namespace App\Http\Controllers\Api;

use App\Models\PlanLinePrice;
use App\Models\PlanLine;
use App\Http\Requests\PlanLinePriceApiRequest;
use App\Services\PlanLinePriceService;

class PlanLinePriceController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PlanLinePriceService();
    }

    /**
     * Return a list of plan line prices
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Plan line prices retrieved successfully.'
        );
    }

    /**
     * Store a newly created plan_line_price
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PlanLine $plan_line, PlanLinePriceApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendResponse(
            $this->service->create($data, $plan_line),
            'Plan line price created successfully.'
        );
    }

    /**
     * Store a newly created plan_line
     *
     * @param \App\Models\Http\Requests\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PlanLinePrice $planLinePrice, PlanLinePriceApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendSingleResult(
            $this->service->update($data, $planLinePrice),
            'Plan line price updated successfully.'
        );
    }

    /**
     * Remove the specified PlanLinePrice
     *
     * @param \App\Models\PlanLinePrice $plan_line_price
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanLinePrice $planLinePrice)
    {
        return $this->sendResponse(
            $this->service->delete($planLinePrice),
            'PlanLinePrice deleted successfully.'
        );
    }
}
