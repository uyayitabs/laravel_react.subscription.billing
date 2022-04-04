<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSubscriptionLineType extends BaseModel
{
    protected $fillable = [
        'line_type',
        'description'
    ];

    public static $fields = [
        'id',
        'line_type',
        'description'
    ];
}
