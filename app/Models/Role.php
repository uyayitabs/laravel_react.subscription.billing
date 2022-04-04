<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
    protected $fillable = [
        'module',
        'name'
    ];

    protected $appends = [
        'slug'
    ];

    public function groupRoles()
    {
        return $this->hasMany(GroupRole::class);
    }

    public function getSlugAttribute()
    {
        return strtolower(str_replace('Controller', '', $this->module));
    }

    public function scopeSlug($query, $slug)
    {
        $module = strtoupper($slug) . "Controller";
        return $query->where("module", $module);
    }
}
