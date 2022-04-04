<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonType extends BaseModel
{
    protected $fillable = ['type'];

    public static $fields = [
        'id',
        'type'
    ];

    public static $scopes = [
        'persons'
    ];

    /**
     * Get persons
     *
     * @return \Person
     */
    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
