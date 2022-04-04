<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCountryTrait;

class State extends BaseModel
{
    use HasCountryTrait;

    protected $fillable = [
        'state',
        'country_id'
    ];

    public $timestamps = true;

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
