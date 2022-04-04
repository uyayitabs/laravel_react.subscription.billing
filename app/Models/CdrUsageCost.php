<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CdrUsageCost extends BaseModel
{
    protected $fillable = [
        'unique_id',
        'customer_number',
        'relation_id',
        'subscription_id',
        'sales_invoice_line_id',
        'channel_id',
        'sender',
        'recipient',
        'duration',
        'platform',
        'total_cost',
        'start_cost',
        'minute_cost',
        'traffic_class',
        'direction',
        'extension',
        'roaming',
        'bundle',
        'order_number',
        'datetime'
    ];

    public static $fields = [
        'unique_id',
        'customer_number',
        'relation_id',
        'subscription_id',
        'sales_invoice_line_id',
        'channel_id',
        'sender',
        'recipient',
        'duration',
        'platform',
        'total_cost',
        'start_cost',
        'minute_cost',
        'traffic_class',
        'direction',
        'extension',
        'roaming',
        'bundle',
        'order_number',
        'datetime'
    ];

    protected $appends = [];

    public static $includes = [
        'unique_id',
        'customer_number',
        'relation_id',
        'subscription_id',
        'sales_invoice_line_id',
        'channel_id',
        'sender',
        'recipient',
        'duration',
        'platform',
        'total_cost',
        'start_cost',
        'minute_cost',
        'traffic_class',
        'direction',
        'extension',
        'roaming',
        'bundle',
        'order_number',
        'datetime'
    ];

    public static $scopes = [];

    public static $sorts = [
        'unique_id',
        'customer_number',
        'relation_id',
        'subscription_id',
        'sales_invoice_line_id',
        'channel_id',
        'sender',
        'recipient',
        'duration',
        'platform',
        'total_cost',
        'start_cost',
        'minute_cost',
        'traffic_class',
        'direction',
        'extension',
        'roaming',
        'bundle',
        'order_number',
        'datetime'
    ];

    protected $casts = [
        'datetime' => 'datetime:Y-m-d',
        'total_cost' => 'float',
        'start_cost' => 'float',
        'minute_cost' => 'float'
    ];

    public static $withScopes = [];

    /**
     * Get Subscription function
     *
     * @return \Subscription
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    public function salesInvoiceLine()
    {
        return $this->belongsTo(SalesInvoiceLine::class);
    }
}
