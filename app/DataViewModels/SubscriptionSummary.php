<?php

namespace App\DataViewModels;

use App\Models\BaseModel;
use App\Models\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionSummary extends BaseModel
{
    protected $table = 'v_subscription_summaries';

    protected $casts = [
        'subscription_start' => 'datetime:Y-m-d',
        'subscription_stop' => 'datetime:Y-m-d',
        'price_excl_vat' => 'float',
        'price_incl_vat' => 'float'
    ];

    protected $fillable = [
        'id',
        'description',
        'subscription_start',
        'subscription_stop',
        'status',
        'customer_number',
        'price_excl_vat',
        'price_incl_vat',
        'json_datas'
    ];

    public static $fields = [
        'id',
        'description',
        'subscription_start',
        'subscription_stop',
        'status',
        'relation_id',
        'customer_number',
        'price_excl_vat',
        'price_incl_vat'
    ];

    public static $filters = [
        'id',
        'description',
        'subscription_start',
        'subscription_stop',
        'status',
        'customer_number',
        'price_excl_vat',
        'price_incl_vat'
    ];

    public static $sortables = [
        'id',
        'description',
        'subscription_start',
        'subscription_stop',
        'status',
        'customer_number',
        'price_excl_vat',
        'price_incl_vat',
        'json_datas'
    ];

    public static $searchables = [
        'description',
        'subscription_start',
        'subscription_stop',
        'customer_number'
    ];

    /**
     * Get Relation function
     *
     * @return BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }
}
