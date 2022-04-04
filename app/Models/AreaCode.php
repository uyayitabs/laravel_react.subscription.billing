<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaCode extends BaseModel
{
    public $incrementing = false;

    protected $fillable = [
        'layer_1',
        'layer_2',
        'sub_area',
        'max_speed_down',
        'max_speed_up',
        'status',
    ];
}
