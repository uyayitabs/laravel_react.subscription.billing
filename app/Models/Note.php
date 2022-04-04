<?php

namespace App\Models;

class Note extends BaseModel
{
    protected $hidden = [];

    protected $fillable = [
        'type',
        'related_id',
        'text',
        'user_id'
    ];

    public static $fields = [
        'id',
        'type',
        'related_id',
        'text',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Search Scope
     * @param $query, $keyword
     *
     * @return object|array data related models
     */
    public function scopeByType($query, $type)
    {
        $query->where('type', $type);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
