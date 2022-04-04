<?php

namespace App\Models;

use App\Traits\HasTenantTrait;
use Illuminate\Database\Eloquent\Model;

class BillingRun extends BaseModel
{
    use HasTenantTrait;

    protected $table = 'billing_runs';

    protected $fillable = [
        'tenant_id',
        'status_id',
        'direct_debit_id',
        'dd_file',
        'last_error',
        'date',
        'sales_invoice_count'
    ];

    public static $sortables = [
        'status_id',
        'date',
        'id'
    ];

    public static $fields = [
        'id',
        'tenant_id',
        'status_id',
        'direct_debit_id',
        'dd_file',
        'last_error',
        'date',
        'price_sum',
        'price_vat_sum',
        'price_total_sum'
    ];

    protected $searchable = ['id'];

    public static $scopes = [];
    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'price_sum' => 'float',
        'price_vat_sum' => 'float',
        'price_total_sum' => 'float',
    ];
    protected $appends = [];

    /**
     * Get SalesInvoice function
     *
     * @return \SalesInvoice[]
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'billing_run_id', 'id');
    }

    /**
     * Get Status function
     *
     * @return \Status
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'status_id', 'id');
    }

    /**
     * Get Status function
     *
     * @return \Status
     */
    public function getPriceSumAttribute()
    {
        return $this->salesInvoices->sum('price');
    }

    /**
     * Get Status function
     *
     * @return \Status
     */
    public function getPriceVatSumAttribute()
    {
        return $this->salesInvoices->sum('price_vat');
    }

    /**
     * Get Status function
     *
     * @return \Status
     */
    public function getPriceTotalSumAttribute()
    {
        return $this->salesInvoices->sum('price_total');
    }
}
