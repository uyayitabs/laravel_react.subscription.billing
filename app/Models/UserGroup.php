<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends BaseModel
{
    protected $table = 'user_groups';

    protected $fillable = [
        'user_id',
        'group_id'
    ];

    protected $field = [
        'user_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class)->with('groupRoles');
    }
}
