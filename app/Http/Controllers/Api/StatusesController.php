<?php

namespace App\Http\Controllers\Api;

use App\Models\Status;
use App\Services\StatusService;
use App\Models\StatusType;

class StatusesController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new StatusService();
    }

    /**
     * Return a paginated list of sales invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($relation = null)
    {
        return $this->sendPaginate(
            $this->service->list($relation),
            'Status listing retrieved successfully'
        );
    }

    /**
     * Store a newly created sales invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(),
            'Status created successfully.'
        );
    }

    /**
     * Display the specified sales invoice
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Status $status)
    {
        $this->authorize('view', $status);
        return $this->sendSingleResult(
            $this->service->show($status->id),
            'Status retrieved successfully.'
        );
    }

    /**
     * Update the specified sales invoice.
     *
     * @param \App\Models\Status $status
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Status $status)
    {
        $this->authorize('update', $status);
        return $this->sendSingleResult(
            $this->service->update(request(Status::$fields), $status),
            'Status updated successfully.'
        );
    }

    /**
     * Remove the specified sales invoice
     *
     * @param \App\Models\Status $status
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        $this->authorize('delete', $status);
        return $this->sendResponse(
            $this->service->delete($status),
            'Status deleted successfully.'
        );
    }

    /**
     * Get invoice record counts
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        return $this->sendResult(
            $this->service->count(),
            'Invoice record counts retrieved successfully.'
        );
    }

    public function statusList(StatusType $statusType)
    {
        return $this->sendResults(
            $this->service->getOptions($statusType->id)
        );
    }
}
