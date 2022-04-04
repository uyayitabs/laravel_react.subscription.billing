<?php

namespace App\Models;

use App\Traits\HasManyEntry;
use App\Traits\BelongsToTenant;

class Account extends BaseModel
{
    use BelongsToTenant;
    use HasManyEntry;

    protected $table = 'accounts';
    protected $fillable = [
        'tenant_id',
        'description',
        'type',
        'code',
        'parent_id',
        'export_code',
    ];
    public static $fields = [
        'tenant_id',
        'description',
        'type',
        'code',
        'parent_id',
        'export_code',
    ];
    protected $searchable = [
        'description,type,code,export_code'
    ];
    public static $searchableCols = [
        'description',
        'type',
        'code',
        'export_code'
    ];

    protected $appends = [];

    public static $includes = [
        'tenant_id',
        'description',
        'type',
        'code',
        'parent_id',
        'export_code',
    ];
    public static $scopes = [];
    public static $sorts = [
        'id',
        'description',
        'type',
        'code',
        'parent_id',
        'export_code',
    ];
}
