<?php

namespace App\DataViewModels;

use App\Models\BaseModel;
use App\Models\Relation;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReport extends BaseModel
{
    protected $table = 'v_payments_report';

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'id',
        'date',
        'amount',
        'descr',
        'iban',
        'account_name',
        'return_code',
        'return_reason',
        'tenant_id',
        'tenant_name',
        'relation_id',
        'type',
        'created_at',
    ];

    public static $fields = [
        'id',
        'date',
        'amount',
        'descr',
        'iban',
        'account_name',
        'return_code',
        'return_reason',
        'tenant_id',
        'tenant_name',
        'relation_id',
        'type',
        'created_at',
    ];

    public static $filters = [
        'id',
        'date',
        'amount',
        'descr',
        'iban',
        'account_name',
        'return_code',
        'return_reason',
        'tenant_id',
        'tenant_name',
        'relation_id',
        'type',
        'created_at',
    ];

    public static $sortables = [
        'id',
        'date',
        'amount',
        'descr',
        'iban',
        'account_name',
        'return_code',
        'return_reason',
        'tenant_id',
        'tenant_name',
        'relation_id',
        'type',
        'created_at',
    ];

    public static $searchables = [
        'id',
        'date',
        'amount',
        'descr',
        'iban',
        'account_name',
        'return_code',
        'return_reason',
        'tenant_id',
        'tenant_name',
        'relation_id',
        'type',
        'created_at',
    ];

    /**
     * Get Tenant function
     *
     * @return BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get Relation function
     *
     * @return BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class, 'relation_id');
    }
}
