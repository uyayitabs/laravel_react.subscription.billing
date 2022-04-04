<?php

namespace App\Http\Controllers\Api;

use App\Models\NumberRange;
use App\Http\Requests\NumberRangeApiRequest;
use App\Models\NetworkOperator;
use App\Services\NetworkOperatorService;
use Illuminate\Http\Response;
use Illuminate\Contracts\Container\BindingResolutionException;

class NetworkOperatorsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new NetworkOperatorService();
    }

    /**
     * List out Network Operators
     */
    public function index()
    {
        return $this->sendResults(
            $this->service->list()
        );
    }

    /**
     * List out Network Operators
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->list()
        );
    }

    /**
     * List out network opts
     */
    public function networkOpts()
    {
        return $this->sendResults(
            $this->service->getNetworks()
        );
    }

    /**
     * Get Operator
     *
     * @param NetworkOperator $networkOperator
     */
    public function operators(NetworkOperator $networkOperator)
    {
        return $this->sendResults(
            $this->service->getOperators($networkOperator->network_id)
        );
    }

    /**
     * Save a new NetworkOperator
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = jsonRecode(request()->all(NetworkOperator::$fields));

        return $this->sendSingleResult(
            $this->service->create($data),
            'Network Operartor created successfully.'
        );
    }

    /**
     * Save a new NetworkOperator
     *
     * @return \Illuminate\Http\Response
     */
    public function update(NetworkOperator $networkOperator)
    {
        $data = jsonRecode(request()->all(NetworkOperator::$fields));

        return $this->sendSingleResult(
            $this->service->update($networkOperator, $data),
            'Network Operator updated successfully.'
        );
    }
}
