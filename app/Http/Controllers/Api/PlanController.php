<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Http\Requests\PlanApiRequest;
use App\Services\PlanService;
use App\Services\PlanLineService;

class PlanController extends BaseController
{
    protected $service;
    protected $planLineService;

    public function __construct()
    {
        $this->service = new PlanService();
        $this->planLineService = new PlanLineService();
    }

    /**
     * Return a paginated list of plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginateOrResult(
            $this->service->list(request()),
            'Plans retrieved successfully',
        );
    }

    /**
     * Store a newly created plan
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PlanApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->service->create($data);
    }

    /**
     * Return plan
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        $this->authorize('view', $plan);
        return $this->service->show($plan->id);
    }

    /**
     * Update plan
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Plan $plan, PlanApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->service->update($data, $plan);
    }

    /**
     * Remove plan
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        Logging::information('Delete Plan', $plan, 1, 1);

        return $this->sendResponse(
            $plan->delete(),
            'Plan deleted successfully.'
        );
    }

    /**
     * Return the list plans with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Plan options list retrieved successfully.'
        );
    }

    /**
     * Show plan lines
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function planLines(Plan $plan)
    {
        $this->authorize('view', $plan);
        return $this->sendNewPaginate(
            $this->planLineService->list($plan),
            'Plan lines retrieved successfully.'
        );
    }
}
