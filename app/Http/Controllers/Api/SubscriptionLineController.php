<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SubscriptionLineApiRequest;
use App\Models\NetworkOperator;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use App\Services\M7Service;
use App\Services\BrightBlueService;
use App\Services\NetworkOperatorService;
use App\Services\SubscriptionLineService;
use App\Models\SubscriptionLineMeta;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use LogicException;

class SubscriptionLineController extends BaseController
{
    protected $m7Service;
    protected $brightBlueService;
    protected $subscriptionLineService;

    public function __construct()
    {
        $this->m7Service = new M7Service();
        $this->brightBlueService = new BrightBlueService();
        $this->subscriptionLineService = new SubscriptionLineService();
    }

    /**
     * Return a paginated list of subscription lines
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = $this->subscriptionLineService->list();
        return $this->sendPaginate($query, 'Subscription lines retrieved successfully');
    }

    /**
     * Store a newly created subscriptionLine
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Subscription $subscription, SubscriptionLineApiRequest $request)
    {
        $this->authorize('createSubscriptionLine', $subscription);
        $data = jsonRecode($request->all());

        $query = $this->subscriptionLineService->create($subscription, $data);
        if (!empty($query["data"])) {
            return $this->sendPaginate($query["data"], 'Subscription Line created successfully.');
        }
        return $this->sendError(
            array_key_exists("message", $query) ? $query["message"] : "Error saving Subscription Line",
            request(Subscription::$fields),
            500
        );
    }

    /**
     * Display the specified subscriptionLine
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($subscriptionLine)
    {
        $this->authorize('view', $subscriptionLine);
        $query = $this->subscriptionLineService->show($subscriptionLine);
        return $this->sendSingleResult($query, 'Subscription line retrieved successfully.');
    }

    /**
     * Update subscription line
     *
     * @param \App\Models\SubscriptionLine $subscriptionLine
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SubscriptionLine $subscriptionLine, SubscriptionLineApiRequest $request)
    {
        $this->authorize('update', $subscriptionLine);
        $data = jsonRecode($request->all());
        $result = $this->subscriptionLineService->update($subscriptionLine, $data);
        if (!empty($result["data"])) {
            return $this->sendSingleResult($result["data"], 'Subscription Line updated successfully.');
        }
        return $this->sendError(
            array_key_exists("message", $result) ? $result["message"] : "Error updating Subscription Line",
            request(Subscription::$fields),
            500
        );
    }

    /**
     * Remove the specified subscriptionLine
     *
     * @param \App\Models\SubscriptionLine $subscriptionLine
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubscriptionLine $subscription_line)
    {
        $this->authorize('delete', $subscription_line);
        $query = $this->subscriptionLineService->delete($subscription_line);
        return $this->sendResponse($query, 'Subscription line deleted successfully.');
    }

    /**
     * Remove the specified subscriptionLine
     *
     * @param \App\Models\SubscriptionLine $subscriptionLine
     *
     * @return \Illuminate\Http\Response
     */
    public function m7(SubscriptionLine $subscriptionLine, $method)
    {
        $subscription = $subscriptionLine->subscription;
        $this->m7Service->setSubscription($subscription);
        return $this->sendResponse($subscription, '');
    }

    /**
     * Get SubscriptionLinePrices of a SubscriptionLine
     *
     * @param Subscription $subscription
     * @param SubscriptionLine $subscriptionLine
     */
    public function subscriptionLinePrices(Subscription $subscription, SubscriptionLine $subscriptionLine)
    {
        return $this->sendResponse($subscriptionLine->subscriptionLinePrices, '');
    }

    /**
     * Save new Serial of a SubscriptionLine
     *
     * @param SubscriptionLine $subscriptionLine
     */
    public function serial(SubscriptionLine $subscriptionLine)
    {
        $newSerialData = $this->subscriptionLineService->newSerial($subscriptionLine);
        return $this->sendResponse($newSerialData['data'], $newSerialData['message'], $newSerialData['status']);
    }

    /**
     * Process request
     *
     * @param mixed $provider
     * @param mixed $method
     * @param SubscriptionLine $subscriptionLine
     */
    public function processRequest($provider, $method, SubscriptionLine $subscriptionLine)
    {
        $processResult = $this->subscriptionLineService->processRequest($provider, $method, $subscriptionLine);
        if (500 == $processResult['code']) {
            return $this->sendError($processResult['msg'], [], $processResult['code']);
        }
        return $this->sendResponse($processResult['data'], $processResult['msg']);
    }

    /**
     * Get SubscriptionLinePrices of a SubscriptionLine
     *
     * @param SubscriptionLine $subscriptionLine
     */
    public function prices(SubscriptionLine $subscriptionLine)
    {
        return $this->sendResponse($this->subscriptionLineService->prices($subscriptionLine), '');
    }

    /**
     * Get gadgets / sub-menu of a SubscriptionLine
     *
     * @param SubscriptionLine $subscriptionLine
     */
    public function gadgets(SubscriptionLine $subscriptionLine)
    {
        return $this->sendResponse($subscriptionLine->gadgets, '');
    }

    /**
     * Process saving of SubscriptionLineMeta of a selected NetworkOperator
     *
     * @param SubscriptionLine $subscriptionLine
     */
    public function processNetworkOperator(SubscriptionLine $subscriptionLine)
    {
        // {"network":{"label":"BBR","value":16},"operator":{"label":"Teleplaza","value":1}}
        $params = jsonRecode(request()->all());
        $data = [
            'network_id' => $params['network']['value'],
            'operator_id' => $params['operator']['value']
        ];

        $service = new NetworkOperatorService();
        $subscriptionLineMeta = $service->saveSubscriptionLineMeta($subscriptionLine, $data);
        if ($subscriptionLineMeta) {
            return $this->sendResponse($subscriptionLine->refresh(), '');
        }
        return $this->sendError('Invalid network and/or operator value(s).', [], 500);
    }
}
