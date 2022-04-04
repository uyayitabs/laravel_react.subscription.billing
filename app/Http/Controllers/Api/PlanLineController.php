<?php

namespace App\Http\Controllers\Api;

use Logging;
use App\Models\Plan;
use App\Models\PlanLine;
use App\Http\Requests\PlanLineApiRequest;
use App\Http\Requests\PlanLineApiStoreRequest;
use App\Services\PlanLinePriceService;
use App\Services\PlanLineService;

class PlanLineController extends BaseController
{
    protected $service,
        $planLinePriceService;

    public function __construct()
    {
        $this->service = new PlanLineService();
        $this->planLinePriceService = new PlanLinePriceService();
    }

    /**
     * Return a paginated list of plan lines
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Plan line listing retrieved successfully.'
        );
    }

    /**
     * Display the specified plan
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(PlanLine $planLine)
    {
        return $this->sendSingleResult(
            $this->service->show($planLine->id),
            'Plan line retrieved successfully.'
        );
    }

    /**
     * Store a newly created plan_line
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Plan $plan, PlanLineApiStoreRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendResults(
            $this->service->create($plan, $data),
            'Plan lines created successfully.'
        );
    }

    /**
     * Update selected plan_line
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PlanLine $planLine, PlanLineApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendSingleResult(
            $this->service->update($data, $planLine),
            'Plan line updated successfully.'
        );
    }

    /**
     * Remove plan line
     *
     * @param \App\Models\PlanLine $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanLine $planLine)
    {
        Logging::information('Delete Planlines', $planLine, 1, 1);
        $planLine->delete();
        return $this->sendResponse(
            $planLine,
            'Plan line deleted successfully.'
        );
    }

    public function planLinePrices(Plan $plan, PlanLine $planLine)
    {
        return $this->sendNewPaginate(
            $this->planLinePriceService->linePrices($planLine),
            'Plan line prices retrieved successfully'
        );
    }
}
