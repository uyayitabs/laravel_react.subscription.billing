<?php

namespace App\Http\Controllers\Api;

use Logging;
use App\Models\Serial;
use App\Services\SerialService;

class SerialController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new SerialService();
    }

    /**
     * Display a listing serials
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Serial listing retrieved successfully'
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
            $this->service->create(request(Serial::$fields)),
            'Serial created successfully.'
        );
    }

    /**
     * Display the specified stock
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
     * @param \App\Models\Serial $serial
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Serial $serial)
    {
        return $this->sendSingleResult(
            $this->service->update(request(Serial::$fields), $serial),
            'Serial updated successfully.'
        );
    }

    /**
     * Remove the specified stock
     *
     * @param \App\Models\Serial $serial
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Serial $serial)
    {
        $serial->delete();
        Logging::information('Delete Serial', $serial, 1, 1);
        return $this->sendResponse($serial, 'Serial deleted successfully.');
    }
}
