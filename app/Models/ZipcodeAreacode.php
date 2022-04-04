<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZipcodeAreacode extends BaseModel
{
    protected $fillable = [
        'zipcode_id',
        'area_code_id',
        'status',
    ];
}
