<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentInvoiceApiRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;

class PaymentsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PaymentService();
    }

    /**
     * Return a paginated list of payments
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResult($this->service->list(), '');
    }
}
