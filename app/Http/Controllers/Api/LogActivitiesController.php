<?php

namespace App\Http\Controllers\Api;

use App\Services\LogActivitiesService;

class LogActivitiesController extends BaseController
{
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new LogActivitiesService();
    }

    /**
     * Return the list of user logs with details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Logs retrieved successfully.'
        );
    }

    /**
     * Return the recent list of user logs for the graph chart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent()
    {
        return $this->service->recent();
    }
}
