<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Plan extends BaseModel
{
    protected $table = 'plans';

    protected $fillable = [
        'tenant_id',
        'parent_plan',
        'project_id',
        'plan_type',
        'description',
        'description_long',
        'billing_start',
        'plan_start',
        'plan_stop'
    ];

    public static $fields = [
        'id',
        'tenant_id',
        'parent_plan',
        'plan_type',
        'project_id',
        'description',
        'description_long',
        'billing_start',
        'plan_start',
        'plan_stop',
    ];

    public static $scopes = [
        'tenant',
        'parent',
        'project',
        'plan-lines',
        'plan-lines.plan-line-price',
        'plan-lines.line-type',
    ];

    public static $withScopes = [
        'tenant',
        'parent',
        'project',
        'planLines.planLinePrice'
    ];

    protected $appends = [
        'costs',
        'active',
        'has_active_line'
    ];

    protected $casts = [
        'plan_start' => 'datetime:Y-m-d',
        'plan_stop' => 'datetime:Y-m-d',
    ];

    protected $searchable = [
        'description,description_long,plan_start,plan_stop'
    ];

    public static $searchableCols = [
        'description',
        'plan_start',
        'plan_stop'
    ];

    public static $filters = [
        'id',
        'tenant_id',
        'parent_plan',
        'plan_type',
        'project_id',
        'description',
        'description_long',
        'billing_start',
        'plan_start',
        'plan_stop',
        'update_line_stop'
    ];

    public static $sortables = [
        'id',
        'tenant_id',
        'parent_plan',
        'plan_type',
        'project_id',
        'description',
        'description_long',
        'billing_start',
        'plan_start',
        'plan_stop',
        'update_line_stop'
    ];

    public static $searchables = [
        'description',
        'description_long',
        'billing_start',
        'plan_start',
        'plan_stop',
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($plan) {
            PlanLine::where('plan_line_id', $plan->id)->delete();
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Plan::class, 'parent_plan');
    }

    public function planLines()
    {
        return $this->hasMany(PlanLine::class)->with(['product', 'lineType']);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get Subscription function
     *
     * @return \Subscription
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get total costs of the plan_line_prices
     *
     * @return String
     */
    public function getCostsAttribute()
    {
        $totalCost = floatval(0);
        $planLines = $this->planLines()->get();
        foreach ($planLines as $planLine) {
            $totalCost += $planLine->line_price;
        }
        return $totalCost;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActiveAttribute()
    {
        $isValid = !$this->plan_start && !$this->plan_stop || $this->plan_start <= now() && $this->plan_stop >= now();
        return $isValid;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getHasActiveLineAttribute()
    {
        foreach ($this->planLines as $plan_line) {
            if ($plan_line->active) {
                return true;
            }
        }
        return false;
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

    /**
     * Scopes to return specified relations
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
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
     * Scopes to return specified relations
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeComplete($query)
    {
        return $query->whereHas('planLines', function ($query) {
            $query->whereHas('planLinePrices');
        });
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query2) {
                $query2->where('plan_stop', '>', now());
                $query2->orWhereNull('plan_stop');
            });
        });
    }
}
