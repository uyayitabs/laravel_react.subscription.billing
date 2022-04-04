<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends BaseModel
{
    protected $table = 'fiscal_years';

    protected $fillable = [
        'tenant_id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    public static $fields = [
        'tenant_id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    protected $searchable = [
        'description,date_from,date_to,is_closed'
    ];

    public static $searchableCols = [
        'description',
        'date_from',
        'date_to',
        'is_closed'
    ];

    protected $casts = [
        'date_from' => 'datetime:Y-m-d',
        'date_to' => 'datetime:Y-m-d',
    ];

    public static $includes = [
        'tenant_id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    public static $scopes = [];

    public static $sorts = [
        'id',
        'description',
        'date_from',
        'date_to',
        'is_closed'
    ];

    public static $withScopes = [];

    /**
     * Get binding Tenant
     *
     * @return \Tenant
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
