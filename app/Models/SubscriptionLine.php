<?php

namespace App\Models;

use Logging;
use App\Services\NetworkOperatorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Services\StatusService;
use App\Traits\HasSubscriptionTrait;
use App\Traits\HasPlanSubscriptionLineTypeTrait;
use App\Traits\HasSubscriptionLinePriceTrait;
use App\Traits\HasSubscriptionLineTypesTrait;
use App\Traits\HasLineJsonDataM7Trait;
use App\Traits\HasLineJsonDataBrightblueTrait;
use App\Traits\HasStatusTrait;
use App\Traits\HasSubscriptionLineMeta;

class SubscriptionLine extends BaseModel
{
    use HasSubscriptionTrait;
    use HasPlanSubscriptionLineTypeTrait;
    use HasSubscriptionLinePriceTrait;
    use HasSubscriptionLineTypesTrait;
    use HasLineJsonDataM7Trait;
    use HasLineJsonDataBrightblueTrait;
    use HasStatusTrait;
    use HasSubscriptionLineMeta;

    protected $fillable = [
        'subscription_id',
        'subscription_line_type',
        'plan_line_id',
        'product_id',
        'serial',
        'mandatory_line',
        'subscription_start',
        'subscription_stop',
        'description',
        'description_long',
        'status_id',
        'last_invoice_stop'
    ];

    public static $fields = [
        'id',
        'subscription_id',
        'subscription_line_type',
        'plan_line_id',
        'product_id',
        'serial',
        'mandatory_line',
        'subscription_start',
        'subscription_stop',
        'description',
        'description_long',
        'status_id',
        'network_operator_id',
        'last_invoice_stop'
    ];

    public static $scopes = [
        'subscription',
        'subscription.relation',
        'subscription.relation.persons',
        'subscription.relation.addresses',
        'product',
        'product.product-type',
        'subscription-line-price',
        'subscription-line-prices',
        'lineType'
    ];

    public static $withScopes = [
        'subscription',
        'subscription.relation',
        'subscription.relation.persons',
        'subscription.relation.addresses',
        'product',
        'product.productType',
        'subscriptionLinePrice',
        'subscriptionLinePrices',
        'lineType'
    ];

    protected $appends = [
        // 'line_price',
        // 'line_type_name',
        // 'subscription_line_price_margin',
        // 'subscription_line_price_valid',
        // 'subscription_line_price_fixed_price',
        // 'json_data_product_type',
        // 'backend_api',
        // 'has_gadget',
        // //'gadgets',
        // 'my_serial',
        // 'm7_main_stb'
    ];

    protected $casts = [
        'subscription_start' => 'datetime:Y-m-d',
        'subscription_stop' => 'datetime:Y-m-d',
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function ($subscriptionLine) {
            $cacheKey = 'SubscriptionLine' . $subscriptionLine->subscription_id;
            if (Cache::store('database')->has($cacheKey)) {
                Cache::store('database')->forget($cacheKey);
            }
        });

        static::created(function ($subscriptionLine) {
            $cacheKey = 'SubscriptionLine' . $subscriptionLine->subscription_id;
            if (Cache::store('database')->has($cacheKey)) {
                Cache::store('database')->forget($cacheKey);
            }
        });

        static::deleting(function ($subscriptionLine) {
            $cacheKey = 'SubscriptionLine' . $subscriptionLine->subscription_id;
            if (Cache::store('database')->has($cacheKey)) {
                Cache::store('database')->forget($cacheKey);
            }
        });
    }

    /**
     * Get the prices
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function subscriptionLinePrices()
    {
        return $this->hasMany(SubscriptionLinePrice::class)->orderBy('price_valid_from', 'DESC');
    }

    /**
     * Get plan line
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function planLine()
    {
        return $this->belongsTo(PlanLine::class, 'plan_line_id', 'id');
    }

    /**
     * Get plan line
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function planLinesRecursive()
    {
        return $this->HasOne(PlanLine::class, 'id', 'plan_line_id')->with('parentPlanLinesRecursive');
    }

    /**
     * Get product
     *
     * @return \Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->with('productType');
    }

    /**
     * Get product
     *
     * @return \Subscription
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get serial
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function itemSerial()
    {
        return $this->belongsTo(Serial::class, 'serial', 'serial');
    }

    /**
     * Get SalesInvoiceLines
     *
     * @return \SalesInvoiceLine[]
     */
    public function salesInvoiceLines()
    {
        return $this->hasMany(SalesInvoiceLine::class, 'subscription_line_id', 'id');
    }

    /**
     * Get JsonData
     *
     * @return \JsonData
     */
    public function jsonDatas()
    {
        return $this->hasMany(JsonData::class, 'subscription_line_id', 'id');
    }

    /**
     * Get price of the subscription_line based on subscription_line_price
     *
     * @return float
     */
    public function getLinePriceAttribute()
    {
        $totalPrice = floatval(0);
        $subscriptionLinePrice = $this->subscriptionLinePrice()->first();
        if (!empty($subscriptionLinePrice)) {
            $totalPrice = floatval($subscriptionLinePrice->fixed_price);
        }
        return $totalPrice;
    }

    /**
     * Get diff in days of subscription_start and subscription_stop
     *
     * @return int
     */
    public function getStartStopDiffInDays()
    {
        $planStart = Carbon::parse($this->subscription_start);
        $subscriptionStop = now()->addYears(100);
        if (!empty($this->subscription_stop)) {
            $subscriptionStop = Carbon::parse($this->subscription_stop);
        }
        return $planStart->diffInDays($subscriptionStop);
    }

    /**
     * Get index among SubscriptionLine siblings function
     *
     * @return int
     */
    public function getIndexAmongSiblings()
    {
        $subscription = $this->subscription()->first();
        $lineIds = $subscription->subscriptionLines()->orderBy("id", "asc")->pluck("id")->toArray();
        return array_search($this->id, $lineIds);
    }

    /**
     * Get previous SubscriptionLine sibling
     *
     * @return \SubscriptionLine
     */
    public function getPreviousSibling()
    {
        $currentIndex = $this->getIndexAmongSiblings();
        if ($currentIndex > -1) {
            $subscription = $this->subscription()->first();
            $lineIds = $subscription->subscriptionLines()->orderBy("id", "asc")->get();
            return $lineIds[$currentIndex - 1];
        }
        return null;
    }


    /**
     * Get associated data
     *
     * @return \SubscriptionLine
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }


    /**
     * Get plan_line_price data of a plan_line
     *
     * @param PlanLine $planLine
     * @return array
     */
    public static function getPlanLinePriceData(PlanLine $planLine)
    {
        $planLinePrice = $planLine->planLinePrice()->first();
        return [
            'plan_id' => $planLine->plan_id,
            'plan_line_cnt' => $planLine->planLinePrice()->count(),
            'plan_line_id' => $planLine->id,
            'fixed_price' => $planLinePrice ? $planLinePrice->fixed_price : floatval(0),
            'margin' => $planLinePrice ? $planLinePrice->margin : floatval(0),
        ];
    }


    /**
     * Get parent plan hierarchy function
     *
     * @param integer $planLineId
     * @param array $planData
     * @return array
     */
    public static function getParentPlanHierarchy($planLineId = 0, &$planData = [])
    {
        $planLine = PlanLine::findOrFail($planLineId);

        if (!is_null($planLine)) {
            $hasParentPlanLine = !empty($planLine->parent_plan_line_id);
            if ($hasParentPlanLine) {
                $planData[] = SubscriptionLine::getPlanLinePriceData($planLine);
                return SubscriptionLine::getParentPlanHierarchy($planLine->parent_plan_line_id, $planData);
            }

            $planData[] = SubscriptionLine::getPlanLinePriceData($planLine);

            $planData = array_reverse($planData);
        }
        return $planData;
    }

    /**
     * Get subscription line starting point
     *
     * @return Carbon
     */
    public function getStartingPointDate()
    {
        $subscription = $this->subscription;
        $relation = $subscription->relation;
        $tenant = $relation->tenant;

        $tenantInvoiceStartCalculationDate = null;
        if (Carbon::make($tenant->invoice_start_calculation)) {
            $tenantInvoiceStartCalculationDate = Carbon::make($tenant->invoice_start_calculation)->format('Y-m-d');
        }
        $subscriptionStart = null;
        if (Carbon::make($subscription->subscription_start)) {
            $subscriptionStart = Carbon::make($subscription->subscription_start)->format('Y-m-d');
        }
        $referenceStartingDates = [
            $subscriptionStart,
            $tenantInvoiceStartCalculationDate,
            Config::get("constants.invoice_starting_point")
        ];
        $filteredDates = array_unique(array_filter($referenceStartingDates));
        $computedStartingDate = collect($filteredDates)->sortByDesc(null)->values()[0];

        $returnDate = null;
        if ($this->subscription_start > $computedStartingDate) {
            $returnDate = Carbon::parse($this->subscription_start);
        } else {
            $returnDate = Carbon::parse($computedStartingDate);
        }

        return $returnDate;
    }

    public function getFixedPrice()
    {
        $price = $this->subscriptionLinePrices()->first();
        if (!$price) {
            return floatval(0);
        }

        if ($price->fixed_price) {
            return $price->fixed_price;
        } else {
            $cost = 0;
            $margins[] = $this->margin;
            $planLine = $this->planLinesRecursive()->with('planLinePrice')->first();
            do {
                if (!$planLine || !$planLine->planLinePrice) {
                    break;
                }
                $planLinePrice = $planLine->planLinePrice;
                if ($planLinePrice->fixed_price) {
                    $cost = $planLinePrice->fixed_price;
                } else {
                    $margins[] = $planLinePrice->margin;
                }
                $planLine = $planLine->parentPlanLines;
            } while ($planLine);
        }

        foreach ($margins as $margin) {
            $cost = $cost * (1 + $margin);
        }

        return $cost;
    }

    public function getVatPercentage($tenantId)
    {
        $tp = TenantProduct::where([['tenant_id', $tenantId], ['product_id', $this->product_id]])->firstOrFail();
        if ($tp && $tp->vatCode) {
            return $tp->vatCode->vat_percentage;
        }
        return floatval(0);
    }

    /**
     * Get fixed price based on plan hierarchy function
     *
     * @param integer $planLineId
     * @param float $margin
     * @return float
     */
    public static function getFixedPriceFromPlanHierarchy($planLineId = 0, $margin = 0.0)
    {
        $fixedPrice = floatval(0);
        $planLineData = [];
        $planLineData = SubscriptionLine::getParentPlanHierarchy($planLineId, $planLineData);

        /**
         * plan_id,
         * plan_line_id,
         * fixed_price,
         * margin
         */
        $rootPlan = $planLineData[0];

        for ($i = 1; $i < count($planLineData); $i++) {
            $thisPlanLine = $planLineData[$i];
            $computedMargin = floatval($thisPlanLine["margin"] + 1);
            if ($i > 1) {
                $thisPlanLine = $planLineData[$i - 1];
                if (!empty($thisPlanLine["computed_price"])) {
                    $sourcePrice = $thisPlanLine["computed_price"];
                } else {
                    $sourcePrice = $rootPlan["fixed_price"];
                }
            } else {
                $sourcePrice = $rootPlan["fixed_price"];
            }
            $computedTotalPrice = $computedMargin * $sourcePrice;
            $planLineData[$i]["computed_price"] = $computedTotalPrice;
        }

        $lastPlan = end($planLineData);
        $fixedPrice = array_key_exists('computed_price', $lastPlan) ?
            floatval(1 + $margin) * $lastPlan['computed_price'] : floatval(0);
        return $fixedPrice;
    }

    /**
     * Set subscription_start attribute
     *
     * @param mixed $value
     */
    public function setSubscriptionStartAttribute($value)
    {
        $this->attributes['subscription_start'] = dateFormat($value);
    }

    /**
     * Set subscription_stop attribute
     *
     * @param mixed $value
     */
    public function setSubscriptionStopAttribute($value)
    {
        $this->attributes['subscription_stop'] = dateFormat($value);
    }

    /**
     * Get subscription line status attribute
     *
     * @return mixed
     */
    public function getLineStatusAttribute()
    {
        $status = [];
        $backend_api = $this->product->backend_api;
        if ($backend_api) {
            switch ($backend_api) {
                case 'm7':
                    $statusType = 'm7-' . $this->json_data_product_type;
                    break;
                case 'brightblue':
                    $statusType = '';
                    break;

                case 'lineProvisioning':
                    $statusType = 'connection';
                    break;
            }

            $status = $this->statuses()->whereHas('type', function ($query) use ($statusType) {
                $query->where('type', $statusType);
            })->first();
        }

        return $status;
    }

    /**
     * Get json_data product type
     *
     * @return mixed
     */
    public function getJsonDataProductTypeAttribute()
    {
        $jsonDta = $this->product ? $this->product->jsonData : null;
        $provider = null;

        if ($jsonDta) {
            if (array_key_exists('m7', $jsonDta->json_data)) {
                $provider = "m7";
            }

            if (array_key_exists('brightblue-fiber', $jsonDta->json_data)) {
                $provider = "brightblue-fiber";
            }
        }

        return $jsonDta && isset($jsonDta->json_data[$provider]) ?
            $jsonDta->json_data[$provider]['type'] : null;
    }

    /**
     * Get backend_api attribute
     *
     * @return mixed
     */
    public function getBackendApiAttribute()
    {
        return $this->product ? $this->product->backend_api : null;
    }

    /**
     * Get is_started attribute
     *
     * @return bool
     */
    public function getIsStartedAttribute()
    {
        return $this->subscription_start == null ||
            $this->subscription_start->format('Y-m-d') <= now()->format('Y-m-d');
    }

    /**
     * Get serial item attribute
     *
     * @return mixed
     */
    public function getSerialItemAttribute()
    {
        return $this->itemSerial;
    }

    /**
     * Get my serial attribute
     *
     * @return array
     */
    public function getMySerialAttribute()
    {
        $serial = '';
        $mac = '';
        $serial_item = $this->serial_item;
        if ($serial_item) {
            $serial = $serial_item->serial;
            $mac = $serial_item->json_data && isset($serial_item->json_data['serial']) ?
                strtoupper(implode(':', str_split($serial_item->json_data['serial']['mac'], 2))) : '';
        }
        return ['serial' => $serial, 'mac' => $mac];
    }

    /**
     * Get m7 ProductID
     * @return int
     */
    public function getM7ProductIdAttribute()
    {
        $prodJsonData = $this->product->jsonData;
        return $prodJsonData &&
        array_key_exists('m7', $prodJsonData->json_data) ? $prodJsonData->json_data['m7']['productId'] : null;
    }

    /**
     * Get is completed with price
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        return $this->subscriptionLinePrices()->count() > 0;
    }

    /**
     * Get m7 main stb
     * @return bool
     */
    public function getM7MainStbAttribute()
    {
        $subscription = $this->subscription;
        return $subscription->is_m7 && 'stb' == $this->json_data_product_type
            && 'Provisioning' != $this->m7_provisioning_status
            && strtolower($this->mac_address) == strtolower($subscription->main_mac_address);
    }

    /**
     * Check if line has_gadget attribute
     *
     * @return int|bool
     */
    public function getHasGadgetAttribute()
    {
        $backend_api = $this->backend_api;

        if (0 == $this->subscription->status) {
            return 1;
        }

        switch ($backend_api) {
            case 'm7':
                if ($this->is_stoped && $this->m7_deprovisioned) {
                    return 0;
                }
                $productType = $this->json_data_product_type;
                $subscription = $this->subscription;
                $m7JsonData = isset($subscription->json_data_m7->json_data['m7']) ?
                    $subscription->json_data_m7->json_data['m7'] : [];

                if (isset($m7JsonData['status']) && $m7JsonData['status'] == 'Deprovisioned') {
                    return 0;
                }

                if ('stb' == $productType) {
                    if (!$this->json_data_m7) {
                        return 1;
                    }
                    $json_data_m7 = $this->json_data_m7;
                    if (
                        $json_data_m7 && isset($json_data_m7->json_data['m7']) &&
                        isset($json_data_m7->json_data['m7']['status']) &&
                        ($json_data_m7->json_data['m7']['status'] == 'Failed' ||
                            $json_data_m7->json_data['m7']['status'] == 'Provisioning')
                    ) {
                        return 1;
                    }

                    if (!empty($m7JsonData) && 'Provisioned' == $m7JsonData['status']) {
                        if ('Provisioned' == $this->m7_provisioning_status && !$this->is_stoped) {
                            return 1;
                        }
                    }
                }
                if (!$this->is_started) {
                    return 0;
                }
                if ('addon' == $productType) {
                    if (!empty($m7JsonData) && 'Provisioned' == $m7JsonData['status']) {
                        return 1;
                    }
                }
                if ('basis' == $productType) {
                    $m7Provisionable = $subscription->m7_provisionable;
                    if (
                        empty($m7JsonData) && $m7Provisionable['completeSerial'] ||
                        (!empty($m7JsonData) && ('Validated' == $m7JsonData['status'] || 'New' == $m7JsonData['status']))
                    ) {
                        $m7_has_provisioning = $subscription->m7_has_provisioning;
                        if (
                            $m7_has_provisioning['hasNewStb'] &&
                            $m7_has_provisioning['hasNewBasis'] &&
                            !$this->is_stoped
                        ) {
                            return 1;
                        }
                    }

                    if (
                        !empty($m7JsonData) && in_array(
                            $subscription->m7_provisioning_status,
                            ['Provisioned', 'Pending', 'Failed']
                        )
                    ) {
                        return 1;
                    }
                }
                break;

            case 'brightblue':
                if (!$this->is_stoped && $this->is_started) {
                    return 1;
                }
                break;

            case 'lineProvisioning':
                $statusService = new StatusService();
                $conceptId = $statusService->getStatusId('subscription', 'Concept');
                $isSubscriptionInConcept = ($conceptId == $this->subscription->status);
                $operator = $this->getSubscriptionLineOperator();
                $lineProvisioningStatusIds = $statusService->getStatusesByType('connection')->pluck('id')->toArray();

                $hasNoStatus = empty($this->status_id);
                $hasOperatorProvApi = ($operator && !empty($operator->provisioning_api));
                $hasStatus = in_array($this->status_id, $lineProvisioningStatusIds);

                $noStatusHasOperator = ($hasNoStatus && $hasOperatorProvApi);
                $hasStatusHasOperator = ($hasStatus && $hasOperatorProvApi);

                return $isSubscriptionInConcept && ($noStatusHasOperator || $hasStatusHasOperator);
                break;
        }
        return 0;
    }

    /**
     * Get Provisioning Gadget
     *
     * @return mixed
     */
    public function getProvisioningGadgetAttribute()
    {
        $subscription = $this->subscription;

        $statusService = new StatusService();
        $conceptId = $statusService->getStatusId('subscription', 'Concept');
        $isSubscriptionInConcept = ($conceptId == $this->subscription->status);
        $operator = $this->getSubscriptionLineOperator();
        $lineProvisioningStatusIds = $statusService->getStatusesByType('connection')->pluck('id')->toArray();

        $hasNoStatus = empty($this->status_id);
        $hasOperatorProvApi = ($operator && !empty($operator->provisioning_api));
        $hasStatus = in_array($this->status_id, $lineProvisioningStatusIds);

        $noStatusHasOperator = ($hasNoStatus && $hasOperatorProvApi);
        $hasStatusHasOperator = ($hasStatus && $hasOperatorProvApi);

        $canProvision = $isSubscriptionInConcept && ($noStatusHasOperator || $hasStatusHasOperator);
        $data = null;
        if ($canProvision) {
            // START PROVISIONING
            $hasInactiveStatus = in_array($this->status_id, [
                $statusService->getStatusId('connection', 'inactive')
            ]);
            $hasInactiveOrEmptyStatus = ($hasInactiveStatus || empty($this->status_id));
            $showStartProvisioning = ($isSubscriptionInConcept && $hasInactiveOrEmptyStatus);
            if ($showStartProvisioning) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/StartProvisioning/{$this->id}";
                $data = $this->gadgetMenu(
                    'Start provisioning',
                    'Confirm',
                    [
                        'label' => 'Start provisioning',
                        'url' => $url,
                        'msg' => 'Are you sure you want to start provisioning?',
                        'show_success_popup' => false
                    ]
                );
            }

            // RETRY PROVISIONING
            $errorStatuses = [
                $statusService->getStatusId('connection', 'check_error'),
                $statusService->getStatusId('connection', 'order_error'),
            ];
            $showRetryProvisioning = (in_array($this->status_id, $errorStatuses));
            if ($showRetryProvisioning) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/RetryProvisioning/{$this->id}";
                $data = $this->gadgetMenu(
                    'Retry provisioning',
                    'Confirm',
                    [
                        'label' => 'Retry provisioning',
                        'url' => $url,
                        'msg' => 'Are you sure you want to retry provisioning?',
                        'show_success_popup' => false
                    ]
                );
            }

            // START MIGRATION AND SET MIGRATION WISHDATE
            $showonfirmMigration = ($this->status_id == $statusService->getStatusId('connection', 'migration_tbc'));
            if ($showonfirmMigration) {
                $wishDate = null;
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/StartMigration/{$this->id}";

                if ($subscription->wish_date) {
                    $data = $this->gadgetMenu(
                        'Start migration',
                        'Confirm',
                        [
                            'label' => 'Start migration',
                            'url' => $url,
                            'msg' => 'Are you sure you want to start migration?',
                            'show_success_popup' => false
                        ]
                    );
                } else {
                    $data = $this->gadgetMenu(
                        'Start migration',
                        'Notice',
                        [
                            'label' => 'Wish date not set',
                            'msg' => 'Please set a wish date on the subscription. Then you can start the migration.'
                        ]
                    );
                }
            }

            // RETRY MIGRATION
            $showRetryMigration = (in_array($this->status_id, [
                $statusService->getStatusId('connection', 'migration_error')
            ]));
            if ($showRetryMigration) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/RetryMigration/{$this->id}";
                $data = $this->gadgetMenu(
                    'Retry migration',
                    'Confirm',
                    [
                        'label' => 'Retry migration',
                        'url' => $url,
                        'msg' => 'Are you sure you want to retry migration?',
                        'show_success_popup' => false
                    ]
                );
            }

            // CANCEL ORDER/MIGRATION
            $showCancelProvisioning = in_array($this->status_id, [
                $statusService->getStatusId('connection', 'order_pending'),
                $statusService->getStatusId('connection', 'migration_pending'),
                $statusService->getStatusId('connection', 'migration_confirmed')
            ]);
            if ($showCancelProvisioning) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/CancelOrderMigration/{$this->id}";

                $inOrderState = $this->status_id == $statusService->getStatusId('connection', 'order_pending');
                $inMigrationState = in_array(
                    $this->status_id,
                    [
                        $statusService->getStatusId('connection', 'migration_pending'),
                        $statusService->getStatusId('connection', 'migration_confirmed')
                    ]
                );

                $labelTitle = '';
                if ($inOrderState) {
                    $labelTitle = 'Cancel order';
                    $msg = 'Are you sure you want to cancel the order?';
                }

                if ($inMigrationState) {
                    $labelTitle = 'Cancel migration';
                    $msg = 'Are you sure you want to cancel the migration?';
                }

                if ($labelTitle) {
                    $data = $this->gadgetMenu(
                        $labelTitle,
                        'Confirm',
                        [
                            'label' => $labelTitle,
                            'url' => $url,
                            'msg' => $msg,
                            'show_success_popup' => false
                        ]
                    );
                }
            }

            // ABORT PROVISIONING
            $showAbortProvisioning = in_array($this->status_id, [
                $statusService->getStatusId('connection', 'check_pending')
            ]);
            if ($showAbortProvisioning) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/AbortProvisioning/{$this->id}";
                $data = $this->gadgetMenu(
                    'Abort provisioning',
                    'Confirm',
                    [
                        'label' => 'Abort provisioning',
                        'url' => $url,
                        'msg' => 'Are you sure you want to abort provisioning?',
                        'show_success_popup' => false
                    ]
                );
            }

            // RETRY PROVISIONING
            $showReprovisioning = ($this->status_id == $statusService->getStatusId('connection', 'terminated'));
            if ($showReprovisioning) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/ReprovisionLine/{$this->id}";
                $data = $this->gadgetMenu(
                    'Reprovision line',
                    'Confirm',
                    [
                        'label' => 'Reprovision line',
                        'url' => $url,
                        'msg' => 'Are you sure you want to reprovision this line?',
                        'show_success_popup' => false
                    ]
                );
            }

            // TERMINATE  LINE
            $showTerminate = ($this->status_id == $statusService->getStatusId('connection', 'active'));
            if ($showTerminate) {
                $hasGadget = true;
                $url = config('app.url') . "/api/subscription_lines/lineProvisioning/TerminateLine/{$this->id}";
                $data = $this->gadgetMenu(
                    'Terminate line',
                    'Confirm',
                    [
                        'label' => 'Confirm line termination',
                        'url' => $url,
                        'msg' => 'Are you sure you want to terminate this line?',
                        'show_success_popup' => false
                    ]
                );
            }
        }
        return $data;
    }

    /**
     * Get Edit network operator gadget (sub-menu)
     *
     * @return array
     */
    public function getEditNetworkGadgetAttribute()
    {
        $subscriptionLineId = $this->id;
        $netOpService = new NetworkOperatorService();
        $networkOperator = $this->getSubscriptionLineNetworkOperator();
        $selectedNetwork = $selectedOperator = null;
        $networkOpts = $netOpService->networkListOpts();
        if ($networkOperator) {
            $selectedNetwork = [
                'label' => $networkOperator->network->name,
                'value' => $networkOperator->network_id
            ];
            $selectedOperator = [
                'label' => $networkOperator->operator->name,
                'value' => $networkOperator->operator_id
            ];

            $operatorOpts = $netOpService
                ->getOperators($networkOperator->network_id)
                ->get()
                ->toArray();
        } else {
            $operatorOpts = $netOpService
                ->getOperators()
                ->get()
                ->toArray();
        }

        return $this->gadgetMenu(
            'Edit network operator',
            'Modal',
            [
                'form' => [
                    [
                        'label' => '',
                        'data' => [
                            [
                                'label' => 'Network',
                                'name' => 'network',
                                'type' => 'select',
                                'placeholder' => 'Select network first',
                                'options' => $networkOpts,
                                'value' => $selectedNetwork
                            ],
                            [
                                'label' => 'Operator',
                                'name' => 'operator',
                                'type' => 'select',
                                'placeholder' => 'Select an operator',
                                'options' => $operatorOpts,
                                'value' => $selectedOperator
                            ]
                        ]
                    ]
                ],
                'url' => config('app.url') . "/api/subscription_lines/$subscriptionLineId/network_operator",
            ]
        );
    }

    /**
     * Get gadgets attribute
     */
    public function getGadgetsAttribute()
    {
        $gadgets = [];
        $data = [];
        $subscription = $this->subscription;
        $hasGadget = false;

        if (0 == $subscription->status) {
            $hasGadget = true;
            $data[] = $this->gadgetMenu(
                'Remove',
                'Confirm',
                [
                    'method' => 'DELETE',
                    'url' => route('subscription_lines.destroy', ['subscription_line' => $this->id]),
                    'msg' => 'Are you sure you want to remove this subscription line?',
                    'show_success_popup' => true
                ]
            );
        }

        try {
            switch ($this->backend_api) {
                case 'm7':
                    $m7JsonData = isset($subscription->json_data_m7->json_data['m7']) ?
                        $subscription->json_data_m7->json_data['m7'] : [];

                    $m7Provisionable = $subscription->m7_provisionable;
                    $jsonData = $this->json_data_m7;
                    switch ($this->json_data_product_type) {
                        case 'stb':
                            $isOk = !$this->is_stoped &&
                                (!$jsonData || (isset($jsonData->json_data['m7']) &&
                                        isset($jsonData->json_data['m7']['status']) && $jsonData->json_data['m7']['status'] == 'Failed'));
                            if ($isOk) {
                                $serialItem = $this->serial_item;
                                $serial = '';
                                $mac = '';
                                if ($serialItem) {
                                    if ($serialItem->json_data && isset($serialItem->json_data['serial'])) {
                                        $serialJsonData = $serialItem->json_data['serial'];
                                        if (isset($serialJsonData['serial'])) {
                                            $serial = $serialJsonData['serial'];
                                        }
                                        if (isset($serialJsonData['mac'])) {
                                            $mac = preg_replace('~(..)(?!$)\.?~', '\1:', strtoupper($serialJsonData['mac']));
                                        }
                                    }
                                }

                                $content = [
                                    'form' => [
                                        [
                                            'label' => '',
                                            'data' => [
                                                [
                                                    'label' => '',
                                                    'type' => 'text',
                                                    'name' => 'serial',
                                                    'value' => $serial,
                                                    'placeholder' => 'Serial'
                                                ]
                                            ]
                                        ],
                                        [
                                            'label' => '',
                                            'data' => [
                                                [
                                                    'label' => '',
                                                    'type' => 'text',
                                                    'name' => 'mac_address',
                                                    'value' => $mac,
                                                    'placeholder' => 'Mac Address'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'url' => config('app.url') . '/api/subscription_lines/' . $this->id . '/serial'
                                ];
                                $data[] = $this->gadgetMenu('Edit serial', 'Modal', $content);
                                $hasGadget = true;
                            }

                            if (!empty($m7JsonData) && 'Provisioned' == $m7JsonData['status']) {
                                $hasGadget = true;

                                if ('Provisioned' == $this->m7_provisioning_status && !$this->is_stoped) {
                                    $content = [
                                        'form' => [
                                            [
                                                'label' => '',
                                                'data' => [
                                                    [
                                                        'label' => '',
                                                        'type' => 'text',
                                                        'name' => 'serial',
                                                        'placeholder' => 'Serials'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'label' => '',
                                                'data' => [
                                                    [
                                                        'label' => '',
                                                        'type' => 'text',
                                                        'name' => 'mac_address',
                                                        'placeholder' => 'Mac Address'
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/m7/SwopSmartcard/' . $this->id
                                    ];

                                    $data[] = $this->gadgetMenu(
                                        'Swap smartcard',
                                        'Modal',
                                        $content
                                    );

                                    $data[] = $this->gadgetMenu(
                                        'Re auth smartcard',
                                        'Confirm',
                                        [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/ReAuthSmartcard/' . $this->id,
                                            'msg' => 'Are you sure you want to re auth this smartcard?',
                                            'show_success_popup' => true
                                        ]
                                    );

                                    $data[] = $this->gadgetMenu(
                                        'Reset pin',
                                        'Confirm',
                                        [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/ResetPin/' . $this->id,
                                            'msg' => 'Are you sure you want to reset pin?',
                                            'show_success_popup' => true
                                        ]
                                    );
                                }

                                if (
                                    'Provisioning' == $this->m7_provisioning_status &&
                                    $this->is_started &&
                                    !$this->is_stoped &&
                                    $this->serial
                                ) {
                                    $jsData = $this->json_data_m7;
                                    if (!$jsData) {
                                        $data[] = $this->gadgetMenu(
                                            'Provision smartcard',
                                            'Confirm',
                                            [
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                                'msg' => 'Are you sure you want to provision this smartcard?',
                                                'show_success_popup' => true
                                            ]
                                        );
                                    } elseif ('Provisioning' == $this->m7_provisioning_status) {
                                        $data[] = $this->gadgetMenu(
                                            'Pending notification',
                                            'Notice',
                                            [
                                                'msg' => 'Provisioning is still on pending state.'
                                            ]
                                        );

                                        $data[] = $this->gadgetMenu('Retry provisioning smartcard', 'Confirm', [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                            'msg' => 'Are you sure you want to retry provisioning this smartcard?',
                                            'show_success_popup' => true
                                        ]);
                                    } else {
                                        $remarks = isset($jsData->json_data['m7']) &&
                                        isset($jsData->json_data['m7']['remarks']) ?
                                            $jsData->json_data['m7']['remarks'] : '';
                                        $data[] = $this->gadgetMenu(
                                            'Failed notification',
                                            'Notice',
                                            [
                                                'msg' => "Provisioning failed for the reason\'s {$remarks}."
                                            ]
                                        );

                                        $data[] = $this->gadgetMenu('Retry provisioning smartcard', 'Confirm', [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                            'msg' => 'Are you sure you want to retry provisioning this smartcard?',
                                            'show_success_popup' => true
                                        ]);
                                    }
                                }

                                $isMainSmartcard = (strtolower($this->mac_address)
                                    == strtolower($subscription->main_mac_address));

                                if (!$isMainSmartcard && 'Provisioned' == $this->m7_provisioning_status) {
                                    $data[] = $this->gadgetMenu(
                                        'Terminate smartcard',
                                        'Confirm',
                                        [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/CloseAccount/' . $this->id,
                                            'msg' => 'Are you sure you want to terminate this smartcard?',
                                            'show_success_popup' => true
                                        ]
                                    );
                                }
                            }
                            break;

                        case 'basis':
                            if (
                                empty($m7JsonData) && $m7Provisionable['completeSerial'] ||
                                (!empty($m7JsonData) &&
                                    ('Validated' == $m7JsonData['status'] || 'New' == $m7JsonData['status']))
                            ) {
                                $m7_has_provisioning = $this->subscription->m7_has_provisioning;

                                if (
                                    $m7_has_provisioning['hasNewStb'] &&
                                    $m7_has_provisioning['hasNewBasis'] &&
                                    !$this->is_stoped
                                ) {
                                    $hasGadget = true;
                                    $data[] = $this->gadgetMenu('Provision', 'Confirm', [
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                        'msg' => 'Are you sure you want to provision this basis?',
                                        'show_success_popup' => true
                                    ]);
                                }
                            }

                            if (!empty($m7JsonData)) {
                                $hasGadget = true;
                                if ('Disconnected' == $m7JsonData['status']) {
                                    $data[] = $this->gadgetMenu('Reconnect', 'Confirm', [
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/m7/Reconnect/' . $this->id,
                                        'msg' => 'Are you sure you want to reconnect this basis?',
                                        'show_success_popup' => true
                                    ]);
                                }

                                if (
                                    'Failed' == $m7JsonData['status'] ||
                                    ('Provisioning' == $m7JsonData['status'] &&
                                        '20' != $m7JsonData['result'])
                                ) {
                                    $data[] = $this->gadgetMenu(
                                        'Provisioning failed',
                                        'Confirm',
                                        [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                            'msg' => 'Provisioning failed for the reason\'s ' .
                                                $m7JsonData['remarks'] . ' Do you wan to re-provision?',
                                            'show_success_popup' => true
                                        ]
                                    );
                                }

                                if (
                                    !$this->is_stoped &&
                                    'Pending' == $m7JsonData['status'] ||
                                    ('Provisioning' == $m7JsonData['status'] &&
                                        !isset($m7JsonData['result']))
                                ) {
                                    $data[] = $this->gadgetMenu(
                                        'Pending notification',
                                        'Notice',
                                        [
                                            'msg' => 'Provisioning is still on pending state.'
                                        ]
                                    );

                                    $data[] = $this->gadgetMenu('Retry provisioning', 'Confirm', [
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/m7/CaptureSubscriber/' . $this->id,
                                        'msg' => 'Are you sure you want to retry provisioning this basis?',
                                        'show_success_popup' => true
                                    ]);
                                }

                                if ('Provisioned' == $m7JsonData['status']) {
                                    if (
                                        $this->is_started && 'Provisioning' ==
                                        $this->m7_provisioning_status && !$this->is_stoped
                                    ) {
                                        $hasGadget = true;
                                        if (!$this->json_data_m7) {
                                            $data[] = $this->gadgetMenu(
                                                'Provision base package',
                                                'Confirm',
                                                [
                                                    'url' => config('app.url') .
                                                        '/api/subscription_lines/m7/ChangePackage/' . $this->id,
                                                    'msg' => 'Are you sure you want to provision this base package?',
                                                    'show_success_popup' => true
                                                ]
                                            );
                                        } else {
                                            $data[] = $this->gadgetMenu(
                                                'Pending provision base package',
                                                'Notice',
                                                [
                                                    'msg' => 'Provisioning is still on pending state.'
                                                ]
                                            );
                                        }
                                    }

                                    if ('Provisioned' == $this->m7_provisioning_status) {
                                        $product_id = $this->product_id;
                                        $where = [
                                            ['json_data->m7->type', '=', 'basis'],
                                            ['product_id', '<>', $product_id]
                                        ];
                                        $tenant_id = $this->subscription->relation->tenant_id;
                                        $tenantProducts = TenantProduct::where('tenant_id', $tenant_id)
                                            ->whereHas('product', function ($query) use ($where) {
                                                $query->whereHas('jsonData', function ($query1) use ($where) {
                                                    $query1->whereNotNull('product_id')->where($where);
                                                });
                                            })->get();
                                        $productOptions = collect($tenantProducts)
                                            ->map(function ($tenantProduct) {
                                                return [
                                                    'value' => $tenantProduct->product->id,
                                                    'label' => $tenantProduct->product->description
                                                ];
                                            });
                                        $m7_has_provisioning = $this->subscription->m7_has_provisioning;
                                        if (!$m7_has_provisioning['hasNewBasis']) {
                                            $hasGadget = true;
                                            $data[] = $this->gadgetMenu('Change base package', 'Modal', [
                                                'form' => [
                                                    [
                                                        'label' => '',
                                                        'data' => [
                                                            [
                                                                'label' => '',
                                                                'type' => 'select',
                                                                'name' => 'product',
                                                                'placeholder' => 'Products',
                                                                'options' => $productOptions
                                                            ],
                                                            // [
                                                            //     'label' => '',
                                                            //     'type' => 'date',
                                                            //     'name' => 'subscription_start',
                                                            //     'placeholder' => 'Start'
                                                            // ]
                                                        ]
                                                    ]
                                                ],
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/ChangePackage/' . $this->id
                                            ]);
                                        }

                                        if (!isset($m7JsonData['account'])) {
                                            $data[] = $this->gadgetMenu('Create GO apps account', 'Modal', [
                                                'form' => [
                                                    [
                                                        'label' => '',
                                                        'data' => [
                                                            [
                                                                'label' => '',
                                                                'type' => 'text',
                                                                'name' => 'email',
                                                                'placeholder' => 'Email/Username',
                                                                'value' => $subscription->person_provisioning->email
                                                            ],
                                                            [
                                                                'label' => '',
                                                                'type' => 'text',
                                                                'name' => 'password',
                                                                'placeholder' => 'Password'
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/CreateMyAccount/' . $this->id
                                            ]);
                                        } else {
                                            $hasGadget = true;
                                            $data[] = $this->gadgetMenu('Change GO apps account', 'Modal', [
                                                'form' => [
                                                    [
                                                        'label' => '',
                                                        'data' => [
                                                            [
                                                                'label' => '',
                                                                'type' => 'text',
                                                                'name' => 'email',
                                                                'placeholder' => 'Email/Username',
                                                                'value' => $m7JsonData['account']['Email']
                                                            ],
                                                            [
                                                                'label' => '',
                                                                'type' => 'text',
                                                                'name' => 'password',
                                                                'placeholder' => 'Password',
                                                                'value' => $m7JsonData['account']['Password']
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/ChangeMyAccount/' . $this->id
                                            ]);

                                            $data[] = $this->gadgetMenu(
                                                'Remove GO apps account',
                                                'Confirm',
                                                [
                                                    'url' => config('app.url') .
                                                        '/api/subscription_lines/m7/RemoveMyAccount/' . $this->id,
                                                    'msg' => 'Are you sure you want to remove this GO apps account?',
                                                    'show_success_popup' => true,
                                                ]
                                            );
                                        }

                                        $data[] = $this->gadgetMenu(
                                            'Disconnect',
                                            'Confirm',
                                            [
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/Disconnect/' . $this->id,
                                                'msg' => 'Are you sure you want to disconnect this basis?',
                                                'show_success_popup' => true
                                            ]
                                        );

                                        $data[] = $this->gadgetMenu('Close account', 'Confirm', [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/CloseAccount/' . $this->id,
                                            'msg' => 'Are you sure you want to close this Account?',
                                            'show_success_popup' => true
                                        ]);
                                    }
                                }
                            }
                            break;

                        case 'addon':
                            if (!empty($m7JsonData) && 'Provisioned' == $m7JsonData['status']) {
                                $hasGadget = true;
                                $jsonData = $this->json_data_m7;
                                if ('Provisioned' == $this->m7_provisioning_status) {
                                    $data[] = $this->gadgetMenu(
                                        'Terminate addon',
                                        'Confirm',
                                        [
                                            'url' => config('app.url') .
                                                '/api/subscription_lines/m7/ChangePackage/' . $this->id,
                                            'msg' => 'Are you sure you want to terminate this addon?',
                                            'show_success_popup' => true
                                        ]
                                    );
                                }

                                if ('Provisioning' == $this->m7_provisioning_status) {
                                    if ($jsonData == null) {
                                        $data[] = $this->gadgetMenu(
                                            'Provision addon',
                                            'Confirm',
                                            [
                                                'url' => config('app.url') .
                                                    '/api/subscription_lines/m7/ChangePackage/' . $this->id,
                                                'msg' => 'Are you sure you want to provision this addon?',
                                                'show_success_popup' => true
                                            ]
                                        );
                                    } else {
                                        $data[] = $this->gadgetMenu(
                                            'Pending notification',
                                            'Notice',
                                            [
                                                'msg' => 'Provisioning is still on pending state.'
                                            ]
                                        );
                                    }
                                }

                                if ('Deprovisioning' == $this->m7_provisioning_status) {
                                    $data[] = $this->gadgetMenu(
                                        'Pending deprovisioning',
                                        'Notice',
                                        [
                                            'msg' => 'Deprovisioning is still on pending state.'
                                        ]
                                    );
                                }
                            }
                            break;
                    }

                    if ($hasGadget) {
                        $gadgets[] = [
                            'label' => '',
                            'data' => $data,
                            'type' => 'DropdownMenu'
                        ];
                    }
                    break;

                case 'brightblue':
                    $jsonData = $this->jsonDatas()
                        ->where('backend_api', 'brightblue')
                        ->first();

                    $tenant = $subscription->relation->tenant;
                    $sluggedTenantName = $tenant->slugged_name;

                    $data = [];
                    $brightblueJsonData = [];
                    if (isset($jsonData->json_data['brightblue'][$sluggedTenantName])) {
                        $brightblueJsonData = $jsonData->json_data['brightblue'][$sluggedTenantName];
                    }

                    // Existing 'brightblue' details
                    if (!empty($brightblueJsonData)) {
                        if ('Pending' == $brightblueJsonData['provisioning']['status']) {
                            $hasGadget = true;
                            $data[] = $this->gadgetMenu(
                                'Pending notification',
                                'Notice',
                                [
                                    'msg' => 'Provisioning is still on pending state.'
                                ]
                            );
                        } elseif (
                            Str::contains(
                                $brightblueJsonData['provisioning']['status'],
                                ['Provisioned', 'Suspended']
                            )
                        ) {
                            $data[] = $this->gadgetMenu('Get account details', 'Modal', [
                                'form' => [
                                    [
                                        'label' => '',
                                        'data' => [
                                            [
                                                'label' => '',
                                                'name' => 'json_data',
                                                'type' => 'textarea',
                                                'placeholder' => 'BrightBlue Account Details',
                                                'value' => json_encode($brightblueJsonData, JSON_PRETTY_PRINT)
                                            ]
                                        ]
                                    ]
                                ],
                                'url' => '',
                            ]);

                            // Open Looking Glass
                            $data[] = $this->gadgetMenu(
                                'Open looking glass',
                                'Link',
                                [
                                    'url' => config('brightblue.lookingglass_url') .
                                        "/{$brightblueJsonData['accountNumber']}"
                                ]
                            );

                            // Active account
                            if ($brightblueJsonData["status"] === "active") {
                                // SuspendAccount
                                $data[] = $this->gadgetMenu(
                                    'Suspend account',
                                    'Confirm',
                                    [
                                        'label' => 'Suspend account',
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/DisconnectAccount/' . $this->id,
                                        'msg' => 'Are you sure you want to suspend this account?',
                                        'show_success_popup' => true
                                    ]
                                );

                                // CancelAccount
                                $data[] = $this->gadgetMenu(
                                    'Cancel account',
                                    'Confirm',
                                    [
                                        'label' => 'Cancel account',
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/CloseAccount/' . $this->id,
                                        'msg' => 'Are you sure you want to cancel this account?',
                                        'show_success_popup' => true
                                    ]
                                );

                                // GenerateUnconfirmedActivationCode

                                $data[] = $this->gadgetMenu(
                                    'Generate activation code',
                                    'Modal',
                                    [
                                        'form' => [
                                            [
                                                'label' => '',
                                                'data' => [
                                                    [
                                                        'label' => 'Description',
                                                        'type' => 'text',
                                                        'name' => 'description',
                                                        'placeholder' => 'Description',
                                                        'value' => $brightblueJsonData["description"]
                                                    ],
                                                    [
                                                        'label' => 'Max use(s)',
                                                        'type' => 'text',
                                                        'name' => 'maxUses',
                                                        'placeholder' => 'Max Use(s)',
                                                        'value' => array_key_exists(
                                                            "activationCode",
                                                            $brightblueJsonData
                                                        ) ? $brightblueJsonData["activationCode"]["maxUses"] : "10"
                                                    ],
                                                    [
                                                        'label' => 'Expiry Date',
                                                        'type' => 'text',
                                                        'name' => 'expiryDate',
                                                        'placeholder' => 'Expiry Date',
                                                        'value' => array_key_exists(
                                                            "activationCode",
                                                            $brightblueJsonData
                                                        ) ?
                                                            Carbon::parse(
                                                                $brightblueJsonData["activationCode"]["expiryDate"]
                                                            )->format("Y-m-d H:i:s") :
                                                            now()->addMonths(3)->format("Y-m-d H:i:s")
                                                    ],
                                                ]
                                            ]
                                        ],
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/NewActivationCode/' . $this->id,
                                    ]
                                );

                                // Set Pin
                                $data[] = $this->gadgetMenu('Set pin', 'Modal', [
                                    'form' => [
                                        [
                                            'label' => '',
                                            'data' => [
                                                [
                                                    'label' => 'PIN',
                                                    'name' => 'pin',
                                                    'type' => 'password',
                                                    'placeholder' => 'PIN'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'url' => config('app.url') .
                                        '/api/subscription_lines/brightblue/SetPin/' . $this->id,
                                ]);

                                // Set User Name
                                $data[] = $this->gadgetMenu(
                                    'Set user name',
                                    'Modal',
                                    [
                                        'form' => [
                                            [
                                                'label' => '',
                                                'data' => [
                                                    [
                                                        'label' => 'User Name',
                                                        'name' => 'name',
                                                        'type' => 'text',
                                                        'placeholder' => 'User Name',
                                                        'value' => $brightblueJsonData["user"]["name"]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/SetUserName/' . $this->id,
                                    ]
                                );

                                // Set Account Description
                                $data[] = $this->gadgetMenu(
                                    'Set account description',
                                    'Modal',
                                    [
                                        'form' => [
                                            [
                                                'label' => '',
                                                'data' => [
                                                    [
                                                        'label' => 'Description',
                                                        'name' => 'description',
                                                        'type' => 'text',
                                                        'placeholder' => 'Description',
                                                        'value' => $brightblueJsonData["description"]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/SetAccountDescription/' . $this->id,
                                    ]
                                );

                                $hasGadget = true;
                            } elseif ($brightblueJsonData["status"] === "inactive") { // Inactive account
                                // ActivateAccount
                                $data[] = $this->gadgetMenu(
                                    'Activate account',
                                    'Confirm',
                                    [
                                        'label' => 'Activate account',
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/ActivateAccount/' . $this->id,
                                        'msg' => 'Are you sure you want to activate this account?',
                                        'show_success_popup' => true
                                    ]
                                );
                                $hasGadget = true;
                            } elseif ($brightblueJsonData["status"] === "suspended") {
                                // ResumeAccount
                                $data[] = $this->gadgetMenu(
                                    'Resume account',
                                    'Confirm',
                                    [
                                        'label' => 'Resume account',
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/ReconnectAccount/' . $this->id,
                                        'msg' => 'Are you sure you want to resume this account?',
                                        'show_success_popup' => true
                                    ]
                                );
                                // CancelAccount
                                $data[] = $this->gadgetMenu(
                                    'Cancel account',
                                    'Confirm',
                                    [
                                        'label' => 'Cancel account',
                                        'url' => config('app.url') .
                                            '/api/subscription_lines/brightblue/CloseAccount/' . $this->id,
                                        'msg' => 'Are you sure you want to cancel this account?',
                                        'show_success_popup' => true
                                    ]
                                );

                                $hasGadget = true;
                            }
                        }
                    } else { // New bright blue details
                        // Create Account
                        $data[] = $this->gadgetMenu(
                            'Create account',
                            'Modal',
                            [
                                'form' => [
                                    [
                                        'label' => '',
                                        'data' => [
                                            [
                                                'label' => 'Description',
                                                'type' => 'text',
                                                'name' => 'description',
                                                'placeholder' => 'Description',
                                                'value' => "[{$subscription->relation->customer_number}] " .
                                                    $subscription->person_provisioning->full_name
                                            ],
                                            [
                                                'label' => 'Email',
                                                'type' => 'email',
                                                'name' => 'primaryUserName',
                                                'placeholder' => 'User Name',
                                                'value' => $subscription->person_provisioning->email
                                            ],
                                            [
                                                'label' => 'PIN',
                                                'name' => 'primaryUserPin',
                                                'type' => 'password',
                                                'placeholder' => 'PIN',
                                                'value' => 1234 // rand(1000,9999)
                                            ]
                                        ]
                                    ]
                                ],
                                'url' => config('app.url') .
                                    '/api/subscription_lines/brightblue/NewAccount/' . $this->id,
                            ]
                        );
                        $hasGadget = true;
                    }

                    break;

                case 'lineProvisioning':
                    $gadget = $this->getAttribute('edit_network_gadget');
                    if (!blank($gadget)) {
                        $data[] = $gadget;
                    }

                    $gadget = $this->getAttribute('provisioning_gadget');
                    if (!blank($gadget)) {
                        $data[] = $gadget;
                    }

                    $hasGadget = count($gadget) > 0;

                    break;
            }
        } catch (\Exception $e) {
            Logging::error(
                $e->getMessage(),
                [
                    'attributes' => $this->attributes,
                    'error_stacktrace' => $e->getTraceAsString()
                ],
                16,
                1,
                $subscription->relation->tenant_id
            );
        }

        if ($hasGadget) {
            $gadgets[] = [
                'label' => '',
                'data' => array_filter($data),
                'type' => 'DropdownMenu'
            ];
        }

        return $gadgets;
    }

    /**
     * Get invoice count using subscription_line_id
     *
     * @return mixed
     */
    public function getInvoiceCountAttribute()
    {
        return SalesInvoiceLine::where('subscription_line_id', $this->id)
            ->select('sales_invoice_id')
            ->distinct()
            ->count();
    }

    /**
     * This method expects the SubscriptionLine to be validated for prices
     * @param Carbon $start
     * @param Carbon $stop
     * @return mixed
     */
    public function getLinePricesDuringPeriod(Carbon $start, Carbon $stop)
    {
        $allPrices = SubscriptionLinePrice::where('subscription_line_id', $this->id)->get();
        $priceBefore = $allPrices->where('price_valid_from', '<=', $start)->sortByDesc('price_valid_from')->first();
        if ($priceBefore && $priceBefore->price_valid_from < $start) {
            $priceBefore->price_valid_from = $start;
        }
        $pricesOverPeriod = $allPrices->whereBetween('price_valid_from', [$start, $stop])->sortBy('price_valid_from');
        if ($pricesOverPeriod->isEmpty()) {
            $pricesOverPeriod[] = $priceBefore;
        } elseif ($priceBefore && $pricesOverPeriod->first()->price_valid_from > $start) {
            $pricesOverPeriod->prepend($priceBefore);
        }
        return $pricesOverPeriod->flatten();
    }
}
