<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zipcode extends BaseModel
{
    protected $fillable = [
        'id',
        'zipcode',
        'housenumber',
        'housenumber_suffix',
        'room',
        'street1',
        'street2',
        'city',
        'country_id',
        'latitude',
        'longitude',
        'rdxCoordinate',
        'rdyCoordinate'
    ];
}
