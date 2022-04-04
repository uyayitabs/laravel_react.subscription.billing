<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebServiceLog extends BaseModel
{
    protected $fillable = [
        'provider',
        'token',
        'ip',
        'req_data'
    ];
}
