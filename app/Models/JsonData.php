<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSubscriptionTrait;
use App\Traits\HasSubscriptionLineTrait;
use App\Traits\HasTenantTrait;
use App\Traits\HasRelationTrait;
use App\Traits\HasPlanTrait;
use App\Traits\HasPlanLineTrait;
use App\Traits\HasProductTrait;

class JsonData extends BaseModel
{
    use HasSubscriptionTrait;
    use HasTenantTrait;

    protected $fillable = [
        'json_data',
        'tenant_id',
        'relation_id',
        'plan_id',
        'plan_line_id',
        'subscription_id',
        'subscription_line_id',
        'product_id',
        'transaction_id',
        'backend_api'
    ];

    protected $casts = [
        'id' => 'int',
        'json_data' => 'array'
    ];

    /**
     * Get relation function
     *
     * @return \Relation
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get relation function
     *
     * @return \Relation
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get relation function
     *
     * @return \SubscriptionLine
     */
    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class);
    }
}
