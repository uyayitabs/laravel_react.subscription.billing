<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderApiRequest;
use App\Services\OrderService;

class OrderController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new OrderService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (blank(request()->bearerToken())) {
            return $this->sendError([], 'Unauthorized access.', 401);
        }
        return $this->sendNewPaginate(
            $this->service->list(null),
            'Orders retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OrderApiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderApiRequest $request)
    {
        $tenantId = currentTenant('id') ? currentTenant('id') : 7;
        $data = jsonRecode($request->all());
        $order = $this->service->create($data);
        if (!blank($order)) {
            $this->service->sendSuccessEmail($order->id, $tenantId);
            return $this->sendResult(
                ['order_id' => $order->id],
                'Order was saved successfully',
                200
            );
        } else {
            $this->service->sendErrorEmail($data);
            // Return error response
            return $this->sendError(
                'Error saving new order',
                $data,
                500
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (blank(request()->bearerToken())) {
            return $this->sendError([], 'Unauthorized access.', 401);
        }
        return $this->sendSingleResult(
            $this->service->show($id),
            'Order retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OrderApiRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderApiRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
