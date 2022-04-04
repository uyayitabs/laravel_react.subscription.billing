<?php

namespace App\Http\Controllers\Api;

use Logging;
use App\Models\Warehouse;
use App\Services\WarehouseService;

class WarehouseController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new WarehouseService();
    }

    /**
     * Return a listing warehouses
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Warehouse listing retrieved successfully'
        );
    }

    /**
     * Store a newly created warehouse
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(Warehouse::$fields)),
            'Warehouse created successfully.'
        );
    }

    /**
     * Return the specified warehouse
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        $this->authorize('view', $warehouse);

        return $this->sendSingleResult(
            $this->service->show($warehouse->id),
            'Warehouse retrieved successfully.'
        );
    }

    /**
     * Update the specified warehouse
     *
     * @param \App\Models\Warehouse $warehouse
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);

        return $this->sendSingleResult(
            $this->service->update(request(Warehouse::$fields), $warehouse),
            'Warehouse updated successfully.'
        );
    }

    /**
     * Remove the specified warehouse
     *
     * @param \App\Models\Warehouse $warehouse
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('delete', $warehouse);

        Logging::information('Delete Warehouse', $warehouse, 1, 1);
        $warehouse->delete();

        return $this->sendResponse(
            $warehouse,
            'Warehouse deleted successfully.'
        );
    }
}
