<?php

namespace App\Services;

use App\DataViewModels\SubscriptionSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Logging;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use App\Models\SubscriptionLinePrice;
use App\Models\PlanLine;
use App\Models\Product;
use App\Models\Relation;
use App\Models\SalesInvoice;
use App\Models\SubscriptionLineMeta;
use App\Filters\SubscriptionCustomerNumberSortFilter;
use App\Http\Resources\SubscriptionLineResource;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PortalSubscriptionResource;
use App\Http\Resources\ProvisioningSubscriptionCountResource;
use App\Http\Resources\ProvisioningSubscriptionResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionLinePriceResource;
use App\Jobs\SendM7Mail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SubscriptionService
{
    public const BACKEND_API = 'lineProvisioning';

    protected $proceed = false;
    protected $errorMessage = '';
    protected $logActivitiesService;
    protected $statusService;

    public function __construct()
    {
        $this->logActivitiesService = new LogActivitiesService();
        $this->statusService = new StatusService();
    }

    /**
     * Get list of subscriptions
     *
     * @param int|null $relationId
     * @return Builder
     */
    public function list(?int $relationId): Builder
    {
        $query = \Querying::for(Subscription::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->whereHas('relation', function (Builder $query) {
                $query->where('tenant_id', currentTenant('id'));
            })
            ->with(['relation', 'plan', 'billingPerson', 'statusSubscription', 'jsonDatas']);

        if ($relationId) {
            $query->where('relation_id', $relationId);
        }

        return $query;
    }

    /**
     * Get list of subscriptions
     *
     * @param int|null $relationId
     * @return Builder
     */
    public function summary(): Builder
    {
        $query = \Querying::for(SubscriptionSummary::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));

        return $query;
    }

    /**
     * List out portal subscription list(s)
     *
     * @param mixed $relationId
     */
    public function listPortalSubscriptions($relationId)
    {
        $subscriptionQuery = Subscription::where('relation_id', $relationId);

        // search filter
        $subscriptionQuery = $this->handleSearchFilters(
            $subscriptionQuery,
            request()->query("filter", [])
        );

        // sorting
        $subscriptionQuery = $this->handleSorting(
            $subscriptionQuery,
            request()->query('sort', '-subscription_start')
        );
        $subscriptionQuery->orderBy('subscription_start', 'DESC');
        // pagination (page & limit)
        $limit = request()->query('offset', 5);
        $subscriptionQuery = $subscriptionQuery->paginate($limit);

        // JsonResource implementation
        $subscriptionQuery->transform(function (Subscription $subscription) {
            return (new PortalSubscriptionResource(
                $subscription,
                'Subscription retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($subscriptionQuery);
    }

    /**
     * Handle searching request via parameters
     *
     * @param mixed $modelQuery
     * @param mixed $searchFilter
     * @return mixed
     */
    public function handleSearchFilters($modelQuery, $searchFilter)
    {
        if (array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $modelQuery = $modelQuery->search($value);
        }
        return $modelQuery;
    }

    /**
     * Handle sorting
     * @param mixed $modelQuery
     * @param mixed $sortFilter
     * @return mixed
     */
    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $modelQuery = QueryBuilder::for($modelQuery)
                ->allowedSorts([
                    'description',
                    'subscription_start',
                    'subscription_stop',
                    AllowedSort::custom('relation.customer_number', new SubscriptionCustomerNumberSortFilter(), ''),
                    AllowedSort::field('subscription_status.label', 'status')
                ]);
        }
        return $modelQuery;
    }

    /**
     * Get subscription data via $id param
     *
     * @param mixed $id
     * @param string $message
     * @param bool $code
     * @return SubscriptionResource
     */
    public function show($id)
    {
        return new SubscriptionResource(Subscription::find($id));
    }

    /**
     * Get subscription
     *
     */
    public function getOne($where = [], $queryOnly = true)
    {
        return [
            'data' => new SubscriptionResource(Subscription::where($where)->first, true),
            'message' => 'Subscription retrieved successfully.'
        ];
    }

    /**
     * Save subscription data
     *
     * @param array $data
     * @return array|Subscription
     */
    public function saveSubscription(array $data)
    {
        $attributes = filterArrayByKeys($data, Subscription::$fields);
        if ($attributes['status'] == '') {
            $attributes['status'] = 0;
        }

        if (!empty($attributes['billing_start']) && (Carbon::parse($attributes['billing_start']) < Carbon::parse($attributes['subscription_start']))) {
            return [
            'success' => false,
            'errorMessage' => "Billing Start must be after Subscription Start"
            ];
        }

        $relationIdParamExists = array_key_exists('relation_id', $attributes);
        $billingAddressParamExists = array_key_exists('billing_address', $attributes);
        $provisioningAddressParamExists = array_key_exists('provisioning_address', $attributes);
        $billingPersonParamExists = array_key_exists('billing_person', $attributes);
        $provisioningPersonParamExists = array_key_exists('provisioning_person', $attributes);

        $willValidateAddressPerson = ($billingAddressParamExists || $provisioningAddressParamExists ||
            $billingPersonParamExists || $provisioningPersonParamExists);

        if ($willValidateAddressPerson) {
            $response = RelationService::validateBillingProvisioningAddressPerson(
                $relationIdParamExists ? $attributes["relation_id"] : null,
                $billingAddressParamExists ? $attributes["billing_address"] : null,
                $billingPersonParamExists ? $attributes["billing_person"] : null,
                $provisioningAddressParamExists ? $attributes["provisioning_address"] : null,
                $provisioningPersonParamExists ? $attributes["provisioning_person"] : null
            );
            $this->proceed = $response["proceed"];
            $this->errorMessage = $response["errorMessage"];
        } else {
            $this->proceed = true;
        }

        if ($this->proceed) {
            $subscription = Subscription::create($attributes);
            if ($subscription && !empty($subscription->plan_id)) {
                $planLines = PlanLine::withRelations(['planLinePrice'])->wherePlanId($subscription->plan_id)->get();
                if ($planLines) {
                    foreach ($planLines as $planLine) {
                        if ($planLine->active) {
                            $newData = new SubscriptionLine([
                                'subscription_line_type' => $planLine->plan_line_type,
                                'plan_line_id' => $planLine->id,
                                'product_id' => $planLine->product_id,
                                'serial' => null,
                                'mandatory_line' => $planLine->mandatory_line,
                                'subscription_start' => $subscription->subscription_start,
                                'subscription_stop' => $subscription->subscription_stop,
                                'description' => $planLine->description,
                                'description_long' => $planLine->description_long
                            ]);

                            $subscriptionLine = $subscription->subscriptionLines()->save($newData);

                            if ($subscriptionLine) {
                                $standardConnectionProductIds = Product::where('backend_api', 'lineProvisioning')
                                    ->pluck('id')
                                    ->toArray();
                                $stanConnProductIdMatched = in_array(
                                    $planLine->product_id,
                                    $standardConnectionProductIds
                                );

                                if ($stanConnProductIdMatched) {
                                    $subscriptionLineMeta = new SubscriptionLineMeta([
                                        'key' => 'network_operator',
                                        'value' => $attributes['network_operator_id']
                                    ]);
                                    $subscriptionLine->subscriptionLineMeta()->save($subscriptionLineMeta);
                                }

                                if (isset($planLine->planLinePrice)) {
                                    $planLinePrice = $planLine->planLinePrice;
                                    $newData = new SubscriptionLinePrice([
                                        'parent_plan_line_id' => $planLinePrice->plan_line_id,
                                        'fixed_price' => $planLinePrice->fixed_price,
                                        'margin' => $planLinePrice->margin,
                                        'price_valid_from' => $planLinePrice->price_valid_from
                                    ]);
                                    $subscriptionLine->subscriptionLinePrice()->save($newData);
                                }
                            }
                        }
                    }
                }
            }
            Logging::information('Create Subscription', $subscription, 1, 1, $subscription->relation->tenant_id, 'subscription', $subscription->id);

            // Call UpdateSubscriptionTotals() stored procedure
            DB::statement("CALL `" . config('database.connections.mysql.database') . "`.`UpdateSubscriptionTotals`($subscription->id);");
            return $subscription;
        }
        Logging::error('Error saving Subscription', $attributes, 1, 0);
        return false;
    }

    /**
     * Create subscription
     *
     * @param array $data
     * @param bool $queryOnly
     * @return (SubscriptionResource|array|string)[]
     */
    public function create(array $data, $queryOnly = true)
    {
        $response = $this->saveSubscription($data);
        if ($response instanceof Subscription) {
            return [
                "data" => $this->show($response->id) ,
                "errorMessage" => '',
            ];
        } elseif (is_array($response) && array_key_exists('errorMessage', $response)) {
            return [
                "data" => null ,
                "errorMessage" => $response['errorMessage'],
            ];
        }
        return null;
    }

    /**
     * Update subscription
     * @param array $data
     * @param Subscription $subscription
     */
    public function update(array $data, Subscription $subscription)
    {
        $updateAttributes = filterArrayByKeys($data, Subscription::$fields);

        $proceed = false;
        $errorMessage = "";
        $log['old_values'] = $subscription->getRawDBData();
        $statusService = new StatusService();

        $terminatedStatusId = $statusService->getStatusId('subscription', 'terminated');
        if ($subscription->status == $terminatedStatusId) {
            return [
                "data" => null,
                "message" => "Cannot edit a terminated subscription"
            ];
        }

        if (
            !empty($updateAttributes['billing_start']) &&
            Carbon::parse($updateAttributes['billing_start']) < Carbon::parse($updateAttributes['subscription_start'])
        ) {
            return [
            'success' => false,
            'message' => "Billing Start must be after Subscription Start"
            ];
        }

        if (array_key_exists('status', $data) && $data['status'] == $terminatedStatusId) {
            $hasEndDate = true;
            if (empty($data['subscription_stop'])) {
                $hasEndDate = false;
            }
            if (!$data['update_line_stop'] && empty($data['subscription_stop'])) {
                foreach ($subscription->subscriptionLines as $line) {
                    if (!$line->subscription_stop) {
                        $hasEndDate = false;
                    }
                }
            }

            if (!$hasEndDate) {
                return [
                "data" => null,
                "message" => "Cannot terminate a subscription without having an end-date on subscription and subscription lines."
                ];
            }
        }

        if (array_key_exists('subscription_start', $updateAttributes)) {
            $ongoingStatusId = $statusService->getStatusId('subscription', 'ongoing');
            if (($subscription->status == $ongoingStatusId) && ($subscription->subscription_start->format("Y-m-d") <> $updateAttributes['subscription_start']) && $subscription->salesinvoiceLines()->count() > 0) {
                return [
                    "data" => null,
                    "message" => "Cannot alter starting date of subscription after an invoice has been created."
                ];
            }
        }


        $willValidateAddressPerson = ((array_key_exists('billing_address', $updateAttributes)) ||
            (array_key_exists('provisioning_address', $updateAttributes)) ||
            (array_key_exists('billing_person', $updateAttributes)) ||
            (array_key_exists('provisioning_person', $updateAttributes)));

        if ($willValidateAddressPerson) {
            $relationId = $subscription->relation_id;
            if (array_key_exists('relation_id', $updateAttributes)) {
                $relationId = $updateAttributes["relation_id"];
            }

            $billingAddress = $subscription->billing_address;
            if (array_key_exists('billing_address', $updateAttributes)) {
                $billingAddress = $updateAttributes["billing_address"];
            }

            $billingPerson = $subscription->billing_person;
            if (array_key_exists('billing_person', $updateAttributes)) {
                $billingPerson = $updateAttributes["billing_person"];
            }

            $provisioningAddress = $subscription->provisioning_address;
            if (array_key_exists('provisioning_address', $updateAttributes)) {
                $provisioningAddress = $updateAttributes["provisioning_address"];
            }

            $provisioningPerson = $subscription->provisioning_person;

            if (array_key_exists('provisioning_person', $updateAttributes)) {
                $provisioningPerson = $updateAttributes["provisioning_person"];
            }

            $response = RelationService::validateBillingProvisioningAddressPerson(
                $relationId,
                $billingAddress,
                $billingPerson,
                $provisioningAddress,
                $provisioningPerson
            );
            $proceed = $response["proceed"];
            $errorMessage = $response["errorMessage"];
        } else {
            $proceed = true;
        }

        if ($proceed) {
            $updateLineSubscriptionStop = array_key_exists(
                'update_line_stop',
                $updateAttributes
            ) && boolval($updateAttributes['update_line_stop']);
            $updateLineSubscriptionStart = array_key_exists(
                'update_line_start',
                $updateAttributes
            ) && boolval($updateAttributes['update_line_start']);

            DB::beginTransaction();
            if ($updateLineSubscriptionStop) {
                $linesService = new SubscriptionLineService();
                $subscriptionStop = $data['subscription_stop'];
                $subscriptionLines = $subscription->subscriptionLines()
                    ->whereNull('subscription_stop')
                    ->get();

                foreach ($subscriptionLines as $subscriptionLine) {
                    $lineUpdateResult = $linesService->update($subscriptionLine, ['subscription_stop' => $subscriptionStop]);
                    if (!$lineUpdateResult['success']) {
                        DB::rollBack();
                        return $lineUpdateResult;
                    }
                }
            }

            if ($updateLineSubscriptionStart) {
                $linesService = new SubscriptionLineService();
                $subscriptionStart = $data['subscription_start'];
                $subscriptionLines = $subscription->subscriptionLines()->get();
                foreach ($subscriptionLines as $subscriptionLine) {
                    $lineUpdateResult = $linesService->update($subscriptionLine, ['subscription_start' => $subscriptionStart]);
                    if (!$lineUpdateResult['success']) {
                        DB::rollBack();
                        return $lineUpdateResult;
                    }
                }
            }

            $subscription->update($updateAttributes);
            $log['new_values'] = $subscription->getRawDBData();
            $log['changes'] = $subscription->getChanges();
            Logging::information('Update Subscription', $log, 1, 1, $subscription->relation->tenant_id, 'subscription', $subscription->id);

            $subscription->fresh();

            // Call UpdateSubscriptionTotals() stored procedure
            DB::commit();
            DB::statement("CALL `" . config('database.connections.mysql.database') . "`.`UpdateSubscriptionTotals`($subscription->id);");

            return [
                "success" => true,
                "data" => $this->show($subscription->id),
                "message" => null
            ];
        } else {
            Logging::error('Error updating Subscription', $updateAttributes, 1, 1);
            return [
                "success" => false,
                "data" => null,
                "message" => $errorMessage
            ];
        }
    }

    /**
     * Delete a subscription
     *
     * @param Subscription $subscription
     * @return BaseResourceCollection
     */
    public function delete(Subscription $subscription)
    {
        $hasSerial = false;
        $hasJsonData = false;
        $lines = $subscription->subscriptionLines;
        foreach ($lines as $line) {
            if (!empty($line->serial)) {
                $hasSerial = true;
            }
            if ($line->jsonDatas()->exists()) {
                $hasJsonData = true;
            }
        }
        $salesLineExists = $subscription->salesInvoiceLines()->exists();
        if ($hasSerial || $hasJsonData || $salesLineExists) {
            return [
            'success' => false, 'message' => 'Cannot delete a Subscription where one of the lines has a serial, json data or invoice line'
            ];
        }
        Logging::information('Delete Subscription', $subscription, 1, 1);
        $subscription->delete();
        return [
            'success' => true, 'message' => 'Subscription deleted successfully',
            'data' => $this->listUnpaginated($subscription->relation_id)
        ];
    }

    /**
     * Get latest subscriptions
     *
     * @param mixed $tenantId
     * @return mixed
     */
    public function latest($tenantId)
    {
        $query = Subscription::latest()
            ->whereHas('relation', function (Builder $query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->with(['plan'])
            ->take(10);
        return $query;
    }

    /**
     * Subscription count
     *
     * @param mixed $tenantId
     * @return mixed
     */
    public function count($tenantId)
    {
        $relations = Relation::where('tenant_id', $tenantId)->withCount('subscriptions')->get();
        $result = $relations->sum(function ($relation) {
            return $relation->subscriptions_count;
        });

        return $result;
    }

    /**
     * Get person(s) linked to a subscription
     *
     * @param Subscription $subscription
     * @return Response
     */
    public function persons(Subscription $subscription)
    {
        $personService = new PersonService();
        return $personService->list($subscription->relation_id)->get();
    }

    /**
     * Get address(es) linked to a subscription
     *
     * @param Subscription $subscription
     * @return BaseResourceCollection
     */
    public function addresses(Subscription $subscription)
    {
        $addressService = new AddressService();
        return $addressService->list($subscription->relation_id)->get();
    }

    /**
     * Call provider:process_subscription artisan command
     *
     * @param mixed $provider
     * @param mixed $transaction
     * @param mixed $status
     * @param mixed $limit
     */
    public function provision($provider, $transaction, $status, $limit)
    {
        $arguments = [
            "backend_api" => $provider,
            "transaction" => $transaction,
            "status" => $status,
            "limit" => $limit
        ];
        Artisan::call("provider:process_subscription", $arguments);
    }

    /**
     * Get provider subscriptions
     *
     * @param mixed $backendApi
     * @param string $transaction
     * @param mixed|null $status
     * @param mixed|null $limit
     * @param mixed|null $product
     * @return mixed
     */
    public function getProviderSubscriptions(
        $backendApi,
        $transaction = 'new',
        $status = null,
        $limit = null,
        $product = null
    ) {
        $query = Subscription::where('subscription_start', '<=', now());
        if ($status == 'all') {
            $status = null;
        }
        if ($backendApi == "brightblue") {
            $query->whereHas('subscriptionLines', function ($query) use ($backendApi, $product) {
                if ($product) {
                    $query->where('product_id', $product);
                }

                $query->whereHas('product', function ($query1) use ($backendApi) {
                    $query1->where('backend_api', 'LIKE', "%{$backendApi}%");
                });

                // $query->whereDoesntHave("jsonData");
            });
        } elseif ($backendApi == "m7") {
            if ('migration' == $transaction) {
                $query->whereHas('subscriptionLines', function ($query) use ($backendApi, $product) {
                    if ($product) {
                        $query->where('product_id', $product);
                    }
                    $query->whereHas('product', function ($query1) use ($backendApi) {
                        $query1->where('backend_api', $backendApi);
                        $query1->whereHas('jsonData', function ($query2) {
                            $query2->where('json_data->m7->type', 'stb');
                        });
                    });
                })->whereHas('jsonDatas', function ($query) use ($transaction, $status) {
                    if ($status) {
                        $query->where('json_data->m7->status', '=', $status);
                    } else {
                        $query->where('json_data->m7->status', '<>', 'Provisioned');
                    }
                    $query->where('json_data->m7->transaction', $transaction);
                });
            } else {
                $query->active()->whereHas('subscriptionLines', function ($query) use ($backendApi) {
                    $query->whereHas('product', function ($query1) use ($backendApi) {
                        $query1->where('backend_api', $backendApi);
                        $query1->whereHas('jsonData', function ($query2) {
                            $query2->where('json_data->m7->type', 'stb');
                        });
                    });
                    $query->has('itemSerial');
                });
            }
        } elseif ($backendApi == "lineProvisioning") {
            $query->whereHas('subscriptionLines', function ($lineQuery) use ($backendApi) {
                $lineQuery->whereHas('product', function ($productQuery) use ($backendApi) {
                    $productQuery->where('backend_api', '=', "{$backendApi}");
                });
            });
        }

        if ($limit && $limit != 'all') {
            $query->limit($limit);
        }
        $query->distinct('id');
        $subscriptions = $query->get();

        return $subscriptions;
    }

    /**
     * Get subscription lines
     *
     * @param Subscription $subscription
     * @return SubscriptionLineResource
     */
    public function getSubscriptionLines2(Subscription $subscription)
    {
        $subscriptionLines = $subscription->subscriptionLines();
        return SubscriptionLineResource::collection($subscriptionLines->get());
    }

    /**
     * Get subscription lines
     *
     * @param Subscription $subscription
     * @return BaseResourceCollection
     */
    public function getSubscriptionLines(Subscription $subscription)
    {
        // pagination (page & limit)
        $limit = request()->query('offset', 10);
        $subscriptionLines = $subscription->subscriptionLines()->paginate($limit);

        // TODO:: sorting
        $sortFilter = request()->query('sort', 'id');

        // JsonResource implementation
        $subscriptionLines->transform(function (SubscriptionLine $subscriptionLine) {
            return (new SubscriptionLineResource($subscriptionLine));
        });

        return new BaseResourceCollection($subscriptionLines);
    }

    /**
     * Send M7 account created
     *
     * @param Subscription $subscription
     * @param string $method
     */
    public function sendM7Account(Subscription $subscription, $method = 'CreateMyAccount')
    {
        $relation = $subscription->relation;
        $jsonData = $subscription->jsonData;

        $data = [
            "user_fullname" => $subscription->person_provisioning->full_name,
            "email" => $subscription->person_provisioning->email,
            "password" => $jsonData->json_data['account']['Password'],
            "slug" => "fiber",
            "tenant" => $relation->tenant->name
        ];

        SendM7Mail::dispatchNow(
            $relation->tenant,
            $data,
            $relation->customer_email,
            $method
        );
    }

    /**
     * Get suscription ending
     *
     * @param mixed $backend_api
     * @return mixed
     */
    public function getSubscriptionsEnding($backend_api)
    {
        $query = Subscription::where('subscription_start', '<=', now())
            ->whereHas('jsonData', function ($query) use ($backend_api) {
                $key = "json_data->$backend_api";
                $query->whereNotNull($key);
                $query->where($key . '->status', 'Provisioned');
            })
            ->whereHas('subscriptionLines', function ($query) {
                $query->whereNotNull('subscription_stop');
                $query->where('subscription_stop', '<=', now());
            })
            ->orWhere('subscription_stop', '<=', now());

        return $query->get();
    }

    /**
     * Get invoice PDF
     *
     * @param mixed $id
     * @return array
     */
    public function getInvoicePdf($id)
    {
        $salesInvoice = SalesInvoice::find($id);
        return [
            "file_exists" => File::exists($salesInvoice->invoice_file_full_path),
            "file" => $salesInvoice->invoice_file_full_path
        ];
    }

    /**
     * Get subscription line price(s)
     *
     * @param Subscription $subscription
     * @return SubscriptionLinePriceResource
     */
    public function subscriptionLinePrices(Subscription $subscription)
    {
        $query = SubscriptionLinePrice::whereHas('subscriptionLine', function ($query) use ($subscription) {
            $query->where('subscription_id', $subscription->id);
        });
        return SubscriptionLinePriceResource::collection($query->get());
    }

    /**
     * Get log_activities
     *
     * @param Subscription $subscription
     * @return BaseResourceCollection
     */
    public function logActivities(Subscription $subscription)
    {
        return $this->logActivitiesService->listBy($subscription->id);
    }

    /**
     * Relation provisioniong subscriptions
     *
     * @param mixed $tenantId
     * @param mixed $status
     * @return mixed
     */
    public function relationProvSubscriptions($tenantId, $status)
    {
        return Relation::where('tenant_id', $tenantId)
            ->whereHas(
                'subscriptions',
                function ($subscriptionQuery) use ($status) {
                    $subscriptionQuery->where('status', 0)
                        ->whereHas(
                            'subscriptionLines',
                            function ($lineQuery) use ($status) {
                                $lineQuery->whereHas(
                                    'product',
                                    function ($productQuery) use ($lineQuery) {
                                        $backendApi = $this::BACKEND_API;
                                        $productQuery->where('backend_api', '=', "{$backendApi}");
                                    }
                                )->whereHas(
                                    'subscriptionLineMeta',
                                    function ($subscriptionLineMetaQuery) {
                                    }
                                );

                                switch ($status) {
                                    case 'new':
                                        $newStatusIds = [$this->statusService->getStatusId('connection', 'inactive')];
                                        $lineQuery->whereNull('status_id')->orWhereIn('status_id', $newStatusIds);
                                        break;

                                    case 'provisioning':
                                        $provisioningStatusIds = [
                                            $this->statusService->getStatusId('connection', 'check_pending'),
                                            $this->statusService->getStatusId('connection', 'order_pending'),
                                            $this->statusService->getStatusId('connection', 'migration_confimed'),
                                            $this->statusService->getStatusId('connection', 'migration_pending'),
                                            $this->statusService->getStatusId('connection', 'cancel_pending'),
                                            $this->statusService->getStatusId('connection', 'termination_pending'),
                                        ];
                                        $lineQuery->whereIn('status_id', $provisioningStatusIds);
                                        break;

                                    case 'failed':
                                        $failedStatusIds = [
                                            $this->statusService->getStatusId('connection', 'error'),
                                            $this->statusService->getStatusId('connection', 'check_error'),
                                            $this->statusService->getStatusId('connection', 'order_error'),
                                            $this->statusService->getStatusId('connection', 'migration_error'),
                                            $this->statusService->getStatusId('connection', 'cancel_error'),
                                            $this->statusService->getStatusId('connection', 'rejected'),
                                        ];
                                        $lineQuery->whereIn('status_id', $failedStatusIds);
                                        break;

                                    case 'active':
                                        $activeStatusIds = [$this->statusService->getStatusId('connection', 'active')];
                                        $lineQuery->whereIn('status_id', $activeStatusIds);
                                        break;

                                    default:
                                        $lineQuery->where('status_id', "*$status");
                                        break;
                                }
                            }
                        );
                }
            );
    }

    /**
     * Get provisioning subscriptions
     *
     * @param mixed $tenantId
     * @return AnonymousResourceCollection
     */
    public function provisioningSubscriptions($tenantId)
    {
        $status = request()->query('status', '');
        $query = $this->relationProvSubscriptions($tenantId, $status);

        /**
         * Sort functionality
         * TODO: Sort by updated subscription_line
         */
        $sort = request()->query('sort', 'updated_at');
        $sequence = Str::contains($sort, '-') ? 'DESC' : 'ASC';
        $columnName = str_replace('-', '', $sort);
        $query->orderBy($columnName, $sequence);

        $limit = request()->query('offset', 10);
        $paginatedQuery = $query->paginate($limit);

        return ProvisioningSubscriptionResource::collection($paginatedQuery)
            ->additional(['meta' => [
                'key' => 'value',
            ]]);
    }

    /**
     * Get provisioning subscription count
     *
     * @param mixed $tenantId
     * @return ProvisioningSubscriptionCountResource
     */
    public function provisioningSubscriptionCount($tenantId)
    {
        $newCount = $this->relationProvSubscriptions($tenantId, 'new')->count();
        $provisioningCount = $this->relationProvSubscriptions($tenantId, 'provisioning')->count();
        $failedCount = $this->relationProvSubscriptions($tenantId, 'failed')->count();
        $activeCount = $this->relationProvSubscriptions($tenantId, 'active')->count();

        $data = collect([
            'total_new' => $newCount,
            'total_provisioning' => $provisioningCount,
            'total_failed' => $failedCount,
            'total_active' => $activeCount
        ]);

        return new ProvisioningSubscriptionCountResource($data);
    }

    /**
     * Get list of subscriptions not paginated
     *
     * @param mixed $relationId
     * @return BaseResourceCollection
     */
    public function listUnpaginated($relationId)
    {
        $subscriptionsQuery = Subscription::where('relation_id', $relationId)
            ->orderBy('subscription_start', 'DESC')
            ->get();


        $subscriptionsQuery->transform(function (Subscription $subscription) {
            return (new SubscriptionResource(
                $subscription,
                true
            ));
        });
        return $subscriptionsQuery;
    }
}
