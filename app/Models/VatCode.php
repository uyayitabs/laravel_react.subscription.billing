<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAccountTrait;

class VatCode extends BaseModel
{
    use HasAccountTrait;

    protected $table = 'vat_codes';

    protected $fillable = [
        'tenant_id',
        'vat_percentage',
        'description',
        'active_from',
        'active_to',
        'account_id'
    ];

    protected $appends = [];

    public static $fields = [
        'tenant_id',
        'vat_percentage',
        'description',
        'active_from',
        'active_to',
        'account_id'
    ];

    protected $searchable = [
        'vat_percentage,description,active_from,active_to',
        'account|description'
    ];

    public static $searchableCol = [
        'vat_percentage',
        'description',
        'active_from',
        'active_to',
        [
            'account' => [
                'description'
            ]
        ]
    ];

    public static $searchableCols = [
        'vat_percentage',
        'description',
        'active_from',
        'active_to',
        'account'
    ];

    public static $includes = [
        'tenant',
    ];

    public static $sorts = [
        'tenant_id',
        'vat_percentage',
        'description',
        'active_from',
        'active_to',
        'account_id'
    ];

    protected $casts = [
        'active_from' => 'datetime:Y-m-d',
        'active_to' => 'datetime:Y-m-d',
        'vat_percentage' => 'float'
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $query->vat_percentage = $query->vat_percentage / 100;
        });

        static::updating(function ($query) {
            $query->vat_percentage = $query->vat_percentage / 100;
        });
    }

    public function setActiveFromAttribute($value)
    {
        $this->attributes['active_from'] = dateFormat($value);
    }

    public function setActiveToAttribute($value)
    {
        $this->attributes['active_to'] = dateFormat($value);
    }

    /**
     * Get tenant
     *
     * @return \Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get tenant product
     *
     * @return \TenantProduct[]
     */
    public function tenantProducts()
    {
        return $this->hasMany(TenantProduct::class);
    }

    /**
     * Get in_use
     * @return boolean
     */
    public function getInUseAttribute()
    {
        return $this->tenantProducts()->count() > 0 ? true : false;
    }
}
