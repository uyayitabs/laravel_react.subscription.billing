<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider',
        'token',
        'ip'
    ];

    /**
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeValidateProvider($query, $provider, $token, $ip = null)
    {
        $where = [
            [ 'provider', '=', $provider ],
            [ 'token', '=', $token ]
        ];

        if ($ip) {
            $where[] =  [ 'ip', '=', $ip ];
        }
        return $query->where($where);
    }

    /**
     * Get the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }
}
