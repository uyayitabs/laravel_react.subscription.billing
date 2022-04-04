<?php

namespace App\Models;

class RelationsPerson extends BaseModel
{
    protected $table = 'relations_persons';

    protected $fillable = [
        'relation_id',
        'person_id',
        'status',
        'primary',
        'person_type_id'
    ];

    protected $casts = [
        'primary' => 'bool'
    ];

    protected $primaryKey = ['relation_id', 'person_id'];
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('relation_id', $this->getAttribute('relation_id'))
            ->where('person_id', $this->getAttribute('person_id'));
    }

    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
    }

    /**
     * Get binding Relation
     *
     * @return \Relation
     */

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * Get binding Person
     *
     * @return \Person
     */

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
