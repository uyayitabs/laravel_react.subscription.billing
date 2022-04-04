<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Traits\HasJsonDataM7Trait;
use App\Traits\HasJsonDataBrightblueTrait;
use App\Traits\HasLineProductBackendApiTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends BaseModel
{
    use HasJsonDataM7Trait;
    use HasJsonDataBrightblueTrait;
    use HasLineProductBackendApiTrait;

    protected $table = 'subscriptions';

    protected $fillable = [
        'subscription_no',
        'type',
        'relation_id',
        'plan_id',
        'billing_start',
        'subscription_start',
        'subscription_stop',
        'billing_person',
        'provisioning_person',
        'billing_address',
        'provisioning_address',
        'description',
        'description_long',
        'status',
        'contract_period_id',
        'wish_date',
    ];

    public static $sortables = [
        'id', 'description', 'subscription_start', 'subscription_stop', 'costs', 'status'
    ];

    public static $fields = [
        'id',
        'type',
        'relation_id',
        'plan_id',
        'billing_start',
        'subscription_start',
        'subscription_stop',
        'billing_person',
        'provisioning_person',
        'billing_address',
        'provisioning_address',
        'description',
        'description_long',
        'status',
        'contract_period_id',
        'update_line_stop',
        'update_line_start',
        'wish_date',
        'network_operator_id'
    ];

    protected $searchable = [
        'id,description,subscription_start,subscription_stop,status,stop,start,wish_date',
        'relation|customer_number,addresses:street1.house_number.room.zipcode.city!name.country!name'
    ];

    public static $searchableCols = [
        'id',
        'description',
        'subscription_start',
        'subscription_stop',
        'status',
        'relation',
        'street',
        'house_number',
        'room',
        'zipcode',
        'city',
        'country',
        'customer_number',
        'stop',
        'start',
        'wish_date'
    ];

    public static $scopes = [
        'relation.tenant',
        'relation.addresses',
        'plan',
        'subscription-lines',
        'subscription-lines.subscription-line-price',
        'subscription-lines.line-type',
        'billing-address',
        'billing-address.city',
        'billing-address.state',
        'billing-address.country',
        'billing-person',
        'provisioning-address',
        'provisioning-address.city',
        'provisioning-address.state',
        'provisioning-address.country',
        'provisioning-person',
        'plan-subscription-line-type'
    ];

    public static $withScopes = [
        'relation.tenant',
        'relation.addresses',
        'plan',
        'subscriptionLines',
        'subscriptionLines.subscriptionLinePrice',
        'subscriptionLines.lineType',
        'provisioningAddress',
        'provisioningAddress.city',
        'provisioningAddress.state',
        'provisioningAddress.country',
        'provisioningPerson',
        'planSubscriptionLineType'
    ];

    protected $casts = [
        'billing_start' => 'datetime:Y-m-d',
        'subscription_start' => 'datetime:Y-m-d',
        'subscription_stop' => 'datetime:Y-m-d',
        'wish_date' => 'datetime:Y-m-d',
        'costs' => 'float',
    ];

    protected $appends = [
        // 'json_datas',
        // 'costs',
        // 'nrc_summary',
        // 'mrc_summary',
        // 'qrc_summary',
        // 'yrc_summary',
        // 'deposit_summary',
        // 'person_billing',
        // 'address_billing',
        // 'person_provisioning',
        // 'address_provisioning',
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Get Relation function
     *
     * @return \Relation
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * Get Relation function
     *
     * @return \PlanSubscriptionLineType
     */
    public function planSubscriptionLineType()
    {
        return $this->belongsTo(PlanSubscriptionLineType::class, 'type', 'id');
    }

    /**
     * Billing Person function
     *
     * @return Person
     */
    public function billingPerson()
    {
        return $this->hasOne(Person::class, 'id', 'billing_person');
    }

    public function getPersonBillingAttribute()
    {
        $person = $this->sbillingPerson;
        if (!$person) {
            $person = $this->relation->persons()->where('primary', 1)->first();
        }
        if (!$person) {
            $person = $this->relation->persons()->where('primary', 1)->first();
        }
        if (!$person) {
            $person = $this->relation->persons()->first();
        }
        return $person;
    }

    /**
     * Billing Address information
     *
     * @return Address
     */
    public function billingAddress()
    {
        return $this->hasOne(Address::class, 'id', 'billing_address');
    }

    public function getAddressBillingAttribute()
    {
        $address = $this->sbillingAddress;
        if (!$address) {
            $address = $this->relation->billingAddress();
        }
        if (!$address) {
            $address = $this->relation->addresses()->orderBy('address_type_id', 'ASC')->first();
        }
        return $address;
    }

    /**
     * Provisioning Person function
     *
     * @return Person
     */
    public function provisioningPerson()
    {
        return $this->belongsTo(Person::class, 'provisioning_person', 'id');
    }

    public function getPersonProvisioningAttribute()
    {
        $person = $this->sprovisioningPerson;
        if (!$person) {
            $person = $this->relation->persons()->first();
        }
        return $person;
    }

    /**
     * Billing Address function
     *
     * @return Address
     */
    public function provisioningAddress()
    {
        return $this->belongsTo(Address::class, 'provisioning_address', 'id');
    }

    public function getAddressProvisioningAttribute()
    {
        $address = $this->sprovisioningAddress;
        if (!$address) {
            $address = $this->relation->provisioningAddress();
        }
        if (!$address) {
            $address = $this->relation->addresses()->first();
        }
        return $address;
    }

    /**
     * Get Plan function
     *
     * @return \Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get JsonData function
     *
     * @return \JsonData
     */
    public function jsonDatas()
    {
        return $this->hasMany(JsonData::class, 'subscription_id', 'id');
    }

    /**
     * Get total costs of the subscription_line_prices
     *
     * @return array
     */
    public function getCosts(): array
    {
        $totalCostEVat = floatval(0);
        $totalCostIVat = floatval(0);


        foreach ($this->subscriptionLines as $line) {
            $fixed_cost = $line->getFixedPrice();
            $totalCostEVat += $fixed_cost;
            $totalCostIVat += $fixed_cost * (1 + $line->getVatPercentage($this->relation->tenant_id));
        }

        return [
            'price_excl_vat' => floatval($totalCostEVat),
            'price_incl_vat' => floatval($totalCostIVat)
        ];
    }

    /**
     * Get total invoiced product function
     *
     * @return int
     */
    public function getTotalInvoicedProductPrice()
    {
        $subscriptionLines = $this->subscriptionLines()->get();
        $prices = [];
        foreach ($subscriptionLines as $subscriptionLine) {
            $prices[] = $subscriptionLine->subscriptionLinePrice()->first()->fixed_price;
        }

        return array_sum($prices);
    }

    /**
     * Get SubscriptionLine function
     *
     * @return HasMany
     */
    public function subscriptionLines()
    {
        return $this->hasMany(SubscriptionLine::class, 'subscription_id', 'id');
    }

    /**
     * Get SalesInvoiceLine function
     *
     * @return HasManyThrough
     */
    public function salesInvoiceLines()
    {
        return $this->hasManyThrough(SalesInvoiceLine::class, SubscriptionLine::class);
    }

    /**
     * Get one-off cost SubscriptionLines
     */
    public function oneOffLines()
    {
        return $this->subscriptionLines()
            ->whereIn('subscription_line_type', [
                Config::get("constants.subscription_line_types.nrc"),
                Config::get("constants.subscription_line_types.deposit")
            ]);
    }

    /**
     * Get periodic cost SubscriptionLines
     *
     * @return void
     */
    public function recurringLines()
    {
        return $this->subscriptionLines()
            ->whereNotIn('subscription_line_type', [
                Config::get("constants.subscription_line_types.nrc"),
                Config::get("constants.subscription_line_types.deposit"),
            ]);
    }


    public function invoiceSubscriptionLines($checkingDate)
    {
        return $this->subscriptionLines()
            ->where("subscription_start", "<=", $checkingDate)
            ->where('subscription_line_type', '<>', Config::get("constants.subscription_line_types.deposit"));
    }

    public function getStartingPointDate()
    {
        $tenant = $this->relation->tenant;

        $tenantInvoiceStartCalculationDate = null;
        if (Carbon::make($tenant->invoice_start_calculation)) {
            $tenantInvoiceStartCalculationDate = Carbon::make($tenant->invoice_start_calculation)->format('Y-m-d');
        }
        $referenceStartingDates = [
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

    /**
     * Get CdrUsageCost[] function
     *
     * @return HasMany
     */
    public function cdrUsageCosts()
    {
        $results = $this->hasMany(CdrUsageCost::class, 'subscription_id', 'id')
            ->whereNull('sales_invoice_line_id');

        return $results;
    }

    /**
     * Get SubscriptionLine Ids
     *
     * @param int $subscriptionId
     * @param string $dateFilter
     * @return array
     */
    public static function getSubscriptionLineIds($subscriptionId, $dateFilter = "")
    {
        $query = Subscription::find($subscriptionId)->subscriptionLines();
        if (!empty($dateFilter)) {
            $query->where("subscription_start", "")->orderBy("subscription_start", "asc");
        }
        if (empty($dateFilter)) {
            $query->orderBy("id", "asc");
        }
        return $query->pluck('id')->toArray();
    }

    /**
     * Get Diff in Days between subscription_start and subscription_stop function
     *
     * @return int
     */
    public function getStartStopDiffInDays()
    {
        $subscriptionStart = Carbon::parse($this->subscription_start);
        $subscriptionStop = now()->addYears(100);
        if (!empty($this->subscription_stop)) {
            $subscriptionStop = Carbon::parse($this->subscription_stop);
        }
        return $subscriptionStart->diffInDays($subscriptionStop);
    }

    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Get generated journal_no without the format
     *
     * @param int $tenantId
     * @return array
     */
    public static function getGeneratedSubscriptionNos($tenantId)
    {
        $subscriptionNos = Relation::select("subscription_no")
            ->where("tenant_id", $tenantId)
            ->get()
            ->pluck('subscription_no')
            ->toArray();

        $results = array_map(
            function ($subscriptionNo) {
                $digitResults = [];
                $withMatch = preg_match("/\d{1,}/", $subscriptionNo, $digitResults);
                if ($withMatch) {
                    return intval($digitResults[0]);
                }
                return null;
            },
            $subscriptionNos
        );
        return $results;
    }

    /**
     * Get subscription with tenant_id = ???
     *
     * @param QueryBuilder $query
     * @param integer[] $tenantIds
     * @return integer[]
     */
    public function scopeGetSubscriptionIdsForTenants($query, $tenantIds, $date)
    {
        return $query->leftJoin('relations', 'relations.id', '=', 'subscriptions.relation_id')
            ->whereIn('relations.tenant_id', $tenantIds)
            ->where('subscriptions.status', 1)
            ->where('subscriptions.subscription_start', '<=', $date)
            ->pluck("subscriptions.id")
            ->toArray();
    }


    /**
     * Get subscription_ids with relation_id = ???
     *
     * @param QueryBuilder $query
     * @param integer[] $relationIds
     * @return integer[]
     */
    public function scopeGetSubscriptionIdsForRelations($query, $relationIds)
    {
        return $query->whereIn('relation_id', $relationIds)
            ->pluck("id")
            ->toArray();
    }

    public function getProvisioningLine($backendApi)
    {
        return $this->subscriptionLines()
            ->whereHas(
                'product',
                function ($productQuery) use ($backendApi) {
                    $productQuery->where('backend_api', '=', "{$backendApi}");
                }
            )->first();
    }

    public function setSubscriptionStartAttribute($value)
    {
        $this->attributes['subscription_start'] = dateFormat($value);
    }

    public function setWishDateAttribute($value)
    {
        $this->attributes['wish_date'] = dateFormat($value);
    }

    public function setSubscriptionStopAttribute($value)
    {
        $this->attributes['subscription_stop'] = dateFormat($value);
    }

    public function setBillingStartAttribute($value)
    {
        $this->attributes['billing_start'] = dateFormat($value);
    }

    public function getJsonDatasAttribute()
    {
        $jsonDatas = $this->jsonDatas()->get();
        return $jsonDatas;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getLinesByType($lineType)
    {
        return $this->subscriptionLines()
            ->where('subscription_line_type', $lineType);
    }

    public function getTotalNRCAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.nrc"))->get() as $line) {
            $total += $line->linePrice;
        }
        return floatval($total);
    }

    public function getTotalNRCIncVatAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.nrc"))->get() as $line) {
            $total += getPriceIncVat($line->linePrice, $line->subscription->relation->tenant_id, $line->product_id);
        }
        return floatval($total);
    }

    public function getTotalMRCAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.mrc"))->get() as $line) {
            $total += $line->linePrice;
        }
        return floatval($total);
    }

    public function getTotalMRCIncVatAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.mrc"))->get() as $line) {
            $total += getPriceIncVat($line->linePrice, $line->subscription->relation->tenant_id, $line->product_id);
        }
        return floatval($total);
    }

    public function getTotalQRCAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.qrc"))->get() as $line) {
            $total += $line->linePrice;
        }
        return floatval($total);
    }

    public function getTotalQRCIncVatAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.qrc"))->get() as $line) {
            $total += getPriceIncVat($line->linePrice, $line->subscription->relation->tenant_id, $line->product_id);
        }
        return floatval($total);
    }

    public function getTotalYRCAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.yrc"))->get() as $line) {
            $total += $line->linePrice;
        }
        return floatval($total);
    }

    public function getTotalYRCIncVatAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.yrc"))->get() as $line) {
            $total += getPriceIncVat($line->linePrice, $line->subscription->relation->tenant_id, $line->product_id);
        }
        return floatval($total);
    }

    public function getTotalDepositAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.deposit"))->get() as $line) {
            $total += $line->linePrice;
        }
        return floatval($total);
    }

    public function getTotalDepositIncVatAttribute()
    {
        $total = 0;
        foreach ($this->getLinesByType(Config::get("constants.subscription_line_types.deposit"))->get() as $line) {
            $total += getPriceIncVat($line->linePrice, $line->subscription->relation->tenant_id, $line->product_id);
        }
        return floatval($total);
    }

    public function getIsStopedAttribute()
    {
        $subscription_stop = $this->getOriginal('subscription_stop');
        $now = now();
        $isSubscriptionStopReady = $subscription_stop && $subscription_stop < $now->format('Y-m-d');
        $subscriptionStopBeforeMidnight = Carbon::parse($subscription_stop)->format('Y-m-d 23:45:00');
        $isSubscriptionStopBeforeMidnight = $subscriptionStopBeforeMidnight <= $now->format('Y-m-d H:m:s');
        if ($isSubscriptionStopReady || $isSubscriptionStopBeforeMidnight) {
            return true;
        }
        return false;
    }

    public function contractPeriod()
    {
        return $this->hasOne(ContractPeriod::class, 'id');
    }

    public function getLineCountNoSubscriptionStopAttribute()
    {
        return $this->subscriptionLines()
            ->whereNull('subscription_stop')
            ->count();
    }

    public function getLineCountChangeSubscriptionStartAttribute()
    {
        return $this->subscriptionLines()->count();
    }

    public function statusSubscription()
    {
        return $this->hasOne(Status::class, 'id', 'status')
            ->where('status_type_id', 4);
    }

    public function getSubscriptionStatusAttribute()
    {
        return $this->statusSubscription()->first();
    }

    public function statusesSubscription()
    {
        return Status::where('status_type_id', 4);
    }

    public function getTerminatedAttribute()
    {
        $statusSubscription = $this->statusSubscription;
        $terminated = $statusSubscription && strtolower($statusSubscription->status) == 'terminated';

        if (!$terminated) {
            $subscription_stop = $this->subscription_stop;
            if ($subscription_stop) {
                $terminated = Carbon::now()->format('Y-m-d') > $subscription_stop;
            }
        }

        return $terminated;
    }

    /**
     * Get invoice count using subscription_line_ids
     *
     * @return mixed
     */
    public function getInvoiceCountAttribute()
    {
        $subscriptionLineIds = $this->subscriptionLines()->pluck('id')->toArray();
        return SalesInvoiceLine::whereIn('subscription_line_id', $subscriptionLineIds)
            ->select('sales_invoice_id')
            ->groupBy('sales_invoice_id')
            ->distinct()
            ->count();
    }
}
