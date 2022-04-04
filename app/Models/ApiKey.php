<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends BaseModel
{
    protected $fillable = [
        'user_id',
        'key',
        'name',
        'description',
        'expire_date',
        'ip_address',
        'last_used',
        'json_data'
    ];

    public static $fields = [
        'user_id',
        'key',
        'name',
        'description',
        'expire_date',
        'ip_address',
        'last_used',
        'json_data'
    ];

    protected $casts = [
        'expire_date' => 'datetime:Y-m-d',
        'last_used' => 'datetime:Y-m-d'
    ];

    public function User()
    {
        $this->belongsTo(User::class, 'user_id', 'id');
    }
}
