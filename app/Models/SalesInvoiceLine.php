<?php

namespace App\Models;

use App\Traits\HasCdrUsageCostTrait;
use App\Traits\HasInvoiceLineGadgetTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

class SalesInvoiceLine extends BaseModel
{
    use HasCdrUsageCostTrait;
    use HasInvoiceLineGadgetTrait;

    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'order_line_id',
        'description',
        'description_long',
        'price_per_piece',
        'quantity',
        'price',
        'price_vat',
        'price_total',
        'vat_code',
        'vat_percentage',
        'subscription_id',
        'subscription_line_id',
        'sales_invoice_line_type',
        'plan_line_id',
        'invoice_start',
        'invoice_stop'
    ];

    protected $casts = [
        'invoice_start' => 'datetime',
        'invoice_stop' => 'datetime',
        'price_per_piece' => 'float',
        'quantity' => 'float',
        'vat_percentage' => 'float',
        'price' => 'float',
        'price_vat' => 'float',
        'price_total' => 'float'
    ];

    protected $dates = [
        'invoice_start',
        'invoice_stop',
        'line_start_date',
        'line_stop_date'
    ];

    protected $appends = [
        'period',
        'line_start_date',
        'line_stop_date',
        'invoice_status',
        'has_gadget'
    ];

    public static $fields = [
        'id',
        'sales_invoice_id',
        'product_id',
        'description',
        'description_long',
        'price_per_piece',
        'quantity',
        'price',
        'sales_invoice_line_type',
        'invoice_start',
        'invoice_stop',
    ];

    public static $scopes = [
        'subscription-line',
        'subscription-line.line-type',
        'plan-line',
        'plan-line.line-type'
    ];

    public static $withScopes = [
        'salesInvoice',
        'subscriptionLine',
        'subscriptionLine.lineType',
        'planLine',
        'planLine.lineType'
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();

        static::creating(static function (SalesInvoiceLine $salesInvoiceLine) {
            $salesInvoiceLine->updatePriceTotals();
        });

        static::updating(static function (SalesInvoiceLine $salesInvoiceLine) {
            $salesInvoiceLine->updatePriceTotals();
        });

        static::created(static function (SalesInvoiceLine $salesInvoiceLine) {
            $salesInvoiceLine->salesInvoice->updatePriceTotals();
        });

        static::updated(static function (SalesInvoiceLine $salesInvoiceLine) {
            $salesInvoiceLine->salesInvoice->updatePriceTotals();
        });

        static::deleted(static function (SalesInvoiceLine $salesInvoiceLine) {
            $salesInvoiceLine->salesInvoice->updatePriceTotals();
        });
    }

    /**
     * Get SalesInvoice function
     *
     * @return \SalesInvoice
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id', 'id');
    }

    /**
     * Get SalesInvoice function
     *
     * @return \SalesInvoice
     */
    public function salesInvoiceLines()
    {
        return $this->hasMany(SalesInvoiceLine::class, 'sales_invoice_id', 'id')->withAll();
    }


    /**
     * Get Product function
     *
     * @return \Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get SubscriptionLine function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class, 'subscription_line_id', 'id');
    }

    /**
     * Get PlanLine function
     *
     * @return \PlanLine
     */
    public function planLine()
    {
        return $this->belongsTo(PlanLine::class, 'plan_line_id', 'id');
    }

    /**
     * Scopes to return all the relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    public function getInvoiceStatusAttribute()
    {
        if (!$this->salesInvoice_id) {
            return null;
        }
        return $this->salesInvoice->invoice_status;
    }

    /**
     * getPeriodAttribute() function
     *
     */
    public function getPeriodAttribute(): array
    {
            $period = [
                'start' => $this->invoice_start,
                'stop' => $this->invoice_stop
            ];
            return $period;
    }

    /**
     * Get line (SubscriptionLine | PlanLine) start date
     *
     * @return string
     */
    public function getLineStartDateAttribute()
    {
        $startDate = "";

        if (!empty($this->subscriptionLine()->first())) {
            $subscriptionLine = $this->subscriptionLine()->first();
            $startDate = $subscriptionLine->subscription_start;
        }
        if (!empty($this->planLine()->first())) {
            $planLine = $this->planLine()->first();
            $startDate = $planLine->plan_start;
        }
        return $startDate;
    }

    /**
     * Get line (SubscriptionLine | PlanLine) stop date
     *
     * @return string
     */
    public function getLineStopDateAttribute()
    {
        $stopDate = "";

        if (!empty($this->subscriptionLine()->first())) {
            $subscriptionLine = $this->subscriptionLine()->first();
            $stopDate = $subscriptionLine->subscription_stop;
        }
        if (!empty($this->planLine()->first())) {
            $planLine = $this->planLine()->first();
            $stopDate = $planLine->plan_stop;
        }
        return $stopDate;
    }

    /**
     * Update price totals
     *
     * @return void
     */
    public function updatePriceTotals(): void
    {
        if ($this->isDirty(['price_per_piece', 'price', 'quantity']) === false) {
            return;
        }
        $vatCodeId = 6;
        $vatPercentage = Config::get('constants.options.default_vat_percentage');
        if ($this->product_id) {
            $tenantProduct = TenantProduct::where([
                ['product_id', $this->product_id],
                ['tenant_id', $this->salesInvoice->tenant_id]
            ])->first();
            $vatCode = $tenantProduct->vatCode ?? null;
            if ($vatCode) {
                $vatCodeId = $vatCode->id;
                $vatPercentage = $vatCode->vat_percentage;
            }
        }
        $pricePerQuantity = $this->price_per_piece * $this->quantity;
        $priceVat = $pricePerQuantity * $vatPercentage;
        $priceTotal =  $pricePerQuantity + $priceVat;
        $this->vat_code = $vatCodeId;
        $this->vat_percentage = $vatPercentage;
        $this->price = $pricePerQuantity;
        $this->price_vat = $priceVat;
        $this->price_total = $priceTotal;
    }
}
