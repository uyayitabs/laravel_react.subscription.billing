<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SubscriptionLinePriceApiRequest;
use App\Models\SubscriptionLine;
use App\Services\SubscriptionLinePriceService;
use App\Models\SubscriptionLinePrice;

class SubscriptionLinePriceController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new SubscriptionLinePriceService();
    }

    /**
     * Return a list of subscription line prices
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Subscription line prices retrieved successfully.'
        );
    }

    /**
     * Store a newly created subscription line price
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscriptionLine $subscriptionLine, SubscriptionLinePriceApiRequest $request)
    {
        $this->authorize('createSubscriptionLinePrice', $subscriptionLine);
        $data = jsonRecode($request->all());

        $result = $this->service->create($subscriptionLine, $data);

        return $this->sendResult(
            array_key_exists('data', $result) ? $result['data'] : null,
            $result['message'],
            $result['success'] ? 200 : 422
        );
    }

    /**
     * Return a subscription line price
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Subscription line price retrieved successfully.'
        );
    }

    /**
     * Update a subscription line price
     *
     * @param SubscriptionLinePrice $subscriptionLinePrice
     * @param SubscriptionLinePriceApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SubscriptionLinePrice $subscriptionLinePrice, SubscriptionLinePriceApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $result = $this->service->update($subscriptionLinePrice, $data);

        return $this->sendResult(
            array_key_exists('data', $result) ? $result['data'] : null,
            $result['message'],
            $result['success'] ? 200 : 422
        );
    }

    /**
     * Remove the specified subscriptionLinePrice
     *
     * @param \App\Models\SubscriptionLinePrice $subscriptionLinePrice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubscriptionLinePrice $subscriptionLinePrice)
    {
        $subscriptionLinePrice->delete();
        return $this->sendResponse($subscriptionLinePrice, 'Subscription line price deleted successfully.');
    }
}
