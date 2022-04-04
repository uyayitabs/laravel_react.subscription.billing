<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberRange extends BaseModel
{
    protected $table = 'number_ranges';

    protected $fillable = [
        'tenant_id',
        'type',
        'description',
        'start',
        'end',
        'format',
        'randomized',
        'current'
    ];

    public static $fields = [
        'id',
        'tenant_id',
        'type',
        'description',
        'start',
        'end',
        'format',
        'randomized',
        'current',
    ];

    protected $searchable = [
        'type,description,start,end,format,randomized,current'
    ];

    public static $searchableCols = [
        'type',
        'description',
        'start',
        'end',
        'format',
        'randomized',
        'current'
    ];

    public static $constTypes = [
        'invoice_no',
        'customer_number',
        'journal_no',
        'entry_no',
        'subscription_no'
    ];

    public static $scopes = [
        'tenant'
    ];

    public static $withScopes = [
        'tenant'
    ];

    protected $appends = [];

    /**
     * Get tenant function
     *
     * @return \Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Sales Invoice relationship
     *
     * @return \SalesInvoice
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Scopes to return all the relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Get sample implemenation of a number_range
     *
     * @return string
     */
    public function getSampleImplementationAttribute()
    {
        return generateNumberFromNumberRange(
            $this->tenant_id,
            $this->type,
            false
        );
    }
}
