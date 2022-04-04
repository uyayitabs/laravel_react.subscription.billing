<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends BaseModel
{
    protected $fillable = [
        'id',
        'status',
        'label',
        'status_type_id',
    ];

    protected $appends = [
        'status_type'
    ];

    public static $fields = [
        'id',
        'status',
        'label',
        'status_type_id',
    ];

    public static $includes = [];

    public static $sorts = [
        'id',
        'status',
        'status_type_id',
    ];

    public static $withScopes = [
        'status_type_id',
    ];

    public static $scopes = [
        'type'
    ];


    /**
     * Get StatusType function
     *
     * @return \StatusType
     */
    public function type()
    {
        return $this->hasOne(StatusType::class, 'id', 'status_type_id');
    }

    public function getStatusTypeAttribute()
    {
        return $this->type;
    }

    /**
     * Scopes to return all the relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }
}
