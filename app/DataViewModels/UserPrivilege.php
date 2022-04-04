<?php

namespace App\DataViewModels;

use App\Models\BaseModel;
use App\Models\Relation;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivilege extends BaseModel
{
    protected $table = 'v_user_privileges';

    protected $casts = [];

    protected $fillable = [
        'user_id',
        'username',
        'person_id',
        'group_id',
        'group_name',
        'group_description',
        'write',
        'read',
        'role_module',
        'role_description',
    ];

    public static $fields = [
        'user_id',
        'username',
        'person_id',
        'group_id',
        'group_name',
        'group_description',
        'write',
        'read',
        'role_module',
        'role_description',
    ];

    public static $filters = [
        'user_id',
        'username',
        'person_id',
        'group_id',
        'group_name',
        'group_description',
        'write',
        'read',
        'role_module',
        'role_description',
    ];

    public static $sortables = [
        'user_id',
        'username',
        'person_id',
        'group_id',
        'group_name',
        'group_description',
        'write',
        'read',
        'role_module',
        'role_description',
    ];

    public static $searchables = [
        'user_id',
        'username',
        'person_id',
        'group_id',
        'group_name',
        'group_description',
        'write',
        'read',
        'role_module',
        'role_description',
    ];
}
