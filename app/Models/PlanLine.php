<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPlanLinePriceTrait;
use App\Traits\HasPlanLineTypesTrait;

// use Kalnoy\Nestedset\NodeTrait;

class PlanLine extends BaseModel
{
    // use NodeTrait;
    use HasPlanLinePriceTrait;
    use HasPlanLineTypesTrait;

    protected $fillable = [
        'plan_id',
        'product_id',
        'plan_line_type',
        'parent_plan_line_id',
        'mandatory_line',
        'plan_start',
        'plan_stop',
        'description',
        'description_long'
    ];

    public static $fields = [
        'id',
        'plan_id',
        'product_id',
        'plan_line_type',
        'parent_plan_line_id',
        'mandatory_line',
        'plan_start',
        'plan_stop',
        'description',
        'description_long'
    ];

    protected $searchable = [
        'plan_start,plan_stop,description',
        'product|name',
        'lineType|line_type'
    ];

    public static $searchableCols = [
        'product',
        'description',
        'plan_start',
        'plan_stop',
        'type'
    ];

    public static $scopes = [
        'parent-plan',
        'plan-line-type',
        'plan-line-price',
        'line-type',
        'product'
    ];

    public static $withScopes = [
        'plan',
        'planLinePrices',
        'parentPlan',
        'product',
        'planLinePrice',
        'subscriptionLines',
        'lineType'
    ];

    protected $appends = [
        'line_price',
        'plan_line_price_margin',
        'plan_line_price_valid',
        'plan_line_price_fixed_price',
        'active'
    ];

    protected $casts = [
        'plan_start' => 'datetime:Y-m-d',
        'plan_stop' => 'datetime:Y-m-d'
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();

        // before delete() method call this
        static::deleting(function ($plan_line) {
            PlanLine::where('parent_plan_line_id', $plan_line->id)->delete();
            PlanLinePrice::where('plan_line_id', $plan_line->id)->delete();
        });
    }

    public function setPlanStartAttribute($value)
    {
        $this->attributes['plan_start'] = dateFormat($value);
    }

    public function setPlanStopAttribute($value)
    {
        $this->attributes['plan_stop'] = dateFormat($value);
    }

    /**
     * Get SubscriptionLine function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLines()
    {
        return $this->hasMany(SubscriptionLine::class, 'plan_line_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function planLinePrices()
    {
        return $this->hasMany(PlanLinePrice::class);
    }

    /**
     * Get the line type
     *
     * @return \PlanSubscriptionLineType
     */
    public function lineType()
    {
        return $this->hasOne(PlanSubscriptionLineType::class, 'id', 'plan_line_type');
    }

    public function parentPlan()
    {
        return $this->belongsTo(Plan::class, 'parent_plan_line_id');
    }

    public function parentPlanLinesRecursive()
    {
        return $this->HasOne(Plan::class, 'id', 'parent_plan_line_id')->with('parentPlanLinesRecursive');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->with('productType');
    }

    /**
     * Get p function
     *
     * @return \PlanLine
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Get price of the plan_line based on plan_line_price
     *
     * @return float
     */
    public function getLinePriceAttribute()
    {
        $totalPrice = floatval(0);
        $planLinePrice = $this->planLinePrice()->first();
        if (!empty($planLinePrice)) {
            $totalPrice = floatval($planLinePrice->fixed_price);
        }
        return $totalPrice;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActiveAttribute()
    {
        $withStartNStop = !$this->plan_start && !$this->plan_stop;
        $planStartLessNowWithStop = $this->plan_start <= now() && (!$this->plan_stop || $this->plan_stop >= now());
        return $withStartNStop || $planStartLessNowWithStop;
    }
}
