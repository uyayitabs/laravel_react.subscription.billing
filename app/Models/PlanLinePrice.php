<?php

namespace App\Models;

class PlanLinePrice extends BaseModel
{
    protected $fillable = [
        'plan_line_id',
        'parent_plan_line_id',
        'fixed_price',
        'margin',
        'price_valid_from'
    ];

    public static $fields = [
        'id',
        'plan_line_id',
        'parent_plan_line_id',
        'fixed_price',
        'margin',
        'price_valid_from'
    ];

    public static $scopes = [
        'plan-line'
    ];

    public static $withScopes = [
        'planLine'
    ];

    protected $casts = [
        'price_valid_from' => 'datetime:Y-m-d',
        'price_incl_vat' => 'float',
        'price_excl_vat' => 'float',
        'margin' => 'float',
        'fixed_price' => 'float'
    ];

    protected $appends = [];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();
    }

    public function setPriceValidFromAttribute($value)
    {
        $this->attributes['price_valid_from'] = dateFormat($value);
    }

    public function planLine()
    {
        return $this->belongsTo(PlanLine::class);
    }

    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    public function getPriceExclVatAttribute()
    {
        return $this->fixed_price;
    }

    public function getPriceInclVatAttribute()
    {
        $tenantId = $this->planLine->plan->tenant_id;
        $productId = $this->planLine->product_id;
        $fixed_price = (float) $this->fixed_price;
        return getPriceIncVat($fixed_price, $tenantId, $productId);
    }
}
