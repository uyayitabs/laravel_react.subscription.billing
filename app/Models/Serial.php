<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serial extends BaseModel
{
    protected $fillable = [
        'serial',
        'json_data',
        'product_id',
        'warehouse_id'
    ];

    public static $fields = [
        'id',
        'product_id',
        'serial',
        'warehouse_id',
        'json_data'
    ];

    protected $casts = [
        'id' => 'int',
        'json_data' => 'array'
    ];

    /**
     * Get Product function
     *
     * @return \Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }

    /**
     * Get Warehouse function
     *
     * @return \Warehouse
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }


    /**
     * Get SubscriptionLine function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLine()
    {
        return $this->hasOne(SubscriptionLine::class, 'subscription_id', 'id');
    }
}
