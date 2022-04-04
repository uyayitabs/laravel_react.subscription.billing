<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends BaseModel
{
    protected $table = 'products';

    protected $fillable = [
        'product_type_id',
        'serialized',
        'status_id',
        'description',
        'description_long',
        'vendor',
        'vendor_partcode',
        'weight',
        'ean_code',
        'active_from',
        'backend_api',
    ];

    public static $fields = [
        'id',
        'product_type_id',
        'serialized',
        'status_id',
        'description',
        'description_long',
        'vendor',
        'vendor_partcode',
        'weight',
        'ean_code',
        'backend_api',
    ];

    public static $scopes = [
        'serial',
        'stock',
        'product-type',
        'subscription-lines'
    ];

    public static $withScopes = [
        'serial',
        'stock',
        'productType',
        'subscriptionLines'
    ];

    protected $appends = [
        'vat_code',
        'product_status'
    ];

    public static $filters = [
        'product_type_id',
        'serialized',
        'status_id',
        'description',
        'description_long',
        'vendor',
        'vendor_partcode',
        'weight',
        'ean_code',
        'active_from',
        'backend_api',
    ];

    public static $sortables = [
        'product_type_id',
        'serialized',
        'status_id',
        'description',
        'description_long',
        'vendor',
        'vendor_partcode',
        'weight',
        'ean_code',
        'active_from',
        'backend_api',
    ];

    public static $searchables = [
        'description',
        'description_long',
        'backend_api',
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
     * Get ProductType function
     *
     * @return \ProductType
     */
    public function productType()
    {
        return $this->hasOne(
            ProductType::class,
            'id',
            'product_type_id'
        );
    }

    /**
     * Get Stock function
     *
     * @return \Stock
     */
    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id', 'id');
    }

    /**
     * Get Serial function
     *
     * @return \Serial
     */
    public function serials()
    {
        return $this->hasMany(Serial::class, 'product_id', 'id');
    }

    /**
     * Get Serial function
     *
     * @return \Serial
     */
    public function serial()
    {
        return $this->hasOne(Serial::class, 'product_id', 'id');
    }

    /**
     * Get SubscriptionLine function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLines()
    {
        return $this->hasMany(SubscriptionLine::class, 'product_id', 'id');
    }

    /**
     * Get the vat_codes that belong to the product.
     */
    public function tenantProducts()
    {
        return $this->hasMany(TenantProduct::class);
    }

    public function tenantProduct()
    {
        return $this->belongsTo(TenantProduct::class, 'id', 'product_id');
    }


    /**
     * Get EmailTemplate function
     *
     * @return \ProductType
     */
    public function emailTemplate()
    {
        return $this->hasOne(
            EmailTemplate::class,
            'id',
            'product_id'
        );
    }

    /**
     * Get p function
     *
     * @return \SubscriptionLine
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Get p function
     *
     * @return \SubscriptionLine
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Get json_data function
     *
     * @return \JsonData
     */
    public function jsonData()
    {
        return $this->hasOne(JsonData::class, 'product_id', 'id');
    }

    public function setActiveFromAttribute($value)
    {
        $this->attributes['active_from'] = dateFormat($value);
    }

    public function getVatCodeAttribute()
    {
        return $this->TenantProducts()
            ->where('tenant_id', currentTenant('id'))
            ->with('vatcode')
            ->get()
            ->pluck('vatcode')
            ->flatten()
            ->first();
    }

    public function statusProduct()
    {
        return $this->hasOne(Status::class, 'id', 'status_id')
            ->where('status_type_id', 5);
    }

    public function getProductStatusAttribute()
    {
        return $this->statusProduct()->first();
    }

    public function statusesProduct()
    {
        return Status::where('status_type_id', 5);
    }

    public function getPriceInclVatAttribute()
    {
        $tenantId = currentTenant('id');
        $now = Carbon::now();
        $price = $this->tenantProducts()->where('active_from', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('active_to')
                    ->orWhere('active_to', '>', $now);
            })->orderBy('active_from', 'desc')->first()->price;
        return getPriceIncVat($price, $tenantId, $this->id);
    }

    public function getPriceExclVatAttribute()
    {
        $now = Carbon::now();
        return $this->tenantProducts()->where('active_from', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('active_to')
                    ->orWhere('active_to', '>', $now);
            })->orderBy('active_from', 'desc')->first()->price;
    }
}
