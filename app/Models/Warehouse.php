<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends BaseModel
{
    protected $fillable = [
        'description',
        'status',
        'active_from',
        'active_to',
        'warehouse_location'
    ];

    public static $fields = [
        'id',
        'tenant_id',
        'warehouse_location',
        'description',
        'status',
        'active_from',
        'active_to'
    ];

    public static $scopes = [
        'tenant',
        'stocks',
        'serials'
    ];

    /**
     * Get tenant
     *
     * @return \Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Get Stock function
     *
     * @return \Stock[]
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get Serial function
     *
     * @return \Serial[]
     */
    public function serials()
    {
        return $this->hasMany(Serial::class);
    }
}
