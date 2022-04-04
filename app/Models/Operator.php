<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends BaseModel
{
    protected $fillable = [
        'name',
        'provisioning_api'
    ];

    public static $fields = [
        'name',
        'provisioning_api'
    ];

    public static $includes = [];

    public static $sorts = [
        'name',
    ];
}
