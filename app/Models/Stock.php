<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends BaseModel
{
    protected $fillable = [
        'stock',
        'min_stock',
    ];

    public static $fields = [
        'id',
        'product_id',
        'warehouse_id',
        'stock',
        'min_stock'
    ];

    public static $scopes = [
        'warehouse',
        'product'
    ];

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
     * Get Product function
     *
     * @return \Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }
}
