<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends BaseModel
{
    protected $fillabe = [
        'id',
        'name'
    ];

    public static $fields = [
        'id',
        'name'
    ];
}
