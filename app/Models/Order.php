<?php

namespace App\Models;

use App\Traits\HasStatusTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    use HasStatusTrait;

    protected $fillable = [
        'date',
        'source',
        'status_id',
        'data',
    ];

    public static $fields = [
        'date',
        'source',
        'status_id',
        'data',
    ];

    public static $scopes = [];

    protected $appends = [];

    protected $casts = [
        'id' => 'int',
        'date' => 'datetime',
        'data' => 'array'
    ];

    protected $hidden = [];

    protected $searchable = [
        'data,date'
    ];

    public static $searchableCols = [
        'data',
        'date',
    ];

    public static $withScopes = [
        'status',
    ];


    public function status()
    {
        $statusType = StatusType::where('type', 'order')->first();
        $statuses = $this->statuses()->where('status_type_id', $statusType->id);
        return $statuses->first();
    }
}
