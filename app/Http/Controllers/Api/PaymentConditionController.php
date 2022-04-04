<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\PaymentCondition;
use App\Http\Requests\PaymentConditionApiRequest;
use App\Services\PaymentConditionService;

class PaymentConditionController extends BaseController
{
    protected $paymentConditionService;

    public function __construct()
    {
        $this->service = new PaymentConditionService();
    }

    public function index(Tenant $tenant)
    {
        $this->authorize('view', $tenant);
        return $this->sendNewPaginate(
            $this->service->list($tenant)
        );
    }

    public function create(Tenant $tenant, PaymentConditionApiRequest $request)
    {
        return $this->sendSingleResult(
            $this->service->create($tenant)
        );
    }

    public function update(PaymentCondition $paymentCondition, PaymentConditionApiRequest $request)
    {
        return $this->sendResponse(
            $this->service->update($paymentCondition)
        );
    }
}
