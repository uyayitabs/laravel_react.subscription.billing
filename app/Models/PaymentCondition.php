<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenantTrait;

class PaymentCondition extends BaseModel
{
    use HasTenantTrait;

    protected $table = 'payment_conditions';

    protected $fillable = [
        'tenant_id',
        'direct_debit',
        'pay_in_advance',
        'status',
        'description',
        'net_days',
        'default',
        'created_by',
        'updated_by'
    ];

    public static $fields = [
        'tenant_id',
        'direct_debit',
        'pay_in_advance',
        'status',
        'description',
        'net_days',
        'default',
        'created_by',
        'updated_by'
    ];

    protected $searchable = [
        'direct_debit,pay_in_advance,status,description,net_days,default'
    ];

    public static $searchableCols = [
        'direct_debit',
        'pay_in_advance',
        'status',
        'description',
        'net_days',
        'default'
    ];

    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeDefault($query)
    {
        return $query->where('default', 1);
    }

    public function scopeDirectDebit($query)
    {
        return $query->where('direct_debit', 1);
    }
}
