<?php

namespace App\DataViewModels;

use App\Models\BaseModel;
use App\Models\Relation;
use App\Models\Tenant;
use App\Models\User;

class TenantUser extends BaseModel
{
    protected $table = 'v_users_tenants';

    protected $fillable = [
        'id',
        'username',
        'person_id',
        'relation_id',
        'tenant_id',
        'children'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }
}
