<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'type',
        'from_name',
        'from_email',
        'bcc_email',
        'subject',
        'body_html',
    ];

    public static $fields = [
        'tenant_id',
        'product_id',
        'type',
        'from_name',
        'from_email',
        'bcc_email',
        'subject',
        'body_html',
    ];

    protected $appends = [];

    protected $casts = [];

    public static $includes = [];

    public static $scopes = [];

    public static $sorts = [];

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



    /**
     * Get binding Product
     *
     * @return \Product
     */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
