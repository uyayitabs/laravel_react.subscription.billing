<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationType extends BaseModel
{
    protected $fillable = [
        'type',
    ];

    /**
     * Get relations[] function
     *
     * @return \Relation
     */
    public function relations()
    {
        return $this->hasMany(Relation::class, 'relation_type_id');
    }
}
