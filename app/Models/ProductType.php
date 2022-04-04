<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends BaseModel
{
    protected $fillable = ['type'];

    public static $fields = [
        'id',
        'type'
    ];
    public static $scopes = [

    ];
    public static $withScopes = [

    ];
    /**
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }
}
