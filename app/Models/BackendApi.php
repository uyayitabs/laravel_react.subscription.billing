<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackendApi extends Model
{
    protected $fillable = [
        'backend_api',
        'status'
    ];

    /**
     * Scopes to return all the relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
