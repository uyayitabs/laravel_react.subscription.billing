<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionLineMeta extends BaseModel
{
    protected $fillable = [
        'subscription_line_id',
        'key',
        'value'
    ];

    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class);
    }

    public function networkOperator()
    {
        return $this->hasOne(NetworkOperator::class, 'id', 'value');
    }
}
