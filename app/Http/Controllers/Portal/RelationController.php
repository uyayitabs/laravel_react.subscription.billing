<?php

namespace App\Http\Controllers\Portal;

use App\Models\Relation;
use App\Http\Requests\BankAccountApiRequest;
use App\Http\Requests\RelationApiRequest;
use App\Http\Requests\RelationCsApiRequest;
use App\Http\Resources\PortalCustomerResource;
use App\Services\AddressService;
use App\Services\BankAccountService;
use App\Services\RelationService;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RelationController extends BaseController
{
    protected $bankAccountService;
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->bankAccountService = new BankAccountService();
        $this->service = new RelationService();
    }

    /**
     * Get portal user data
     * @param mixed $relationId
     * @return PortalCustomerResource
     */
    public function getUserData($relationId)
    {
        if (isPortalRelationIdAuthorized($relationId)) {
            return $this->service->showPortalUser(
                $relationId,
                'Successfully retrieved customer'
            );
        }
        return $this->sendError('Unauthorized access', [], 403);
    }
}
