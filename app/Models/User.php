<?php

namespace App\Models;

use App\DataViewModels\TenantUser;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Traits\HasUserCodeTrait;
use App\Traits\HasSearchTrait;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasUserCodeTrait;
    use HasSearchTrait;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        //'email',
        'password',
        'person_id',
        'last_tenant_id',
        'last_login',
        //'type',
        'enabled'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public static $hiders = [
        'password',
        'remember_token'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public static $scopes = [
        'person'
    ];

    public static $withScopes = [
        'person.full_name'
    ];

    public static $fields = [
        'id',
        'username',
        'person_id',
        'last_login',
        'enabled',
        'activated',
        'password_expiration',
        'created_at',
        'updated_at',
        'last_tenant_id',
        'password',
        'remember_token',
    ];

    public static $sortables = [
        'id',
        'person_id',
        'username',
        'last_login',
        'enabled'
    ];

    protected $appends = [
        'roles',
        'groups'
    ];

    public static $searchables = [
        'username',
    ];

    public static $searchableCols = [
        'username',
        'person'
    ];

    protected $casts = [
        'last_login' => 'datetime:Y-m-d H:i:s'
    ];

    public static $filters = [
        'id',
        'username',
        'person_id',
        'last_login',
        'enabled',
        'activated',
        'last_tenant_id',
    ];

    /**
     * Auto delete user relations
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->userGroups()->delete();
        });

        static::updating(function ($user) {
            if ($user->password) {
                $user->password_expiration = carbonAdd('now', 60);
            }
        });
    }

    /**
     * Get associated person
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function tenantUsers()
    {
        return $this->hasMany(TenantUser::class);
    }

    public function userGroups()
    {
        return $this->hasMany(UserGroup::class);
    }

    public function getRolesAttribute()
    {
        $return = $this->userGroups()->with(['group'])->whereHas('group', function ($query) {
            $query->where('tenant_id', currentTenant('id'));
        })->first();
        if ($return) {
            return $return->group->groupRoles;
        }

        return array();
    }

    public function getGroupsAttribute()
    {
        $r = [];

        $return = $this->userGroups()->with(['group'])->whereHas('group', function ($query) {
            $query->where('tenant_id', currentTenant('id'));
        })->first();

        if ($return) {
            $r['id'] = $return->group->id;
            $r['name'] = $return->group->name;
        }

        return (object) $r;
    }

    public function getGroupAttribute()
    {
        $r = [];

        $return = $this->userGroups()->with(['group'])->whereHas('group', function ($query) {
            $query->where('tenant_id', currentTenant('id'));
        })->first();

        if ($return) {
            $r['id'] = $return->group->id;
            $r['name'] = $return->group->name;
        }

        return $r;
    }

    public function getIsEmployeeAttribute()
    {
        return $this->tenantUsers()->where('children', '>', 0)->count() > 0;
    }

    public function getIsEndCustomerAttribute()
    {
        return $this->tenantUsers()->where('children', 0)->count() > 0;
    }

    public function getMailingEmailAttribute()
    {
        return setEmailAddress($this->username);
    }
}
