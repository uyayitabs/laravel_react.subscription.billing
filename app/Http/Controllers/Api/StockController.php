<?php

namespace App\Http\Controllers\Api;

use Logging;
use App\Models\Stock;
use App\Services\StockService;

class StockController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new StockService();
    }

    /**
     * Return a paginated list of stocks
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Stock listing retrieved successfully'
        );
    }

    /**
     * Store a newly created stock
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(Stock::$fields)),
            'Serial created successfully.'
        );
    }

    /**
     * Return the specified stock
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Serial retrieved successfully.'
        );
    }

    /**
     * Update the specified stock
     *
     * @param \App\Models\Stock $stock
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Stock $stock)
    {
        return $this->sendSingleResult(
            $this->service->update(request(Stock::$fields), $stock),
            'Stock updated successfully.'
        );
    }

    /**
     * Remove the specified stock
     *
     * @param \App\Models\Stock $stock
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        Logging::information('Delete Stock', $stock, 1, 1);
        $stock->delete();
        return $this->sendResponse(
            $stock,
            'Stock deleted successfully.'
        );
    }
}
