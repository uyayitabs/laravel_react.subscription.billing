<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends BaseModel
{
    protected $fillable = [
        'numeric',
        'alpha2',
        'alpha3',
        'name',
        'official_name',
        'sovereignty'
    ];

    public static $fields = [
        'numeric',
        'alpha2',
        'alpha3',
        'name',
        'official_name',
        'sovereignty'
    ];

    public static $scopes = [
        'address'
    ];

    protected $primaryKey = 'id';

    /**
     * Get binding Address
     *
     * @return \Address
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'country', 'id');
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class);
    }
}
