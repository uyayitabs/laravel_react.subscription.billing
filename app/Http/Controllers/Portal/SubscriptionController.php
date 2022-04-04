<?php

namespace App\Http\Controllers\Portal;

use App\Http\Requests\SubscriptionApiRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;

class SubscriptionController extends BaseController
{
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SubscriptionService();
    }

    /**
     * List portal subscriptions
     *
     * @param mixed $relationId
     */
    public function listSubscriptions($relationId)
    {
        if (isPortalRelationIdAuthorized($relationId)) {
            return $this->sendNewPaginate(
                $this->service->listPortalSubscriptions($relationId),
                'Subscriptions retrieved successfully.'
            );
        }
        return $this->sendError('Unauthorized access', [], 403);
    }
}
