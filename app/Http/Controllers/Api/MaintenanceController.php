<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MaintenanceService;

class MaintenanceController extends Controller
{
    protected $maintenanceService;

    public function __construct()
    {
        $this->maintenanceService = new MaintenanceService();
    }

    public function maintenance()
    {
        $token = request()->bearerToken();
        if (config('maintenance.token') != $token) {
            return response('', 404);
        }

        $maintenance = request('maintenance');
        $this->maintenanceService->maintanance($maintenance);
        return response('', 200);
    }
}
