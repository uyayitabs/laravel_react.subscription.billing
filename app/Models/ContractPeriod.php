<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractPeriod extends BaseModel
{
    protected $fillable = [
        'id',
        'period',
        'net_days',
        'tenant_id',
    ];

    public static $fields = [
        'id',
        'period',
        'tenant_id',
        'net_days',
    ];

    /**
     * Get binding Tenant
     *
     * @return \Tenant
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
