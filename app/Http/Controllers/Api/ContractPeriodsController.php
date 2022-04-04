<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\ContractPeriod;
use App\Http\Requests\ContractPeriodApiRequest;
use App\Services\ContractPeriodService;

class ContractPeriodsController extends BaseController
{
    protected $contractPeriodService;

    public function __construct()
    {
        $this->service = new ContractPeriodService();
    }

    public function index()
    {
        return $this->sendResults(
            $this->service->list()
        );
    }

    public function list()
    {
        return $this->sendResults(
            $this->service->list()
        );
    }

    public function create(ContractPeriodApiRequest $request)
    {
        return $this->sendSingleResult(
            $this->service->create()
        );
    }

    public function update(ContractPeriod $contractPeriod, ContractPeriodApiRequest $request)
    {
        return $this->sendResponse(
            $this->service->update($contractPeriod)
        );
    }
}
