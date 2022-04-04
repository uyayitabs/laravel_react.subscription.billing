<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantProduct extends BaseModel
{
    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'active_from',
        'active_to',
        'price',
        'status',
        'account_id',
        'vat_code_id',
    ];

    public static $fields = [
        'tenant_id',
        'product_id',
        'active_from',
        'active_to',
        'price',
        'status',
        'account_id',
        'vat_code_id',
    ];

    public static $scopes = [
        'product',
        'product.productType'
    ];

    public static $withScopes = [
        'product',
        'product.productType'
    ];

    protected $searchable = [
        'price',
        'product|serialized,' .
            'description,description_long,vendor,' .
            'vendor_partcode,weight,ean_code,' .
            'backend_api,productType:type'
    ];

    protected $casts = [
        'active_from' => 'datetime:Y-m-d',
        'active_to' => 'datetime:Y-m-d',
        'price' => 'float'
    ];

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('tenant_id', '=', $this->getAttribute('tenant_id'))
            ->where('product_id', '=', $this->getAttribute('product_id'));

        return $query;
    }

    /**
     * Get VatCode function
     *
     * @return \VatCode
     */
    public function vatCode()
    {
        return $this->hasOne(VatCode::class, 'id', 'vat_code_id');
    }

    /**
     * Get Product
     *
     * @return \Product
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * Static function that returns account_id based on
     * tenantId and productId params
     *
     * @param mixed $tenantId
     * @param mixed $productId
     * @return mixed
     */
    public static function getAccountId($tenantId, $productId)
    {
        return TenantProduct::where([
            ['tenant_id', '=', $tenantId],
            ['product_id', '=', $productId]
        ])->pluck('account_id')->first();
    }

    /**
     * Static function that returns vat_code_id based on
     * tenantId and productId params
     *
     * @param mixed $tenantId
     * @param mixed $productId
     * @return mixed
     */
    public static function getVatCodeId($tenantId, $productId)
    {
        return TenantProduct::where([
            ['tenant_id', '=', $tenantId],
            ['product_id', '=', $productId]
        ])->pluck('vat_code_id')->first();
    }


    public static function getVatCode($tenantId, $productId)
    {
        return TenantProduct::where([
            ['tenant_id', '=', $tenantId],
            ['product_id', '=', $productId]
        ])->first()->vatcode;
    }

    public function getVatPercentageAttribute()
    {
        return $this->vatCode->vat_percentage;
    }

    // public function getPriceAttribute()
    // {
    //     $price = $this->attributes['price'];
    //     if (!$price) $this->product->price;
    //     return $price;
    // }
}
