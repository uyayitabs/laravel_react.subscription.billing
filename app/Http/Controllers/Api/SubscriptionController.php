<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\DataViewModels\SubscriptionSummary;
use App\Http\Requests\SubscriptionApiRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PortalSubscriptionResource;
use App\Http\Resources\RelationPersonResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\JsonData;
use App\Models\Person;
use App\Models\Relation;
use App\Models\RelationsPerson;
use App\Services\AddressService;
use App\Services\PersonService;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Models\Tenant;
use Carbon\Carbon;
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
     * Return a paginated list of subscriptions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $relationId = request('relation_id');
        return $this->sendPaginateOrResult(
            $this->service->list($relationId),
            'Subscriptions retrieved successfully',
            function (Subscription $subscription) {
                return (new SubscriptionResource(
                    $subscription,
                    true
                ));
            }
        );
    }

    /**
     * Store a newly created subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscriptionApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $query = $this->service->create($data);

        if (!empty($query["data"])) {
            return $this->sendResult([
                'data' => $this->sendResult($this->service->show($query["data"]['id'])),
                'message' => 'Subscription created successfully.'
            ]);
        }

        return $this->sendError(
            array_key_exists("errorMessage", $query) ? $query["errorMessage"] : "Error saving Subscription",
            request(Subscription::$fields),
            500
        );
    }

    /**
     * Return the specified subscription
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Subscription $subscription)
    {
        $this->authorize('view', $subscription);
        return $this->service->show(
            $subscription->id,
            'Subscription retrieved successfully.'
        );
    }

    /**
     * Update the specified subscription
     *
     * @param \App\Models\Subscription $subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Subscription $subscription, SubscriptionApiRequest $request)
    {
        $this->authorize('update', $subscription);
        $data = jsonRecode($request->all());
        $result = $this->service->update(
            $data,
            $subscription
        );

        if ($result['success']) {
            return $this->sendResult(
                $result['data'],
                'Subscription updated successfully.'
            );
        }

        return $this->sendResult(
            $result['data'] ?? null,
            $result['message'],
            402
        );
    }

    /**
     * Remove the specified subscription
     *
     * @param \App\Models\Subscription $subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);
        $result = $this->service->delete($subscription);
        return $this->sendResult(
            $result['data'] ?? [],
            $result['message'],
            $result['success'] == true ? 200 : 400
        );
    }

    /**
     * Returns a list of latest subscriptions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        return $this->sendResults(
            $this->service->latest(currentTenant('id')),
            'Latest 10 subscriptions retrieved successfully.'
        );
    }

    /**
     * Return a paginated list of subscriptions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary()
    {
        return $this->sendPaginateOrResult(
            $this->service->summary(),
            'Subscriptions retrieved successfully',
            function (SubscriptionSummary $subscription) {
                $json_data = JsonData::where('subscription_id', $subscription->id)->get();
                if ($json_data->count()) {
                    $subscription->json_datas = $json_data;
                }
                return $subscription;
            }
        );
    }

    /**
     * Generate invoice, return download request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInvoiceFile($id)
    {
        $response = $this->service->getInvoicePdf($id);

        if ($response["file_exists"]) {
            return response()->file($response["file"]);
        }

        return $this->sendResponse("Invoice PDF not found.", []);
    }

    /**
     * Get subscription counts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        return $this->sendResult(
            $this->service->count(currentTenant('id')),
            'Subscription counts retrieved successfully.'
        );
    }

    /**
     * Return subscription lines list
     *
     * @param Subscription $subscription
     */
    public function subscriptionLines(Subscription $subscription)
    {
        return $this->service->getSubscriptionLines2($subscription);
    }

    /**
     * Process provision request
     *
     * @param mixed $provider
     * @param mixed $transaction
     * @param mixed $status
     * @param mixed $limit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function provision($provider, $transaction, $status, $limit)
    {
        $user = request()->user();

        if (!$user->is_super_admin && $user->username != 'chej@f2x.nl') {
            return $this->sendResult('', 'Permission denied');
        }

        $this->service->provision($provider, $transaction, $status, $limit);
        return $this->sendResult('', 'Provisioned');
    }

    /**
     * Return a list of addresses belong to a subscription.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addresses(Subscription $subscription)
    {
        $this->authorize('view', $subscription);
        $addressService = new AddressService();

        return $this->sendPaginateOrResult(
            $addressService->list($subscription->relation->id),
            'Relation addresses retrieved successfully',
            function (Address $address) {
                return (new AddressResource(
                    $address
                ));
            }
        );
    }

    /**
     * Return a list of persons belong to a subscription.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function persons(Subscription $subscription)
    {
        $this->authorize('view', $subscription);
        $personService = new PersonService();
        $relationId = $subscription->relation->id;
        return $this->sendPaginateOrResult(
            $personService->listRelationsPersons($relationId),
            'Relation persons retrieved successfully',
            function (Person $person) use ($relationId) {
                return (new RelationPersonResource(
                    $person,
                    RelationsPerson::where([['relation_id', $relationId], ['person_id', $person->id]])->first()
                ));
            }
        );
    }

    /**
     * Return a list of persons belong to a subscription.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptionLinePrices(Subscription $subscription)
    {
        $this->authorize('view', $subscription);
        return $this->service->subscriptionLinePrices($subscription);
    }

    /**
     * Return list of log_activities linked to a subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logActivities(Subscription $subscription)
    {
        return $this->sendNewPaginate(
            $this->service->logActivities($subscription),
            'Log Activities retrieved successfully'
        );
    }

    /**
     * Return a list of subscriptions with subscriptionLines.product.backend_api = lineProvisioning
     * @return \Illuminate\Http\JsonResponse
     */
    public function provisioningSubscriptions()
    {
        $tenantId = request()->query('tenant_id', currentTenant('id'));
        return $this->sendNewPaginate(
            $this->service->provisioningSubscriptions($tenantId),
            'Provisioning subscriptions retrieved successfully'
        );
    }

    /**
     * Get provisioning subscription count
     *
     * @return JsonResponse
     */
    public function provisioningSubscriptionsCount()
    {
        $tenantId = request()->query('tenant_id', currentTenant('id'));
        return $this->sendNewJson(
            $this->service->provisioningSubscriptionCount($tenantId),
            'Provisioning subscription count retrieved successfully'
        );
    }
}
