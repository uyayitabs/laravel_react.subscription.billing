<?php

namespace App\DataViewModels;

use App\Models\BaseModel;
use App\Models\Relation;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M7SubscriptionLine extends BaseModel
{
    protected $table = 'v_m7_subscription_lines';

    protected $casts = [
        'subscription_line_start' => 'datetime:Y-m-d',
        'subscription_line_end' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'subscription_id',
        'subscription_line_id',
        'subscription_line_start',
        'subscription_line_end',
        'descr',
        'tenant',
        'relation_id',
        'customer_number',
    ];

    public static $fields = [
        'subscription_id',
        'subscription_line_id',
        'subscription_line_start',
        'subscription_line_end',
        'descr',
        'tenant',
        'relation_id',
        'customer_number',
    ];

    public static $filters = [
        'subscription_id',
        'subscription_line_id',
        'subscription_line_start',
        'subscription_line_end',
        'descr',
        'tenant',
        'relation_id',
        'customer_number',
    ];

    public static $sortables = [
        'subscription_id',
        'subscription_line_id',
        'subscription_line_start',
        'subscription_line_end',
        'descr',
        'tenant',
        'relation_id',
        'customer_number',
    ];

    public static $searchables = [
        'subscription_id',
        'subscription_line_id',
        'subscription_line_start',
        'subscription_line_end',
        'descr',
        'tenant',
        'customer_number',
        'relation_id',
    ];

    /**
     * Get Subscription function
     *
     * @return BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get Subscription function
     *
     * @return BelongsTo
     */
    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class, 'subscription_line_id');
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
