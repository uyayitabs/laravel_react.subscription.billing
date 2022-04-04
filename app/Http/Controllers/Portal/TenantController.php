<?php

namespace App\Http\Controllers\Portal;

use App\Models\BillingRun;
use App\Models\Group;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Http\Requests\TenantApiRequest;
use App\Services\BillingRunService;
use App\Services\TenantService;
use App\Services\GroupService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Spatie\QueryBuilder\QueryBuilder;

class TenantController extends BaseController
{
    protected $service;
    protected $billingRunService;
    protected $groupService;
    protected $subscriptionService;

    public function __construct()
    {
        $this->service = new TenantService();
        $this->groupService = new GroupService();
        $this->billingRunService = new BillingRunService();
    }

    /**
     * Return the specified tenant
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */

    public function show(int $id)
    {
        $query = QueryBuilder::for(Tenant::where('id', $id))
            ->allowedIncludes(Tenant::$scopes);

        return $this->sendSingleResult($query, 'Tenant retrieved successfully.');
    }
}
