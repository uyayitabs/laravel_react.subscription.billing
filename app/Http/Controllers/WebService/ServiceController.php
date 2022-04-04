<?php

namespace App\Http\Controllers\WebService;

use App\Services\WebService;

class ServiceController extends BaseController
{
    public function __construct()
    {
        $this->service = new WebService();
    }

    public function index($provider)
    {
        $success = $this->service->manager($provider);
        return $this->sendResponse($success);
    }
}
