<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends BaseModel
{
    protected $table = 'groups';

    protected $fillable = [
        'name',
        'description',
        'tenant_id'
    ];

    public static $fields = [
        'id',
        'description',
        'journal_no',
        'tenant_id'
    ];

    protected $searchable = [
        'name,description'
    ];

    public static $searchableCols = [
        'name',
        'description'
    ];

    public static $includes = [
        'group-roles'
    ];

    public static $scopes = [
        'group-role'
    ];

    public function groupRoles()
    {
        return $this->hasMany(GroupRole::class, 'group_id')->with('role');
    }
}
