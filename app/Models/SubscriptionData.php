<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionData extends BaseModel
{
    protected $fillable = ['subscription_id', 'transaction_id', 'vendor', 'data'];

    protected $casts = [
        'data' => 'json'
    ];
}
