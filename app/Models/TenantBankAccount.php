<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantBankAccount extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'account_id',
        'iban',
        'bic',
        'status',
        'bank_name',
        'bank_api'
    ];

    public static $fields = [
        'tenant_id',
        'account_id',
        'iban',
        'bic',
        'status',
        'bank_name',
        'bank_api',
    ];

    public static $scopes = [
        'tenant',
        'account'
    ];

    public static $withScopes = [
        'tenant',
        'account'
    ];

    protected $searchable = [
        'status,bank_name'
    ];

    /**
     * Get Tenant function
     *
     * @return \Tenant
     */
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * Get Account function
     *
     * @return \Account
     */
    public function account()
    {
        return $this->hasOne(Account::class);
    }
}
