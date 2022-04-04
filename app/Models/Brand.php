<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends BaseModel
{
    protected $fillable = ['tenant_id', 'relation_id', 'name', 'logo', 'favicon', 'theme'];

    public static $fields = [
        'id',
        'tenant_id',
        'relation_id',
        'name',
        'logo',
        'favicon',
        'theme'
    ];

    public static $scopes = [
        'tenant'
    ];

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
