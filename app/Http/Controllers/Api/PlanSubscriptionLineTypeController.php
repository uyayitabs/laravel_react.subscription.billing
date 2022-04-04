<?php

namespace App\Http\Controllers\Api;

use App\Models\PlanSubscriptionLineType;
use App\Services\PlanSubscriptionLineTypeService;

class PlanSubscriptionLineTypeController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PlanSubscriptionLineTypeService();
    }

    /**
     * Return a paginated list of the line types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Line type listing retrieved successfully'
        );
    }

    /**
     * Return the list line types with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Line type lists retrieved successfully.'
        );
    }
}
