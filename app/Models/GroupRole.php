<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRole extends BaseModel
{
    protected $fillable = [
        'role_id',
        'group_id',
        'read',
        'write'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
